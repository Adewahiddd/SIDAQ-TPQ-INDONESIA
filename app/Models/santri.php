<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\guru;
class santri extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_santri';
    protected $table = 'santri';
    protected $fillable = [
        'nama', 'gambar', 'email', 'password', 'tgl_lahir','ustadz', 'amanah',
        'kedisiplinan', 'hafalan', 'mutqin','fundraising', 'alpha', 'sakit',
        'izin', 'tahajjud', 'odoj', 'stw', 'majelis', 'khidmat', 'leadership',
        'enterpreneur', 'speaking', 'operation', 'mengajar', 'administation',
        'hafalan' //26
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
