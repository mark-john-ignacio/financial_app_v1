<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBIRYearFormRegistration extends Migration
{
    public function up()
    {
        // Check if the table exists
        if (!$this->db->tableExists('bir_year_form_registration')) {
            // Define the table fields
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'year_id' => [
                    'type' => 'INT',
                ],
                'form_id' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                ],
            ]);
    
            // Add primary key
            $this->forge->addKey('id', true);
    
            // Create the table
            $this->forge->createTable('bir_year_form_registration');
    
            // Run the seeder
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\BIRYearFormSeeder');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('bir_year_form_registration')) {
            // Drop the table if it exists
            $this->forge->dropTable('bir_year_form_registration');
        }
    }
}
