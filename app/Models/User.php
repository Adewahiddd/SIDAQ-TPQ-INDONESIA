<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id_user';
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_admin',
        'id_ustadz',
        'id_santri',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return $this->role && in_array($this->role, $roles);
        }

        return $this->role && $this->role == $roles;
    }


    public function profileSantri()
    {
        return $this->hasOne(ProfileSantri::class, 'id_user', 'id_user');
    }

    public function amalSholehs()
    {
        return $this->hasMany(AmalSholeh::class, 'id_santri');
    }

    public function kemampuans()
    {
        return $this->hasMany(Kemampuan::class);
    }

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    public function hafalans()
    {
        return $this->hasMany(Hafalan::class);
    }









}
