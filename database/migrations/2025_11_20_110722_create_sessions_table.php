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
        // Check if this is Laravel's sessions table (has 'id' as string) or our university_sessions table
        if (!Schema::hasTable('university_sessions')) {
            Schema::create('university_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('university_id');
                $table->string('session_label'); // e.g., "Jul-Dec 2025"
                $table->enum('session_type', ['jul-dec', 'jan-jun']);
                $table->integer('year');
                $table->timestamps();
                
                $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
                $table->unique(['university_id', 'session_label']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_sessions');
    }
};
