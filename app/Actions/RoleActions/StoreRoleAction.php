<?php

namespace App\Actions\RoleActions;

use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class StoreRoleAction
{
    public function __invoke(StoreRoleRequest|Request $request): ?Role
    {
        $displayName = $request->input('name');
        $name = Str::snake($displayName);
        $description = $request->input('description');

        $role = Role::create([
            'name' => $name,
            'display_name' => $displayName,
            'description' => $description
        ]);

        return $role;
    }
}
