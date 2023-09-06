<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriAmanah extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_amanah';
    protected $fillable = [
        'id_admin',
        'amanah',
    ];
}
