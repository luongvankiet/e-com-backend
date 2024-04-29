<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()['cache']->forget('spatie.permission.cache');

        // fetch permissions from config file
        $permissions = config('permissions');

        $newPermissions = collect();

        foreach ($permissions as $permissionGroup) {
            foreach ($permissionGroup as $permission) {
                $newPermissions->push(
                    Permission::updateOrCreate(
                        ['name' => $permission['name']],
                        ['description' => $permission['description']]
                    )
                );
            }
        }

        $admin = Role::where('name', 'super_admin')->first();

        if (!$admin) {
            $admin = Role::create([
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Super Admin has full permissions',
            ]);
        }

        $admin->syncPermissions($newPermissions);
    }
}
