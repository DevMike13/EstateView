<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'EstateView Admin',
            'email' => 'estateview@gmail.com',
            'password' => Hash::make('estateview@2026'),
            'is_verified' => true,
            'is_active' => true,
            'role' => 'admin',
        ]);
    }
}
