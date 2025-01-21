<?php

namespace Modules\WooCommerceWebhook\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\DeliveryReceipt;
use Modules\WooCommerceWebhook\Models\DeliveryReceiptItem;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\SalesOrder;
use Modules\WooCommerceWebhook\Models\SalesOrderItem;
use Modules\WooCommerceWebhook\Models\WoocommerceProductMapping as ProductMapping;

class OrderService
{
    private $company_code = '001';

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
        $created_data = DB::transaction(function () use ($orderData, $myxfinProductIds) {
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
                 $product = Item::find($productMapping->myxfin_product_id);
                 $SOItemsCidentity = $this->generateSOItemsCidentity($SOCtranno);
                 $nident = intval(substr($SOItemsCidentity, strrpos($SOItemsCidentity, 'P') + 1));

                 if($product){
                     $myxfinProductId = $productMapping->myxfin_product_id;
                     $salesOrder->sales_order_items()->create([
                         'compcode' => $this->company_code,
                         'cidentity' => $SOItemsCidentity,
                         'ctranno' => $SOCtranno,
                         'creference' => $orderData['order_key'],
                         'nident' => $nident,
                         'nrefident' => $nident,
                         'citemno' => $product->cpartno,
                         'nqty' => $item['quantity'],
                         'cunit' => $product->cunit,
                         'nprice' => $item['total'],
                         'namount' => $item['price'],
                         'nbaseamount' => $item['price'],
                         'cmainunit' => $product->cunit,
                         'nfactor' => 1,
                         'nbase' => 0,
                         'ndisc' => 0,
                         'nnet' => 0,
                         'ctaxcode' => 'NT',
                         'nrate' => 0,
                         'citemremarks' => 'from_woocommerce',
                     ]);
                 } else {
                     throw new \Exception('No mapping found for WooCommerce product ID: ' . $item['product_id']);
                 }
             }
            //Create DR
            $drCtranno = $this->generateDRCtranno();
            DeliveryReceipt::create([
                'compcode' => $this->company_code,
                'ctranno' => $drCtranno,
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

            //Create DR Items under the DR referencing the SO for each SO Items
            foreach($salesOrder->sales_order_items as $soItem){
                $drItemsCidentity = $this->generateDRItemsCidentity($drCtranno);
                DeliveryReceiptItem::create([
                    'compcode' => $this->company_code,
                    'cidentity' => $drItemsCidentity,
                    'nident' => 1,
                    'ctranno' => $drCtranno,
                    'creference' => $SOCtranno,
                    'crefident' => 1,
                    'citemno' => $soItem->citemno,
                    'nqtyorig' => $soItem->nqty,
                    'nqty' => $soItem->nqty,
                    'nqtyscan' => $soItem->nqty,
                    'cunit' => $soItem->cunit,
                    'nprice' => $soItem->nprice,
                    'namount' => $soItem->namount,
                    'nbaseamount' => $soItem->nbaseamount,
                    'cmainunit' => $soItem->cmainunit,
                    'nfactor' => $soItem->nfactor,
                    'nbase' => $soItem->nbase,
                    'ndisc' => $soItem->ndisc,
                    'nnet' => $soItem->nnet,
                    'cacctcode' => 'from_woocommerce',
                ]);
            }

            return [
                'sales_order_ctranno' => $SOCtranno,
                'delivery_receipt_ctranno' => $drCtranno,
            ];
        });

        return $created_data;
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

    private function generateSOItemsCidentity($salesOrderId)
    {
        $pattern = $salesOrderId . 'P%';
        $latestItem = SalesOrderItem::where('cidentity', 'like', $pattern)
            ->orderBy('cidentity', 'desc')
            ->first();

        $nextNumber = 0; // Default if no previous items are found
        if ($latestItem) {
            $currentNumber = substr($latestItem->cidentity, strrpos($latestItem->cidentity, 'P') + 1);
            $nextNumber = intval($currentNumber) + 1;
        }

        $newCidentity = $salesOrderId . 'P' . $nextNumber;

        return $newCidentity;
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

    public function generateDRItemsCidentity($deliveryReceiptId)
    {
        $pattern = $deliveryReceiptId . 'P%';
        $latestItem = DeliveryReceiptItem::where('cidentity', 'like', $pattern)
            ->orderBy('cidentity', 'desc')
            ->first();

        $nextNumber = 0; // Default if no previous items are found
        if ($latestItem) {
            $currentNumber = substr($latestItem->cidentity, strrpos($latestItem->cidentity, 'P') + 1);
            $nextNumber = intval($currentNumber) + 1;
        }

        $newCidentity = $deliveryReceiptId . 'P' . $nextNumber;

        return $newCidentity;
    }

    private function getCustomerCode($data)
    {
        $customerName = strtoupper($data['billing']['first_name'] . ' ' . $data['billing']['last_name']);
        $customer = Customer::where('cname', $customerName)->first();

        if ($customer) {
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

    private function generateCustomerCode()
    {
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
