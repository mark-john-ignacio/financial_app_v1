<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsertFilterByCompanyCode'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeUpdateFilterByCompanyCode'];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['beforeFindFilterByCompanyCode'];
    protected $afterFind      = [];
    protected $beforeDelete   = ['beforeDeleteFilterByCompanyCode'];
    protected $afterDelete    = [];

    public function beforeFindFilterByCompanyCode(){
        $company_code = session()->get('current_company')->company_code;
        $this->where('compcode', $company_code);
    }

    public function beforeInsertFilterByCompanyCode($data){
        $company_code = session()->get('current_company')->company_code;
        $data['compcode'] = $company_code;
        return $data;
    }

    public function beforeUpdateFilterByCompanyCode($data){
        $company_code = session()->get('current_company')->company_code;
        $data['compcode'] = $company_code;
        return $data;
    }

    public function beforeDeleteFilterByCompanyCode(){
        $company_code = session()->get('current_company')->company_code;
        $this->where('compcode', $company_code);
    }
}
