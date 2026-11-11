<?php

Flight::route('GET /reviews', function () {
    try {
        Flight::json(
            Flight::review_service()->get_all(),
            200
        );
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load reviews.'
        ], 500);
    }
});

Flight::route('GET /review/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Review ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $review = Flight::review_service()->get_by_id($id);

        if ($review === false) {
            Flight::json([
                'message' => 'Review not found.'
            ], 404);

            return;
        }

        Flight::json($review, 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the review.'
        ], 500);
    }
});

Flight::route('POST /reviews', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::review_service()->add($data);

        Flight::json([
            'message' => 'Review created successfully.',
            'review_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the review.'
        ], 500);
    }
});

Flight::route('PATCH /review/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Review ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::review_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'Review not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'Review updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the review.'
        ], 500);
    }
});

Flight::route('DELETE /review/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'Review ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::review_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'Review not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'Review deleted successfully.'
        ], 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to delete the review.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/reviews",
 *     tags={"Reviews"},
 *     summary="Get all reviews",
 *     @OA\Response(response=200, description="List of reviews"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/review/{id}",
 *     tags={"Reviews"},
 *     summary="Get a review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Review details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Review not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/reviews",
 *     tags={"Reviews"},
 *     summary="Create a review",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id", "user_id", "rating"},
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 minimum=1,
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 minimum=1,
 *                 example=3
 *             ),
 *             @OA\Property(
 *                 property="rating",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=5,
 *                 example=4
 *             ),
 *             @OA\Property(
 *                 property="comment",
 *                 type="string",
 *                 nullable=true,
 *                 description="Maximum UTF-8 size is 65535 bytes.",
 *                 example="Comfortable everyday shoes."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Review created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="review_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data or user or product does not exist"
 *     ),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/review/{id}",
 *     tags={"Reviews"},
 *     summary="Update selected review fields",
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
 *                 property="product_id",
 *                 type="integer",
 *                 minimum=1
 *             ),
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 minimum=1
 *             ),
 *             @OA\Property(
 *                 property="rating",
 *                 type="integer",
 *                 minimum=1,
 *                 maximum=5,
 *                 example=5
 *             ),
 *             @OA\Property(
 *                 property="comment",
 *                 type="string",
 *                 nullable=true,
 *                 description="Maximum UTF-8 size is 65535 bytes.",
 *                 example="Updated review."
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Review updated"),
 *     @OA\Response(response=400, description="Invalid review data"),
 *     @OA\Response(response=404, description="Review not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/review/{id}",
 *     tags={"Reviews"},
 *     summary="Delete a review",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Review deleted"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Review not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */