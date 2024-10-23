<?php

namespace App\Entities\Account;

use CodeIgniter\Entity\Entity;

class AccountEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
