<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Rename1601ETo0619EBIRForm extends Migration
{
    public function up()
    {
        $data = [
            "form_link" => "bir0619e",
            "form_code" => "0619E",
            "params" => "bir0619e_param"
        ];
        $this->db->table('nav_menu_forms')->where('form_code', '1601E')->update($data);
    }

    public function down()
    {
        if (!$this->db->tableExists('nav_menu_forms')) {
            // Drop the table if it exists
            return;
        }
        $this->db->table('nav_menu_forms')->where('form_code', '0619E')->update(['form_code' => '1601E']);
    }
}
