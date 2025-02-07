<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerNameAddrTinToPosTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pos', [
            'cust_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'payment_reference'
            ],
            'cust_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'cust_name'
            ],
            'cust_tin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'cust_address'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pos', 'cust_name');
        $this->forge->dropColumn('pos', 'cust_address');
        $this->forge->dropColumn('pos', 'cust_tin');
    }
}
