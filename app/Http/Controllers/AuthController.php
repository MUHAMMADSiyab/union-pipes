<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Attempt Login
     * @param App\Requests\LoginRequest $request
     * @return Illuminate\Http\Response 
     */
    public function attemptLogin(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error' => __('Credentials do not match')
            ], 401);
        }

        return response()->json(compact('token'), 200);
    }

    /**
     * Logout the user
     * @return void
     */
    public function getAuthUser()
    {
        return auth()->user();
    }

    /**
     * Log the user out
     * @return void
     */
    public function logout()
    {
        if (auth('api')->invalidate(true)) {
            return response()->json(['message' => 'Logged out'], 200);
        }
    }
}
