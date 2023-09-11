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
        'id_admin',
        'id_ustadz',
        'id_santri',
        'gambar',
        'tgl_lahir',
        'gender',
        'angkatan',
        'name_divisi',
        'provinsi',
        'kabupaten',
        'alamat_masjid',
        'verifikasi',
        'idcard',
        'nomorwa',
        'status',
        'aktivitas',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

















}
