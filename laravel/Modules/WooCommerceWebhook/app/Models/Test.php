<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\WooCommerceWebhook\Database\Factories\TestFactory;

class Test extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TestFactory
    // {
    //     // return TestFactory::new();
    // }
}
