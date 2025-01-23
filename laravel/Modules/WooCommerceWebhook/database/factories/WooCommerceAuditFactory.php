<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WooCommerceAuditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\WooCommerceWebhook\Models\WooCommerceAudit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

