<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@dsas.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@dsas.com',
            'password' => Hash::make('manager123'),
            'role_id' => $managerRole->id,
        ]);
    }
}
