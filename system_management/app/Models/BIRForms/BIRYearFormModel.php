<?php

namespace App\Models\BIRForms;

use CodeIgniter\Model;

class BIRYearFormModel extends Model
{
    protected $table            = 'bir_year_form_registration';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'year_id',
        'form_id',
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

    public function getAssociations()
    {
        $thisTable = $this->table; 
    
        return $this->select('by.id, by.year, GROUP_CONCAT(nmf.form_code ORDER BY nmf.form_code SEPARATOR ", ") AS forms', false)
                    ->join('bir_year by', 'by.id = ' . $thisTable . '.year_id', 'inner')
                    ->join('nav_menu_forms nmf', 'nmf.id = ' . $thisTable . '.form_id', 'inner')
                    ->groupBy('by.id')
                    ->findAll();
    }

    public function getFormsByYear($year_id){
        return $this->select('form_id')
                    ->where('year_id', $year_id)
                    ->findAll();
    }
}
