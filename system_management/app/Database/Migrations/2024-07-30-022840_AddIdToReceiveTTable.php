<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToReceiveTTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('id', 'receive_t')) {
            return;
        }
        $this->db->query('ALTER TABLE receive_t DROP PRIMARY KEY');

        $this->db->query('ALTER TABLE receive_t ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    public function down()
    {
        $forge = \Config\Database::forge();

        if ($this->db->fieldExists('id', 'receive_t')) {
            // Drop the 'id' column if it exists
            $forge->dropColumn('receive_t', 'id');
        }

        $this->db->query('ALTER TABLE receive_t ADD PRIMARY KEY(compcode, cidentity)');
    }
}
