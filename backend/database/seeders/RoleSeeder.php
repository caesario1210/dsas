<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrator with full access',
            'permissions' => [
                'upload_file',
                'manage_etl',
                'view_dashboard',
                'export_report',
                'manage_users',
            ],
        ]);

        Role::create([
            'name' => 'Manager',
            'description' => 'Manager with view and export access only',
            'permissions' => [
                'view_dashboard',
                'export_report',
            ],
        ]);
    }
}
