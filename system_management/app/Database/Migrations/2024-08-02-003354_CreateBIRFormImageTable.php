<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBIRFormImageTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('bir_form_images')) {
            return;
        }
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'form_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'image' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('form_id', 'nav_menu_forms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bir_form_images');
    }

    public function down()
    {
        $this->forge->dropTable('bir_form_images');
    }
}
