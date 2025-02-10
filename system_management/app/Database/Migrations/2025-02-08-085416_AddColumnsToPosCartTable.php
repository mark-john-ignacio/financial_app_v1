<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToPosCartTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pos_cart', [
            'item' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'status'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'item'
            ],
            'item_specialDisc' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'quantity'
            ],
            'item_coupon' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'item_specialDisc'
            ],
            'employee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'item_coupon'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pos_cart', 'item');
        $this->forge->dropColumn('pos_cart', 'quantity');
        $this->forge->dropColumn('pos_cart', 'item_specialDisc');
        $this->forge->dropColumn('pos_cart', 'item_coupon');
        $this->forge->dropColumn('pos_cart', 'employee_name');
    }
}
