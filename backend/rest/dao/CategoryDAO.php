<?php

require_once __DIR__ . '/BaseDAO.php';

class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'categories',
            'category_id',
            [
                'name',
                'description'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function updateCategory($category_id, $data)
    {
        return $this->update($data, $category_id);
    }

    public function getByName($name)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE name = :name";

        return $this->query_unique($sql, ['name' => $name]);
    }

    public function nameExists($name, $excludeId = null)
    {
        $sql = "SELECT category_id FROM `{$this->table_name}`
                WHERE name = :name";

        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND category_id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        return $this->query_unique($sql, $params) !== false;
    }
}