<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// untuk swagger
/**
 * @OA\Info(
 *     description="API documentation Training Restful API",
 *     version="1.0.0",
 *     title="API training",
 *     termsOfService="http://swagger.io/terms/",
 *     @OA\Contact(
 *         email="isan@gmail.com"
 *     ),
 * )
 */

class Controller extends BaseController
{


    use AuthorizesRequests, ValidatesRequests;
}
