<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
            'cempid' => $this->faker->unique()->bothify('EMP###'),
            'cname' => $this->faker->name,
            'ctradename' => $this->faker->company,
            'cacctcodesales' => $this->faker->bothify('ACC###'),
            'cacctcodesalescr' => $this->faker->bothify('ACC###'),
            'cacctcodetype' => $this->faker->word,
            'cacctcodesales2' => $this->faker->bothify('ACC###'),
            'ccustomertype' => $this->faker->word,
            'ccustomerclass' => $this->faker->word,
            'cpricever' => $this->faker->word,
            'cvattype' => $this->faker->word,
            'cterms' => $this->faker->bothify('TERM###'),
            'ctin' => $this->faker->bothify('TIN###'),
            'chouseno' => $this->faker->buildingNumber,
            'ccity' => $this->faker->city,
            'cstate' => $this->faker->state,
            'ccountry' => $this->faker->country,
            'czip' => $this->faker->postcode,
            'cuserpic' => $this->faker->imageUrl(),
            'nlimit' => $this->faker->randomFloat(4, 0, 10000),
            'cparentcode' => $this->faker->bothify('PARENT###'),
            'csman' => $this->faker->bothify('SMAN###'),
            'cGroup1' => $this->faker->word,
            'cGroup2' => $this->faker->word,
            'cGroup3' => $this->faker->word,
            'cGroup4' => $this->faker->word,
            'cGroup5' => $this->faker->word,
            'cGroup6' => $this->faker->word,
            'cGroup7' => $this->faker->word,
            'cGroup8' => $this->faker->word,
            'cGroup9' => $this->faker->word,
            'cGroup10' => $this->faker->word,
            'cstatus' => 'ACTIVE',
            'dsince' => $this->faker->date(),
            'cdefaultcurrency' => 'PHP',
            'created_by' => $this->faker->randomNumber(),
            'updated_by' => $this->faker->randomNumber(),
            'deleted_by' => $this->faker->randomNumber(),
            'deleted' => $this->faker->boolean,
        ];
    }
}
