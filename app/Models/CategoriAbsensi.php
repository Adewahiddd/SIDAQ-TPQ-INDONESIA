<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriAbsensi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_categoriabsen';
    protected $table = 'categori_absensis';
    protected $fillable = [
        'id_admin',
        'name_kategori',
    ];
}
