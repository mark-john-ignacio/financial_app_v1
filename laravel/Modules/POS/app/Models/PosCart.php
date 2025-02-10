<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\POS\Database\Factories\PosCartFactory;

class PosCart extends Model
{
    use HasFactory;

    protected $table = 'pos_cart';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'item_id',
        'item_option_id',
        'qty',
        'price',
        'special_discount',
        'coupon',
        'status',
        'item',
        'quantity',
        'item_specialDisc',
        'item_coupon',
        'employee_name',
    ];

    

    protected static function newFactory(): PosCartFactory
    {
        return PosCartFactory::new();
    }
}
