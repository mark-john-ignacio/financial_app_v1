<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use App\Models\WooCommerceProductMapping as ProductMapping;
use Illuminate\Support\Facades\Log;

class WooCommerceWebhookController extends Controller
{
    private $company_code = '001';
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
        $customerCode = $this->getCustomerCode($orderData);

        $salesOrder = SalesOrder::create([
            'compcode' => $this->company_code,
            'ctranno' => $ctranno,
            'ccode' => $customerCode,
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
            'cdelcode' => $customerCode, // Delivery customer
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
        ]);



    }

    private function generateCtranno()
    {
        $prefix = 'SO';
        $month = date('m');
        $year = date('y');

        $highestCtranno = SalesOrder::where('ctranno', 'like', $prefix . $month . $year . '%')
            ->orderBy('ctranno', 'desc')
            ->first();

        if ($highestCtranno) {
            $numberPart = intval(substr($highestCtranno->ctranno, 6));
        } else {
            $numberPart = 0;
        }

        $newNumber = $numberPart + 1;

        $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return $prefix . $month . $year . $formattedNumber;
    }

    private function getCustomerCode($data){
        $customerName = strtoupper($data['billing']['first_name'] . ' ' . $data['billing']['last_name']);
        $customer = Customer::where('cname', $customerName)->first();

        if($customer){
            return $customer->cempid;
        } else {
            $customer = Customer::create([
                'compcode' => $this->company_code,
                'cempid' => $this->generateCustomerCode(),
                'cname' => $customerName,
                'ctradename' => strtoupper($data['billing']['company']),
                'chouseno' => $data['billing']['address_1'],
                'ccity' => $data['billing']['city'],
                'cstate' => $data['billing']['state'],
                'ccountry' => $data['billing']['country'],
                'czip' => $data['billing']['postcode'],
                'cacctcodesales' => '14',
                'cterms' => '30DY'
            ]);
            return $customer->cempid;
        }
    }

    private function generateCustomerCode(){
        $prefix = 'CUSTWC_';
        $highestCode = Customer::where('compcode', $this->company_code)
            ->where('cempid', 'like', $prefix . '%')
            ->orderBy('cempid', 'desc')
            ->first();

        if ($highestCode) {
            $numberPart = intval(substr($highestCode->cempid, 8));
        } else {
            $numberPart = 0;
        }
        $newNumber = $numberPart + 1;

        $paddingLength = max(3, strlen((string)$numberPart));

        $formattedNumber = str_pad($newNumber, $paddingLength, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }
}
