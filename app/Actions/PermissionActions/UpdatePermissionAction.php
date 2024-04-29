<?php

namespace App\Actions\PermissionActions;

use App\Http\Requests\StorePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UpdatePermissionAction
{
    public function __invoke(StorePermissionRequest|Request $request, Permission $permission): ?Permission
    {
        $name = $request->input('name');
        $description = $request->input('description');

        $permission->name = $name;
        $permission->description = $description;

        return $permission;
    }
}
