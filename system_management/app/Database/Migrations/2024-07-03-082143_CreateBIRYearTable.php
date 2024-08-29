<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBIRYearTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('bir_year')) {
            return;
        }
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'year' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('bir_year');
        $seeder = \Config\Database::seeder();
        $seeder->call('App\Database\Seeds\BIRYearSeeder');
    }

    public function down()
    {
        if (!$this->db->tableExists('bir_year')) {
            return;
        }
        $this->forge->dropTable('bir_year');
    }
}
