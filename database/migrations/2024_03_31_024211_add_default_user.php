<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $admin = Role::where('name', 'super_admin')->first();

        if (!$admin) {
            $admin = Role::create([
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Super Admin has full permissions',
            ]);
        }

        if (!User::where('email', 'admin@example.com')->exists()) {
            $user = \App\Models\User::factory()->create([
                'email' => 'admin@example.com',
            ]);

            $user->assignRole($admin);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::query()->where('email', 'admin@example.com')->delete();
    }
};
