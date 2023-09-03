<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'tanggal',
        'surah',
        'jumlah_ayat',
        'nilai',
    ];

}
