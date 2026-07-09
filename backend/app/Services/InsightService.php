<?php

namespace App\Services;

use App\Repositories\KpiRepository;
use App\Models\Branch;
use App\Models\Dealer;

class InsightService
{
    private KpiRepository $kpiRepository;

    public function __construct(KpiRepository $kpiRepository)
    {
        $this->kpiRepository = $kpiRepository;
    }

    public function generate(?int $year = null): array
    {
        $insights = [];
        $year = $year ?? ((int) date('Y'));

        $revenue = $this->kpiRepository->totalRevenue($year);
        $profit = $this->kpiRepository->totalProfit($year);
        $margin = $this->kpiRepository->profitMargin($year);
        $achievement = $this->kpiRepository->targetAchievement($year);
        $growth = $this->kpiRepository->salesGrowth($year);

        if ($revenue === 0.0 && $profit === 0.0) return [];

        if ($growth > 20) $insights[] = ['type' => 'success', 'title' => 'Significant Growth', 'message' => "Sales grew {$growth}% YoY — well above average."];
        elseif ($growth < -10) $insights[] = ['type' => 'danger', 'title' => 'Sales Decline', 'message' => "Sales dropped {$growth}% YoY. Investigate market conditions."];
        elseif ($growth >= 0) $insights[] = ['type' => 'info', 'title' => 'Positive Growth', 'message' => "Sales grew {$growth}% YoY this period."];

        if ($margin < 10) $insights[] = ['type' => 'warning', 'title' => 'Low Profit Margin', 'message' => "Margin only {$margin}%. Review cost structure."];
        elseif ($margin > 30) $insights[] = ['type' => 'success', 'title' => 'Healthy Margin', 'message' => "Profit margin at {$margin}% — healthy."];

        if ($achievement >= 100) $insights[] = ['type' => 'success', 'title' => 'Target Exceeded', 'message' => "Achievement {$achievement}% — target exceeded!"];
        elseif ($achievement < 50) $insights[] = ['type' => 'danger', 'title' => 'Far Below Target', 'message' => "Only {$achievement}% of target reached. Improvement needed."];
        elseif ($achievement < 80) $insights[] = ['type' => 'warning', 'title' => 'Below Target', 'message' => "Achievement {$achievement}% — " . round(100 - $achievement, 1) . "% short of target."];

        $dealerTop = $this->kpiRepository->dealerRankings($year, 'revenue', 1);
        if (!empty($dealerTop) && !empty($dealerTop[0])) {
            $d = $dealerTop[0];
            $insights[] = ['type' => 'info', 'title' => 'Top Dealer', 'message' => "{$d['dealer_name']} leads with Rp " . number_format($d['revenue'], 0, ',', '.') . " revenue."];
        }

        $productTop = $this->kpiRepository->productRankings($year, 'revenue', 1);
        if (!empty($productTop) && !empty($productTop[0])) {
            $p = $productTop[0];
            $insights[] = ['type' => 'info', 'title' => 'Best-Selling Product', 'message' => "{$p['product_name']} — top revenue at Rp " . number_format($p['revenue'], 0, ',', '.') . "."];
        }

        $monthly = $this->kpiRepository->monthlySalesTrend($year);
        $monthsWithData = array_filter($monthly, fn($m) => $m['revenue'] > 0);
        if (count($monthsWithData) > 1) {
            $vals = array_values($monthsWithData);
            $last = end($vals);
            $prev = prev($vals);
            if ($last && $prev && $prev['revenue'] > 0) {
                $change = round((($last['revenue'] - $prev['revenue']) / $prev['revenue']) * 100, 2);
                $mn = date('F', mktime(0, 0, 0, $last['month'], 1));
                $chgDir = $change >= 0 ? 'up' : 'down';
                $insights[] = [
                    'type' => $change >= 0 ? 'success' : 'warning',
                    'title' => $change >= 0 ? "{$mn} Uptick" : "{$mn} Dip",
                    'message' => "Revenue {$chgDir} {$change}% from previous month.",
                ];
            }
        }

        $pareto = $this->kpiRepository->paretoInsight($year);
        if ($pareto['top_n'] > 0 && $pareto['total_dealers'] > 1) {
            $insights[] = ['type' => 'info', 'title' => 'Revenue Concentration', 'message' => "Top {$pareto['top_n']} of {$pareto['total_dealers']} dealers contribute {$pareto['pct_revenue']}% of revenue (80/20 rule)."];
        }

        $branches = Branch::whereHas('salesTransactions.salesPeriod', fn($q) => $q->where('year', $year))->get();
        if ($branches->count() > 1) {
            $branchRevenues = [];
            foreach ($branches as $b) {
                $r = $this->kpiRepository->totalRevenue($year, null, $b->id);
                if ($r > 0) $branchRevenues[] = ['name' => $b->branch_name, 'revenue' => $r];
            }
            usort($branchRevenues, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
            if (count($branchRevenues) >= 2) {
                $best = $branchRevenues[0];
                $worst = $branchRevenues[count($branchRevenues) - 1];
                $insights[] = ['type' => 'info', 'title' => 'Branch Leader', 'message' => "{$best['name']} is top branch. {$worst['name']} lowest — gap of " . round(($best['revenue'] - $worst['revenue']) / ($worst['revenue'] ?: 1) * 100, 0) . "%."];
            }
        }

        $dealers = Dealer::whereHas('salesTransactions.salesPeriod', fn($q) => $q->where('year', $year))->limit(5)->get();
        $trending = [];
        foreach ($dealers as $d) {
            $prevYr = $this->kpiRepository->totalRevenue($year - 1, null, null, $d->id);
            $curYr = $this->kpiRepository->totalRevenue($year, null, null, $d->id);
            if ($prevYr > 0 && $curYr > 0) {
                $chg = round(($curYr - $prevYr) / $prevYr * 100, 1);
                if (abs($chg) > 30) $trending[] = ['name' => $d->dealer_name, 'change' => $chg];
            }
        }
        usort($trending, fn($a, $b) => abs($b['change']) <=> abs($a['change']));
        if (!empty($trending)) {
            $t = $trending[0];
            $tType = $t['change'] > 0 ? 'success' : 'warning';
            $tDir = $t['change'] > 0 ? 'grew' : 'declined';
            $insights[] = ['type' => $tType, 'title' => 'Dealer Spotlight', 'message' => "{$t['name']} {$tDir} " . abs($t['change']) . "% YoY — significant shift."];
        }

        return $insights;
    }
}
