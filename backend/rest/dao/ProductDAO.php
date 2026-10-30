<?php

require_once __DIR__ . '/BaseDAO.php';

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'products',
            'product_id',
            [
                'name',
                'description',
                'price',
                'image_url',
                'category_id',
                'stock'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function updateProduct($product_id, $data)
    {
        return $this->update($data, $product_id);
    }

    public function getByCategory($categoryId)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE category_id = :category_id";

        return $this->query($sql, [
            'category_id' => $categoryId
        ]);
    }

    public function search($term)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE name LIKE :name_term
                   OR description LIKE :description_term";

        return $this->query($sql, [
            'name_term' => '%' . $term . '%',
            'description_term' => '%' . $term . '%'
        ]);
    }
}