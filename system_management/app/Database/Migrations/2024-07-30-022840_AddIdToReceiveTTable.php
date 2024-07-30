<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToReceiveTTable extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE receive_t DROP PRIMARY KEY');

        $this->db->query('ALTER TABLE receive_t ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE receive_t DROP COLUMN id');

        $this->db->query('ALTER TABLE receive_t ADD PRIMARY KEY(compcode, cidentity)');
    }
}
