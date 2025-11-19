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
            'email' => 'admin@unjani.ac.id',
            'password' => Hash::make('password'), // ubah kalau mau
            'is_verified' => true,
        ]);
        $admin->assignRole('admin');

        // 2. pimpinan
        $pimpinan = User::firstOrCreate([
            'name' => 'Pimpinan Satu',
            'email' => 'pimpinan@unjani.ac.id',
            'password' => Hash::make('password'),
            'is_verified' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        // 3. mahasiswa
        $mahasiswa = User::firstOrCreate([
            'name' => 'Mahasiswa Satu',
            'email' => 'mahasiswa@unjani.ac.id',
            'password' => Hash::make('password'),
            'is_verified' => true,
        ]);
        $mahasiswa->assignRole('mahasiswa');
        $mahasiswa2 = User::firstOrCreate([
            'name' => 'Mahasiswa Dua',
            'email' => 'mahasiswa2@unjani.ac.id',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa2->assignRole('mahasiswa');

        $mahasiswa3 = User::firstOrCreate([
            'name' => 'Mahasiswa Tiga',
            'email' => 'mahasiswa3@unjani.ac.id',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa3->assignRole('mahasiswa');
    }
}
