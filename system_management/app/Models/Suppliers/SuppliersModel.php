<?php

namespace App\Models\Suppliers;

use App\Models\BaseModel;

class SuppliersModel extends BaseModel
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'nid';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\Suppliers\SuppliersEntity';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "compcode",
        "ccode",
        "cname",
        "ctradename",
        "ctin",
        "chouseno",
        "ccity",
        "cstate",
        "ccountry",
        "czip",
        "csuppliertype",
        "csupplierclass",
        "cacctcode",

    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

}
