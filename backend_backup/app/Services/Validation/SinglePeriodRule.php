<?php

namespace App\Services\Validation;

class SinglePeriodRule
{
    protected $detectedPeriods = [];

    public function validateBatch(array $rows): ?array
    {
        $this->detectedPeriods = [];

        foreach ($rows as $row) {
            $salesMonth = trim($row['data']['sales_month'] ?? '');
            
            if (!empty($salesMonth)) {
                $this->detectedPeriods[$salesMonth] = true;
            }
        }

        $periodCount = count($this->detectedPeriods);

        if ($periodCount > 1) {
            return [
                'rule' => 'single_period',
                'field' => 'sales_month',
                'detected_periods' => array_keys($this->detectedPeriods),
                'message' => 'Multiple periods detected (Q5-B): ' . implode(', ', array_keys($this->detectedPeriods)) . '. Only one period allowed per upload.',
                'action' => 'rejected_all',
            ];
        }

        if ($periodCount === 0) {
            return [
                'rule' => 'single_period',
                'field' => 'sales_month',
                'message' => 'No sales_month found in CSV data',
                'action' => 'rejected_all',
            ];
        }

        return null;
    }

    public function getDescription(): string
    {
        return 'Q5-B: One upload must contain only one sales period';
    }
}
