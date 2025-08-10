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
            'name' => 'Admin SPT',
            'email' => 'adminsemetonpesiar@gmail.com',
            'password' => Hash::make('adminsemeton2001'), // Ganti dengan password yang aman
            'role' => 'admin',
        ]);

        // Membuat pengguna Staff
        User::create([
            'name' => 'Staff SPT',
            'email' => 'staffsemetonpesiar@gmail.com',
            'password' => Hash::make('staffsemeton1993'), // Ganti dengan password yang aman
            'role' => 'staff',
        ]);
    }
}
