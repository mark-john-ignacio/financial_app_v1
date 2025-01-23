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
            Schema::create('items', function (Blueprint $table) {
                $table->string('compcode', 10)->default('001');
                $table->increments('nid');
                $table->string('cpartno', 50);
                $table->string('cskucode', 255)->nullable();
                $table->text('citemdesc');
                $table->string('cunit', 10)->nullable();
                $table->string('cclass', 10);
                $table->string('ctype', 10)->nullable();
                $table->string('csalestype', 50);
                $table->string('ctradetype', 50)->nullable();
                $table->string('ctaxcode', 5)->nullable();
                $table->string('cpricetype', 5)->default('MU');
                $table->decimal('nmarkup', 18, 2)->default(0.00);
                $table->string('cacctcodesales', 10)->nullable();
                $table->string('cacctcodesalescr', 10)->nullable();
                $table->string('cacctcodewrr', 10)->nullable();
                $table->string('cacctcodedr', 10)->nullable();
                $table->string('cacctcoderet', 10)->nullable();
                $table->string('cacctcodecog', 10)->nullable();
                $table->string('cGroup1', 25)->nullable();
                $table->string('cGroup2', 25)->nullable();
                $table->string('cGroup3', 25)->nullable();
                $table->string('cGroup4', 25)->nullable();
                $table->string('cGroup5', 25)->nullable();
                $table->string('cGroup6', 25)->nullable();
                $table->string('cGroup7', 25)->nullable();
                $table->string('cGroup8', 25)->nullable();
                $table->string('cGroup9', 25)->nullable();
                $table->string('cGroup10', 25)->nullable();
                $table->text('cnotes')->nullable();
                $table->boolean('lSerial')->default(0);
                $table->boolean('lbarcode')->default(0);
                $table->boolean('lpack')->default(0);
                $table->decimal('ninvmin', 18, 2)->default(0.00);
                $table->decimal('ninvmax', 18, 2)->default(0.00);
                $table->decimal('ninvordpt', 18, 2)->default(0.00);
                $table->string('cuserpic', 100)->nullable();
                $table->boolean('linventoriable')->default(0);
                $table->string('cstatus', 10)->default('ACTIVE');
                $table->timestamps();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->softDeletes();
                $table->integer('deleted_by')->nullable();
                $table->boolean('deleted')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('testing')) {
            Schema::dropIfExists('items');
        }
    }
};
