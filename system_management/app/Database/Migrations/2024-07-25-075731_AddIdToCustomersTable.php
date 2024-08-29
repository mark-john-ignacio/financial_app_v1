<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToCustomersTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('id', 'customers')) {
            return;
        }
        $this->db->query('ALTER TABLE customers DROP PRIMARY KEY');

        $this->db->query('ALTER TABLE customers ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    public function down()
    {
        if ($this->db->fieldExists('id', 'customers')) {
            // Drop the 'id' column if it exists
            $this->forge->dropColumn('customers', 'id');
        }

        $this->db->query('ALTER TABLE customers ADD PRIMARY KEY(compcode, cempid)');
    }
}
