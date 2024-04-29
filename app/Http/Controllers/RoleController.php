<?php

namespace App\Http\Controllers;

use App\Actions\RoleActions\AssignPermissionsToRoleAction;
use App\Actions\RoleActions\StoreRoleAction;
use App\Actions\RoleActions\UpdateRoleAction;
use App\Http\QueryFilters\RoleQueryFilter;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoleResource::collection(
            RoleQueryFilter::make(
                Role::with('permissions')
                    ->withCount('permissions')
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreRoleRequest $request,
        StoreRoleAction $storeRoleAction,
        AssignPermissionsToRoleAction $assignPermissionsToRoleAction
    ) {
        $this->authorize('create', Role::class);

        $role = ($storeRoleAction)($request);

        if ($request->has('permissions')) {
            $this->authorize('assignPermissions', Role::class);

            $assignPermissionsToRoleAction($request, $role);
        }

        return RoleResource::make($role->load('permissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return RoleResource::make($role->load('permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateRoleRequest $request,
        UpdateRoleAction $updateRoleAction,
        AssignPermissionsToRoleAction $assignPermissionsToRoleAction,
        Role $role
    ) {
        $this->authorize('update', $role);

        $role = ($updateRoleAction)($request, $role);

        if ($request->has('permissions')) {
            $this->authorize('assignPermissions', Role::class);

            $assignPermissionsToRoleAction($request, $role);
        }

        return RoleResource::make($role->load('permissions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function deleteMany(Request $request)
    {
        $this->authorize('deleteMany', Role::class);

        try {
            $request->validate([
                'role_ids' => 'required|array|min:1',  // Ensures 'ids' is a non-empty array
            ]);

            $ids = $request->input('role_ids');

            $this->findAdminRoles($ids);

            $existingIds = $this->findNonExistingIds($ids);

            Role::whereIn('id', $existingIds)->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function findAdminRoles(array $ids = [])
    {
        // Retrieve all IDs that exist in the database
        $adminIds = Role::whereIn('id', $ids)
            ->where('name', 'admin')
            ->pluck('id')
            ->toArray();

        if (!empty($adminIds)) {
            throw new Exception('Cannot delete admin role!');
        }

        return $adminIds;
    }

    private function findNonExistingIds(array $ids = [])
    {
        // Retrieve all IDs that exist in the database
        $existingIds = Role::whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        // Compare with the original array to find non-existing IDs
        $nonExistingIds = array_diff($ids, $existingIds);

        if (!empty($nonExistingIds)) {
            throw new Exception('Roles with ids [' . implode(',', $nonExistingIds) . '] not found!');
        }

        return $existingIds;
    }
}
