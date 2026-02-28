<?php

namespace App\Http\Controllers;

use App\ApiResponse;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="CarClean API",
 *     description="Documentacion oficial de la API de CarClean"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor principal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Usa: Bearer {token}"
 * )
 */
abstract class Controller
{
    use ApiResponse;
}
