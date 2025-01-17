<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
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

        $ctranno = $this->generateCtranno();

        $salesOrderData = [
            'compcode' => '001',
            'ctranno' => $ctranno,
            'ccode' => $orderData['customer_id'],
            'ddate' => $orderData['date_created'],
            'dcutdate' => $orderData['date_created'],
            'dpodate' => $orderData['date_created'],
            'csalestype' => 'Goods',
            'cpono' => $orderData['order_key'],
            'ngross' => $orderData['total'],
            'nbasegross' => $orderData['total'],
            'ccurrencycode' => $orderData['currency'],
            'ccurrencydesc' => $orderData['currency_symbol'],
            'nexchangerate' => 1, // Set your exchange rate
            'cremarks' => $orderData['customer_note'],
            'cspecins' => '', // Set your special instructions
            'cpreparedby' => 'WooCommerce',
            'csalesman' => '', // Set the salesman field
            'cdelcode' => '', // Set the delivery code
            'cdeladdno' => $orderData['shipping']['address_1'],
            'cdeladdcity' => $orderData['shipping']['city'],
            'cdeladdstate' => $orderData['shipping']['state'],
            'cdeladdcountry' => $orderData['shipping']['country'],
            'cdeladdzip' => $orderData['shipping']['postcode'],
            'lapproved' => 0,
            'lvoid' => 0,
            'lcancelled' => 0,
            'lsent' => 0,
            'lprintposted' => 0,
        ];

        $salesOrder = SalesOrder::create($salesOrderData);

    }

    private function generateCtranno()
    {
        $prefix = 'SO';
        $month = date('m');
        $year = date('y');

        // Fetch the highest ctranno for the current month and year
        $highestCtranno = SalesOrder::where('ctranno', 'like', $prefix . $month . $year . '%')
            ->orderBy('ctranno', 'desc')
            ->first();

        if ($highestCtranno) {
            // Extract the numeric part of the highest ctranno
            $numberPart = intval(substr($highestCtranno->ctranno, 6));
        } else {
            // If no ctranno exists, start from 0
            $numberPart = 0;
        }

        // Increment the number to get the next unique number
        $newNumber = $numberPart + 1;

        // Format the new number to maintain leading zeros
        $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Generate the new ctranno
        return $prefix . $month . $year . $formattedNumber;
    }
}
