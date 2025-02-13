<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpecialDicountTTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'compcode' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tranno' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'itemno' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'person' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'personID' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('specialdiscount_t');
    }

    public function down()
    {
        $this->forge->dropTable('specialdiscount_t');
    }
}
