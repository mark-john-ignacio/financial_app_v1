<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItems extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptItemsFactory> */
    use HasFactory;

    protected $table = "dr_t";
}
