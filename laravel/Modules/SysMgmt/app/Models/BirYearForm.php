<?php

namespace Modules\SysMgmt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SysMgmt\Database\Factories\BirYearFormFactory;

class BirYearForm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_id',
        'form_id',
        'compcode'
    ];

    // protected static function newFactory(): BirYearFormFactory
    // {
    //     // return BirYearFormFactory::new();
    // }

    public function birForm()
    {
        return $this->belongsTo(BirForm::class, 'form_id');
    }
}
