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
            Schema::create('dr', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10)->default('001');
                $table->string('ctranno', 50);
                $table->string('ccode', 50)->nullable();
                $table->text('cremarks')->nullable();
                $table->dateTime('ddate')->nullable();
                $table->date('dcutdate')->nullable();
                $table->string('cdrprintno', 50)->nullable();
                $table->decimal('ngross', 18, 4)->nullable();
                $table->decimal('nbasegross', 18, 4)->default(0.0000);
                $table->string('ccurrencycode', 5)->nullable();
                $table->string('ccurrencydesc', 50)->nullable();
                $table->decimal('nexchangerate', 18, 4)->default(0.0000);
                $table->string('cpreparedby', 50)->nullable();
                $table->string('cacctcode', 50)->nullable();
                $table->string('csalesman', 10);
                $table->string('cdelcode', 10);
                $table->string('cdeladdno', 100);
                $table->string('cdeladdcity', 50);
                $table->string('cdeladdstate', 50);
                $table->string('cdeladdcountry', 50);
                $table->boolean('lprintposted')->default(0);
                $table->boolean('lapproved')->default(0);
                $table->boolean('lvoid')->default(0);
                $table->boolean('lcancelled')->default(0);
                $table->string('cdeladdzip', 5);
                $table->string('cterms', 25)->nullable();
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
            Schema::dropIfExists('dr');
        }
    }
};
