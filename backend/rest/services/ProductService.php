<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDAO.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';

class ProductService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new ProductDAO());
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

    public function get_by_category_id($categoryId)
    {
        return $this->dao->getByCategory($categoryId);
    }

    private function validateData($data, $isCreate)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'Product data is required.'
            );
        }

        if ($isCreate) {
            foreach (['name', 'price', 'category_id'] as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new InvalidArgumentException(
                        "Field '$field' is required."
                    );
                }
            }
        }

        if (array_key_exists('name', $data)) {
            if (
                !is_string($data['name'])
                || trim($data['name']) === ''
            ) {
                throw new InvalidArgumentException(
                    'Product name must be a non-empty string.'
                );
            }

            $data['name'] = trim($data['name']);

            if (preg_match_all('/./us', $data['name']) > 100) {
                throw new InvalidArgumentException(
                    'Product name must not exceed 100 characters.'
                );
            }
        }

        if (array_key_exists('price', $data)) {
            if (
                !is_scalar($data['price'])
                || is_bool($data['price'])
                || !preg_match(
                    '/^\d{1,8}(\.\d{1,2})?$/D',
                    (string) $data['price']
                )
            ) {
                throw new InvalidArgumentException(
                    'Price must be between 0 and 99999999.99, with up to two decimal places.'
                );
            }
        }

        if (array_key_exists('stock', $data)) {
            $stock = filter_var(
                $data['stock'],
                FILTER_VALIDATE_INT,
                ['options' => [
                    'min_range' => 0,
                    'max_range' => 2147483647
                ]]
            );

            if (is_bool($data['stock']) || $stock === false) {
                throw new InvalidArgumentException(
                    'Stock must be a non-negative integer.'
                );
            }

            $data['stock'] = $stock;
        }

        if (array_key_exists('category_id', $data)) {
            $categoryId = filter_var(
                $data['category_id'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (is_bool($data['category_id']) || $categoryId === false) {
                throw new InvalidArgumentException(
                    'Category ID must be a positive integer.'
                );
            }

            $categoryDAO = new CategoryDAO();

            if ($categoryDAO->getById($categoryId) === false) {
                throw new InvalidArgumentException(
                    'The selected category does not exist.'
                );
            }

            $data['category_id'] = $categoryId;
        }

        foreach (['description', 'image_url'] as $field) {
            if (
                array_key_exists($field, $data)
                && $data[$field] !== null
                && !is_string($data[$field])
            ) {
                throw new InvalidArgumentException(
                    "Field '$field' must be a string or null."
                );
            }
        }

        if (
            isset($data['description'])
            && strlen($data['description']) > 65535
        ) {
            throw new InvalidArgumentException(
                'Description must not exceed 65535 bytes.'
            );
        }

        if (
            isset($data['image_url'])
            && preg_match_all('/./us', $data['image_url']) > 255
        ) {
            throw new InvalidArgumentException(
                'Image URL must not exceed 255 characters.'
            );
        }

        return $data;
    }
}