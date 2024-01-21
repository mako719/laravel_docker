<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function detail()
    {
        return $this->hasOne('\App\Models\Bookdetail');
    }

    public function author()
    {
        return $this->belongsTo('\App\Models\author');
    }
}
