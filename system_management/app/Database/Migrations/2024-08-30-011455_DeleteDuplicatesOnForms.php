<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteDuplicatesOnForms extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('nav_menu_forms')) {
            // Identify and remove duplicates
            $subQuery = $this->db->table('nav_menu_forms')
                ->select('MIN(id) as id')
                ->groupBy('form_code')
                ->getCompiledSelect();
    
            $this->db->table('nav_menu_forms')
                ->where("id NOT IN ($subQuery)", null, false)
                ->delete();
        }
    }

    public function down()
    {
        // No down function
    }
}
