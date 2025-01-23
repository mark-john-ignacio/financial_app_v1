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
        if (app()->environment('testing')) {
            Schema::create('dr_t', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10)->default('001');
                $table->string('cidentity', 25);
                $table->integer('nident');
                $table->string('ctranno', 50)->nullable();
                $table->string('creference', 50)->nullable();
                $table->integer('crefident')->nullable();
                $table->string('citemno', 50)->nullable();
                $table->decimal('nqtyorig', 18, 4)->default(0.0000);
                $table->decimal('nqty', 18, 4)->default(0.0000);
                $table->decimal('nqtyscan', 18, 4);
                $table->string('cunit', 10)->nullable();
                $table->decimal('nprice', 18, 4)->default(0.0000);
                $table->decimal('namount', 18, 4)->default(0.0000);
                $table->decimal('nbaseamount', 18, 4)->default(0.0000);
                $table->string('cmainunit', 10)->nullable();
                $table->decimal('nfactor', 18, 4)->default(0.0000);
                $table->decimal('nnet', 18, 4)->default(0.0000);
                $table->decimal('nbase', 18, 4)->default(0.0000);
                $table->decimal('ndisc', 18, 4)->default(0.0000);
                $table->string('cacctcode', 50)->nullable();
                $table->string('cacctcost', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('testing')) {
            Schema::dropIfExists('dr_t');
        }
    }
};
