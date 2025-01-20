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
        Schema::create('woocommerce_product_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('woocommerce_product_id')->nullable();
            $table->unsignedBigInteger('myxfin_product_id')->unique;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_product_mappings');
    }
};
