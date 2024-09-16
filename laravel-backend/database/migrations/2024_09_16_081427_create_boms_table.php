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
        if (Schema::hasTable('boms')) {
            Schema::dropIfExists('boms');
        }
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('bom_type');
            $table->integer('quantity')->default(1);
            $table->string('uom')->default('BATCH');
//            $table->foreignId('item_id')->constrained('items', 'nid')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
