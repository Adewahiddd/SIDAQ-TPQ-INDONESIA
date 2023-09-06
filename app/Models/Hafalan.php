<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_hafalan';
    protected $fillable = [
        'id_santri',
        'id_ustadz',
        'tanggal',
        'surah',
        'jumlah_ayat',
        'nilai',
    ];

    public function santri()
    {
        return $this->belongsTo(User::class, 'id_santri');
    }




    
}
