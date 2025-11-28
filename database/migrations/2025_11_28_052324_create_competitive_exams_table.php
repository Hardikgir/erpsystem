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
        if (!Schema::hasTable('competitive_exams')) {
            Schema::create('competitive_exams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->string('exam_name');
                $table->string('exam_code')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->unique(['university_id', 'exam_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitive_exams');
    }
};
