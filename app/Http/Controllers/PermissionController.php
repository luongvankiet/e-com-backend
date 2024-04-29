<?php

namespace App\Http\Controllers;

use App\Actions\PermissionActions\StorePermissionAction;
use App\Actions\PermissionActions\UpdatePermissionAction;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PermissionResource::collection(config('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StorePermissionRequest $request,
        StorePermissionAction $storePermissionAction
    ) {
        return PermissionResource::make(
            ($storePermissionAction)($request)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return PermissionResource::make($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdatePermissionRequest $request,
        UpdatePermissionAction $updatePermissionAction,
        Permission $permission
    ) {
        return PermissionResource::make(
            ($updatePermissionAction)($request, $permission)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
