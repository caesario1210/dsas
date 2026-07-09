<?php

namespace App\Services\Validation;

class RevenueConsistencyRule
{
    public function validate(array $row): ?array
    {
        $data = $row['data'];
        
        $quantity = (int)($data['quantity'] ?? 0);
        $unitPrice = (float)($data['unit_price'] ?? 0);
        $revenue = (float)($data['revenue'] ?? 0);

        if ($quantity <= 0) {
            return [
                'rule' => 'revenue_consistency',
                'field' => 'quantity',
                'value' => $quantity,
                'message' => 'Quantity must be greater than 0',
                'action' => 'rejected',
            ];
        }

        if ($unitPrice < 0) {
            return [
                'rule' => 'revenue_consistency',
                'field' => 'unit_price',
                'value' => $unitPrice,
                'message' => 'Unit price cannot be negative',
                'action' => 'rejected',
            ];
        }

        if ($revenue < 0) {
            return [
                'rule' => 'revenue_consistency',
                'field' => 'revenue',
                'value' => $revenue,
                'message' => 'Revenue cannot be negative',
                'action' => 'rejected',
            ];
        }

        $expectedRevenue = $quantity * $unitPrice;
        $difference = abs($expectedRevenue - $revenue);

        if ($difference > 0.01) {
            return [
                'rule' => 'revenue_consistency',
                'field' => 'revenue',
                'expected' => $expectedRevenue,
                'actual' => $revenue,
                'message' => "Revenue mismatch (Q1-B): Expected {$expectedRevenue}, got {$revenue}",
                'action' => 'rejected',
            ];
        }

        return null;
    }

    public function getDescription(): string
    {
        return 'Q1-B: Revenue must equal quantity × unit_price';
    }
}
