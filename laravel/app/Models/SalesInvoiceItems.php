<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceItems extends Model
{
    /** @use HasFactory<\Database\Factories\SalesInvoiceItemsFactory> */
    use HasFactory;

    protected $table = "sales_t";

    public function sales_invoice():belongsTo
    {
        return $this->belongsTo(SalesInvoice::class, "ctranno", "ctranno");
    }

}
