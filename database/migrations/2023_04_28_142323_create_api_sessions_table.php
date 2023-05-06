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
        Schema::create('api_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('token_id');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('hostname')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->foreign('token_id')
                ->references('id')
                ->on('personal_access_tokens')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_sessions');
    }
};
