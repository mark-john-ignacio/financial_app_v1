<?php

namespace App\Entities\Items;

use CodeIgniter\Entity\Entity;

class ItemsEntity extends Entity
{
    protected $datamap = [
        'id' => 'nid',
        'item_code' => 'cpartno',
        'item_description' => 'citemdesc',
        'unit_of_measure' => 'cunit',
        'notes' => 'cnotes',
        'class' => 'cclass',
        'type' => 'ctype',
        'sku' => 'cskucode',
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
