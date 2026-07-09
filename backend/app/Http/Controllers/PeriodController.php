<?php

namespace App\Http\Controllers;

use App\Models\SalesPeriod;
use App\Models\SalesTransaction;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index()
    {
        $periods = SalesPeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'year' => $p->year,
                'month' => $p->month,
                'month_name' => date('F', mktime(0, 0, 0, $p->month, 1)),
                'status' => $p->status,
                'total_rows' => $p->total_rows,
                'imported_rows' => $p->imported_rows,
                'transaction_count' => SalesTransaction::where('sales_period_id', $p->id)->count(),
            ]);

        return response()->json(['status' => 'success', 'data' => $periods]);
    }

    public function destroy(Request $request, int $id)
    {
        $period = SalesPeriod::findOrFail($id);
        $period->salesTransactions()->delete();
        $period->delete();

        $this->auditService->log('delete.period', 'sales_period', $id, "Deleted sales period: {$period->year}-{$period->month}", null, null, $request);

        return response()->json(['status' => 'success', 'message' => 'Period deleted']);
    }
}
