<?php

Flight::route('GET /orders', function () {
    try {
        Flight::json(
            Flight::order_service()->get_all(),
            200
        );
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load orders.'
        ], 500);
    }
});

Flight::route('GET /order/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $order = Flight::order_service()->get_by_id($id);

        if ($order === false) {
            Flight::json([
                'message' => 'Order not found.'
            ], 404);

            return;
        }

        Flight::json($order, 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the order.'
        ], 500);
    }
});

Flight::route('POST /orders', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::order_service()->add($data);

        Flight::json([
            'message' => 'Order created successfully.',
            'order_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the order.'
        ], 500);
    }
});

Flight::route('PATCH /order/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::order_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'Order not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'Order updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the order.'
        ], 500);
    }
});

Flight::route('DELETE /order/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Order ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::order_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'Order not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'Order deleted successfully.'
        ], 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to delete the order.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/orders",
 *     tags={"Orders"},
 *     summary="Get all orders",
 *     @OA\Response(response=200, description="List of orders"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/order/{id}",
 *     tags={"Orders"},
 *     summary="Get an order by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Order details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Order not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/orders",
 *     tags={"Orders"},
 *     summary="Create an empty pending order",
 *     description="The initial total is 0.00. Adding order items recalculates the total.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 minimum=1,
 *                 example=3
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="order_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid data or user does not exist"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/order/{id}",
 *     tags={"Orders"},
 *     summary="Update the order user or status",
 *     description="The total amount cannot be updated directly.",
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
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 minimum=1
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={
 *                     "pending",
 *                     "paid",
 *                     "shipped",
 *                     "completed",
 *                     "cancelled"
 *                 },
 *                 example="cancelled"
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Order updated"),
 *     @OA\Response(response=400, description="Invalid order data"),
 *     @OA\Response(response=404, description="Order not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/order/{id}",
 *     tags={"Orders"},
 *     summary="Delete an order and its items",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Order deleted"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Order not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */