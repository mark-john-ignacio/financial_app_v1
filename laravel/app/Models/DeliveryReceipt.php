<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptFactory> */
    use HasFactory;

    protected $table = "dr";
}
