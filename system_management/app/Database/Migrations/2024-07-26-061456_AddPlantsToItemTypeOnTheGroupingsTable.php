<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlantsToItemTypeOnTheGroupingsTable extends Migration
{
    public function up()
    {
        $data = [
            [
                'compcode' => '001',
                'ccode' => 'PLANT',
                'cdesc' => 'PLANT',
                'ctype' => 'ITEMTYP',
            ],
            [
                'compcode' => '002',
                'ccode' => 'PLANT',
                'cdesc' => 'PLANT',
                'ctype' => 'ITEMTYP',
            ]
        ];
        $this->db->table('groupings')->insertBatch($data);
    }

    public function down()
    {
        $this->db->table('groupings')->delete(['ccode' => 'PLANT']);
    }
}
