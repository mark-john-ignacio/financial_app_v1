<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryReceipt;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use App\Models\WooCommerceProductMapping as ProductMapping;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($orderData, $myxfinProductIds) {
            $SOCtranno = $this->generateSOCtranno();
            $customerCode = $this->getCustomerCode($orderData);
            $salesOrder = SalesOrder::create([
                'compcode' => $this->company_code,
                'ctranno' => $SOCtranno,
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
                'cremarks' => 'from_woocommerce',
                'cspecins' => $orderData['customer_note'],
                'cpreparedby' => 'WooCommerce',
                'csalesman' => '', // Set the salesman field
                'cdelcode' => $customerCode, // Delivery customer
                'cdeladdno' => $orderData['shipping']['address_1'],
                'cdeladdcity' => $orderData['shipping']['city'],
                'cdeladdstate' => $orderData['shipping']['state'],
                'cdeladdcountry' => $orderData['shipping']['country'],
                'cdeladdzip' => $orderData['shipping']['postcode'],
            ]);

            //TODO: Create Sales Order Items
            foreach ($orderData['line_items'] as $item){
                $productMapping = ProductMapping::where('woocommerce_product_id', $item['product_id'])
                    ->first();

                if($productMapping){
                    $myxfinProductId = $productMapping->myxfin_product_id;
                    $salesOrder->sales_order_items()->create([
                        'compcode' => $this->company_code,
                        'ctranno' => $SOCtranno,
                        'citemcode' => $myxfinProductId,
                        'citemdesc' => $item['name'],
                        'nquantity' => $item['quantity'],
                        'nprice' => $item['price'],
                        'nbaseprice' => $item['price'],
                        'namount' => $item['total'],
                        'nbaseamount' => $item['total'],
                        'ccurrencycode' => $orderData['currency'],
                        'ccurrencydesc' => $orderData['currency_symbol'],
                        'nexchangerate' => 1, // Set your exchange rate
                        'cremarks' => 'from_woocommerce',
                    ]);
                } else {
                    throw new \Exception('No mapping found for WooCommerce product ID: ' . $item['product_id']);
                }
            }
            //Create DR
            DeliveryReceipt::create([
                'compcode' => $this->company_code,
                'ctranno' => $this->generateDRCtranno(),
                'ccode' => $customerCode,
                'cremarks' => 'from_woocommerce',
                'ddate' => $orderData['date_created'],
                'dcutdate' => $orderData['date_created'],
                'ngross' => $orderData['total'],
                'nbasegross' => $orderData['total'],
                'ccurrencycode' => $orderData['currency'],
                'ccurrencydesc' => $orderData['currency_symbol'],
                'nexchangerate' => 1, // Set your exchange rate
                'cpreparedby' => 'WooCommerce',
                'cacctcode' => '14', // Set your account code
                'csalesman' => '', // Set the salesman field
                'cdelcode' => $customerCode, // Delivery customer
                'cdeladdno' => $orderData['shipping']['address_1'],
                'cdeladdcity' => $orderData['shipping']['city'],
                'cdeladdstate' => $orderData['shipping']['state'],
                'cdeladdcountry' => $orderData['shipping']['country'],
                'cdeladdzip' => $orderData['shipping']['postcode'],
                'cterms' => '30DY',

            ]);
        });
        //TODO: Create DR_t with ref to the sales order


    }

    private function generateSOCtranno()
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

    private function generateDRCtranno()
    {
        $prefix = 'DR';
        $month = date('m');
        $year = date('y');

        $highestCtranno = DeliveryReceipt::where('ctranno', 'like', $prefix . $month . $year . '%')
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
                'cterms' => '30DY',
                'cGroup1' => 'from_woocommerce',
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

    //TODO: Remove this method in production
    public function deleteAll()
    {
        DB::transaction(function () {
            $salesOrder = SalesOrder::where('cremarks', 'from_woocommerce')->delete();
            $deliveryReceipt = DeliveryReceipt::where('cremarks', 'from_woocommerce')->delete();
//        $salesOrderItems = SalesOrderItems::where('cremarks', 'from_woocommerce')->get();
//        $deliveryReceiptItems = DeliveryReceiptItems::where('cremarks', 'from_woocommerce')->get();
//        $salesInvoice = SalesInvoice::where('cremarks', 'from_woocommerce')->get();
//        $salesInvoiceItems = SalesInvoiceItems::where('cremarks', 'from_woocommerce')->get();
            $customer = Customer::where('cGroup1', 'from_woocommerce')->delete();
//        $productMapping = ProductMapping::where('woocommerce_product_id', '!=', '')->get();
        });

        return response()->json(['status' => 'success']);

    }
}
