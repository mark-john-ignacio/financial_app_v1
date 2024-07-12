<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\BIRForms\BIRYearModel;

class BIRYearSeeder extends Seeder
{
    public function run()
    {
        $data = [[
                'year' => '2021',
            ],
            [
                'year' => '2022',
            ],
            [
                'year' => '2023',
            ],
            [
                'year' => '2024',
            ],
            [
                'year' => '2025',
            ],
        ];
        $model = new BIRYearModel();
        $model -> insertBatch($data);
    }
}
