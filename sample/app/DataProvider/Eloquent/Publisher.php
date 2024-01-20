<?php

namespace App\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Model;

class publisher extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];
}
