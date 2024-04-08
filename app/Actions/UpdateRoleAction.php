<?php

namespace App\Actions;

use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    public function __invoke(UpdateRoleRequest|Request $request, Role $role): ?Role
    {
        $role->update(['name' => $request->input('name')]);

        $permissionRequest = $request->input('permissions', []);

        if (count($permissionRequest) !== 0) {
            $permissions = collect();

            foreach ($permissionRequest as $value) {
                $permission = Permission::where('name', $value)->first();

                if ($permission) {
                    $permissions->push($permission);
                    continue;
                }

                $permissions->push(Permission::create(['name' => $value]));
            }

            $role->syncPermissions($permissions);
        }

        return $role->load('permissions');
    }
}
