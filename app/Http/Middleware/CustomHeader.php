<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // custom header
        $ah = AuthController::Header;
        $res1 = $request->header('X-PARTNER-ID');
        $res2 = $request->header('X-TIMESTAMP');
        $res3 = $request->header('X-SIGNATURE');

        // logika header custom
        if ($res1 === $ah['X-PARTNER-ID'] && $res2 === $ah['X-TIMESTAMP'] && $res3 === $ah['X-SIGNATURE']) {
            return $next($request);
        } else {
            return response()->json([
                'status' => 503,
                'message' => 'Silahkan cek kembali custom header anda'
            ], 503);
        }
    }
}
