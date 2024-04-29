<?php

namespace App\Actions\PermissionActions;

use App\Http\Requests\StorePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class StorePermissionAction
{
    public function __invoke(StorePermissionRequest|Request $request): ?Permission
    {
        $name = $request->input('name');
        $description = $request->input('description');

        $permission = Permission::create([
            'name' => $name,
            'description' => $description
        ]);

        return $permission;
    }
}
