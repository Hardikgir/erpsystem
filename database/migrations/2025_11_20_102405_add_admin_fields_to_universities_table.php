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
        Schema::table('universities', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_user_id')->nullable()->after('id');
            $table->string('admin_password_display')->nullable()->after('password'); // Store masked password for display
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropForeign(['admin_user_id']);
            $table->dropColumn(['admin_user_id', 'admin_password_display']);
        });
    }
};
