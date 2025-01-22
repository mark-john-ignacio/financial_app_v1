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
        if(!Schema::hasColumn('sales_t', 'id')) {
            DB::statement('ALTER TABLE sales_t DROP PRIMARY KEY');
            Schema::table('sales_t', function (Blueprint $table) {
                $table->id()->first();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('sales_t', 'id')){
            Schema::table('sales_t', function(Blueprint $table){
                $table->dropColumn('id');
            });

            DB::statement('ALTER TABLE sales_t ADD PRIMARY KEY(compcode, cidentity)');
        }
    }
};
