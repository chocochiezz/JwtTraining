<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwtController extends Controller
{
    // register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // buat user
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user
        ]);
    }
    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // jwt token
        $token = JWTAuth::attempt([
            'email' => $request['email'],
            'password' => $request['password']
        ]);

        if (!empty($token)) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Login berhasil',
                'token' => $token
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Login gagal'
        ]);
    }
    // profile
    public function profile()
    {
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Detail User',
            'data' => auth()->user()
        ];
        return response()->json(($data), Response::HTTP_OK);
    }

    // refresh token
    public function refresh()
    {
        $newToken = auth()->refresh();
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'New token generated',
            'token' => $newToken
        ];
        return response()->json(($data), Response::HTTP_OK);
    }

    // logout
    public function logout()
    {
        auth()->logout();
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Logout',
        ];
        return response()->json(($data), Response::HTTP_OK);
    }
}
