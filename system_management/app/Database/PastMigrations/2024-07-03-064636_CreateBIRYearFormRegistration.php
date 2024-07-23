<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBIRYearFormRegistration extends Migration
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
        $this->forge->createTable('bir_year_form_registration');
        $seeder = \Config\Database::seeder();
        $seeder->call('App\Database\Seeds\BIRYearFormSeeder');

    }

    public function down()
    {
        $this->forge->dropTable('bir_year_form_registration');
    }
}
