<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support adding foreign keys to existing columns easily
        // For production, use MySQL/PostgreSQL
        // For now, we'll skip this if using SQLite
        if (config('database.default') !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
    }
};
