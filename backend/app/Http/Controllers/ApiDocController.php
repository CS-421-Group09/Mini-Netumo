<?php

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
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiDocController extends Controller
{
    //
}
