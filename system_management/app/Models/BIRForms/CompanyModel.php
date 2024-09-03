<?php

namespace App\Models\BIRForms;

use App\Models\BaseModel;
use App\Entities\BIRForms\CompanyEntity;

class CompanyModel extends BaseModel
{
    protected $table            = 'company';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = CompanyEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["reporting_period_type", "fiscal_month_start_end", "taxpayer_size_class"];

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
    protected $validationRules      = [
        'reporting_period_type' => 'required|in_list[fiscal,calendar]',
        'fiscal_month_start_end' => 'required|in_list[01,02,03,04,05,06,07,08,09,10,11,12]',
        'taxpayer_size_class'    => 'required|in_list[Micro,Small,Medium,Large]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
