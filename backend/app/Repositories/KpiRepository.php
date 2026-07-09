<?php

namespace App\Repositories;

use App\Models\SalesTransaction;
use Illuminate\Support\Facades\DB;

class KpiRepository
{
    private function baseQuery(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null)
    {
        $query = SalesTransaction::join('sales_periods', 'sales_transactions.sales_period_id', '=', 'sales_periods.id');

        if ($year) $query->where('sales_periods.year', $year);
        if ($month) $query->where('sales_periods.month', $month);
        if ($branchId) $query->where('sales_transactions.branch_id', $branchId);
        if ($dealerId) $query->where('sales_transactions.dealer_id', $dealerId);
        if ($productId) $query->where('sales_transactions.product_id', $productId);

        return $query;
    }

    public function totalRevenue(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): float
    {
        return (float) $this->baseQuery($year, $month, $branchId, $dealerId, $productId)->sum('sales_transactions.revenue');
    }

    public function totalProfit(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): float
    {
        return (float) $this->baseQuery($year, $month, $branchId, $dealerId, $productId)->sum('sales_transactions.profit');
    }

    public function totalQuantity(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): int
    {
        return (int) $this->baseQuery($year, $month, $branchId, $dealerId, $productId)->sum('sales_transactions.quantity');
    }

    public function totalTransactions(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): int
    {
        return $this->baseQuery($year, $month, $branchId, $dealerId, $productId)->count();
    }

    public function profitMargin(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): float
    {
        $revenue = $this->totalRevenue($year, $month, $branchId, $dealerId, $productId);
        $profit = $this->totalProfit($year, $month, $branchId, $dealerId, $productId);
        return $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
    }

    public function targetAchievement(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): float
    {
        $result = $this->baseQuery($year, $month, $branchId, $dealerId, $productId)->select(
            DB::raw('SUM(sales_transactions.revenue) as total_revenue'),
            DB::raw('SUM(sales_transactions.target) as total_target')
        )->first();
        $target = (float) ($result->total_target ?? 0);
        return $target > 0 ? round(((float)($result->total_revenue ?? 0) / $target) * 100, 2) : 0;
    }

    public function salesGrowth(?int $year = null, ?int $branchId = null, ?int $dealerId = null): float
    {
        if (!$year) {
            $year = \App\Models\SalesPeriod::max('year') ?? (int) date('Y');
        }
        $current = $this->baseQuery($year, null, $branchId, $dealerId)->sum('sales_transactions.revenue');
        $previous = $this->baseQuery($year - 1, null, $branchId, $dealerId)->sum('sales_transactions.revenue');
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : ($current > 0 ? 100 : 0);
    }

    public function monthlySalesTrend(int $year, ?int $branchId = null, ?int $dealerId = null): array
    {
        $results = SalesTransaction::select(
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit'),
            DB::raw('SUM(sales_transactions.quantity) as quantity'),
            'sales_periods.month'
        )->join('sales_periods', 'sales_transactions.sales_period_id', '=', 'sales_periods.id')
        ->where('sales_periods.year', $year)
        ->when($branchId, fn($q) => $q->where('sales_transactions.branch_id', $branchId))
        ->when($dealerId, fn($q) => $q->where('sales_transactions.dealer_id', $dealerId))
        ->groupBy('sales_periods.month')
        ->orderBy('sales_periods.month')
        ->get();

        $trend = [];
        for ($m = 1; $m <= 12; $m++) {
            $data = $results->firstWhere('month', $m);
            $trend[] = [
                'month' => $m, 'month_name' => date('F', mktime(0, 0, 0, $m, 1)),
                'revenue' => (float) ($data->revenue ?? 0),
                'profit' => (float) ($data->profit ?? 0),
                'quantity' => (int) ($data->quantity ?? 0),
            ];
        }
        return $trend;
    }

    public function monthlyDrilldown(int $year, int $month): array
    {
        $base = SalesTransaction::whereHas('salesPeriod', fn($q) => $q->where('year', $year)->where('month', $month));

        $dealers = (clone $base)->select(
            'dealers.dealer_name',
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit')
        )->join('dealers', 'sales_transactions.dealer_id', '=', 'dealers.id')
        ->groupBy('dealers.id', 'dealers.dealer_name')
        ->orderByDesc('revenue')
        ->limit(5)->get()->toArray();

        $products = (clone $base)->select(
            'products.product_name',
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit')
        )->join('products', 'sales_transactions.product_id', '=', 'products.id')
        ->groupBy('products.id', 'products.product_name')
        ->orderByDesc('revenue')
        ->limit(5)->get()->toArray();

        $branches = (clone $base)->select(
            'branches.branch_name',
            DB::raw('SUM(sales_transactions.revenue) as revenue')
        )->join('branches', 'sales_transactions.branch_id', '=', 'branches.id')
        ->groupBy('branches.id', 'branches.branch_name')
        ->orderByDesc('revenue')
        ->get()->toArray();

        return ['dealers' => $dealers, 'products' => $products, 'branches' => $branches];
    }

    public function dealerRankings(?int $year = null, string $sortBy = 'revenue', int $limit = 10, ?int $branchId = null): array
    {
        $allowedSort = ['revenue', 'profit'];
        if (!in_array($sortBy, $allowedSort)) $sortBy = 'revenue';

        return $this->baseQuery($year, null, $branchId)->select(
            'dealers.dealer_code', 'dealers.dealer_name',
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit'),
            DB::raw('SUM(sales_transactions.quantity) as quantity'),
            DB::raw('CASE WHEN SUM(sales_transactions.revenue) > 0 THEN ROUND((SUM(sales_transactions.profit) / SUM(sales_transactions.revenue)) * 100, 2) ELSE 0 END as margin')
        )->join('dealers', 'sales_transactions.dealer_id', '=', 'dealers.id')
        ->groupBy('dealers.id', 'dealers.dealer_code', 'dealers.dealer_name')
        ->orderByDesc($sortBy)->limit($limit)->get()->toArray();
    }

    public function productRankings(?int $year = null, string $sortBy = 'revenue', int $limit = 10): array
    {
        $allowedSort = ['revenue', 'profit', 'quantity'];
        if (!in_array($sortBy, $allowedSort)) $sortBy = 'revenue';

        return $this->baseQuery($year)->select(
            'products.product_code', 'products.product_name',
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit'),
            DB::raw('SUM(sales_transactions.quantity) as quantity'),
            DB::raw('CASE WHEN SUM(sales_transactions.revenue) > 0 THEN ROUND((SUM(sales_transactions.profit) / SUM(sales_transactions.revenue)) * 100, 2) ELSE 0 END as margin')
        )->join('products', 'sales_transactions.product_id', '=', 'products.id')
        ->groupBy('products.id', 'products.product_code', 'products.product_name')
        ->orderByDesc($sortBy)->limit($limit)->get()->toArray();
    }

    public function dealerRevenueByBranch(?int $year = null): array
    {
        return $this->baseQuery($year)->select(
            'branches.branch_name', 'branches.branch_code',
            DB::raw('SUM(sales_transactions.revenue) as revenue'),
            DB::raw('SUM(sales_transactions.profit) as profit')
        )->join('branches', 'sales_transactions.branch_id', '=', 'branches.id')
        ->groupBy('branches.id', 'branches.branch_name', 'branches.branch_code')
        ->orderByDesc('revenue')->get()->toArray();
    }

    public function availableFilters(int $year): array
    {
        $branches = \App\Models\Branch::whereHas('salesTransactions.salesPeriod', fn($q) => $q->where('year', $year))
            ->select('id', 'branch_name')->get();

        $dealers = \App\Models\Dealer::whereHas('salesTransactions.salesPeriod', fn($q) => $q->where('year', $year))
            ->select('id', 'dealer_name')->get();

        return [
            'branches' => $branches,
            'dealers' => $dealers,
        ];
    }

    public function paretoInsight(int $year): array
    {
        $top = $this->dealerRankings($year, 'revenue', 9999);
        $total = array_sum(array_column($top, 'revenue'));
        $cumulative = 0;
        foreach ($top as $i => $d) {
            $cumulative += $d['revenue'];
            if ($total > 0 && ($cumulative / $total) >= 0.8) {
                return ['top_n' => $i + 1, 'total_dealers' => count($top), 'pct_revenue' => round(($cumulative / $total) * 100, 1)];
            }
        }
        return ['top_n' => count($top), 'total_dealers' => count($top), 'pct_revenue' => 100];
    }
}
