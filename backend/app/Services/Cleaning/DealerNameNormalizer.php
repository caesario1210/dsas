<?php

namespace App\Services\Cleaning;

use App\Models\Dealer;
use Illuminate\Support\Facades\Cache;

class DealerNameNormalizer
{
    private array $dealerMap = [];

    public function __construct()
    {
        $this->loadDealerMap();
    }

    public function normalize(array $row): array
    {
        if (isset($row['dealer_code']) && isset($this->dealerMap[$row['dealer_code']])) {
            $row['dealer_name'] = $this->dealerMap[$row['dealer_code']];
        }

        return $row;
    }

    private function loadDealerMap(): void
    {
        $this->dealerMap = Cache::remember('dealer_name_map', 3600, function () {
            return Dealer::pluck('dealer_name', 'dealer_code')->toArray();
        });
    }
}
