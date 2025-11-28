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
        Schema::create('seat_matrices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('academic_session_id');
            $table->unsignedBigInteger('admission_session_id');
            $table->json('mode'); // Store array of selected modes: ['direct'], ['counselling'], ['merit'], or combinations
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('publish_mode', ['public', 'private'])->default('public');
            $table->integer('total_seats');
            $table->enum('define_category', ['yes', 'no'])->default('no');
            $table->timestamps();
            
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('academic_session_id')->references('id')->on('university_sessions')->onDelete('cascade');
            $table->foreign('admission_session_id')->references('id')->on('university_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_matrices');
    }
};


