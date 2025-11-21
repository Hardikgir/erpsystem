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
        // First, convert status data from enum to boolean
        DB::statement('UPDATE universities SET status = CASE WHEN status = "active" THEN 1 ELSE 0 END');
        
        // Drop foreign key constraint if it exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'universities' 
            AND COLUMN_NAME = 'admin_user_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $foreignKey) {
            DB::statement("ALTER TABLE universities DROP FOREIGN KEY `{$foreignKey->CONSTRAINT_NAME}`");
        }
        
        // Drop old columns if they exist
        Schema::table('universities', function (Blueprint $table) {
            if (Schema::hasColumn('universities', 'admin_username')) {
                $table->dropColumn('admin_username');
            }
            if (Schema::hasColumn('universities', 'admin_user_id')) {
                $table->dropColumn('admin_user_id');
            }
            if (Schema::hasColumn('universities', 'admin_password_display')) {
                $table->dropColumn('admin_password_display');
            }
            if (Schema::hasColumn('universities', 'url')) {
                $table->dropColumn('url');
            }
            if (Schema::hasColumn('universities', 'password')) {
                $table->dropColumn('password');
            }
        });
        
        // Change status column type from enum to boolean
        DB::statement('ALTER TABLE universities MODIFY COLUMN status TINYINT(1) DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            // Revert status to enum
            DB::statement('ALTER TABLE universities MODIFY COLUMN status ENUM("active", "inactive") DEFAULT "active"');
            DB::statement('UPDATE universities SET status = CASE WHEN status = 1 THEN "active" ELSE "inactive" END');
            
            // Re-add old columns
            $table->string('admin_username')->unique()->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->string('admin_password_display')->nullable();
            $table->string('url')->nullable();
            $table->string('password')->nullable();
        });
    }
};
