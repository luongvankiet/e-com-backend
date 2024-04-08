<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!User::where('email', 'admin@example.com')->exists()) {
            \App\Models\User::factory()->create([
                'email' => 'admin@example.com',
            ]);
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
