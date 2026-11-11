<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class OrderService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new OrderDAO());
    }

    public function add($data)
    {
        $data = $this->validateData($data, true);

        $data['status'] = 'pending';
        $data['total_amount'] = '0.00';

        return parent::add($data);
    }

    public function update($data, $id)
    {
        $data = $this->validateData($data, false);

        return parent::update($data, $id);
    }

    public function get_by_user_id($userId)
    {
        return $this->dao->getByUser($userId);
    }

    private function validateData($data, $isCreate)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'Order data is required.'
            );
        }

        // New orders accept only the customer ID.
        $allowedFields = $isCreate
            ? ['user_id']
            : ['user_id', 'status'];

        foreach (array_keys($data) as $field) {
            if (!in_array($field, $allowedFields, true)) {
                throw new InvalidArgumentException(
                    'Unknown or non-writable field: ' . $field
                );
            }
        }

        if ($isCreate && !array_key_exists('user_id', $data)) {
            throw new InvalidArgumentException(
                'User ID is required.'
            );
        }

        if (array_key_exists('user_id', $data)) {
            $userId = filter_var(
                $data['user_id'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (is_bool($data['user_id']) || $userId === false) {
                throw new InvalidArgumentException(
                    'User ID must be a positive integer.'
                );
            }

            $userDAO = new UserDAO();

            if ($userDAO->getById($userId) === false) {
                throw new InvalidArgumentException(
                    'The selected user does not exist.'
                );
            }

            $data['user_id'] = $userId;
        }

        if (array_key_exists('status', $data)) {
            $allowedStatuses = [
                'pending',
                'paid',
                'shipped',
                'completed',
                'cancelled'
            ];

            if (!in_array($data['status'], $allowedStatuses, true)) {
                throw new InvalidArgumentException(
                    'Invalid order status.'
                );
            }
        }

        return $data;
    }
}