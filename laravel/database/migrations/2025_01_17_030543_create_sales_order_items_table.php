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
        // if it exists, continue
        if (Schema::hasTable('so_t')) {
            return;
        }
        Schema::create('so_t', function (Blueprint $table) {
            $table->id();
            $table->string('compcode', 10)->default('001');
            $table->string('cidentity', 25)->primary();
            $table->string('ctranno', 50);
            $table->string('creference', 50)->nullable();
            $table->integer('nident')->nullable();
            $table->string('citemno', 50);
            $table->decimal('nqty', 18, 4);
            $table->string('cunit', 10);
            $table->decimal('nexprice', 18, 6)->default(0.000000);
            $table->decimal('nprice', 18, 6)->default(0.000000);
            $table->decimal('namount', 18, 4)->default(0.0000);
            $table->decimal('nbaseamount', 18, 4)->default(0.0000);
            $table->string('cmainunit', 5)->nullable();
            $table->decimal('nfactor', 18, 4)->default(0.0000);
            $table->decimal('nbase', 18, 4)->default(0.0000);
            $table->decimal('ndisc', 18, 4)->default(0.0000);
            $table->decimal('nnet', 18, 4)->default(0.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        Schema::dropIfExists('so_t');
    }
};
