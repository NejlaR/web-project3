<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/ProductService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/OrderService.php';
require_once __DIR__ . '/rest/services/OrderItemService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/CurrencyService.php';

Flight::register('category_service', 'CategoryService');
Flight::register('product_service', 'ProductService');
Flight::register('user_service', 'UserService');
Flight::register('order_service', 'OrderService');
Flight::register('order_item_service', 'OrderItemService');
Flight::register('review_service', 'ReviewService');
Flight::register('currency_service', 'CurrencyService');

Flight::route('GET /', function () {
    Flight::json([
        'message' => 'Aurora Shoes API is running'
    ]);
});

require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/ProductRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/OrderRoutes.php';
require_once __DIR__ . '/rest/routes/OrderItemRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
require_once __DIR__ . '/rest/routes/CurrencyRoutes.php';

Flight::map('notFound', function () {
    Flight::json([
        'message' => 'Endpoint not found.'
    ], 404);
});

Flight::map('error', function ($exception) {
    error_log($exception->getMessage());

    Flight::response()->clearBody();

    Flight::json([
        'message' => 'An unexpected server error occurred.'
    ], 500);
});

Flight::start();