<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToCustomersTable extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE customers DROP PRIMARY KEY');

        $this->db->query('ALTER TABLE customers ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    public function down()
    {
        $columnExists = $this->db->query("SELECT COUNT(*) AS count 
                                          FROM information_schema.columns 
                                          WHERE table_name = 'customers' 
                                          AND column_name = 'id'")
                                 ->getRow()
                                 ->count;

        if ($columnExists > 0) {
            // Drop the 'id' column if it exists
            $this->db->query('ALTER TABLE customers DROP COLUMN id');
        }

        $this->db->query('ALTER TABLE customers ADD PRIMARY KEY(compcode, cempid)');
    }
}
