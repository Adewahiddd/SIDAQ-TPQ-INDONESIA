<?php

namespace Database\Seeders;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role; // Import model Role

class AdminPusat extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            [
                'nama_masjid' => 'Masjid A',
                'gambar' => 'image1.jpg',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'provinsi' => 'Provinsi A',
                'kabupaten' => 'Kabupaten A',
                'alamat_masjid' => 'Alamat A',
                'verifikasi' => true,
            ],
            // Tambahkan data user lainnya di sini
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            if ($user->email === 'admin@gmail.com') {
                $adminRole = Role::where('name_masjid', 'admin_pusat')->first(); // Gunakan model Role
                if ($adminRole) {
                    $user->roles()->attach($adminRole);
                }
            }
        }
    }
}
