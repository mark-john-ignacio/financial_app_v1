<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PinModel;

class BIRPinSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'pin' => '$2y$10$lQWLwfhcs7T6..Dj6s1wmOegjJziOPleuzd7Mny8AavHsu/hBgXRm'
        ];
        //secret
        $model = new PinModel();
        $model->insert($data);

    }
}
