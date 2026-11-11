<?php

Flight::route('GET /order-items', function () {
    try {
        Flight::json(
            Flight::order_item_service()->get_all(),
            200
        );
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load order items.'
        ], 500);
    }
});

Flight::route('GET /order-item/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order item ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $item = Flight::order_item_service()->get_by_id($id);

        if ($item === false) {
            Flight::json([
                'message' => 'Order item not found.'
            ], 404);

            return;
        }

        Flight::json($item, 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the order item.'
        ], 500);
    }
});

Flight::route('POST /order-items', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::order_item_service()->add($data);

        Flight::json([
            'message' => 'Order item created successfully.',
            'order_item_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the order item.'
        ], 500);
    }
});

Flight::route('PATCH /order-item/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order item ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::order_item_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'Order item not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'Order item updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the order item.'
        ], 500);
    }
});

Flight::route('DELETE /order-item/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order item ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::order_item_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'Order item not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'Order item deleted successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to delete the order item.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/order-items",
 *     tags={"Order Items"},
 *     summary="Get all order items",
 *     @OA\Response(response=200, description="List of order items"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/order-item/{id}",
 *     tags={"Order Items"},
 *     summary="Get an order item by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Order item details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Order item not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/order-items",
 *     tags={"Order Items"},
 *     summary="Add an item to a pending order",
 *     description="The unit price is taken from the product. The order total is recalculated in the same transaction.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"order_id", "product_id", "quantity"},
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="order_id",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=2147483647,
 *                 example=2
 *             ),
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=2147483647,
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=2147483647,
 *                 example=2
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order item created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="order_item_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data, missing order or product, or order is not pending"
 *     ),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/order-item/{id}",
 *     tags={"Order Items"},
 *     summary="Update the quantity of an order item",
 *     description="Only pending orders can be changed. The saved unit price is preserved and the order total is recalculated.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=2147483647,
 *                 example=3
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Order item updated"),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data or order is not pending"
 *     ),
 *     @OA\Response(response=404, description="Order item not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/order-item/{id}",
 *     tags={"Order Items"},
 *     summary="Delete an item from a pending order",
 *     description="The order total is recalculated after deletion.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Order item deleted"),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID or order is not pending"
 *     ),
 *     @OA\Response(response=404, description="Order item not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */