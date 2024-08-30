<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdToCompanyTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('id', 'company')) {
            $this->db->query('ALTER TABLE company DROP PRIMARY KEY');

            $this->db->query('ALTER TABLE company ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('id', 'company')) {
            $this->forge->dropColumn('company', 'id');
            $this->db->query('ALTER TABLE company ADD PRIMARY KEY (compcode)');
        }
    }
}
