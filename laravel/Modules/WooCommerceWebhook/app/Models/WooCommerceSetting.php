<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\WooCommerceWebhook\Database\Factories\WooCommerceSettingFactory;

class WooCommerceSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];

    protected $table = 'woocommerce_settings';

    // protected static function newFactory(): WooCommerceSettingFactory
    // {
    //     // return WooCommerceSettingFactory::new();
    // }
}
