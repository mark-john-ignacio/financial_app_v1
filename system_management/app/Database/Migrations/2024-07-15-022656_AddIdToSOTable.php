<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToSOTable extends Migration
{
    public function up()
    {
        // Remove existing primary key from banks table
        $this->db->query('ALTER TABLE so DROP PRIMARY KEY');

        // Add id field to banks table and set it as the primary key
        $this->db->query('ALTER TABLE so ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

    }

    public function down()
    {
        $columnExists = $this->db->query("SELECT COUNT(*) AS count 
                                          FROM information_schema.columns 
                                          WHERE table_name = 'so' 
                                          AND column_name = 'id'")
                                 ->getRow()
                                 ->count;

        if ($columnExists > 0) {
            // Drop the 'id' column if it exists
            $this->db->query('ALTER TABLE so DROP COLUMN id');
        }

        // Restore primary key of banks table
        $this->db->query('ALTER TABLE so ADD PRIMARY KEY(compcode, ctranno)');
    
    }
}
