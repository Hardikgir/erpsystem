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
        if (!Schema::hasTable('course_documents')) {
            Schema::create('course_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->unsignedBigInteger('program_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('session_id');
                $table->string('domicile');
                $table->string('document_name');
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('session_id')->references('id')->on('university_sessions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_documents');
    }
};
