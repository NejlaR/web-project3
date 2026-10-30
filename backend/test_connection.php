<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/rest/dao/CategoryDAO.php';
require_once __DIR__ . '/rest/dao/ProductDAO.php';

try {
    $categoryDAO = new CategoryDAO();
    $productDAO = new ProductDAO();

    echo "Database connection successful!" . PHP_EOL;

    echo PHP_EOL . "Categories:" . PHP_EOL;
    print_r($categoryDAO->getAll());

    echo PHP_EOL . "Products:" . PHP_EOL;
    print_r($productDAO->getAll());
} catch (PDOException $exception) {
    echo "Database check failed. Error code: "
        . $exception->getCode()
        . PHP_EOL;

    exit(1);
}