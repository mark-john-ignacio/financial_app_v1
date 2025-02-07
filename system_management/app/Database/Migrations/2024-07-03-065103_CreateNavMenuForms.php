<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNavMenuForms extends Migration
{
    public function up()
    {
        if(!$this->db->tableExists("nav_menu_forms")) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'form_code' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'form_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'filter' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'cstatus' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->createTable('nav_menu_forms');
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\NavMenuFormsSeeder');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('nav_menu_forms')) {
            // Drop the table if it exists
            $this->forge->dropTable('nav_menu_forms');
        }
    }
}
