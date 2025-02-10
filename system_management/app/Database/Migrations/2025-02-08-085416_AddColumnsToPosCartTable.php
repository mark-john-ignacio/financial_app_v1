<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreatePosCartTable extends Migration
{
    public function up()
    {
        // Drop the existing pos_cart table
        $this->forge->dropTable('pos_cart', true);

        // Recreate the pos_cart table with the new structure
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'item_option_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'price' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'special_discount' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'coupon' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'item' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
            'item_specialDisc' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'item_coupon' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'employee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('pos_cart');
    }

    public function down()
    {
        // Drop the pos_cart table
        $this->forge->dropTable('pos_cart', true);
    }
}