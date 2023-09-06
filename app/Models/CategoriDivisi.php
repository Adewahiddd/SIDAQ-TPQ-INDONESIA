<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriDivisi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_divisi';
    protected $fillable = [
        'id_admin',
        'divisi',
    ];
}
