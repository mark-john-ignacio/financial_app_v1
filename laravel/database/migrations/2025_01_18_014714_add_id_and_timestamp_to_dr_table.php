<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(!Schema::hasColumn('dr', 'id')) {
            DB::statement('ALTER TABLE dr DROP PRIMARY KEY');

            Schema::table('dr', function (Blueprint $table){
               $table->id()->first();
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('dr', 'id')) {
            Schema::table('dr', function (Blueprint $table) {
                $table->dropColumn('id');
            });

            DB::statement('ALTER TABLE dr ADD PRIMARY KEY(compcode, ctranno)');
        }
    }
};
