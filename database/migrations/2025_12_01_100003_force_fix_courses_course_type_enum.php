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
        // Force fix the course_type ENUM column
        // This migration will run regardless of previous migrations
        
        // First, update any invalid values
        DB::statement("UPDATE courses SET course_type = 'Semester' WHERE course_type NOT IN ('Semester', 'Year') OR course_type IS NULL");
        
        // Drop and recreate the column to ensure it's correct
        // We need to drop foreign keys first if they exist
        try {
            // Get foreign key constraints
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'courses' 
                AND COLUMN_NAME = 'course_type'
            ");
            
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE courses DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }
        } catch (\Exception $e) {
            // Ignore if no foreign keys exist
        }
        
        // Modify the column
        DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to same values
        DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
    }
};


