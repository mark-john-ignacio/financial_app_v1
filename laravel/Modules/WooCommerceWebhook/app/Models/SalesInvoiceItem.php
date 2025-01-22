<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceItem extends Model
{
    /** @use HasFactory<\Database\Factories\SalesInvoiceItemsFactory> */
    use HasFactory;

    protected $table = "sales_t";

    protected $fillable = [
        "compcode",
        "cidentity",
        "ctranno",
        "creference",
        "nrefident",
        "nident",
        "citemno",
        "nqty",
        "nqtyreturned",
        "cunit",
        "nprice",
        "ndiscount",
        "namount",
        "nbaseamount",
        "nnetvat",
        "nlessvat",
        "cmainunit",
        "nfactor",
        "cacctcode",
        "ctaxcode",
        "nrate",
        "cewtcode",
        "newtrate",
    ];

    public function sales_invoice():belongsTo
    {
        return $this->belongsTo(SalesInvoice::class, "ctranno", "ctranno");
    }

    public function delivery_receipt():belongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, "creference", "ctranno");
    }

}
