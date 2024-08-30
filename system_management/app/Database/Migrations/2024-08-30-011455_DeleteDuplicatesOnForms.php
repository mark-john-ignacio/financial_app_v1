<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteDuplicatesOnForms extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('nav_menu_forms')) {
            $this->db->query("
            DELETE FROM nav_menu_forms
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM nav_menu_forms
                GROUP BY form_code
            )
            ");
        }
    }

    public function down()
    {
        // No down function
    }
}
