<?php

namespace App\Entities\NavMenus;

use CodeIgniter\Entity\Entity;

class NavMenuEntity extends Entity
{
    protected $datamap = [
        "status" => "cstatus",
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
