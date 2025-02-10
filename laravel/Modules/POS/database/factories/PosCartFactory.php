<?php

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PosCartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\POS\Models\PosCart::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'employee_id' => 1,
            'item_id' => 2,
            'item_option_id' => $this->faker->uuid,
            'qty' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'special_discount' => $this->faker->randomFloat(2, 0, 10),
            'coupon' => $this->faker->randomFloat(2, 0, 10),
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Cancelled']),
            'item' => 'ITEM0155', //Must exist on both Items and items_pm_t
            'quantity' => $this->faker->numberBetween(1, 10),
            'item_specialDisc' => $this->faker->randomFloat(2, 0, 10),
            'item_coupon' => $this->faker->randomFloat(2, 0, 10),
            'employee_name' => 'Admin',
        ];
    }
}

