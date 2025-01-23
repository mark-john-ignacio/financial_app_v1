<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WooCommerceProductMapping>
 */
class WooCommerceProductMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'woocommerce_product_id' => $this->faker->unique()->randomNumber(),
            'myxfin_product_id' => $this->faker->unique()->randomNumber(),
        ];
    }
}
