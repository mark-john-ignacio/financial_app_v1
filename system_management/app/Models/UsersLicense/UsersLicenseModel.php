<?php

namespace App\Models\UsersLicense;

use CodeIgniter\Model;

class UsersLicenseModel extends Model
{
    protected $table            = 'users_license';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\UsersLicense\UsersLicenseEntity';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["value"];

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

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getLicensesWithCompany(){
        $this->select('users_license.id, users_license.value, users_license.compcode, company.compname as company_name');
        $this->join('company', 'company.compcode = users_license.compcode');
        return $this->findAll();
    }

    public function getLicense($id){
        $this->select('users_license.id, users_license.value, users_license.compcode, company.compname as company_name');
        $this->join('company', 'company.compcode = users_license.compcode');
        return $this->find($id);
    }
}
