<?php

namespace App\Http\Controllers;

use App\Actions\StoreRoleAction;
use App\Actions\UpdateRoleAction;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoleResource::collection(Role::with('permissions')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreRoleRequest $request,
        StoreRoleAction $storeRoleAction
    ) {
        return RoleResource::make(
            ($storeRoleAction)($request)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return RoleResource::make($role->load('permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateRoleRequest $request,
        UpdateRoleAction $updateRoleAction,
        Role $role
    ) {
        return RoleResource::make(
            ($updateRoleAction)($request, $role)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
