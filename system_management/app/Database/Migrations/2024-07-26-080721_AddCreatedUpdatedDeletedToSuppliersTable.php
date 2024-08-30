<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedUpdatedDeletedToSuppliersTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('created_at', 'suppliers')) {
            return;
        }
        $this->forge->addColumn('suppliers',
        [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'procurement'
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'created_at'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_by'
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'updated_at'
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_by'
            ],
            'deleted_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'deleted_at'
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'deleted_by'
            ]
        ]);
    }

    public function down()
    {
        $forge = \Config\Database::forge();

        $columns = [
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'deleted_at',
            'deleted_by',
            'deleted'
        ];
    
        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'suppliers')) {
                $forge->dropColumn('suppliers', $column);
            }
        }
    }
}
