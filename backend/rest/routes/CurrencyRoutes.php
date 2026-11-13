<?php

/**
 * @OA\Get(
 *     path="/currency/convert",
 *     tags={"Currency"},
 *     summary="Convert an amount between currencies",
 *     @OA\Parameter(
 *         name="amount",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="number",
 *             minimum=0,
 *             maximum=99999999.99,
 *             example=99.90
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="from",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             enum={"EUR", "USD", "GBP"},
 *             example="EUR"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="to",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             enum={"EUR", "USD", "GBP"},
 *             example="USD"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Conversion result",
 *         @OA\JsonContent(
 *             @OA\Property(property="amount", type="number"),
 *             @OA\Property(property="from", type="string"),
 *             @OA\Property(property="to", type="string"),
 *             @OA\Property(property="rate", type="number"),
 *             @OA\Property(property="converted_amount", type="number"),
 *             @OA\Property(
 *                 property="date",
 *                 type="string",
 *                 format="date",
 *                 nullable=true
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid parameters"),
 *     @OA\Response(
 *         response=503,
 *         description="Currency conversion temporarily unavailable"
 *     )
 * )
 */

Flight::route('GET /currency/convert', function () {
    try {
        $query = Flight::request()->query;

        $result = Flight::currency_service()->convert(
            $query['amount'],
            $query['from'],
            $query['to']
        );

        Flight::json($result, 200);

    } catch (InvalidArgumentException $exception) {
        Flight::json([
            'message' => $exception->getMessage()
        ], 400);

    } catch (RuntimeException $exception) {
        Flight::json([
            'message' => 'Currency conversion is temporarily unavailable.'
        ], 503);
    }
});