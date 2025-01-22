<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    /** @use HasFactory<\Database\Factories\SalesInvoiceFactory> */
    use HasFactory;

    protected $table = "sales";

    protected $fillable = [
        'compcode',
        'ctranno',
        'ccode',
        'cremarks',
        'ddate',
        'dcutdate',
        'nexempt',
        'nzerorated',
        'nnet',
        'nvat',
        'newt',
        'cewtcode',
        'ngrossbefore',
        'ngrossdisc',
        'ngross',
        'nbasegross',
        'ntotaldiscounts',
        'ccurrencycode',
        'ccurrencydesc',
        'nexchangerate',
        'cpreparedby',
        'lapproved',
        'lvoid',
        'lcancelled',
        'cacctcode',
        'cvatcode',
        'ncreditbal',
        'npayed',
        'csalestype',
        'csiprintno',
        'creinvoice',
        'lstopreinvoice',
        'cterms',
        'cpaytype',
        "crefmodule",
        'crefmoduletran',
        'nordue',
    ];

    public function sales_invoice_items():hasMany
    {
        return $this->hasMany(SalesInvoiceItems::class, "ctranno", "ctranno");
    }

    public function delivery_receipt():belongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class, "crefmoduletran", "ctranno");
    }
}
