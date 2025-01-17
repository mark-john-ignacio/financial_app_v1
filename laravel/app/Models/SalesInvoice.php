<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    /** @use HasFactory<\Database\Factories\SalesInvoiceFactory> */
    use HasFactory;

    protected $table = "sales";
}
