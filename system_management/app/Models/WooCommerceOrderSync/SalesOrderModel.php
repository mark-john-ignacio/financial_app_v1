<?php

namespace App\Models\WooCommerceOrderSync;

use CodeIgniter\Model;

class SalesOrderModel extends Model
{
    protected $table            = 'so';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "compcode",
        "ctranno",
        "ccode",
        "ddate",
        "dcutdate",
        "dpodate",
        "csalestype",
        "cpono",
        "ngross",
        "nbasegross",
        "ccurrencycode",
        "ccurrencydesc",
        "nexchangerate",
        "cremarks",
        "cpreparedby",
        "csalesman",
        "cdelcode",
        "cdeladdno",
        "cdeladdcity",
        "cdeladdstate",
        "cdeladdcountry",
        "cdeladdzip",
        
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

    public function generateSONumber($company_code)
    {
        $dmonth = date("m");
        $dyear = date("y");
    
        // First, check if there are any records for the current year
        $count = $this->where('compcode', $company_code)
                      ->where('YEAR(ddate)', 'YEAR(CURDATE())', false)
                      ->countAllResults();
    
        if ($count == 0) {
            $cSINo = "SO" . $dmonth . $dyear . "00000";
        } else {
            // Since there are records, now fetch the latest one
            $row = $this->select('ctranno')
                        ->where('compcode', $company_code)
                        ->where('YEAR(ddate)', 'YEAR(CURDATE())', false)
                        ->orderBy('ctranno', 'desc')
                        ->first();
    
            $lastSI = $row->ctranno;
    
            if (substr($lastSI, 2, 2) != $dmonth) {
                $cSINo = "SO" . $dmonth . $dyear . "00000";
            } else {
                $baseno = intval(substr($lastSI, 6, 5)) + 1;
                $baseno = str_pad($baseno, 5, '0', STR_PAD_LEFT);
                $cSINo = "SO" . $dmonth . $dyear . $baseno;
            }
        }
    
        return $cSINo;
    }
}
