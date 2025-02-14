<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosSystemTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('pos_system')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'compcode' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cserialno' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cmachine' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cpoweredname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cpoweredadd' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cpoweredtin' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'caccredno' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'ddateissued' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'deffectdate' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'cptunum' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'dptuissued' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pos_system');
    }

    public function down()
    {
        $this->forge->dropTable('pos_system');
    }
}
