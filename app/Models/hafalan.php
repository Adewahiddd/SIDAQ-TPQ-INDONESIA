<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hafalan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_hafalan';

    protected $fillable = [
        'tanggal',
        'nama_santri',
        'surah',
        'jumlah_ayat',
        'nilai',
    ];
}
