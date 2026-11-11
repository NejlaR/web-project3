<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new UserDAO());
    }

    public function get_all()
    {
        $users = parent::get_all();

        foreach ($users as &$user) {
            unset($user['password_hash']);
        }

        unset($user);

        return $users;
    }

    public function get_by_id($id)
    {
        $user = parent::get_by_id($id);

        if ($user !== false) {
            unset($user['password_hash']);
        }

        return $user;
    }

    public function add($data)
    {
        $data = $this->validateData($data, true);

        return parent::add($data);
    }

    public function update($data, $id)
    {
        $data = $this->validateData($data, false, $id);

        return parent::update($data, $id);
    }

    private function validateData($data, $isCreate, $id = null)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'User data is required.'
            );
        }

        $allowedFields = ['full_name', 'email', 'password', 'role'];

        foreach (array_keys($data) as $field) {
            if (!in_array($field, $allowedFields, true)) {
                throw new InvalidArgumentException(
                    'Unknown field: ' . $field
                );
            }
        }

        if ($isCreate) {
            foreach (['full_name', 'email', 'password'] as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new InvalidArgumentException(
                        "Field '$field' is required."
                    );
                }
            }

            if (!array_key_exists('role', $data)) {
                $data['role'] = 'customer';
            }
        }

        if (array_key_exists('full_name', $data)) {
            if (
                !is_string($data['full_name'])
                || trim($data['full_name']) === ''
            ) {
                throw new InvalidArgumentException(
                    'Full name must be a non-empty string.'
                );
            }

            $data['full_name'] = trim($data['full_name']);

            if (preg_match_all('/./us', $data['full_name']) > 100) {
                throw new InvalidArgumentException(
                    'Full name must not exceed 100 characters.'
                );
            }
        }

        if (array_key_exists('email', $data)) {
            if (!is_string($data['email'])) {
                throw new InvalidArgumentException(
                    'Email must be a string.'
                );
            }

            $data['email'] = trim($data['email']);

            if (
                strlen($data['email']) > 120
                || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            ) {
                throw new InvalidArgumentException(
                    'A valid email address is required.'
                );
            }

            $existingUser = $this->dao->getByEmail($data['email']);

            if (
                $existingUser !== false
                && (int) $existingUser['user_id'] !== (int) $id
            ) {
                throw new InvalidArgumentException(
                    'This email address is already in use.'
                );
            }
        }

        if (
            array_key_exists('role', $data)
            && !in_array($data['role'], ['customer', 'admin'], true)
        ) {
            throw new InvalidArgumentException(
                'Role must be customer or admin.'
            );
        }

        if (array_key_exists('password', $data)) {
            if (
                !is_string($data['password'])
                || strlen($data['password']) < 8
                || strlen($data['password']) > 72
            ) {
                throw new InvalidArgumentException(
                    'Password must contain between 8 and 72 bytes.'
                );
            }

            $data['password_hash'] = password_hash(
                $data['password'],
                PASSWORD_BCRYPT
            );

            unset($data['password']);
        }

        return $data;
    }
}