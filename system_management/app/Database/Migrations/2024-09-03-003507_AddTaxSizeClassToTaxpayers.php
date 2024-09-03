<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaxSizeClassToTaxpayers extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('taxpayer_size_class', 'company')) {
            $this->forge->addColumn('company', [
                'taxpayer_size_class' => [
                    'type'    => 'ENUM',
                    'constraint' => ['Micro', 'Small', 'Medium', 'Large'],
                    'null'    => false,
                    'default' => 'Micro',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('taxpayer_size_class', 'company')) {
            $this->forge->dropColumn('company', 'taxpayer_size_class');
        }
    }
}
