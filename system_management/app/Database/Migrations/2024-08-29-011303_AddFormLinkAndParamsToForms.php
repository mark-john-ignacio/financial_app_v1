<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFormLinkAndParamsToForms extends Migration
{
    public function up()
    {
        // Check if the 'form_link' column exists
        if (!$this->db->fieldExists('form_link', 'nav_menu_forms')) {
            // Add 'form_link' and 'params' columns
            $this->forge->addColumn('nav_menu_forms', [
                'form_link' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'form_name'
                ],
                'params' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'form_link'
                ]
            ]);

                    // Insert new rows
            $data = [
                [
                    "form_code" => "2550M",
                    "form_name" => "Monthly Value-Added Tax Declaration",
                    "form_link" => "bir2550m",
                    "params" => "bir2550m_param",
                    "filter" => "Monthly",
                    "cstatus" => "Active"
                ],
                [
                    "form_code" => "2550Q",
                    "form_name" => "Quarterly Value-Added Tax Return",
                    "form_link" => "bir2550q",
                    "params" => "bir2550q_param",
                    "filter" => "Quarterly",
                    "cstatus" => "Active"
                ],
                [
                    "form_code" => "1601Q",
                    "form_name" => "Quarterly Remittance Return of Creditable Income Taxes Withheld (Expanded)",
                    "form_link" => "bir1601q",
                    "params" => "bir1601q_param",
                    "filter" => "Quarterly",
                    "cstatus" => "Active"
                ]
            ];
            $this->db->table('nav_menu_forms')->insertBatch($data);
        }
    
        // Update existing entry
        $this->db->table('nav_menu_forms')->where('form_code', '1601E')->update([
            'form_link' => 'bir0619e',
            'params' => 'bir0619e_param'
        ]);
    

    }
    
    public function down()
    {
        if ($this->db->tableExists('nav_menu_forms')) {
            // Drop the 'form_link' and 'params' columns if they exist
            if ($this->db->fieldExists('form_link', 'nav_menu_forms')) {
                $this->forge->dropColumn('nav_menu_forms', 'form_link');
            }
            if ($this->db->fieldExists('params', 'nav_menu_forms')) {
                $this->forge->dropColumn('nav_menu_forms', 'params');
            }
        }
    }
}
