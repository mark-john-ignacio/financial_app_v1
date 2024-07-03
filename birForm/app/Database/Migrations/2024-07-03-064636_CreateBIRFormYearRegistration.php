<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBIRFormYearRegistration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'year_id' => [
                'type' => 'INT',
            ],
            'form_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('bir_form_year_registration');
    }

    public function down()
    {
        $this->forge->dropTable('bir_form_year_registration');
    }
}
