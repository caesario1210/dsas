<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        $jktBranch = Branch::where('branch_code', 'JKT')->first();
        $sbyBranch = Branch::where('branch_code', 'SBY')->first();
        $bdgBranch = Branch::where('branch_code', 'BDG')->first();

        $dealers = [
            ['branch_id' => $jktBranch->id, 'dealer_name' => 'Dealer Jakarta 1', 'dealer_code' => 'DLR-JKT-001', 'city' => 'Jakarta', 'phone' => '021-11111111'],
            ['branch_id' => $jktBranch->id, 'dealer_name' => 'Dealer Jakarta 2', 'dealer_code' => 'DLR-JKT-002', 'city' => 'Jakarta', 'phone' => '021-22222222'],
            ['branch_id' => $sbyBranch->id, 'dealer_name' => 'Dealer Surabaya 1', 'dealer_code' => 'DLR-SBY-001', 'city' => 'Surabaya', 'phone' => '031-11111111'],
            ['branch_id' => $sbyBranch->id, 'dealer_name' => 'Dealer Surabaya 2', 'dealer_code' => 'DLR-SBY-002', 'city' => 'Surabaya', 'phone' => '031-22222222'],
            ['branch_id' => $bdgBranch->id, 'dealer_name' => 'Dealer Bandung 1', 'dealer_code' => 'DLR-BDG-001', 'city' => 'Bandung', 'phone' => '022-11111111'],
        ];

        foreach ($dealers as $dealer) {
            Dealer::create($dealer);
        }
    }
}
