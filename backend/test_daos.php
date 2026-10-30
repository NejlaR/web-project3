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

function testDAO($name, $dao)
{
    echo PHP_EOL . "=== $name ===" . PHP_EOL;

    $result = $dao->getAll();

    if ($name === 'Users') {
        foreach ($result as &$user) {
            unset($user['password_hash']);
        }

        unset($user);
    }

    print_r($result);
}

try {
    echo "=== DAO Testing Script ===" . PHP_EOL;

    $userDao = new UserDAO();
    $categoryDao = new CategoryDAO();
    $productDao = new ProductDAO();
    $orderDao = new OrderDAO();
    $orderItemDao = new OrderItemDAO();
    $reviewDao = new ReviewDAO();

    testDAO('Users', $userDao);
    testDAO('Categories', $categoryDao);
    testDAO('Products', $productDao);
    testDAO('Orders', $orderDao);
    testDAO('Order Items', $orderItemDao);
    testDAO('Reviews', $reviewDao);

    echo PHP_EOL
        . "DAO read tests completed successfully!"
        . PHP_EOL;

} catch (PDOException $exception) {
    echo "Database test failed. Error code: "
        . $exception->getCode()
        . PHP_EOL;

    exit(1);
}