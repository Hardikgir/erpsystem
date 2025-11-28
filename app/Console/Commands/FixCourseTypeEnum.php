<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCourseTypeEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:course-type-enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the course_type ENUM column in courses table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing course_type ENUM column...');
        
        try {
            // Update any invalid values first
            DB::statement("UPDATE courses SET course_type = 'Semester' WHERE course_type NOT IN ('Semester', 'Year') OR course_type IS NULL");
            $this->info('Updated invalid values.');
            
            // Modify the column
            DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL");
            $this->info('Column modified successfully.');
            
            // Verify the column structure
            $column = DB::select("SHOW COLUMNS FROM courses WHERE Field = 'course_type'");
            $this->info('Current column definition: ' . json_encode($column));
            
            $this->info('✓ Course type ENUM column fixed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error fixing column: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}


