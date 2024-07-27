<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlantClassificationOnTheGroupingsTable extends Migration
{
    public function up()
    {
        $data = [
            [
                'compcode' => '001',
                'ccode' => 'CACTUS',
                'cdesc' => 'CACTUS',
                'ctype' => 'ITEMCLS',
            ],
            [   
                'compcode' => '002',
                'ccode' => 'CACTUS',
                'cdesc' => 'CACTUS',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'SUCCULENT',
                'cdesc' => 'SUCCULENT',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'SUCCULENT',
                'cdesc' => 'SUCCULENT',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'HAWORTHIA',
                'cdesc' => 'HAWORTHIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'HAWORTHIA',
                'cdesc' => 'HAWORTHIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'GASTERIA',
                'cdesc' => 'GASTERIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'GASTERIA',
                'cdesc' => 'GASTERIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'ALOE',
                'cdesc' => 'ALOE',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'ALOE',
                'cdesc' => 'ALOE',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'SANSEVERIA',
                'cdesc' => 'SANSEVERIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'SANSEVERIA',
                'cdesc' => 'SANSEVERIA',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '001',
                'ccode' => 'AGAVE',
                'cdesc' => 'AGAVE',
                'ctype' => 'ITEMCLS',
            ],
            [
                'compcode' => '002',
                'ccode' => 'AGAVE',
                'cdesc' => 'AGAVE',
                'ctype' => 'ITEMCLS',
            ],
        
        ];
        $this->db->table('groupings')->insertBatch($data);
                
    }

    public function down()
    {
        $this->db->table('groupings')->delete(['ccode' => 'CACTUS']);
        $this->db->table('groupings')->delete(['ccode' => 'SUCCULENT']);
        $this->db->table('groupings')->delete(['ccode' => 'HAWORTHIA']);
        $this->db->table('groupings')->delete(['ccode' => 'GASTERIA']);
        $this->db->table('groupings')->delete(['ccode' => 'ALOE']);
        $this->db->table('groupings')->delete(['ccode' => 'SANSEVERIA']);
        $this->db->table('groupings')->delete(['ccode' => 'AGAVE']);
    }
}
