<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedUpdatedDeletedToCustomersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('customers',
        [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'cdefaultcurrency'
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
        $this->forge->dropColumn('customers', 'created_at');
        $this->forge->dropColumn('customers', 'created_by');
        $this->forge->dropColumn('customers', 'updated_at');
        $this->forge->dropColumn('customers', 'updated_by');
        $this->forge->dropColumn('customers', 'deleted_at');
        $this->forge->dropColumn('customers', 'deleted_by');
        $this->forge->dropColumn('customers', 'deleted');
    }
}
