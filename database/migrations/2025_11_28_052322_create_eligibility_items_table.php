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
        if (!Schema::hasTable('eligibility_items')) {
            Schema::create('eligibility_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('eligibility_id');
                $table->unsignedBigInteger('min_qualification_id')->nullable();
                $table->decimal('min_marks', 5, 2)->nullable();
                $table->unsignedBigInteger('board_id')->nullable();
                $table->unsignedBigInteger('exam_id')->nullable();
                $table->decimal('min_percentile', 5, 2)->nullable();
                $table->timestamps();
                
                $table->foreign('eligibility_id')->references('id')->on('eligibility_criteria')->onDelete('cascade');
                $table->foreign('min_qualification_id')->references('id')->on('minimum_qualifications')->onDelete('set null');
                $table->foreign('board_id')->references('id')->on('boards')->onDelete('set null');
                $table->foreign('exam_id')->references('id')->on('competitive_exams')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibility_items');
    }
};
