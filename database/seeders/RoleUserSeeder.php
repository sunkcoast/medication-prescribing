<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Dr. Delta',
            'email' => 'doctor@deltasurya.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        User::create([
            'name' => 'Apoteker Surya',
            'email' => 'pharmacist@deltasurya.com',
            'password' => Hash::make('password'),
            'role' => 'pharmacist',
        ]);
    }
}
