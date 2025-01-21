<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    /** @use HasFactory<\Database\Factories\SalesOrderFactory> */
    use HasFactory;

    protected $table = 'so';
    protected $fillable = [
        'compcode', 'ctranno', 'ccode', 'ddate', 'dcutdate', 'dpodate', 'csalestype', 'cpono', 'ngross', 'nbasegross', 'ccurrencycode', 'ccurrencydesc', 'nexchangerate', 'cremarks', 'cspecins', 'cpreparedby', 'csalesman', 'cdelcode', 'cdeladdno', 'cdeladdcity', 'cdeladdstate', 'cdeladdcountry', 'cdeladdzip', 'lapproved', 'lvoid', 'lcancelled', 'lsent', 'lprintposted'
    ];

    public function sales_order_items():hasMany
    {
        return $this->hasMany(SalesOrderItem::class, "ctranno", "ctranno");
    }

    public function delivery_receipt_items():hasMany
    {
        return $this->hasMany(DeliveryReceiptItem::class, "creference", "ctranno");
    }
}
