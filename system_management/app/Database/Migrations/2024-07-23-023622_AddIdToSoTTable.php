<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToSoTTable extends Migration
{
    public function up()
    {
        // Remove existing primary key from banks table
        $this->db->query('ALTER TABLE so_t DROP PRIMARY KEY');

        // Add id field to banks table and set it as the primary key
        $this->db->query('ALTER TABLE so_t ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

    }

    public function down()
    {
        if ($this->db->fieldExists('id', 'so_t')) {
            // Drop the 'id' column if it exists
            $this->forge->dropColumn('so_t', 'id');
        }
        // Restore primary key of banks table
        $this->db->query('ALTER TABLE so_t ADD PRIMARY KEY(compcode, cidentity)');
    
    }
}
