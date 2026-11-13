<?php

/**
 * @OA\Info(
 *     title="Aurora Shoes API",
 *     description="REST API for the Aurora Shoes application.",
 *     version="1.0.0"
 * )
 *
 * @OA\Server(
 *     url="http://localhost/web-project3/backend",
 *     description="Local API server"
 * )
 *
 * @OA\Get(
 *     path="/",
 *     tags={"Status"},
 *     summary="Check whether the API is running",
 *     @OA\Response(
 *         response=200,
 *         description="API is running",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Aurora Shoes API is running"
 *             )
 *         )
 *     )
 * )
 */