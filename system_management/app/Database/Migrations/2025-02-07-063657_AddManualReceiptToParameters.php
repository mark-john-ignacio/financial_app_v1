<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualReceiptToParameters extends Migration
{
    public function up()
    {
        // Get all company codes first
        $companies = $this->db->table('company')->select('compcode')->get()->getResultArray();
        
        // Prepare batch insert data
        $data = [];
        foreach ($companies as $company) {
            $data[] = [
                'compcode' => $company['compcode'],
                'ccode' => 'MANUAL_RECEIPT',
                'nallow' => 1,
            ];
        }

        // Insert new parameter for all companies
        if (!empty($data)) {
            $this->db->table('parameters')->insertBatch($data);
        }
    }

    public function down()
    {
        // Remove all MANUAL_RECEIPT parameters
        $this->db->table('parameters')
                 ->where('ccode', 'MANUAL_RECEIPT')
                 ->delete();
    }
}
