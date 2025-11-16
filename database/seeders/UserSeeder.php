<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. admin
        $admin = User::firstOrCreate([
            'name' => 'Admin Satu',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'), // ubah kalau mau
        ]);
        $admin->assignRole('admin');

        // 2. pimpinan
        $pimpinan = User::firstOrCreate([
            'name' => 'Pimpinan Satu',
            'email' => 'pimpinan@test.com',
            'password' => Hash::make('password'),
        ]);
        $pimpinan->assignRole('pimpinan');

        // 3. mahasiswa
        $mahasiswa = User::firstOrCreate([
            'name' => 'Mahasiswa Satu',
            'email' => 'mahasiswa@test.com',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa->assignRole('mahasiswa');
        $mahasiswa2 = User::firstOrCreate([
            'name' => 'Mahasiswa Dua',
            'email' => 'mahasiswa2@test.com',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa2->assignRole('mahasiswa');

        $mahasiswa3 = User::firstOrCreate([
            'name' => 'Mahasiswa Tiga',
            'email' => 'mahasiswa3@test.com',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa3->assignRole('mahasiswa');
    }
}
