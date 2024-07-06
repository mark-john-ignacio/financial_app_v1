<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBirPinTable extends Migration
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
            'pin' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('bir_pin');
        $seeder = \Config\Database::seeder();
        $seeder->call('App\Database\Seeds\BirPinSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('bir_pin');
    }
}
