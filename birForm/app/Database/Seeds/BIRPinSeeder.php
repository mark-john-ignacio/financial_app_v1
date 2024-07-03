<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PinModel;

class BIRPinSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'pin' => '$2y$10$JqyEPWqVjLn0dngavKiwW.KQCbakTNMLqJDVuztw1y4CA5EOYJEJ6'
        ];
        //456789
        $model = new PinModel();
        $model->insert($data);

    }
}
