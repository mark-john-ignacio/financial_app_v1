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
        Schema::table('pos', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->string('coupon')->nullable()->after('exchange');
            $table->decimal('serviceFee', 10, 2)->nullable()->after('coupon');
            $table->decimal('subtotal', 10, 2)->nullable()->after('serviceFee');
            $table->string('payment_method')->nullable()->after('subtotal');
            $table->string('payment_reference')->nullable()->after('payment_method');
        });

        //Create pendingorder_status table
        Schema::create('pendingorder_status', function (Blueprint $table) {
            $table->id();
            $table->string('tranno');
            $table->string('payment_transaction');
            //items
            $table->string('items');
            //quantity
            $table->integer('quantity');
            $table->string('waiting_time');

            $table->string('transaction_type');
            $table->string('pstatus');
            $table->string('order_adding');
            $table->string('receipt');
            $table->timestamps();
        });

        // Create pos_system
        Schema::create('pos_system', function (Blueprint $table) {
            $table->id();
            //compcode
            $table->string('compcode');
            $table->string('cserialno');
            $table->string('cmachine');
            $table->string('cpoweredname');
            $table->string('cpoweredadd');
            $table->string('cpoweredtin');
            $table->string('caccredno');
            $table->date('ddateissued');
            $table->date('deffectdate');
            $table->string('cptunum');
            $table->date('dptuissued');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos', function (Blueprint $table) {
            $table->dropColumn('coupon');
            $table->dropColumn('serviceFee');
            $table->dropColumn('subtotal');
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_reference');
        });

        Schema::dropIfExists('pendingorder_status');
        Schema::dropIfExists('pos_system');
    }
};
