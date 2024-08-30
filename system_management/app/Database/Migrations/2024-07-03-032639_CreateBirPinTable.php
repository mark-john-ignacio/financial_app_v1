<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBirPinTable extends Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();
    
        // Check if the table exists
        if (!$this->db->tableExists('bir_pin')) {
            // Define the table fields
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'pin' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
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
    
            // Add primary key
            $this->forge->addKey('id', true);
    
            // Create the table
            $this->forge->createTable('bir_pin');
    
            // Run the seeder
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\BirPinSeeder');
        }
    }

    public function down()
    {
        // Check if the table exists
        if ($this->db->tableExists('bir_pin')) {
            // Drop the table if it exists
            $this->forge->dropTable('bir_pin');
        }
    }
}
