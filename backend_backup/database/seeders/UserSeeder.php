<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin DSAS',
            'email' => 'admin@dsas.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Manager DSAS',
            'email' => 'manager@dsas.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
    }
}
