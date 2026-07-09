<?php

namespace App\Services;

use App\Repositories\KpiRepository;

class KpiEngineService
{
    private KpiRepository $kpiRepository;

    public function __construct(KpiRepository $kpiRepository)
    {
        $this->kpiRepository = $kpiRepository;
    }

    public function calculateKpiCards(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null, ?int $productId = null): array
    {
        return [
            'total_revenue' => ['label' => 'Total Revenue', 'value' => $this->kpiRepository->totalRevenue($year, $month, $branchId, $dealerId, $productId), 'format' => 'currency'],
            'total_profit' => ['label' => 'Total Profit', 'value' => $this->kpiRepository->totalProfit($year, $month, $branchId, $dealerId, $productId), 'format' => 'currency'],
            'profit_margin' => ['label' => 'Profit Margin', 'value' => $this->kpiRepository->profitMargin($year, $month, $branchId, $dealerId, $productId), 'format' => 'percentage'],
            'target_achievement' => ['label' => 'Target Achievement', 'value' => $this->kpiRepository->targetAchievement($year, $month, $branchId, $dealerId, $productId), 'format' => 'percentage'],
            'total_quantity' => ['label' => 'Total Items Sold', 'value' => $this->kpiRepository->totalQuantity($year, $month, $branchId, $dealerId, $productId), 'format' => 'number'],
            'total_transactions' => ['label' => 'Total Transactions', 'value' => $this->kpiRepository->totalTransactions($year, $month, $branchId, $dealerId, $productId), 'format' => 'number'],
            'sales_growth' => ['label' => 'Sales Growth', 'value' => $this->kpiRepository->salesGrowth($year, $branchId, $dealerId), 'format' => 'percentage'],
        ];
    }

    public function calculateMonthlyTrend(int $year, ?int $branchId = null, ?int $dealerId = null): array
    {
        return $this->kpiRepository->monthlySalesTrend($year, $branchId, $dealerId);
    }

    public function calculateMonthlyDrilldown(int $year, int $month): array
    {
        return $this->kpiRepository->monthlyDrilldown($year, $month);
    }

    public function calculateDealerRankings(?int $year = null, string $sortBy = 'revenue', ?int $branchId = null): array
    {
        return [
            'top_10' => $this->kpiRepository->dealerRankings($year, $sortBy, 10, $branchId),
            'bottom_5' => $this->kpiRepository->dealerRankings($year, $sortBy, 5, $branchId),
        ];
    }

    public function calculateProductRankings(?int $year = null, string $sortBy = 'revenue'): array
    {
        return [
            'top_10' => $this->kpiRepository->productRankings($year, $sortBy, 10),
            'bottom_5' => $this->kpiRepository->productRankings($year, $sortBy, 5),
        ];
    }

    public function getAvailableFilters(int $year): array
    {
        return $this->kpiRepository->availableFilters($year);
    }

    public function calculateDashboard(?int $year = null, ?int $month = null, ?int $branchId = null, ?int $dealerId = null): array
    {
        return [
            'kpi_cards' => $this->calculateKpiCards($year, $month, $branchId, $dealerId),
            'monthly_trend' => $year ? $this->calculateMonthlyTrend($year, $branchId, $dealerId) : [],
            'dealer_rankings' => $this->calculateDealerRankings($year, 'revenue', $branchId),
            'product_rankings' => $this->calculateProductRankings($year, 'revenue'),
            'branch_summary' => $this->kpiRepository->dealerRevenueByBranch($year),
            'year' => $year, 'month' => $month,
        ];
    }
}
