<?php

namespace Modules\WooCommerceWebhook\App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\DB;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\DeliveryReceipt;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\SalesOrder;
use Modules\WooCommerceWebhook\Models\SalesOrderItem;

class DeleteAllAction
{
    use AsAction;

    public function handle()
    {
        DB::transaction(function () {
            SalesOrder::where('cremarks', 'from_woocommerce')->delete();
            DeliveryReceipt::where('cremarks', 'from_woocommerce')->delete();
            SalesOrderItem::where('citemremarks', 'from_woocommerce')->delete();
            Item::where('cGroup1', 'from_woocommerce')->delete();
            Customer::where('cGroup1', 'from_woocommerce')->delete();
        });

        return response()->json(['status' => 'success']);
    }
}
