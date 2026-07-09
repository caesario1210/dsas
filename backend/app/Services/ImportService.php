<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Dealer;
use App\Models\Product;
use App\Models\SalesPeriod;
use App\Models\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportService
{
    public function import(array $cleanedData, string $cleanedPath): array
    {
        $imported = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];
        $periodIds = [];

        DB::beginTransaction();

        try {
            foreach ($cleanedData as $index => $row) {
                try {
                    if ($this->rowExists($row)) {
                        $skipped++;
                        continue;
                    }
                    $period = $this->resolveRowPeriod($row);
                    $periodIds[] = $period->id;
                    $this->importRow($row, $period->id);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'line' => $index + 2,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            $periodIds = array_unique($periodIds);
            foreach ($periodIds as $pid) {
                SalesPeriod::find($pid)?->update(['status' => 'completed']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import transaction failed: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage(),
                'summary' => [
                    'total_rows' => count($cleanedData),
                    'imported' => 0,
                    'failed' => count($cleanedData),
                ],
                'errors' => [],
            ];
        }

        return [
            'status' => $failed > 0 ? 'partial' : 'success',
            'message' => "Import completed: {$imported} imported, {$skipped} skipped (already exist), {$failed} failed",
            'summary' => [
                'total_rows' => count($cleanedData),
                'imported' => $imported,
                'skipped' => $skipped,
                'failed' => $failed,
                'periods' => count($periodIds),
            ],
            'errors' => $errors,
        ];
    }

    private function resolveRowPeriod(array $row): SalesPeriod
    {
        $salesMonth = $row['sales_month'] ?? '';
        if (empty($salesMonth)) {
            throw new \Exception('sales_month is required');
        }

        $parts = explode('-', $salesMonth);
        if (count($parts) < 2) {
            throw new \Exception("Invalid sales_month format: {$salesMonth}");
        }

        $year = (int) $parts[0];
        $month = (int) $parts[1];

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return SalesPeriod::firstOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'processing',
            ]
        );
    }

    private function rowExists(array $row): bool
    {
        $invoiceNo = $row['invoice_no'] ?? '';
        if (empty($invoiceNo)) {
            return false;
        }

        return SalesTransaction::where('invoice_no', $invoiceNo)->exists();
    }

    private function importRow(array $row, int $salesPeriodId): void
    {
        $branch = $this->resolveBranch($row);
        $dealer = $this->resolveDealer($row, $branch->id);
        $product = $this->resolveProduct($row);

        $quantity = (int) ($row['quantity'] ?? 0);
        $unitPrice = (float) ($row['unit_price'] ?? 0);
        $revenue = (float) ($row['revenue'] ?? 0);
        $cost = (float) ($row['cost'] ?? 0);
        $target = (float) ($row['target'] ?? 0);
        $profit = $revenue - $cost;

        SalesTransaction::create([
            'sales_period_id' => $salesPeriodId,
            'dealer_id' => $dealer->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'transaction_date' => $row['transaction_date'] ?? now(),
            'invoice_no' => $row['invoice_no'] ?? '',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
            'discount' => 0,
            'target' => $target,
            'sales_person' => $row['sales_person'] ?? null,
            'sales_month' => $row['sales_month'] ?? '',
            'dealer_code' => $row['dealer_code'] ?? '',
            'dealer_name' => $row['dealer_name'] ?? '',
            'branch' => $row['branch'] ?? '',
        ]);
    }

    private function resolveBranch(array $row): Branch
    {
        $branchName = trim($row['branch'] ?? '');

        return Branch::firstOrCreate(
            ['branch_name' => $branchName],
            [
                'branch_code' => strtoupper(substr($branchName, 0, 3)),
                'is_active' => true,
            ]
        );
    }

    private function resolveDealer(array $row, int $branchId): Dealer
    {
        $dealerCode = trim($row['dealer_code'] ?? '');
        $dealerName = trim($row['dealer_name'] ?? '');

        $dealer = Dealer::where('dealer_code', $dealerCode)->first();

        if ($dealer) {
            return $dealer;
        }

        return Dealer::create([
            'branch_id' => $branchId,
            'dealer_name' => $dealerName,
            'dealer_code' => $dealerCode,
            'city' => null,
            'is_active' => true,
        ]);
    }

    private function resolveProduct(array $row): Product
    {
        $productCode = trim($row['product_code'] ?? '');
        $productName = trim($row['product_name'] ?? '');

        $product = Product::where('product_code', $productCode)->first();

        if ($product) {
            return $product;
        }

        return Product::create([
            'product_name' => $productName,
            'product_code' => $productCode,
            'category' => null,
            'base_price' => 0,
            'is_active' => true,
        ]);
    }
}
