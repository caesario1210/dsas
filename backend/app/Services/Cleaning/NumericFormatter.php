<?php

namespace App\Services\Cleaning;

class NumericFormatter
{
    public function format(array $row): array
    {
        $numericFields = [
            'quantity',
            'unit_price',
            'revenue',
            'cost',
            'target',
        ];

        foreach ($numericFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = $this->formatNumeric($row[$field]);
            }
        }

        return $row;
    }

    private function formatNumeric($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = str_replace(',', '', $value);
        $value = str_replace(' ', '', $value);
        
        $value = preg_replace('/[^0-9.-]/', '', $value);

        return (float) $value;
    }
}
