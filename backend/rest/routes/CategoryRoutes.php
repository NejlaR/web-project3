<?php

Flight::route('GET /categories', function () {
    try {
        $categories = Flight::category_service()->get_all();

        Flight::json($categories, 200);
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load categories.'
        ], 500);
    }
});

Flight::route('GET /category/@id', function ($id) {
    if (
        filter_var(
            $id,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) === false
    ) {
        Flight::json([
            'message' => 'Category ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $category = Flight::category_service()->get_by_id($id);

        if ($category === false) {
            Flight::json([
                'message' => 'Category not found.'
            ], 404);

            return;
        }

        Flight::json($category, 200);
    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to load the category.'
        ], 500);
    }
});
Flight::route('POST /categories', function () {
    try {
        $data = Flight::request()->data->getData();

        $id = Flight::category_service()->add($data);

        Flight::json([
            'message' => 'Category created successfully.',
            'category_id' => (int) $id
        ], 201);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to create the category.'
        ], 500);
    }
});

Flight::route('PATCH /category/@id', function ($id) {
    if (
        filter_var(
            $id,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) === false
    ) {
        Flight::json([
            'message' => 'Category ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $service = Flight::category_service();

        if ($service->get_by_id($id) === false) {
            Flight::json([
                'message' => 'Category not found.'
            ], 404);

            return;
        }

        $data = Flight::request()->data->getData();

        $service->update($data, $id);

        Flight::json([
            'message' => 'Category updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (PDOException $exception) {
        Flight::json([
            'message' => 'Unable to update the category.'
        ], 500);
    }
});

Flight::route('DELETE /category/@id', function ($id) {
    if (
        filter_var(
            $id,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) === false
    ) {
        Flight::json([
            'message' => 'Category ID must be a positive integer.'
        ], 400);

        return;
    }

    try {
        $deleted = Flight::category_service()->delete($id);

        if (!$deleted) {
            Flight::json([
                'message' => 'Category not found.'
            ], 404);

            return;
        }

        Flight::json([
            'message' => 'Category deleted successfully.'
        ], 200);

    } catch (PDOException $exception) {
        if (
            isset($exception->errorInfo[1])
            && (int) $exception->errorInfo[1] === 1451
        ) {
            Flight::json([
                'message' => 'This category contains products and cannot be deleted.'
            ], 400);

            return;
        }

        Flight::json([
            'message' => 'Unable to delete the category.'
        ], 500);
    }
});
/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"Categories"},
 *     summary="Get all categories",
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="Sneakers"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string",
 *                     nullable=true,
 *                     example="Comfortable everyday shoes"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Get(
 *     path="/category/{id}",
 *     tags={"Categories"},
 *     summary="Get a category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Category details"),
 *     @OA\Response(response=400, description="Invalid ID"),
 *     @OA\Response(response=404, description="Category not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Post(
 *     path="/categories",
 *     tags={"Categories"},
 *     summary="Create a category",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 minLength=1,
 *                 maxLength=100,
 *                 example="Sandals"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 nullable=true,
 *                 example="Summer shoes"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Category created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid category data"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Patch(
 *     path="/category/{id}",
 *     tags={"Categories"},
 *     summary="Update a category",
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
 *                 maxLength=100,
 *                 example="Updated category"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 nullable=true
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category updated"),
 *     @OA\Response(response=400, description="Invalid category data"),
 *     @OA\Response(response=404, description="Category not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 *
 * @OA\Delete(
 *     path="/category/{id}",
 *     tags={"Categories"},
 *     summary="Delete a category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
 *     @OA\Response(response=200, description="Category deleted"),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID or category contains products"
 *     ),
 *     @OA\Response(response=404, description="Category not found"),
 *     @OA\Response(response=500, description="Database error")
 * )
 */