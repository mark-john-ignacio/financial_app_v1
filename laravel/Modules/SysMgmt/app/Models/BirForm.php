<?php

namespace Modules\SysMgmt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SysMgmt\Database\Factories\BirFormFactory;

class BirForm extends Model
{
    use HasFactory;
    protected $table = 'nav_menu_forms';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'form_code',
        'form_name',
        'filter',
        'cstatus',
        'form_link',
        'params'
    ];

    protected static function newFactory(): BirFormFactory
    {
        return BirFormFactory::new();
    }
    public function yearForms()
    {
        return $this->hasMany(BirYearForm::class, 'form_id');
    }

}
