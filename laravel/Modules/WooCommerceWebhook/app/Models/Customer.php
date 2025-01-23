<?php

namespace Modules\WooCommerceWebhook\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\WooCommerceWebhook\Database\Factories\CustomerFactory;

class Customer extends Model
{
    use HasFactory;
     protected static function newFactory(): CustomerFactory
     {
          return CustomerFactory::new();
     }

    protected $fillable = [
      'compcode', 'cempid', 'cname', 'ctradename', 'chouseno', 'ccity', 'cstate', 'ccountry', 'czip', 'cacctcodesales',
        'cterms', 'cGroup1'
    ];
}
