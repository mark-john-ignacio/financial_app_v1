<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedUpdatedDeletedToItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('items',
        [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'cstatus'
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
        $this->forge->dropColumn('items', 'created_at');
        $this->forge->dropColumn('items', 'created_by');
        $this->forge->dropColumn('items', 'updated_at');
        $this->forge->dropColumn('items', 'updated_by');
        $this->forge->dropColumn('items', 'deleted_at');
        $this->forge->dropColumn('items', 'deleted_by');
        $this->forge->dropColumn('items', 'deleted');
    }
}
