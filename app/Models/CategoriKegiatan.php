<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriKegiatan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kegiatan';
    protected $table = 'categori_kegiatans';
    protected $fillable = [
        'id_admin',
        'name_kegiatan',
    ];
}
