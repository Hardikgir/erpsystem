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
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->unsignedBigInteger('program_id');
                $table->unsignedBigInteger('session_id')->nullable(); // Will be set after sessions table is created
                $table->string('course_code');
                $table->string('course_name');
                $table->enum('course_type', ['Semester', 'Yearly']);
                $table->integer('course_duration');
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
                // Note: session_id foreign key will be added after sessions table is created
                $table->unique(['university_id', 'course_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
