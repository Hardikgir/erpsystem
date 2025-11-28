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
        if (!Schema::hasTable('eligibility_criteria')) {
            Schema::create('eligibility_criteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->unsignedBigInteger('program_id');
                $table->unsignedBigInteger('course_id');
                $table->string('semester_year')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->enum('gender', ['male', 'female', 'both'])->default('both');
                $table->integer('min_age')->nullable();
                $table->integer('max_age')->nullable();
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibility_criteria');
    }
};
