<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_code',
        'vat_number',
        'request_date',
        'valid',
        'name',
        'address',
    ];

    protected $hidden = ['deleted_at'];

    protected $table = 'vat';
}
