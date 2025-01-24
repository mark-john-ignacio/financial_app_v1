<?php

namespace Modules\WooCommerceWebhook\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WooCommerceWebhook\Models\Customer;

class WooCommerceWebhookDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ItemSeeder::class,
        ]);

        $this->call([
            CustomerSeeder::class,
        ]);

    }
}
