<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WooCommerceProductMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \Modules\WooCommerceWebhook\Models\WooCommerceProductMapping::class;
    public function definition(): array
    {
        return [
            'woocommerce_product_id' => $this->faker->unique()->randomNumber(),
            'myxfin_product_id' => $this->faker->unique()->randomNumber(),
        ];
    }
}
