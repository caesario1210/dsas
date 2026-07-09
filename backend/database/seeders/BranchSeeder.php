<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'branch_name' => 'Jakarta Branch',
                'branch_code' => 'JKT',
                'address' => 'Jl. Sudirman No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'phone' => '021-12345678',
            ],
            [
                'branch_name' => 'Surabaya Branch',
                'branch_code' => 'SBY',
                'address' => 'Jl. Basuki Rahmat No. 2',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'phone' => '031-87654321',
            ],
            [
                'branch_name' => 'Bandung Branch',
                'branch_code' => 'BDG',
                'address' => 'Jl. Asia Afrika No. 3',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'phone' => '022-11223344',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
