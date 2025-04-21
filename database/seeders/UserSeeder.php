<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create(attributes: [
            'nama' => 'Admin',
            'angkatan' => 0, // Untuk admin, nilai ini bisa diabaikan
            'jurusan' => 'Admin',
            'password' => Hash::make('rahasia'), // Gantilah password sesuai kebutuhan
            'role' => 'admin',
        ]);
        User::create(attributes: [
            'nama' => 'valen',
            'angkatan' => 2018, // Untuk admin, nilai ini bisa diabaikan
            'jurusan' => 'rpl 2',
            'password' => Hash::make('rahasia'), // Gantilah password sesuai kebutuhan
            'role' => 'bendahara',
        ]);
        User::create([
            'nama' => 'Valen Urianto Wijaya',
            'angkatan' => 2018, // Untuk admin, nilai ini bisa diabaikan
            'jurusan' => 'rpl 2',
            'password' => Hash::make('rahasia'), // Gantilah password sesuai kebutuhan
            'role' => 'bendahara',
        ]);
    }
}
