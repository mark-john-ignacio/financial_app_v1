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
                    'constraint' => 2, // Example format: 'MM'
                    'default' => "01", // Example default value
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
