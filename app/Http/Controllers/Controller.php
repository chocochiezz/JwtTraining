<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
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
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     */

    use AuthorizesRequests, ValidatesRequests;
}
