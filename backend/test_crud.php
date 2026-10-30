<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/rest/dao/UserDAO.php';
require_once __DIR__ . '/rest/dao/CategoryDAO.php';
require_once __DIR__ . '/rest/dao/ProductDAO.php';
require_once __DIR__ . '/rest/dao/OrderDAO.php';
require_once __DIR__ . '/rest/dao/OrderItemDAO.php';
require_once __DIR__ . '/rest/dao/ReviewDAO.php';

function checkResult($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$connection = null;
$exitCode = 0;

try {
    $connection = Database::connect();

    // Rollback requires transactional tables.
    $tables = $connection->query(
        "SELECT TABLE_NAME, ENGINE
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
         AND TABLE_NAME IN (
             'users', 'categories', 'products',
             'orders', 'order_items', 'reviews'
         )"
    )->fetchAll();

    checkResult(count($tables) === 6, 'Missing database tables.');

    foreach ($tables as $table) {
        checkResult(
            strtoupper($table['ENGINE']) === 'INNODB',
            'All test tables must use InnoDB.'
        );
    }

    $connection->beginTransaction();

    $userDAO = new UserDAO();
    $categoryDAO = new CategoryDAO();
    $productDAO = new ProductDAO();
    $orderDAO = new OrderDAO();
    $itemDAO = new OrderItemDAO();
    $reviewDAO = new ReviewDAO();

    $suffix = bin2hex(random_bytes(6));

    // CREATE

    $userId = $userDAO->create([
        'full_name' => 'Test Customer',
        'email' => "test-$suffix@example.com",
        'password_hash' => password_hash(
            'TestPassword123!',
            PASSWORD_BCRYPT
        ),
        'role' => 'customer'
    ]);

    $categoryId = $categoryDAO->create([
        'name' => "Test Category $suffix",
        'description' => 'Temporary category'
    ]);

    $productId = $productDAO->create([
        'name' => 'Test Shoe',
        'price' => '50.00',
        'category_id' => $categoryId,
        'stock' => 10
    ]);

    $orderId = $orderDAO->create([
        'user_id' => $userId,
        'status' => 'pending',
        'total_amount' => '50.00'
    ]);

    $itemId = $itemDAO->create([
        'order_id' => $orderId,
        'product_id' => $productId,
        'quantity' => 1,
        'unit_price' => '50.00'
    ]);

    $reviewId = $reviewDAO->create([
        'product_id' => $productId,
        'user_id' => $userId,
        'rating' => 5,
        'comment' => 'Test review'
    ]);

    $cases = [
        [
            'Users',
            $userDAO,
            $userId,
            ['full_name' => 'Updated Customer']
        ],
        [
            'Categories',
            $categoryDAO,
            $categoryId,
            ['description' => 'Updated category']
        ],
        [
            'Products',
            $productDAO,
            $productId,
            ['name' => 'Updated Shoe']
        ],
        [
            'Orders',
            $orderDAO,
            $orderId,
            ['status' => 'paid']
        ],
        [
            'Order Items',
            $itemDAO,
            $itemId,
            ['unit_price' => '45.00']
        ],
        [
            'Reviews',
            $reviewDAO,
            $reviewId,
            ['comment' => "Customer's updated review"]
        ]
    ];

    // READ and UPDATE

    foreach ($cases as [$name, $dao, $id, $changes]) {
        checkResult(
            $dao->getById($id) !== false,
            "$name: created record was not found."
        );

        checkResult(
            $dao->update($changes, $id),
            "$name: update failed."
        );

        $record = $dao->getById($id);

        checkResult(
            $record !== false,
            "$name: updated record was not found."
        );

        foreach ($changes as $column => $expectedValue) {
            checkResult(
                (string) $record[$column] === (string) $expectedValue,
                "$name: updated value does not match."
            );
        }

        echo "$name: CREATE, READ and UPDATE passed."
            . PHP_EOL;
    }

    // DELETE children before parent records.

    foreach (array_reverse($cases) as [$name, $dao, $id]) {
        checkResult(
            $dao->delete($id),
            "$name: delete failed."
        );

        checkResult(
            $dao->getById($id) === false,
            "$name: record still exists after deletion."
        );

        echo "$name: DELETE passed." . PHP_EOL;
    }

    echo PHP_EOL
        . "All CRUD tests passed for all six entities!"
        . PHP_EOL;

} catch (Throwable $exception) {
    $exitCode = 1;

    if ($exception instanceof PDOException) {
        echo "Database test failed. Error code: "
            . $exception->getCode()
            . PHP_EOL;
    } else {
        echo "Test failed: "
            . $exception->getMessage()
            . PHP_EOL;
    }

} finally {
    if ($connection !== null && $connection->inTransaction()) {
        $connection->rollBack();

        echo "Test transaction rolled back." . PHP_EOL;
    }
}

exit($exitCode);