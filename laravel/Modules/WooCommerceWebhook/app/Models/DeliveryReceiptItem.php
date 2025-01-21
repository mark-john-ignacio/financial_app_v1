<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReceiptItem extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptItemsFactory> */
    use HasFactory;

    protected $table = "dr_t";

    protected $fillable = [
        'compcode',
        'cidentity',
        'nident',
        'ctranno',
        'creference',
        'crefident',
        'citemno',
        'nqtyorig',
        'nqty',
        'nqtyscan',
        'cunit',
        'nprice',
        'namount',
        'nbaseamount',
        'cmainunit',
        'nfactor',
        'nbase',
        'ndisc',
        'nnet',
        'cacctcode',
    ];

    public function delivery_receipt():belongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, "ctranno", "ctranno");
    }

    public function sales_order():belongsTo
    {
        return $this->belongsTo(SalesOrder::class, "creference", "ctranno");
    }
}
