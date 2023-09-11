<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_absen';
    protected $table = 'absens';
    protected $fillable = [
        'id_ustadz',
        'id_santri',
        'name_kegiatan', //waktunya
        'name_kategori', // keterangannya
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_santri');
    }

}
