<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWoocommerceLandingOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'compcode' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'json_data' => [
                    'type' => 'TEXT',
                ],
                "status" => [
                    'type' => 'ENUM',
                    'constraint' => ['pending', 'approved', 'rejected'],
                    'default' => 'pending',
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
        $this->forge->createTable('woocommerce_landing_orders');
    }

    public function down()
    {
        $this->forge->dropTable('woocommerce_landing_orders');
    }
}
