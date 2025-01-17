<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesOrderItem>
 */
class SalesOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'compcode' => '001',
            'cidentity' => substr(fake()->uuid, 0, 10),
            'ctranno' => substr(fake()->uuid, 0, 10),
            'creference' => fake()->optional()->uuid,
            'nident' => fake()->optional()->randomNumber(),
            'citemno' => fake()->word,
            'nqty' => fake()->randomFloat(4, 1, 1000),
            'cunit' => fake()->word,
            'nexprice' => fake()->randomFloat(6, 0, 1000),
            'nprice' => fake()->randomFloat(6, 0, 1000),
            'namount' => fake()->randomFloat(4, 0, 1000),
            'nbaseamount' => fake()->randomFloat(4, 0, 1000),
            'cmainunit' => fake()->randomDigit(),
            'nfactor' => fake()->randomFloat(4, 0, 1000),
            'nbase' => fake()->randomFloat(4, 0, 1000),
            'ndisc' => fake()->randomFloat(4, 0, 1000),
            'nnet' => fake()->randomFloat(4, 0, 1000),
        ];
    }
}
