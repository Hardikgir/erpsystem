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
        Schema::create('fee_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id');
            $table->string('element_name');
            $table->enum('pattern', ['Annual', 'Semester', 'Quarter', 'Monthly', 'One Time']);
            $table->timestamps();
            
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
            $table->unique(['university_id', 'element_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_elements');
    }
};
