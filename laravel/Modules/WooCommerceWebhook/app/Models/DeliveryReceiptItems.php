<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReceiptItems extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptItemsFactory> */
    use HasFactory;

    protected $table = "dr_t";

    public function delivery_receipt():belongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, "ctranno", "ctranno");
    }

    public function sales_order():belongsTo
    {
        return $this->belongsTo(SalesOrder::class, "creference", "ctranno");
    }
}
