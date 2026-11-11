<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/ProductDAO.php';

class ReviewService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new ReviewDAO());
    }

    public function add($data)
    {
        $data = $this->validateData($data, true);

        return parent::add($data);
    }

    public function update($data, $id)
    {
        $data = $this->validateData($data, false);

        return parent::update($data, $id);
    }

    public function get_by_product_id($productId)
    {
        return $this->dao->getByProduct($productId);
    }

    public function get_average_rating($productId)
    {
        return $this->dao->getAverageRating($productId);
    }

    private function validateData($data, $isCreate)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'Review data is required.'
            );
        }

        $allowedFields = [
            'product_id',
            'user_id',
            'rating',
            'comment'
        ];

        foreach (array_keys($data) as $field) {
            if (!in_array($field, $allowedFields, true)) {
                throw new InvalidArgumentException(
                    'Unknown field: ' . $field
                );
            }
        }

        if ($isCreate) {
            foreach (['product_id', 'user_id', 'rating'] as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new InvalidArgumentException(
                        "Field '$field' is required."
                    );
                }
            }
        }

        foreach (['product_id', 'user_id'] as $field) {
            if (array_key_exists($field, $data)) {
                $id = filter_var(
                    $data[$field],
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (is_bool($data[$field]) || $id === false) {
                    throw new InvalidArgumentException(
                        "Field '$field' must be a positive integer."
                    );
                }

                $data[$field] = $id;
            }
        }

        if (isset($data['user_id'])) {
            $userDAO = new UserDAO();

            if ($userDAO->getById($data['user_id']) === false) {
                throw new InvalidArgumentException(
                    'The selected user does not exist.'
                );
            }
        }

        if (isset($data['product_id'])) {
            $productDAO = new ProductDAO();

            if ($productDAO->getById($data['product_id']) === false) {
                throw new InvalidArgumentException(
                    'The selected product does not exist.'
                );
            }
        }

        if (array_key_exists('rating', $data)) {
            $rating = filter_var(
                $data['rating'],
                FILTER_VALIDATE_INT,
                ['options' => [
                    'min_range' => 1,
                    'max_range' => 5
                ]]
            );

            if (is_bool($data['rating']) || $rating === false) {
                throw new InvalidArgumentException(
                    'Rating must be an integer between 1 and 5.'
                );
            }

            $data['rating'] = $rating;
        }

        if (
            array_key_exists('comment', $data)
            && $data['comment'] !== null
        ) {
            if (!is_string($data['comment'])) {
                throw new InvalidArgumentException(
                    'Comment must be a string or null.'
                );
            }

            if (strlen($data['comment']) > 65535) {
                throw new InvalidArgumentException(
                    'Comment is too long.'
                );
            }

            $data['comment'] = trim($data['comment']);
        }

        return $data;
    }
}