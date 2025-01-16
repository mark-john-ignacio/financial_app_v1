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
    }
}
