<?php

namespace App\Services\Validation;

class MissingValueDetector
{
    protected $requiredFields = [
        'transaction_date',
        'invoice_no',
        'dealer_code',
        'dealer_name',
        'branch',
        'product_code',
        'product_name',
        'quantity',
        'unit_price',
        'revenue',
        'cost',
        'target',
        'sales_month',
    ];

    public function detect(array $rows): array
    {
        $missingValues = [];

        foreach ($rows as $row) {
            $data = $row['data'];
            $rowMissingFields = [];

            foreach ($this->requiredFields as $field) {
                $value = $data[$field] ?? null;
                
                if ($value === null || $value === '' || (is_string($value) && trim($value) === '')) {
                    $rowMissingFields[] = $field;
                }
            }

            if (!empty($rowMissingFields)) {
                $missingValues[] = [
                    'line' => $row['line'],
                    'invoice_no' => $data['invoice_no'] ?? 'N/A',
                    'missing_fields' => $rowMissingFields,
                    'message' => 'Missing required fields: ' . implode(', ', $rowMissingFields),
                ];
            }
        }

        return $missingValues;
    }
}
