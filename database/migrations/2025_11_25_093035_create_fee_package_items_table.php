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
        Schema::create('fee_package_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('element_id');
            $table->enum('pattern', ['Annual', 'Semester', 'Quarter', 'Monthly', 'One Time']);
            $table->timestamps();
            
            $table->foreign('package_id')->references('id')->on('fee_packages')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('fee_elements')->onDelete('cascade');
            $table->unique(['package_id', 'element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_package_items');
    }
};
