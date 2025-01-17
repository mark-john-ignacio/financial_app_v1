<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryReceipt extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptFactory> */
    use HasFactory;

    protected $table = "dr";

    public function delivery_receipt_items():hasMany
    {
        return $this->hasMany(DeliveryReceiptItems::class, "ctranno", "ctranno");
    }
}
