<?php

namespace Modules\WooCommerceWebhook\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WooCommerceWebhook\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
