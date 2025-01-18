<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    protected $fillable = [
      'compcode', 'cempid', 'cname', 'ctradename', 'chouseno', 'ccity', 'cstate', 'ccountry', 'czip', 'cacctcodesales',
        'cterms', 'cGroup1'
    ];
}
