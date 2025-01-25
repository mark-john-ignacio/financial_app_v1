<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\WooCommerceWebhook\Database\Factories\ItemFactory;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }

    public function woocommerce_product_mapping()
    {
        return $this->hasOne(WooCommerceProductMapping::class, 'myxfin_product_id', 'nid');
    }
    public function getIdAttribute()
    {
        return $this->attributes['nid'];
    }
}
