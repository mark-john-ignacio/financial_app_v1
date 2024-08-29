<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToSalesTable extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE sales DROP PRIMARY KEY');

        $this->db->query('ALTER TABLE sales ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    public function down()
    {
        $columnExists = $this->db->query("SELECT COUNT(*) AS count 
                                          FROM information_schema.columns 
                                          WHERE table_name = 'sales' 
                                          AND column_name = 'id'")
                                 ->getRow()
                                 ->count;

        if ($columnExists > 0) {
            // Drop the 'id' column if it exists
            $this->db->query('ALTER TABLE sales DROP COLUMN id');
        }
        $this->db->query('ALTER TABLE sales ADD PRIMARY KEY(compcode, ctranno)');
    }
}
