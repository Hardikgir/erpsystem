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
        // First, check if there are any existing records with invalid values and update them
        // Then modify the course_type column to ensure it has the correct ENUM values
        try {
            // Update any invalid values first (if any exist)
            DB::statement("UPDATE courses SET course_type = 'Semester' WHERE course_type NOT IN ('Semester', 'Year')");
            
            // Modify the course_type column to ensure it has the correct ENUM values
            // Using raw SQL to modify ENUM columns properly
            DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
        } catch (\Exception $e) {
            // If the column doesn't exist or has issues, try to modify it anyway
            DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert if needed (same values)
        DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
    }
};

