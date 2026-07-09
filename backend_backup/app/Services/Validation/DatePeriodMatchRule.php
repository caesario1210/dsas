<?php

namespace App\Services\Validation;

use Carbon\Carbon;

class DatePeriodMatchRule
{
    public function validate(array $row): ?array
    {
        $data = $row['data'];
        
        $transactionDate = trim($data['transaction_date'] ?? '');
        $salesMonth = trim($data['sales_month'] ?? '');

        if (empty($transactionDate)) {
            return [
                'rule' => 'date_period_match',
                'field' => 'transaction_date',
                'message' => 'Transaction date is required',
                'action' => 'rejected',
            ];
        }

        if (empty($salesMonth)) {
            return [
                'rule' => 'date_period_match',
                'field' => 'sales_month',
                'message' => 'Sales month is required',
                'action' => 'rejected',
            ];
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $salesMonth)) {
            return [
                'rule' => 'date_period_match',
                'field' => 'sales_month',
                'value' => $salesMonth,
                'message' => "Invalid sales_month format: '{$salesMonth}'. Expected format: YYYY-MM (e.g., 2026-01)",
                'action' => 'rejected',
            ];
        }

        try {
            $date = Carbon::parse($transactionDate);
            $dateMonth = $date->format('Y-m');

            if ($dateMonth !== $salesMonth) {
                return [
                    'rule' => 'date_period_match',
                    'field' => 'transaction_date',
                    'transaction_date' => $transactionDate,
                    'transaction_month' => $dateMonth,
                    'sales_month' => $salesMonth,
                    'message' => "Date-period mismatch: Transaction date '{$transactionDate}' is in '{$dateMonth}', but sales_month is '{$salesMonth}'",
                    'action' => 'rejected',
                ];
            }

        } catch (\Exception $e) {
            return [
                'rule' => 'date_period_match',
                'field' => 'transaction_date',
                'value' => $transactionDate,
                'message' => "Invalid transaction_date format: '{$transactionDate}'. Expected format: YYYY-MM-DD",
                'action' => 'rejected',
            ];
        }

        return null;
    }

    public function getDescription(): string
    {
        return 'Transaction date must match sales_month period';
    }
}
