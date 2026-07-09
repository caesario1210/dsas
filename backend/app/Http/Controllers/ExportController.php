<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Dealer;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    private function buildQuery(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');
        $branchId = $request->input('branch_id');
        $dealerId = $request->input('dealer_id');

        $query = SalesTransaction::with(['dealer', 'product', 'branch', 'salesPeriod'])
            ->whereHas('salesPeriod', fn($q) => $q->where('year', $year));

        if ($month) $query->whereHas('salesPeriod', fn($q) => $q->where('month', $month));
        if ($branchId) $query->where('branch_id', $branchId);
        if ($dealerId) $query->where('dealer_id', $dealerId);

        return $query;
    }

    public function csv(Request $request)
    {
        $transactions = $this->buildQuery($request)->get();
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');

        $filename = "sales_report_{$year}" . ($month ? "_{$month}" : '') . ".csv";

        $handle = fopen('php://temp', 'w+');
        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, ['Invoice','Date','Dealer','Product','Branch','Qty','Unit Price','Revenue','Cost','Profit','Target','Sales Person','Period']);

        foreach ($transactions as $t) {
            fputcsv($handle, [
                $t->invoice_no,
                $t->transaction_date,
                $t->dealer?->dealer_name ?? '-',
                $t->product?->product_name ?? '-',
                $t->getAttribute('branch') ?: $t->branch?->branch_name ?? '-',
                $t->quantity,
                $t->unit_price,
                $t->revenue,
                $t->cost,
                $t->profit,
                $t->target,
                $t->sales_person ?? '-',
                "{$t->salesPeriod?->month}/{$t->salesPeriod?->year}",
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function html(Request $request)
    {
        $transactions = $this->buildQuery($request)->get();
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');

        $totalRevenue = $transactions->sum('revenue');
        $totalProfit = $transactions->sum('profit');
        $totalQuantity = $transactions->sum('quantity');

        $title = "Sales Report {$year}" . ($month ? " - " . date('F', mktime(0, 0, 0, $month, 1)) : '');

        $filterParts = [];
        if ($month) $filterParts[] = date('F', mktime(0, 0, 0, $month, 1));
        if ($bid = $request->input('branch_id')) {
            $b = Branch::find($bid);
            if ($b) $filterParts[] = $b->branch_name;
        }
        if ($did = $request->input('dealer_id')) {
            $d = Dealer::find($did);
            if ($d) $filterParts[] = $d->dealer_name;
        }
        $filterLabel = !empty($filterParts) ? ' — ' . implode(', ', $filterParts) : '';

        $rows = '';
        foreach ($transactions as $t) {
            $rows .= '<tr>
                <td>' . $t->invoice_no . '</td>
                <td>' . $t->transaction_date . '</td>
                <td>' . ($t->dealer?->dealer_name ?? '-') . '</td>
                <td>' . ($t->product?->product_name ?? '-') . '</td>
                <td>' . ($t->getAttribute('branch') ?: $t->branch?->branch_name ?? '-') . '</td>
                <td>' . $t->quantity . '</td>
                <td>Rp ' . number_format($t->revenue, 0, ',', '.') . '</td>
                <td>Rp ' . number_format($t->profit, 0, ',', '.') . '</td>
            </tr>' . "\n";
        }

        $rTotal = 'Rp ' . number_format($totalRevenue, 0, ',', '.');
        $pTotal = 'Rp ' . number_format($totalProfit, 0, ',', '.');
        $qTotal = number_format($totalQuantity, 0, ',', '.');
        $count = $transactions->count();

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>{$title}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; color: #333; padding: 24px; }
  .print-btn { position: fixed; top: 12px; right: 12px; padding: 8px 18px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; z-index: 999; }
  .print-btn:hover { background: #0056b3; }
  h1 { font-size: 22px; margin-bottom: 2px; }
  .sub { color: #666; font-size: 13px; margin-bottom: 20px; }
  .grid { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
  .card { flex: 1; min-width: 140px; padding: 14px; background: #f8f9fa; border-radius: 6px; text-align: center; border: 1px solid #e9ecef; }
  .card .v { font-size: 18px; font-weight: 700; color: #222; }
  .card .l { font-size: 11px; color: #777; margin-top: 4px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #2c3e50; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
  td { padding: 6px 10px; border-bottom: 1px solid #e9ecef; }
  tr:nth-child(even) td { background: #f8f9fa; }
  .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #dee2e6; font-size: 10px; color: #999; text-align: center; }
  @media print { .print-btn { display: none; } body { padding: 12px; } }
</style></head>
<body>
<button class="print-btn" onclick="window.print()">🖨 Print</button>
<h1>{$title}</h1>
<div class="sub">Generated {$filterLabel} — {$count} transactions</div>
<div class="grid">
  <div class="card"><div class="v">{$rTotal}</div><div class="l">Total Revenue</div></div>
  <div class="card"><div class="v">{$pTotal}</div><div class="l">Total Profit</div></div>
  <div class="card"><div class="v">{$qTotal}</div><div class="l">Items Sold</div></div>
  <div class="card"><div class="v">{$count}</div><div class="l">Transactions</div></div>
</div>
<table>
  <thead><tr><th>Invoice</th><th>Date</th><th>Dealer</th><th>Product</th><th>Branch</th><th>Qty</th><th>Revenue</th><th>Profit</th></tr></thead>
  <tbody>{$rows}</tbody>
</table>
<div class="footer">DSAS — Dealer Sales Analytics System</div>
</body></html>
HTML;

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'inline',
        ]);
    }
}
