<?php

namespace App\Http\Controllers;

use App\Actions\UserActions\StoreUserAction;
use App\Actions\UserActions\UpdateUserAction;
use App\Http\QueryFilters\UserQueryFilter;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(
            UserQueryFilter::make(
                User::with(['images', 'roles'])
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreUserRequest $request,
        StoreUserAction $storeUserAction
    ) {
        $this->authorize('create', User::class);

        return UserResource::make(
            ($storeUserAction)($request)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('users.view', $user);

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
        $this->authorize('update', $user);

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
        $this->authorize('delete', $user);

        $user->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function statusCount()
    {
        $total = User::count();

        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        $trashedCount = User::onlyTrashed()->count();

        return response()->json([
            'data' => [
                'total' => $total,
                'verified_count' => $verifiedUsers,
                'pending_count' => $total - $verifiedUsers,
                'trashed_count' => $trashedCount
            ]
        ]);
    }

    public function deleteMany(Request $request)
    {
        $this->authorize('deleteMany', User::class);

        try {
            $ids = $this->findNonExistingIds($request);

            User::whereIn('id', $ids)->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function restoreMany(Request $request)
    {
        $this->authorize('restore', User::class);

        try {
            $ids = $this->findNonExistingIds($request, true);

            User::whereIn('id', $ids)->restore();

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function permanentDeleteMany(Request $request)
    {
        $this->authorize('forceDelete', User::class);

        try {
            $ids = $this->findNonExistingIds($request);

            User::whereIn('id', $ids)->forceDelete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function findNonExistingIds(Request $request, ?bool $isTrashed = false)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',  // Ensures 'ids' is a non-empty array
        ]);

        $ids = $request->input('user_ids');

        // Retrieve all IDs that exist in the database
        if (!$isTrashed) {
            $existingIds = User::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();
        } else {
            $existingIds = User::whereIn('id', $ids)
                ->withTrashed()
                ->pluck('id')->toArray();
        }

        // Compare with the original array to find non-existing IDs
        $nonExistingIds = array_diff($ids, $existingIds);

        if (!empty($nonExistingIds)) {
            throw new Exception('Users with ids [' . implode(',', $nonExistingIds) . '] not found!');
        }

        return $ids;
    }
}
