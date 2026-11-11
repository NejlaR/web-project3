<?php

Flight::route('GET /products', function () {
    try {
        Flight::json(
            Flight::product_service()->get_all(),
            200
        );
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load products.'
        ], 500);
    }
});

Flight::route('GET /product/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Product ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $product = Flight::product_service()->get_by_id($id);

        if ($product === false) {
            Flight::json([
                'message' => 'Product not found.'
            ], 404);

            return;
        }

        Flight::json($product, 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the product.'
        ], 500);
    }
});

Flight::route('POST /products', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::product_service()->add($data);

        Flight::json([
            'message' => 'Product created successfully.',
            'product_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the product.'
        ], 500);
    }
});

Flight::route('PATCH /product/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Product ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::product_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'Product not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'Product updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the product.'
        ], 500);
    }
});

Flight::route('DELETE /product/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Product ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::product_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'Product not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'Product deleted successfully.'
        ], 200);

    } catch (PDOException $exception) {
        if (
            isset($exception->errorInfo[1])
            && (int) $exception->errorInfo[1] === 1451
        ) {
            Flight::json([
                'message' => 'This product belongs to an order and cannot be deleted.'
            ], 400);

            return;
        }

        Flight::json([
            'message' => 'Unable to delete the product.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/products",
 *     tags={"Products"},
 *     summary="Get all products",
 *     @OA\Response(response=200, description="List of products"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/product/{id}",
 *     tags={"Products"},
 *     summary="Get a product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Product details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Product not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/products",
 *     tags={"Products"},
 *     summary="Create a product",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "price", "category_id"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 minLength=1,
 *                 maxLength=100,
 *                 example="API Test Shoe"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 nullable=true,
 *                 example="Comfortable everyday shoe"
 *             ),
 *             @OA\Property(
 *                 property="price",
 *                 type="string",
 *                 pattern="^\d{1,8}(\.\d{1,2})?$",
 *                 example="49.90"
 *             ),
 *             @OA\Property(
 *                 property="image_url",
 *                 type="string",
 *                 maxLength=255,
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="category_id",
 *                 type="integer",
 *                 minimum=1,
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="stock",
 *                 type="integer",
 *                 minimum=0,
 *                 maximum=2147483647,
 *                 default=0,
 *                 example=5
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="product_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid product data"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/product/{id}",
 *     tags={"Products"},
 *     summary="Update selected product fields",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             minProperties=1,
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 minLength=1,
 *                 maxLength=100
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="price",
 *                 type="string",
 *                 pattern="^\d{1,8}(\.\d{1,2})?$",
 *                 example="59.90"
 *             ),
 *             @OA\Property(
 *                 property="image_url",
 *                 type="string",
 *                 maxLength=255,
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="category_id",
 *                 type="integer",
 *                 minimum=1
 *             ),
 *             @OA\Property(
 *                 property="stock",
 *                 type="integer",
 *                 minimum=0,
 *                 maximum=2147483647
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Product updated"),
 *     @OA\Response(response=400, description="Invalid product data"),
 *     @OA\Response(response=404, description="Product not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/product/{id}",
 *     tags={"Products"},
 *     summary="Delete a product",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Product deleted"),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID or product belongs to an order"
 *     ),
 *     @OA\Response(response=404, description="Product not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */