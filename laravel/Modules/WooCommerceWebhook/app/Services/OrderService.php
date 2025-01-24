<?php

namespace Modules\WooCommerceWebhook\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\DeliveryReceipt;
use Modules\WooCommerceWebhook\Models\DeliveryReceiptItem;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\SalesInvoice;
use Modules\WooCommerceWebhook\Models\SalesInvoiceItem;
use Modules\WooCommerceWebhook\Models\SalesOrder;
use Modules\WooCommerceWebhook\Models\SalesOrderItem;
use Modules\WooCommerceWebhook\Models\WooCommerceAudit as Audit;
use Modules\WooCommerceWebhook\Models\WoocommerceProductMapping as ProductMapping;

class OrderService
{
    private $company_code = '001';

    public function processOrder($orderData)
    {
        try {
            $created_data = DB::transaction(function () use ($orderData) {
                $soCtranno = $this->generateSOCtranno();
                $customerCode = Customer::where('cname', 'CASH SALES')->first()->cempid;

                $salesOrder = $this->createSalesOrder($orderData, $soCtranno, $customerCode);
                $this->createSalesOrderItems($orderData, $salesOrder, $soCtranno);

                $drCtranno = $this->generateDRCtranno();
                $deliveryReceipt = $this->createDeliveryReceipt($orderData, $drCtranno, $customerCode);
                $this->createDeliveryReceiptItems($salesOrder, $deliveryReceipt, $drCtranno, $soCtranno);

                $siCtranno = $this->generateSICtranno();
                $this->createSalesInvoice($orderData, $siCtranno, $customerCode, $drCtranno);
                $this->createSalesInvoiceItems($deliveryReceipt, $siCtranno, $drCtranno);

                return [
                    'sales_order_ctranno' => $soCtranno,
                    'delivery_receipt_ctranno' => $drCtranno,
                    'sales_invoice_ctranno' => $siCtranno,
                ];
            });

            Audit::create([
                'request_data' => $orderData,
                'response_data' => $created_data,
                'status' => 'success'
            ]);

            return $created_data;
        } catch (\Exception $e) {
            Audit::create([
                'request_data' => $orderData,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            Log::error('Order processing failed: ' . $e->getMessage());

            throw $e;
        }
    }

    private function createSalesOrder($orderData, $soCtranno, $customerCode)
    {
        return SalesOrder::create([
            'compcode' => $this->company_code,
            'ctranno' => $soCtranno,
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
    }

    private function createSalesOrderItems($orderData, $salesOrder, $soCtranno)
    {
        foreach ($orderData['line_items'] as $item) {
            $productMapping = ProductMapping::where('woocommerce_product_id', $item['product_id'])->first();
            if(!$productMapping) {
                throw new \Exception('No mapping found for WooCommerce product ID: ' . $item['product_id']);
            }
            $product = Item::find($productMapping->myxfin_product_id);
            $SOItemsCidentity = $this->generateSOItemsCidentity($soCtranno);
            $nident = intval(substr($SOItemsCidentity, strrpos($SOItemsCidentity, 'P') + 1));

            if ($product) {
                $salesOrder->sales_order_items()->create([
                    'compcode' => $this->company_code,
                    'cidentity' => $SOItemsCidentity,
                    'ctranno' => $soCtranno,
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
    }

    private function createDeliveryReceipt($orderData, $drCtranno, $customerCode)
    {
        return DeliveryReceipt::create([
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
    }

    private function createDeliveryReceiptItems($salesOrder, $deliveryReceipt, $drCtranno, $soCtranno)
    {
        foreach ($salesOrder->sales_order_items as $soItem) {
            $drItemsCidentity = $this->generateDRItemsCidentity($drCtranno);
            DeliveryReceiptItem::create([
                'compcode' => $this->company_code,
                'cidentity' => $drItemsCidentity,
                'nident' => 1,
                'ctranno' => $drCtranno,
                'creference' => $soCtranno,
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
    }

    private function createSalesInvoice($orderData, $siCtranno, $customerCode, $drCtranno)
    {
        SalesInvoice::create([
            'compcode' => $this->company_code,
            'ctranno' => $siCtranno,
            'ccode' => $customerCode,
            'cremarks' => 'from_woocommerce',
            'ddate' => $orderData['date_created'],
            'dcutdate' => $orderData['date_created'],
            'nexempt' => 0,
            'nzerorated' => 0,
            'nnet' => $orderData['total'],
            'nvat' => 0,
            'newt' => 0,
            'cewtcode' => '',
            'ngrossbefore' => $orderData['total'],
            'ngrossdisc' => 0,
            'ngross' => $orderData['total'],
            'nbasegross' => $orderData['total'],
            'ntotaldiscounts' => 0,
            'ccurrencycode' => $orderData['currency'],
            'ccurrencydesc' => $orderData['currency_symbol'],
            'nexchangerate' => 1, // Set your exchange rate
            'cpreparedby' => 'WooCommerce',
            'lapproved' => 0,
            'lvoid' => 0,
            'lcancelled' => 0,
            'cacctcode' => null,
            'cvatcode' => null,
            'ncreditbal' => "0.0000",
            'npayed' => "0.0000",
            'csalestype' => 'Goods',
            'csiprintno' => null,
            'creinvoice' => "NO",
            'lstopreinvoice' => 0,
            'cterms' => "",
            'cpaytype' => "",
            "crefmodule" => "DR",
            'crefmoduletran' => $drCtranno,
            'nordue' => "0.0000",
        ]);
    }

    private function createSalesInvoiceItems($deliveryReceipt, $siCtranno, $drCtranno)
    {
        foreach ($deliveryReceipt->delivery_receipt_items as $drItem) {
            $siItemsCidentity = $this->generateSIItemsCidentity($siCtranno);
            SalesInvoiceItem::create([
                "compcode" => $this->company_code,
                "cidentity" => $siItemsCidentity,
                "ctranno" => $siCtranno,
                "creference" => $drCtranno,
                "nrefident" => 1,
                "nident" => 1,
                "citemno" => $drItem->citemno,
                "nqty" => $drItem->nqty,
                "nqtyreturned" => 0,
                "cunit" => $drItem->cunit,
                "nprice" => $drItem->nprice,
                "ndiscount" => 0,
                "namount" => $drItem->namount,
                "nbaseamount" => $drItem->nbaseamount,
                "nnetvat" => 0,
                "nlessvat" => 0,
                "cmainunit" => $drItem->cmainunit,
                "nfactor" => $drItem->nfactor,
                "cacctcode" => "from_woocommerce",
                "ctaxcode" => "NT",
                "nrate" => 0,
                "cewtcode" => "",
                "newtrate" => 0,
            ]);
        }
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

    public function generateSICtranno(){
        $prefix = 'SI';
        $month = date('m');
        $year = date('y');

        $highestCtranno = SalesInvoice::where('ctranno', 'like', $prefix . $month . $year . '%')
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

    public function generateSIItemsCidentity($salesInvoiceId)
    {
        $pattern = $salesInvoiceId . 'P%';
        $latestItem = SalesInvoiceItem::where('cidentity', 'like', $pattern)
            ->orderBy('cidentity', 'desc')
            ->first();

        $nextNumber = 0; // Default if no previous items are found
        if ($latestItem) {
            $currentNumber = substr($latestItem->cidentity, strrpos($latestItem->cidentity, 'P') + 1);
            $nextNumber = intval($currentNumber) + 1;
        }

        $newCidentity = $salesInvoiceId . 'P' . $nextNumber;

        return $newCidentity;
    }

}
