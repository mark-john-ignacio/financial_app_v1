<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyColumnToBirYearFormRegistrationTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bir_year_form_registration', [
            'compcode' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => false,
                'after' => 'id',
                'default' => '001',
                'comment' => 'Company Code'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bir_year_form_registration', 'compcode');
    }
}
