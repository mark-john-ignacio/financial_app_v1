<?php

namespace App\Models\WooCommerceOrderSync;

use CodeIgniter\Model;

class SalesOrderItemsModel extends Model
{
    protected $table            = 'so_t';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "compcode",
        "cidentity",
        "ctranno",
        "creference",
        "nident",
        "nrefident",
        "citemno",
        "nqty",
        "cunit",
        'nprice',
        'namount',
        'nbaseamount',
        'cmainunit',
        'nfactor',
        'nbase',
        'ndisc',
        'nnet',
        'ctaxcode',
        'nrate',
        
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
