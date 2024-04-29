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
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($request->input('email'));

        return response()->json([
            'data' => [
                'user' => UserResource::make($user->load('roles.permissions')),
                'access_token' => $token->plainTextToken,
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken($request->input('email'));

        return response()->json([
            'data' => [
                'user' => UserResource::make($user->load('roles.permissions')),
                'access_token' => $token->plainTextToken,
            ]
        ]);
    }

    public function getAuthenticatedUser()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            return UserResource::make($user->load('roles.permissions'));
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
