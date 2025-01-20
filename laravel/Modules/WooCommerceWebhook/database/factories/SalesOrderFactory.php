<?php

namespace Modules\WooCommerceWebhook\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesOrder>
 */
class SalesOrderFactory extends Factory
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
            'ctranno' => fake()->unique()->lexify('TRN-?????'),
            'ccode' => fake()->lexify('CODE-??'),
            'ddate' => fake()->dateTime,
            'dcutdate' => fake()->date,
            'csalestype' => fake()->optional()->lexify('SALESTYPE-??'),
            'cpono' => fake()->optional()->lexify('PONO-?????'),
            'cpmid' => fake()->optional()->lexify('PMID-?????'),
            'ngross' => fake()->randomFloat(4, 0, 10000),
            'nbasegross' => fake()->randomFloat(4, 0, 10000),
            'ccurrencycode' => substr(fake()->optional()->lexify('CUR-??'), 0 , 5),
            'ccurrencydesc' => fake()->optional()->lexify('CURRENCY-??'),
            'nexchangerate' => fake()->randomFloat(4, 0, 100),
            'cremarks' => fake()->optional()->text(25),
            'cspecins' => fake()->optional()->text,
            'cpreparedby' => fake()->lexify('USER-??'),
            'csalesman' => substr(fake()->lexify('SALESMAN-??'), 0, 10),
            'cdelcode' => fake()->lexify('DEL-??'),
            'cdeladdno' => substr(fake()->address, 0, 10),
            'cdeladdcity' => fake()->city,
            'cdeladdstate' => fake()->state,
            'cdeladdcountry' => substr(fake()->country, 0, 10),
            'cdeladdzip' => substr(fake()->postcode, 0, 5),
            'lapproved' => fake()->boolean,
            'lcancelled' => fake()->boolean,
            'lprintposted' => fake()->boolean,
            'lvoid' => fake()->boolean,
        ];
    }
}
