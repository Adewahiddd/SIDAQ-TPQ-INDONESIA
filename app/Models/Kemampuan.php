<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kemampuan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_kemampuan';
    protected $fillable = [
        'id_ustadz',
        'id_santri',
        'khidmat',
        'leadership',
        'speaking',
        'operation',
        'mengajar',
        'admiristation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ustadz');
    }


}
