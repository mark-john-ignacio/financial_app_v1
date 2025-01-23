<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\WooCommerceWebhook\Models\Item;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'compcode' => '001',
            'cpartno' => $this->faker->unique()->bothify('PART###'),
            'cskucode' => $this->faker->unique()->bothify('SKU###'),
            'citemdesc' => $this->faker->sentence,
            'cunit' => $this->faker->randomElement(['pcs', 'box', 'kg']),
            'cclass' => $this->faker->randomElement(['A', 'B', 'C']),
            'ctype' => $this->faker->randomElement(['type1', 'type2', 'type3']),
            'csalestype' => $this->faker->randomElement(['Retail', 'Wholesale']),
            'ctradetype' => $this->faker->randomElement(['Trade1', 'Trade2']),
            'ctaxcode' => $this->faker->randomElement(['TAX1', 'TAX2']),
            'cpricetype' => 'MU',
            'nmarkup' => $this->faker->randomFloat(2, 0, 100),
            'cacctcodesales' => $this->faker->bothify('ACC###'),
            'cacctcodesalescr' => $this->faker->bothify('ACC###'),
            'cacctcodewrr' => $this->faker->bothify('ACC###'),
            'cacctcodedr' => $this->faker->bothify('ACC###'),
            'cacctcoderet' => $this->faker->bothify('ACC###'),
            'cacctcodecog' => $this->faker->bothify('ACC###'),
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
            'cnotes' => $this->faker->paragraph,
            'lSerial' => $this->faker->boolean,
            'lbarcode' => $this->faker->boolean,
            'lpack' => $this->faker->boolean,
            'ninvmin' => $this->faker->randomFloat(2, 0, 100),
            'ninvmax' => $this->faker->randomFloat(2, 0, 100),
            'ninvordpt' => $this->faker->randomFloat(2, 0, 100),
            'cuserpic' => $this->faker->imageUrl(),
            'linventoriable' => $this->faker->boolean,
            'cstatus' => 'ACTIVE',
            'created_by' => $this->faker->randomNumber(),
            'updated_by' => $this->faker->randomNumber(),
            'deleted_by' => $this->faker->randomNumber(),
            'deleted' => $this->faker->boolean,
        ];
    }
}