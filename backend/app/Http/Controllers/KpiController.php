<?php

namespace App\Http\Controllers;

use App\Services\KpiEngineService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KpiController extends Controller
{
    private KpiEngineService $kpiEngine;
    private AuditService $auditService;

    public function __construct(KpiEngineService $kpiEngine, AuditService $auditService)
    {
        $this->kpiEngine = $kpiEngine;
        $this->auditService = $auditService;
    }

    public function dashboard(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $branchId = $request->input('branch_id');
        $dealerId = $request->input('dealer_id');
        $cacheKey = "dashboard_{$year}_{$month}_{$branchId}_{$dealerId}";

        $data = Cache::remember($cacheKey, 300, function () use ($year, $month, $branchId, $dealerId) {
            return $this->kpiEngine->calculateDashboard(
                $year ? (int) $year : null,
                $month ? (int) $month : null,
                $branchId ? (int) $branchId : null,
                $dealerId ? (int) $dealerId : null
            );
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function filters(Request $request)
    {
        $year = $request->input('year', date('Y'));

        return response()->json([
            'status' => 'success',
            'data' => $this->kpiEngine->getAvailableFilters((int) $year),
        ]);
    }

    public function drilldown(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        return response()->json([
            'status' => 'success',
            'data' => $this->kpiEngine->calculateMonthlyDrilldown((int) $year, (int) $month),
        ]);
    }

    public function recalculate(Request $request)
    {
        Cache::flush();
        $this->auditService->log('kpi.recalculate', 'kpi', null, 'Manual KPI recalculated', null, null, $request);
        return response()->json(['status' => 'success', 'message' => 'KPI cache cleared']);
    }
}
