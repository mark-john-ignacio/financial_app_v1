<?php

namespace App\Models\API\BIRForms;

use CodeIgniter\Model;
use App\Entities\API\BIRForms\SalesEntity;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SalesEntity::class;

    public function getSalesPerMonth($month, $year, $company) {
        $this->select('SUM(ngross) as total_sales');
        $this->where('MONTH(ddate)', $month);
        $this->where('YEAR(ddate)', $year);
        $this->where('compcode', $company);
        $this->groupBy('MONTH(ddate)');
        $this->groupBy('YEAR(ddate)');
        return $this->get()->getRow();
    }
}

