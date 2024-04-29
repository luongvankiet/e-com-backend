<?php

namespace App\Actions\RoleActions;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionsToRoleAction
{
    public function __invoke(Request $request, Role $role): ?Role
    {
        $permissionRequest = $request->input('permissions', []);

        if (count($permissionRequest) !== 0) {
            $permissions = collect();

            foreach ($permissionRequest as $value) {
                if (!isset($value['name'])) {
                    continue;
                }

                $permission = Permission::where('name', $value['name'])->first();

                if (!$permission) {
                    continue;
                }

                $permissions->push($permission);
            }

            $role->syncPermissions($permissions);
        }

        return $role;
    }
}
