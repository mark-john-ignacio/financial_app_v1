<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \Modules\WooCommerceWebhook\Models\Customer::class;
    public function definition(): array
    {
        return [
            'compcode' => '001',
            'cempid' => fake()->unique()->bothify('EMP###'),
            'cname' => fake()->name,
            'ctradename' => fake()->company,
            'cacctcodesales' => fake()->bothify('ACC###'),
            'cacctcodesalescr' => fake()->bothify('ACC###'),
            'cacctcodetype' => fake()->word,
            'cacctcodesales2' => fake()->bothify('ACC###'),
            'ccustomertype' => fake()->word,
            'ccustomerclass' => fake()->word,
            'cpricever' => fake()->word,
            'cvattype' => fake()->word,
            'cterms' => fake()->bothify('TERM###'),
            'ctin' => fake()->bothify('TIN###'),
            'chouseno' => fake()->buildingNumber,
            'ccity' => fake()->city,
            'cstate' => fake()->state,
            'ccountry' => fake()->country,
            'czip' => fake()->postcode,
            'cuserpic' => fake()->imageUrl(),
            'nlimit' => fake()->randomFloat(4, 0, 10000),
            'cparentcode' => fake()->bothify('PARENT###'),
            'csman' => fake()->bothify('SMAN###'),
            'cGroup1' => fake()->word,
            'cGroup2' => fake()->word,
            'cGroup3' => fake()->word,
            'cGroup4' => fake()->word,
            'cGroup5' => fake()->word,
            'cGroup6' => fake()->word,
            'cGroup7' => fake()->word,
            'cGroup8' => fake()->word,
            'cGroup9' => fake()->word,
            'cGroup10' => fake()->word,
            'cstatus' => 'ACTIVE',
            'dsince' => fake()->date(),
            'cdefaultcurrency' => 'PHP',
            'created_by' => fake()->randomNumber(),
            'updated_by' => fake()->randomNumber(),
            'deleted_by' => fake()->randomNumber(),
            'deleted' => fake()->boolean,
        ];
    }
}
