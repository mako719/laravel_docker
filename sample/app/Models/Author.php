<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\Cast\String_;

class Author extends Model
{
    use SoftDeletes;

    public function books()
    {
        return $this->hasMany('\App\Models\Book');
    }

    public function getKanaAttribute(String $value): String
    {
        // KANAカラムの値を半角カナに変換
        return mb_convert_kana($value, "k");
    }

    public function setKanaAttribute(String $value): Void
    {
        // KANAカラムの値を全角カナに変換
        $this->attributes['kana'] = mb_convert_kana($value, "KV");
    }
}
