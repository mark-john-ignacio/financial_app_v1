<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PosAdjustment extends Migration
{
    public function up()
    {
        // Add columns to pos table
        $this->forge->addColumn('pos', [
            'coupon' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'exchange'
            ],
            'serviceFee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'coupon'
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'serviceFee'
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'subtotal'
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'payment_method'
            ]
        ]);

        // Create pendingorder_status table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'tranno' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'payment_transaction' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'items' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'waiting_time' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'transaction_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'pstatus' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'order_adding' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'receipt' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pendingorder_status');

        // Create pos_system table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'compcode' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cserialno' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cmachine' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cpoweredname' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cpoweredadd' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cpoweredtin' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'caccredno' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'ddateissued' => [
                'type' => 'DATE'
            ],
            'deffectdate' => [
                'type' => 'DATE'
            ],
            'cptunum' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'dptuissued' => [
                'type' => 'DATE'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pos_system');

        // Seed pos_system data
        $data = [
            'compcode' => '001',
            'cserialno' => '0001',
            'cmachine' => '0001',
            'cpoweredname' => '0001',
            'cpoweredadd' => '0001',
            'cpoweredtin' => '0001',
            'caccredno' => '0001',
            'ddateissued' => '2025-02-05',
            'deffectdate' => '2025-02-05',
            'cptunum' => '0001',
            'dptuissued' => '2025-02-05',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pos_system')->insert($data);
    }

    public function down()
    {
        // Drop columns from pos table
        $this->forge->dropColumn('pos', 'coupon');
        $this->forge->dropColumn('pos', 'serviceFee');
        $this->forge->dropColumn('pos', 'subtotal');
        $this->forge->dropColumn('pos', 'payment_method');
        $this->forge->dropColumn('pos', 'payment_reference');

        // Drop tables
        $this->forge->dropTable('pendingorder_status');
        $this->forge->dropTable('pos_system');
    }
}