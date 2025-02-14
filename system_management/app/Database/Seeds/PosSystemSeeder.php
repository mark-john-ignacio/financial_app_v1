<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PosSystemSeeder extends Seeder
{
    public function run()
    {
        // Get all companies
        $companies = $this->db->table('company')->get()->getResultArray();

        // Prepare data for each company
        $data = [];
        foreach ($companies as $company) {
            // Check if compcode already exists in pos_system table
            $exists = $this->db->table('pos_system')
                               ->where('compcode', $company['compcode'])
                               ->countAllResults();

            if ($exists == 0) {
                $data[] = [
                    'compcode'     => $company['compcode'],
                    'cserialno'    => 'SN' . rand(100000000, 999999999),
                    'cmachine'     => 'POS-Machine-' . rand(1, 10),
                    'cpoweredname' => $company['compname'],
                    'cpoweredadd'  => $company['compadd'],
                    'cpoweredtin'  => $company['comptin'],
                    'caccredno'    => 'ACC' . rand(100000000, 999999999),
                    'ddateissued'  => date('Y-m-d', strtotime('-1 year')),
                    'deffectdate'  => date('Y-m-d'),
                    'cptunum'      => 'PTU' . rand(100000000, 999999999),
                    'dptuissued'   => date('Y-m-d'),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Insert data into pos_system table
        if (!empty($data)) {
            $this->db->table('pos_system')->insertBatch($data);
        }
    }
}