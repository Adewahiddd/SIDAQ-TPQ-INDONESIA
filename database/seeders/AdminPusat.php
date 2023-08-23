<?php

namespace Database\Seeders;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AdminPusat extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $adminData = [
            'nama_masjid' => 'Admin Pusat',
            'gambar' => 'image1.jpg',
            'email' => 'adminpusat@gmail.com',
            'password' => Hash::make('12345678'),
            'provinsi' => 'Provinsi Pusat',
            'kabupaten' => 'Kabupaten Pusat',
            'alamat_masjid' => 'Alamat Pusat',
            'verifikasi' => true,
        ];

        $admin = User::create($adminData);
        $adminRole = Role::where('name', 'admin_pusat')->first();
        if ($adminRole) {
            $admin->roles()->attach($adminRole);
        }
    }

}
