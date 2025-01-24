<?php

namespace Modules\WooCommerceWebhook\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class WooCommerceWebhookDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::select('nid')->get();

        foreach ($items as $item){
            WooCommerceProductMapping::create([
                'woocommerce_product_id' => null,
                'myxfin_product_id' => $item->nid
            ]);
        }

//         Create Item
        $itemA4=Item::where('cpartno', 'ITEMWC001')->first();
        if(!$itemA4){
            $itemA4=Item::create([
                "compcode" => "001",
                "cpartno" => "ITEMWC0001",
                "cskucode" => null,
                "citemdesc" => "A4 TECH OP-720 OPTICAL MOUSE",
                "cunit" => "PCS",
                "cclass" => "ICLS0015",
                "ctype" => "ITEM",
                "csalestype" => "Goods",
                "ctradetype" => "Trade",
                "ctaxcode" => "VT",
                "cpricetype" => "PM",
                "nmarkup" => "0.00",
                "cacctcodesales" => "90",
                "cacctcodesalescr" => null,
                "cacctcodewrr" => "102",
                "cacctcodedr" => null,
                "cacctcoderet" => "100",
                "cacctcodecog" => null,
                'cGroup1' => 'from_woocommerce',
            ]);
        }

        WooCommerceProductMapping::create([
            'woocommerce_product_id' => 11123,
            'myxfin_product_id' => $itemA4->nid
        ]);

        // Create default customer
        Customer::create([
            'compcode' => "001",
            'cempid' => "CUSTWC_001",
            'cname' => 'CASH SALES',
            'ctradename' => 'WOOCOMMERCE',
            'chouseno' => null,
            'ccity' => null,
            'cstate' => null,
            'ccountry' => null,
            'czip' => null,
            'cacctcodesales' => '166',
            'cacctcodetype' => 'single',
            'ccustomertype' => 'TYP001',
            'ccustomerclass' => 'CLS001',
            'cpricever' => 'None',
            'cvattype' => 'VT',
            'cterms' => '30DY',
            'cGroup1' => 'from_woocommerce',
        ]);


    }
}
