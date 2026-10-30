<?php

require_once __DIR__ . '/BaseDAO.php';

class ReviewDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'reviews',
            'review_id',
            [
                'product_id',
                'user_id',
                'rating',
                'comment'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function getByProduct($productId)
    {
        $sql = "SELECT r.*, u.full_name AS user_name
                FROM `{$this->table_name}` r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.product_id = :product_id
                ORDER BY r.created_at DESC";

        return $this->query($sql, [
            'product_id' => $productId
        ]);
    }

    public function getAverageRating($productId)
    {
        $sql = "SELECT AVG(rating) AS avg_rating
                FROM `{$this->table_name}`
                WHERE product_id = :product_id";

        $result = $this->query_unique($sql, [
            'product_id' => $productId
        ]);

        if ($result['avg_rating'] === null) {
            return null;
        }

        return round((float) $result['avg_rating'], 2);
    }

    public function deleteByUser($userId)
    {
        $sql = "DELETE FROM `{$this->table_name}`
                WHERE user_id = :user_id";

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'user_id' => $userId
        ]);

        return $statement->rowCount() > 0;
    }
}