<?php

Flight::route('GET /users', function () {
    try {
        Flight::json(
            Flight::user_service()->get_all(),
            200
        );
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load users.'
        ], 500);
    }
});

Flight::route('GET /user/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'User ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $user = Flight::user_service()->get_by_id($id);

        if ($user === false) {
            Flight::json([
                'message' => 'User not found.'
            ], 404);

            return;
        }

        Flight::json($user, 200);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the user.'
        ], 500);
    }
});

Flight::route('POST /users', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::user_service()->add($data);

        Flight::json([
            'message' => 'User created successfully.',
            'user_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the user.'
        ], 500);
    }
});

Flight::route('PATCH /user/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'User ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::user_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'User not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'User updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the user.'
        ], 500);
    }
});

Flight::route('DELETE /user/@id', function ($id) {
    if (!ctype_digit($id) || (int) $id < 1) {
        Flight::json([
            'message' => 'User ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::user_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'User not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'User deleted successfully.'
        ], 200);

    } catch (PDOException $exception) {
        if (
            isset($exception->errorInfo[1])
            && (int) $exception->errorInfo[1] === 1451
        ) {
            Flight::json([
                'message' => 'This user has orders and cannot be deleted.'
            ], 400);

            return;
        }

        Flight::json([
            'message' => 'Unable to delete the user.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Get all users without password hashes",
 *     @OA\Response(response=200, description="List of users"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/user/{id}",
 *     tags={"Users"},
 *     summary="Get a user without the password hash",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="User details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="User not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Create a user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"full_name", "email", "password"},
 *             additionalProperties=false,
 *             @OA\Property(
 *                 property="full_name",
 *                 type="string",
 *                 minLength=1,
 *                 maxLength=100,
 *                 example="API Test User"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 maxLength=120,
 *                 example="api.test@example.com"
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 format="password",
 *                 writeOnly=true,
 *                 description="Between 8 and 72 bytes",
 *                 example="TestPass123!"
 *             ),
 *             @OA\Property(
 *                 property="role",
 *                 type="string",
 *                 enum={"customer", "admin"},
 *                 default="customer",
 *                 example="customer"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="user_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid data or email already in use"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/user/{id}",
 *     tags={"Users"},
 *     summary="Update selected user fields",
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
 *                 property="full_name",
 *                 type="string",
 *                 minLength=1,
 *                 maxLength=100,
 *                 example="Updated API Test User"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 maxLength=120
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 format="password",
 *                 writeOnly=true,
 *                 description="Between 8 and 72 bytes"
 *             ),
 *             @OA\Property(
 *                 property="role",
 *                 type="string",
 *                 enum={"customer", "admin"}
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated"),
 *     @OA\Response(response=400, description="Invalid data or email already in use"),
 *     @OA\Response(response=404, description="User not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/user/{id}",
 *     tags={"Users"},
 *     summary="Delete a user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="User deleted"),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID or user has orders"
 *     ),
 *     @OA\Response(response=404, description="User not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */