<?php

namespace App\Entities\Items;

use CodeIgniter\Entity\Entity;

class ItemsEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
