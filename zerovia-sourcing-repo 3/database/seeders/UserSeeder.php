<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@zerovia.ch'],
            [
                'name'     => 'ZEROvia Admin',
                'password' => Hash::make('zerovia2026!'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'procurement@zerovia.ch'],
            [
                'name'     => 'Procurement Team',
                'password' => Hash::make('zerovia2026!'),
            ]
        );
    }
}
