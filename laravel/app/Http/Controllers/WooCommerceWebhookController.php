<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WooCommerceProductMapping as ProductMapping;
use Illuminate\Support\Facades\Log;

class WooCommerceWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $orderData = $request->all();

        $woocommerceProductIds = array_map(function($item){
            return $item['product_id'];
        }, $orderData['line_items']);

        $myxfinProductIds = $this->getMyxfinProductIds($woocommerceProductIds);
        $this->processOrder($orderData, $myxfinProductIds);


        return response()->json(['status' => 'success']);
    }

    public function getMyxfinProductIds($woocommerceProductIds)
    {
        $myxfinProductIds = [];
        foreach ($woocommerceProductIds as $woocommerceProductId){
            $mapping = ProductMapping::where('woocommerce_product_id', $woocommerceProductId)
            ->first();

            if($mapping){
                $myxfinProductIds[] = $mapping->myxfin_product_id;
            } else {
                Log::error('No mapping found for WooCommerce product ID: ' . $woocommerceProductId);
            }
        }

        return $myxfinProductIds;
    }

    public function processOrder($orderData, $myxfinProductIds)
    {
        //Order Transaction will get into Sales Order.
        //Sales Order will have reference Delivery Receipt.
        //Delivery Receipt will have reference Sales Invoice

    }
}
