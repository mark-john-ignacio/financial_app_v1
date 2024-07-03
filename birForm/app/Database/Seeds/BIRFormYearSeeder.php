<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\BIRFormYearModel;

class BIRFormYearSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'year_id' => '1',
                'form_id' => '3',
            ],
            [
                'year_id' => '1',
                'form_id' => '4',
            ],
            [
                'year_id' => '1',
                'form_id' => '5',
            ],
            [
                'year_id' => '1',
                'form_id' => '6',
            ],
            [
                'year_id' => '2',
                'form_id' => '7',
            ],
            [
                'year_id' => '2',
                'form_id' => '8',
            ],
            [
                'year_id' => '2',
                'form_id' => '9',
            ],
            [
                'year_id' => '2',
                'form_id' => '1',
            ],
            [
                'year_id' => '3',
                'form_id' => '2',
            ],
            [
                'year_id' => '3',
                'form_id' => '3',
            ],
            [
                'year_id' => '3',
                'form_id' => '4',
            ],
            [
                'year_id' => '3',
                'form_id' => '5',
            ],
            [
                'year_id' => '4',
                'form_id' => '6',
            ],
            [
                'year_id' => '4',
                'form_id' => '7',
            ],
            [
                'year_id' => '4',
                'form_id' => '8',
            ],
            [
                'year_id' => '4',
                'form_id' => '9',
            ],
            [
                'year_id' => '5',
                'form_id' => '1',
            ],
            [
                'year_id' => '5',
                'form_id' => '2',
            ],
            [
                'year_id' => '5',
                'form_id' => '3',
            ],
            [
                'year_id' => '5',
                'form_id' => '4',
            ],

        ];
        $model = new BIRFormYearModel();
        $model -> insertBatch($data);

    }
}
