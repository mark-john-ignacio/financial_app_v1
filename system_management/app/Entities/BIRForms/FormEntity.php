<?php

namespace App\Entities\BIRForms;

use CodeIgniter\Entity\Entity;

class FormEntity extends Entity
{
    protected $datamap = [

    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
