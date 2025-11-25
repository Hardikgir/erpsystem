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
        Schema::create('fee_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('element_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('semester_no')->nullable();
            $table->integer('installment_no')->default(0);
            $table->timestamps();
            
            $table->foreign('plan_id')->references('id')->on('fee_plans')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('fee_elements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_plan_items');
    }
};
