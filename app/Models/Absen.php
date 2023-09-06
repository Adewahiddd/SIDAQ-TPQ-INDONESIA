<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_absen';
    protected $fillable = [
        'id_ustadz',
        'id_santri',
        'waktu',
        'hadir',
        'alpha',
        'izin',
        'sakit',
    ];

}
