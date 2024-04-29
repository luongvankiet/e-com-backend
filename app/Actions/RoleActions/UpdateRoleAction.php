<?php

namespace App\Actions\RoleActions;

use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UpdateRoleAction
{
    public function __invoke(UpdateRoleRequest|Request $request, Role $role): ?Role
    {
        $displayName = $request->input('name');
        $name = Str::snake($displayName);
        $description = $request->input('description');

        $role->update([
            'name' => $name,
            'display_name' => $displayName,
            'description' => $description
        ]);

        return $role;
    }
}
