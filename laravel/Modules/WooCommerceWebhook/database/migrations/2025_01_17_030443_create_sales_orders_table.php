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
        if (app()->environment('testing')) {
            Schema::create('so', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10);
                $table->string('ctranno', 50);
                $table->string('ccode', 10);
                $table->dateTime('ddate');
                $table->date('dcutdate');
                $table->string('csalestype', 50)->nullable();
                $table->string('cpono', 50)->nullable();
                $table->string('cpmid', 50)->nullable();
                $table->decimal('ngross', 18, 4);
                $table->decimal('nbasegross', 18, 4)->default(0.0000);
                $table->string('ccurrencycode', 5)->nullable();
                $table->string('ccurrencydesc', 50)->nullable();
                $table->decimal('nexchangerate', 18, 4)->default(0.0000);
                $table->string('cremarks', 100)->nullable();
                $table->text('cspecins')->nullable();
                $table->string('cpreparedby', 50);
                $table->string('csalesman', 10);
                $table->string('cdelcode', 10);
                $table->string('cdeladdno', 100);
                $table->string('cdeladdcity', 50);
                $table->string('cdeladdstate', 50);
                $table->string('cdeladdcountry', 50);
                $table->string('cdeladdzip', 5);
                $table->boolean('lapproved')->default(false);
                $table->boolean('lcancelled')->nullable()->default(false);
                $table->boolean('lprintposted')->default(false);
                $table->boolean('lvoid')->nullable()->default(false);
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
            Schema::dropIfExists('so');
        }
    }
};
