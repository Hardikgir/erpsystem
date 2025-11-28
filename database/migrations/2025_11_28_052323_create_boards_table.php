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
        if (!Schema::hasTable('boards')) {
            Schema::create('boards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->string('board_name');
                $table->string('board_code')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->unique(['university_id', 'board_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
