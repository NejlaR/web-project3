<?php

require_once __DIR__ . '/BaseDAO.php';

class OrderDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'orders',
            'order_id',
            [
                'user_id',
                'status',
                'total_amount'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function getByUser($userId)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE user_id = :user_id
                ORDER BY order_date DESC";

        return $this->query($sql, [
            'user_id' => $userId
        ]);
    }

    public function updateOrderStatus($orderId, $status)
    {
        return $this->update(
            ['status' => $status],
            $orderId
        );
    }

    public function getDetails($orderId)
    {
        $sql = "SELECT o.*, u.full_name, u.email
                FROM `{$this->table_name}` o
                JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = :order_id";

        return $this->query_unique($sql, [
            'order_id' => $orderId
        ]);
    }
}