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
        "ngross",
        "dcutdate",
        "dpodate",
        "cpono",
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
        $query = $this->where('YEAR(ddate)', 'YEAR(CURDATE())')
            ->orderBy('ctranno', 'desc')
            ->first();

        if ($query->getNumRows() == 0) {
            $cSINo = "SO" . $dmonth . $dyear . "00000";
        } else {
            $row = $query->getRow();
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
