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
        Schema::create('stock_use_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('type');
            $table->integer('qty');
            $table->string('description')->nullable();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_use_records');
    }
};
