<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\User;

class guru extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_ust';
    protected $table = 'gurus';
    protected $fillable = ['nama', 'gambar', 'email', 'password', 'tgl_lahir','role'];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user'); // Hubungkan ke model User dengan foreign key id_user
    }

    public function santris()
    {
        return $this->hasMany(santri::class);
    }


}


