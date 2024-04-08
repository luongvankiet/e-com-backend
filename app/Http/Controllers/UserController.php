<?php

namespace App\Http\Controllers;

use App\Actions\StoreUserAction;
use App\Actions\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreUserRequest $request,
        StoreUserAction $storeUserAction
    ) {
        return UserResource::make(
            ($storeUserAction)($request)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!$user) {
            return response()->json([
                'message' => 'User not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        return UserResource::make($user->load('images'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateUserRequest $request,
        User $user,
        UpdateUserAction $updateUserAction
    ) {
        if (!$user) {
            return response()->json([
                'message' => 'User not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        return UserResource::make(
            ($updateUserAction)($request, $user)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
