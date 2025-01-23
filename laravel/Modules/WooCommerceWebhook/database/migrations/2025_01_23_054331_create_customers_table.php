a<?php

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
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('compcode', 10)->default('001');
                $table->string('cempid', 10);
                $table->string('cname', 255);
                $table->string('ctradename', 255)->nullable();
                $table->string('cacctcodesales', 10);
                $table->string('cacctcodesalescr', 10)->nullable();
                $table->string('cacctcodetype', 25)->nullable();
                $table->string('cacctcodesales2', 50)->nullable();
                $table->string('ccustomertype', 10)->nullable();
                $table->string('ccustomerclass', 10)->nullable();
                $table->string('cpricever', 10)->nullable();
                $table->string('cvattype', 10)->nullable();
                $table->string('cterms', 10);
                $table->string('ctin', 25)->nullable();
                $table->string('chouseno', 50)->nullable();
                $table->string('ccity', 50)->nullable();
                $table->string('cstate', 50)->nullable();
                $table->string('ccountry', 50)->nullable();
                $table->integer('czip')->nullable();
                $table->string('cuserpic', 100)->nullable();
                $table->decimal('nlimit', 18, 4)->default(0.0000);
                $table->string('cparentcode', 10)->nullable();
                $table->string('csman', 10)->nullable();
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
                $table->string('cstatus', 20)->default('ACTIVE');
                $table->date('dsince')->nullable();
                $table->string('cdefaultcurrency', 25)->default('PHP');
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
            Schema::dropIfExists('customers');
        }
    }
};
