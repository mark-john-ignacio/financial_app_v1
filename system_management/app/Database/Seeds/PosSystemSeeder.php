<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PosSystemSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'compcode'     => '001',
            'cserialno'    => 'SN123456789',
            'cmachine'     => 'POS-Machine-01',
            'cpoweredname' => 'Myxfin Solutions Inc.',
            'cpoweredadd'  => '1234 Elm Street, Manila, PH',
            'cpoweredtin'  => 'TIN123456789',
            'caccredno'    => 'ACC123456789',
            'ddateissued'  => '2025-01-01',
            'deffectdate'  => '2025-01-01',
            'cptunum'      => 'PTU123456789',
            'dptuissued'   => '2025-01-01',
            'created_at'   => '2025-02-05 06:18:44',
            'updated_at'   => '2025-02-05 06:18:44',
        ];

        // Using Query Builder
        $this->db->table('pos_system')->insert($data);
    }
}