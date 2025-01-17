<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    /** @use HasFactory<\Database\Factories\SalesOrderFactory> */
    use HasFactory;

    protected $table = 'so';

    public function sales_order_items():hasMany
    {
        return $this->hasMany(SalesOrderItem::class, "ctranno", "ctranno");
    }

    public function delivery_receipt_items():hasMany
    {
        return $this->hasMany(DeliveryReceiptItems::class, "creference", "ctranno");
    }
}
