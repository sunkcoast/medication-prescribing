<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'dr. REZA RAHMAN RAMADHANI, Sp.OT',
            'email' => 'dokter@test.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);
    
        User::create([
            'name' => 'Gustin Wahyu Diyanti',
            'email' => 'apoteker@test.com',
            'password' => Hash::make('password'),
            'role' => 'pharmacist',
        ]);

        Patient::factory()->count(10)->create();
    }
}
