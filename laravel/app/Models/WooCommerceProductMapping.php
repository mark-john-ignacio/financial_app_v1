<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WooCommerceProductMapping extends Model
{
    /** @use HasFactory<\Database\Factories\WooCommerceProductMappingFactory> */
    use HasFactory;

    protected $table = 'woocommerce_product_mappings';
    protected $fillable = [
        'woocommerce_product_id',
        'myxfin_product_id'
    ];
}
