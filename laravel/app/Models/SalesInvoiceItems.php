<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItems extends Model
{
    /** @use HasFactory<\Database\Factories\SalesInvoiceItemsFactory> */
    use HasFactory;

    protected $table = "sales_t";
}
