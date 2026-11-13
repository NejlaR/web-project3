<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';

class CategoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new CategoryDAO());
    }

    public function add($data)
    {
        $data = $this->validateData($data, true);

        if ($this->dao->nameExists($data['name'])) {
            throw new InvalidArgumentException(
                'A category with this name already exists.'
            );
        }

        return parent::add($data);
    }

    public function update($data, $id)
    {
        $data = $this->validateData($data, false);

        if (
            isset($data['name'])
            && $this->dao->nameExists($data['name'], $id)
        ) {
            throw new InvalidArgumentException(
                'A category with this name already exists.'
            );
        }

        return parent::update($data, $id);
    }

    private function validateData($data, $isCreate)
    {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException(
                'Category data is required.'
            );
        }

        if ($isCreate && !array_key_exists('name', $data)) {
            throw new InvalidArgumentException(
                'Category name is required.'
            );
        }

        if (array_key_exists('name', $data)) {
            if (!is_string($data['name'])) {
                throw new InvalidArgumentException(
                    'Category name must be a string.'
                );
            }

            $data['name'] = trim($data['name']);

            if ($data['name'] === '') {
                throw new InvalidArgumentException(
                    'Category name is required.'
                );
            }

            if (preg_match_all('/./us', $data['name']) > 100) {
                throw new InvalidArgumentException(
                    'Category name must not exceed 100 characters.'
                );
            }
        }

        if (
            array_key_exists('description', $data)
            && $data['description'] !== null
            && !is_string($data['description'])
        ) {
            throw new InvalidArgumentException(
                'Description must be a string or null.'
            );
        }

        if (
            isset($data['description'])
            && strlen($data['description']) > 65535
        ) {
            throw new InvalidArgumentException(
                'Description must not exceed 65535 bytes.'
            );
        }

        return $data;
    }
}