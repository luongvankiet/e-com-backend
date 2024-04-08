<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ])) {
            return response()->json([
                'message' => 'Email or password is incorrect',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $request->user()->createToken($request->input('email'));

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'access_token' => $token->plainTextToken,
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        Auth::login($user);

        $token = $request->user()->createToken($request->input('email'));

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'access_token' => $token->plainTextToken,
            ]
        ]);
    }

    public function getAuthenticatedUser()
    {
        if (Auth::check()) {
            return UserResource::make(Auth::user());
        }

        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
