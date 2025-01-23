<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\WooCommerceWebhook\Database\Factories\WooCommerceAuditFactory;

class WooCommerceAudit extends Model
{
    use HasFactory;

    protected $table = 'woocommerce_audits';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'request_data',
        'response_data',
        'status',
        'error_message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    // protected static function newFactory(): WooCommerceAuditFactory
    // {
    //     // return WooCommerceAuditFactory::new();
    // }
}
