<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sync existing roles: populate 'name' and 'guard_name' from 'role_name'
        if (Schema::hasTable('roles')) {
            // Update existing roles to have name and guard_name
            DB::table('roles')->whereNull('name')->orWhere('name', '')->chunkById(100, function ($roles) {
                foreach ($roles as $role) {
                    DB::table('roles')
                        ->where('id', $role->id)
                        ->update([
                            'name' => $role->role_name ?? 'role_' . $role->id,
                            'guard_name' => 'web'
                        ]);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - we're just populating data
    }
};
