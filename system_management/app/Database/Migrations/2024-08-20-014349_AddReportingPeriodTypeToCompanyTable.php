<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReportingPeriodTypeToCompanyTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('company', [
            'reporting_period_type' => [
                'type' => 'ENUM',
                'constraint' => ['fiscal', 'calendar'],
                'default' => 'fiscal',
                'after' => 'bir_sig_email',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('company', 'reporting_period_type');
    }
}
