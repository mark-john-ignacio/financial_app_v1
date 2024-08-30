<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFiscalEndMonthToCompanyTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('fiscal_month_start_end', 'company')) {
            $fields = [
                'fiscal_month_start_end' => [
                    'type' => 'VARCHAR',
                    'constraint' => 7, // Example format: 'MM-YYYY' (or adjust as needed)
                    'default' => "01-2024", // Example default value
                    'after' => 'reporting_period_type'
                ]
            ];
            $this->forge->addColumn('company', $fields);
        }

    }

    public function down()
    {
        if ($this->db->fieldExists('fiscal_month_start_end', 'company')) {
            $this->forge->dropColumn('company', 'fiscal_month_start_end');
        }
    }
    
}
