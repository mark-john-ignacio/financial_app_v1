<?php

namespace Modules\WooCommerceWebhook\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\DeliveryReceipt;
use Modules\WooCommerceWebhook\Models\DeliveryReceiptItem;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\SalesInvoice;
use Modules\WooCommerceWebhook\Models\SalesInvoiceItem;
use Modules\WooCommerceWebhook\Models\SalesOrder;
use Modules\WooCommerceWebhook\Models\SalesOrderItem;

class DeleteAll
{
    use AsAction;

    public function handle()
    {
        DB::transaction(function () {
            SalesOrder::where('cremarks', 'from_woocommerce')->delete();
            DeliveryReceipt::where('cremarks', 'from_woocommerce')->delete();
            SalesOrderItem::where('citemremarks', 'from_woocommerce')->delete();
            DeliveryReceiptItem::where('cacctcode', 'from_woocommerce')->delete();
            SalesInvoice::where('cremarks', 'from_woocommerce')->delete();
            SalesInvoiceItem::where('cacctcode', 'from_woocommerce')->delete();
//            Item::where('cGroup1', 'from_woocommerce')->delete();
            Customer::where('cGroup1', 'from_woocommerce')->delete();
        });

        return response()->json(['status' => 'success']);
    }
}
