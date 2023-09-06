<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriAbsensi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_categoriabsen';
    protected $fillable = [
        'id_admin',
        'kategori',
    ];
}
