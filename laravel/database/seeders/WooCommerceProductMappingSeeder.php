<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\WooCommerceProductMapping;

class WooCommerceProductMappingSeeder extends Seeder
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
            ]);
        }

        WooCommerceProductMapping::create([
            'woocommerce_product_id' => 11123,
            'myxfin_product_id' => $itemA4->nid
        ]);


    }
}
