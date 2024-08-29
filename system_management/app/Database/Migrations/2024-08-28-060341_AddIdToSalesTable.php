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
        $forge = \Config\Database::forge();

        if ($this->db->fieldExists('id', 'sales')) {
            // Drop the 'id' column if it exists
            $forge->dropColumn('sales', 'id');
        }
        $this->db->query('ALTER TABLE sales ADD PRIMARY KEY(compcode, ctranno)');
    }
}
