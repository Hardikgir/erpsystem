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
        if (!Schema::hasTable('course_document_mappings')) {
            Schema::create('course_document_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->unsignedBigInteger('program_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('session_id');
                $table->string('domicile');
                $table->unsignedBigInteger('document_id');
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('session_id')->references('id')->on('university_sessions')->onDelete('cascade');
                $table->foreign('document_id')->references('id')->on('course_documents')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_document_mappings');
    }
};
