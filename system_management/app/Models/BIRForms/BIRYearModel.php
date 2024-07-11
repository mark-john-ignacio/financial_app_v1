<?php

namespace App\Models\BIRForms;

use CodeIgniter\Model;

class BIRYearModel extends Model
{
    protected $table            = 'bir_year';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['year'];

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


    public function getYearsWithoutEntries()
    {
        $currentCompanyCode = session()->get('current_company')->company_code;

        $query = $this->db->table('bir_year')
                          ->select('bir_year.id, bir_year.year')
                          ->whereNotIn('bir_year.id', function($builder) use ($currentCompanyCode) {
                              return $builder->select('year_id')
                                             ->from('bir_year_form_registration')
                                             ->where('compcode', $currentCompanyCode);
                          })
                          ->orderBy('bir_year.year', 'ASC');

        return $query->get()->getResult();
    }
}
