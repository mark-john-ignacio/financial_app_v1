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
            Schema::create('sales_t', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10)->default('001');
                $table->string('cidentity', 25);
                $table->string('ctranno', 50)->nullable();
                $table->string('creference', 50)->nullable();
                $table->integer('nrefident')->nullable();
                $table->integer('nident')->nullable();
                $table->string('citemno', 50)->nullable();
                $table->decimal('nqty', 18, 4)->default(0.0000);
                $table->decimal('nqtyreturned', 18, 4)->nullable();
                $table->string('cunit', 10)->nullable();
                $table->decimal('nprice', 18, 4)->default(0.0000);
                $table->decimal('ndiscount', 18, 4)->default(0.0000);
                $table->decimal('namount', 18, 4)->default(0.0000);
                $table->decimal('nbaseamount', 18, 4)->default(0.0000);
                $table->decimal('nnetvat', 18, 10)->default(0.0000000000);
                $table->decimal('nlessvat', 18, 10)->default(0.0000000000);
                $table->string('cmainunit', 10)->nullable();
                $table->decimal('nfactor', 18, 4)->default(0.0000);
                $table->string('cacctcode', 50)->nullable();
                $table->string('ctaxcode', 5)->nullable();
                $table->integer('nrate')->default(0);
                $table->string('cewtcode', 25)->nullable();
                $table->integer('newtrate')->nullable();
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
            Schema::dropIfExists('sales_t');
        }
    }
};
