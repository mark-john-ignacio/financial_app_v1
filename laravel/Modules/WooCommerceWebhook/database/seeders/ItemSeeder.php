<?php

namespace Modules\WooCommerceWebhook\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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

        // Create Item
        $item2 = Item::factory()->create([
            "compcode" => "001",
            "cpartno" => "ITEMWC0002",
            "cskucode" => null,
            "citemdesc" => "A4TECH  KEYBOARD KRS-8372",
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

        WooCommerceProductMapping::create([
            'woocommerce_product_id' => 11159,
            'myxfin_product_id' => $item2->nid
        ]);

        $item3 = Item::factory()->create([
            "compcode" => "001",
            "cpartno" => "ITEMWC0003",
            "cskucode" => null,
            "citemdesc" => "A4TECHKRS-3330",
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

        WooCommerceProductMapping::create([
            'woocommerce_product_id' => 10915,
            'myxfin_product_id' => $item3->nid
        ]);

    }
}
