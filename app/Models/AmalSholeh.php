<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmalSholeh extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_hafalan';
    protected $fillable = [
        'id_santri',
        'id_ustadz',
        'hafalan',
        'mutqin',
        'gambar',
        'fundraising',
        'amanah',
        'kedisiplinan',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri');
    }



}
