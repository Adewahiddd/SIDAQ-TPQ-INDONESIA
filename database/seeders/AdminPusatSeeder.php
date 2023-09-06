<?php

namespace Database\Seeders;

use App\Models\ProfileSantri;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminPusatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $maxIdAdminPondok = User::max('id_admin') ?? 0;
        $newIdAdmin = $maxIdAdminPondok + 1;

        $adminPusat = User::create([
            'id_admin' => $newIdAdmin,
            'name' => 'ismuhu yahya',
            'email' => 'adminpusat@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin_pusat',
        ]);

        // Upload dan simpan gambar jika validasi berhasil
        $gambar = 'your_image.jpg'; // Ganti dengan nama file gambar yang valid
        $gambarPath = 'images/poto-masjid/' . $adminPusat->getKey() . '.' . pathinfo($gambar, PATHINFO_EXTENSION);

        // Simpan path gambar di tabel profile_santris
        $profileSantri = ProfileSantri::create([
            'id_user' => $adminPusat->getKey(),
            'id_admin' => $newIdAdmin,
            'gambar' => $gambarPath,
            'provinsi' => 'kalimantan',
            'kabupaten' => 'kalimantan',
            'alamat_masjid' => 'kalimantan parit 3',
            // 'verifikasi' => 1,
        ]);
    }
}
