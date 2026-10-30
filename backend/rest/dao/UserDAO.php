<?php

require_once __DIR__ . '/BaseDAO.php';

class UserDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct(
            'users',
            'user_id',
            [
                'full_name',
                'email',
                'password_hash',
                'role'
            ]
        );
    }

    public function create($data)
    {
        return $this->add($data);
    }

    public function updateUser($user_id, $data)
    {
        return $this->update($data, $user_id);
    }

    public function getByEmail($email)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE email = :email";

        return $this->query_unique($sql, ['email' => $email]);
    }

    public function getAllOrdered()
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                ORDER BY created_at DESC";

        return $this->query($sql);
    }

    public function search($term)
    {
        $sql = "SELECT * FROM `{$this->table_name}`
                WHERE full_name LIKE :name_term
                   OR email LIKE :email_term";

        return $this->query($sql, [
            'name_term' => '%' . $term . '%',
            'email_term' => '%' . $term . '%'
        ]);
    }
}