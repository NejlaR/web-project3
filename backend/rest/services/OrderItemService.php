<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderItemDAO.php';
require_once __DIR__ . '/../dao/OrderDAO.php';
require_once __DIR__ . '/../dao/ProductDAO.php';

class OrderItemService extends BaseService
{
    private $orderDAO;
    private $productDAO;

    public function __construct()
    {
        parent::__construct(new OrderItemDAO());

        $this->orderDAO = new OrderDAO();
        $this->productDAO = new ProductDAO();
    }

    public function get_by_order_id($orderId)
    {
        return $this->dao->getByOrder($orderId);
    }

    public function add($data)
    {
        $this->validateFields(
            $data,
            ['order_id', 'product_id', 'quantity']
        );

        foreach (['order_id', 'product_id', 'quantity'] as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException(
                    "Field '$field' is required."
                );
            }

            $data[$field] = $this->positiveInteger(
                $data[$field],
                $field
            );
        }

        return $this->changeOrder(
            $data['order_id'],
            function () use ($data) {
                $product = $this->productDAO->getById(
                    $data['product_id']
                );

                if ($product === false) {
                    throw new InvalidArgumentException(
                        'The selected product does not exist.'
                    );
                }

                return $this->dao->add([
                    'order_id' => $data['order_id'],
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'unit_price' => $product['price']
                ]);
            }
        );
    }

    public function update($data, $id)
    {
        // Existing items allow quantity changes only.
        $this->validateFields($data, ['quantity']);

        $quantity = $this->positiveInteger(
            $data['quantity'],
            'quantity'
        );

        $item = $this->dao->getById($id);

        if ($item === false) {
            throw new InvalidArgumentException(
                'Order item not found.'
            );
        }

        return $this->changeOrder(
            $item['order_id'],
            function () use ($id, $quantity) {
                if ($this->dao->getById($id) === false) {
                    throw new InvalidArgumentException(
                        'Order item not found.'
                    );
                }

                return $this->dao->update([
                    'quantity' => $quantity
                ], $id);
            }
        );
    }

    public function delete($id)
    {
        $item = $this->dao->getById($id);

        if ($item === false) {
            return false;
        }

        return $this->changeOrder(
            $item['order_id'],
            function () use ($id) {
                return $this->dao->delete($id);
            }
        );
    }

    private function changeOrder($orderId, $operation)
    {
        $connection = Database::connect();
        $connection->beginTransaction();

        try {
            $order = $this->orderDAO->getForUpdate($orderId);

            if ($order === false) {
                throw new InvalidArgumentException(
                    'The selected order does not exist.'
                );
            }

            if ($order['status'] !== 'pending') {
                throw new InvalidArgumentException(
                    'Only pending orders can have their items changed.'
                );
            }

            $result = $operation();

            $this->orderDAO->recalculateTotal($orderId);

            $connection->commit();

            return $result;

        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    private function validateFields($data, $allowedFields)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'Order item data is required.'
            );
        }

        foreach (array_keys($data) as $field) {
            if (!in_array($field, $allowedFields, true)) {
                throw new InvalidArgumentException(
                    'Unknown or non-writable field: ' . $field
                );
            }
        }
    }

    private function positiveInteger($value, $field)
    {
        $number = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => [
                'min_range' => 1,
                'max_range' => 2147483647
            ]]
        );

        if (is_bool($value) || $number === false) {
            throw new InvalidArgumentException(
                "Field '$field' must be a positive integer."
            );
        }

        return $number;
    }
}