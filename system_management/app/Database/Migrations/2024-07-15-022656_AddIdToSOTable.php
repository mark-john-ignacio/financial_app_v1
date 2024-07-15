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
        // Remove id field from banks table
        $this->db->query('ALTER TABLE so DROP COLUMN id');

        // Restore primary key of banks table
        $this->db->query('ALTER TABLE so ADD PRIMARY KEY(compcode, ctranno)');
    
    }
}
