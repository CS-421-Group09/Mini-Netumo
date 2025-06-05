<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="Netumo API",
 *     version="1.0.0",
 *     description="API documentation for the Netumo project"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

abstract class Controller
{
    //
}
