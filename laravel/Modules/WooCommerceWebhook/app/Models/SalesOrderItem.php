<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\SalesOrderItemFactory> */
    use HasFactory;

    protected $table = 'so_t';

    protected $fillable = [
        'compcode',
        'cidentity',
        'ctranno',
        'creference',
        'nident',
        'nrefident',
        'citemno',
        'nqty',
        'cunit',
        'nprice',
        'namount',
        'nbaseamount',
        'cmainunit',
        'nfactor',
        'nbase',
        'ndisc',
        'nnet',
        'ctaxcode',
        'nrate',
        'citemremarks'
        ];

    public function sales_order():BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, "ctranno", "ctranno");
    }

    public function item():BelongsTo
    {
        return $this->belongsTo(Item::class, "citemno", "cpartno");
    }
}
