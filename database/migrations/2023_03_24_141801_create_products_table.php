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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('itemcode')->unique();
            $table->string('description');
            $table->string('unit');
            $table->integer('minqty');
            $table->double('max_retail_price');
            $table->double('saler_discount_rate');
            $table->double('profit_percent');
            $table->double('price_matara');
            $table->double('price_akuressa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
