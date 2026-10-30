<?php

require_once __DIR__ . '/BaseDAO.php';

class OrderItemDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'order_items',
            'order_item_id',
            [
                'order_id',
                'product_id',
                'quantity',
                'unit_price'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function getByOrder($orderId)
    {
        $sql = "SELECT oi.*, p.name AS product_name,
                       p.price AS current_price
                FROM `{$this->table_name}` oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = :order_id";

        return $this->query($sql, [
            'order_id' => $orderId
        ]);
    }

    public function deleteByOrder($orderId)
    {
        $sql = "DELETE FROM `{$this->table_name}`
                WHERE order_id = :order_id";

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'order_id' => $orderId
        ]);

        return $statement->rowCount() > 0;
    }
}