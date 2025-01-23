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
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10)->default('001');
                $table->string('ctranno', 50);
                $table->string('ccode', 50)->nullable();
                $table->string('cremarks', 100)->nullable();
                $table->dateTime('ddate')->nullable();
                $table->date('dcutdate')->nullable();
                $table->decimal('nexempt', 18, 4)->default(0.0000);
                $table->decimal('nzerorated', 18, 4)->default(0.0000);
                $table->decimal('nnet', 18, 4)->default(0.0000);
                $table->decimal('nvat', 18, 4)->default(0.0000);
                $table->decimal('newt', 18, 4)->default(0.0000);
                $table->string('cewtcode', 25)->nullable();
                $table->decimal('ngrossbefore', 18, 4)->default(0.0000);
                $table->decimal('ngrossdisc', 18, 4)->default(0.0000);
                $table->decimal('ngross', 18, 4)->default(0.0000);
                $table->decimal('nbasegross', 18, 4)->default(0.0000);
                $table->decimal('ntotaldiscounts', 18, 4)->default(0.0000);
                $table->string('ccurrencycode', 5)->nullable();
                $table->string('ccurrencydesc', 50)->nullable();
                $table->decimal('nexchangerate', 18, 4)->default(0.0000);
                $table->string('cpreparedby', 50)->nullable();
                $table->boolean('lapproved')->default(0);
                $table->boolean('lvoid')->default(0);
                $table->boolean('lcancelled')->default(0);
                $table->boolean('lprintposted')->default(0);
                $table->string('cacctcode', 50)->nullable();
                $table->string('cvatcode', 5)->nullable();
                $table->decimal('ncreditbal', 18, 4)->default(0.0000);
                $table->decimal('npayed', 18, 4)->default(0.0000);
                $table->string('csalestype', 50)->nullable();
                $table->string('csiprintno', 50)->nullable();
                $table->string('creinvoice', 25)->default('NO');
                $table->boolean('lstopreinvoice')->default(0);
                $table->string('cterms', 25)->nullable();
                $table->string('cpaytype', 25)->nullable();
                $table->string('crefmodule', 50)->nullable();
                $table->text('crefmoduletran')->nullable();
                $table->decimal('nordue', 18, 4)->default(0.0000);
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
            Schema::dropIfExists('sales');
        }
    }
};
