<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\WooCommerceWebhook\Database\Factories\WooCommerceProductMappingFactory;

class WooCommerceProductMapping extends Model
{
    use HasFactory;

    protected static function newFactory(): WooCommerceProductMappingFactory
    {
        return WooCommerceProductMappingFactory::new();
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'myxfin_product_id', 'nid');
    }

    protected $table = 'woocommerce_product_mappings';
    protected $fillable = [
        'woocommerce_product_id',
        'myxfin_product_id'
    ];
}
