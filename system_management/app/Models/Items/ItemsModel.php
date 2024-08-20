<?php

namespace App\Models\Items;

use App\Models\BaseModel;

class ItemsModel extends BaseModel
{
    protected $table            = 'items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\Items\ItemsEntity';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cpartno',
        'citemdesc',
        'cunit',
        'cnotes',
        'cclass',
        'ctype',
        'cskucode',
        'ctradetype',
        'csalestype',
        'ctaxcode',
        'cpurchtaxcode',
        'cpricetype',
        'cacctcodesales',
        'cacctcoderet',
        'cacctcodewrr',
        'linventoriable',
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
