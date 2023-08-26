<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\User;
use App\Models\guru;
class santri extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_santri';
    protected $table = 'santris';
    protected $fillable = [
        'id_ust', 'nama', 'gambar', 'email', 'password', 'tgl_lahir','ustadz', 'amanah',
        'kedisiplinan', 'hafalans', 'mutqin','fundraising', 'alpha', 'sakit',
        'izin', 'tahajjud', 'odoj', 'stw', 'majelis', 'khidmat', 'leadership',
        'enterpreneur', 'speaking', 'operation', 'mengajar', 'administration',
        'hafalan', 'tanggal', 'image', 'entrepreneur', //26
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function guru()
    {
        return $this->belongsTo(guru::class);
    }

    public function pondok()
    {
        return $this->belongsTo(User::class);
    }


}
