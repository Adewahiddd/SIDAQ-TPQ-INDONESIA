<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'waktu',
        'hadir',
        'alpha',
        'izin',
        'sakit',
    ];

}
