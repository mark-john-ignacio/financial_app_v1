<?php

namespace Modules\SysMgmt\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BirFormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\SysMgmt\Models\BirForm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'form_code' => fake()->word,
            'form_name' => fake()->word,
            'filter' => fake()->word,
            'cstatus' => fake()->word,
        ];
    }
}