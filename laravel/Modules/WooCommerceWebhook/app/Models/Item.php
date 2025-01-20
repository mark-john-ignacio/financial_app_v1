<?php

namespace Modules\WooCommerceWebhook\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $table = 'items';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    public function getIdAttribute()
    {
        return $this->attributes['nid'];
    }
}
