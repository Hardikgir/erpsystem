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
        Schema::create('seat_matrix_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seat_matrix_id');
            $table->enum('category_name', ['GENERAL', 'OBC', 'SC', 'ST']);
            $table->integer('direct_seats')->default(0);
            $table->integer('counselling_seats')->default(0);
            $table->integer('merit_seats')->default(0);
            $table->integer('total_seats')->default(0);
            $table->timestamps();
            
            $table->foreign('seat_matrix_id')->references('id')->on('seat_matrices')->onDelete('cascade');
            $table->unique(['seat_matrix_id', 'category_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_matrix_categories');
    }
};


