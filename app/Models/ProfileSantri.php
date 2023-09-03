<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSantri extends Model
{
    use HasFactory;

    protected $table = 'profile_santris';
    protected $primaryKey = 'id_profile';

    protected $fillable = [
        'id_user',
        'gambar',
        'tgl_lahir',
        'gender',
        'angkatan',
        'provinsi',
        'kabupaten',
        'alamat_masjid',
        'verifikasi',
        'id_admin',
        'id_ustadz',
        'id_santri',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

















}
