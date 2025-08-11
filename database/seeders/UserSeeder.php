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
        // Membuat pengguna Admin
        User::create([
            'name' => 'Admin',
            'email' => 'adminsemetonpesiar@gmail.com',
            'password' => Hash::make('adminsemeton2001'), // Ganti dengan password yang aman
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadminsemetonpesiar@gmail.com',
            'password' => Hash::make('superadmin1993'), // Ganti dengan password yang aman
            'role' => 'superadmin',
        ]);

        // Membuat pengguna Staff
        User::create([
            'name' => 'Staff',
            'email' => 'staffsemetonpesiar@gmail.com',
            'password' => Hash::make('staffsemeton1993'), // Ganti dengan password yang aman
            'role' => 'staff',
        ]);
    }
}
