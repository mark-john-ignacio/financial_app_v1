<?php

namespace App\Models\API\BIRForms;

use CodeIgniter\Model;

class BirFormsModel extends Model
{
    public function getCompanyInfo($companyId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('company');
        $query = $builder->where('compcode', $companyId)->get();
        return $query->getRowArray();
    }

    public function getApvData($companyId, $year, $quarter)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('apv_t a');
        $builder->select('SUM(a.ncredit-a.ndebit) as ncredit, a.cewtcode, a.newtrate');
        $builder->join('apv b', 'a.compcode = b.compcode AND a.ctranno = b.ctranno', 'left');
        $builder->join('suppliers c', 'b.compcode = c.compcode AND b.ccode = c.ccode', 'left');
        $builder->join('groupings d', 'c.compcode = d.compcode AND c.csuppliertype = d.ccode AND d.ctype = "SUPTYP"', 'left');
        $builder->where('a.compcode', $companyId);
        $builder->whereIn('MONTH(b.dapvdate)', $this->getMonthsForQuarter($quarter));
        $builder->where('YEAR(b.dapvdate)', $year);
        $builder->where('b.lapproved', 1);
        $builder->where('b.lvoid', 0);
        $builder->where('b.lcancelled', 0);
        $builder->where('a.cacctno', $this->getEwtPayDefault($companyId));
        $builder->where('IFNULL(a.cewtcode,"")', '', '!=');
        $builder->groupBy('a.cewtcode, a.newtrate');
        $builder->orderBy('a.cewtcode');
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getDefaultAccounts($companyId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('accounts_default a');
        $builder->select('a.ccode, a.cacctno, b.cacctdesc');
        $builder->join('accounts b', 'a.compcode = b.compcode AND a.cacctno = b.cacctid', 'left');
        $builder->where('a.compcode', $companyId);
        $builder->whereIn('a.ccode', ['EWTPAY', 'PURCH_VAT']);
        $query = $builder->get();
        return $query->getResultArray();
    }

    private function getMonthsForQuarter($quarter)
    {
        $quarters = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12]
        ];
        return $quarters[$quarter] ?? [];
    }

    private function getEwtPayDefault($companyId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('accounts_default');
        $builder->select('cacctno');
        $builder->where('compcode', $companyId);
        $builder->where('ccode', 'EWTPAY');
        $query = $builder->get();
        $result = $query->getRowArray();
        return $result ? $result['cacctno'] : '';
    }
}