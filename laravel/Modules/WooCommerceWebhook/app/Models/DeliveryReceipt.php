<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryReceipt extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryReceiptFactory> */
    use HasFactory;

    protected $table = "dr";

    protected $fillable = [
        'compcode',
        'ctranno',
        'ccode',
        'cremarks',
        'ddate',
        'dcutdate',
        'ngross',
        'nbasegross',
        'ccurrencycode',
        'ccurrencydesc',
        'nexchangerate',
        'cpreparedby',
        'cacctcode',
        'csalesman',
        'cdelcode',
        'cdeladdno',
        'cdeladdcity',
        'cdeladdstate',
        'cdeladdcountry',
        'cdeladdzip',
        'cterms',
    ];

    public function delivery_receipt_items():hasMany
    {
        return $this->hasMany(DeliveryReceiptItem::class, "ctranno", "ctranno");
    }

    public function sales_invoices():hasMany
    {
        return $this->hasMany(SalesInvoice::class, "crefmoduletran", "ctranno");
    }

    public function sales_invoice_items():hasMany
    {
        return $this->hasMany(SalesInvoiceItem::class, "creference", "ctranno");
    }
}
