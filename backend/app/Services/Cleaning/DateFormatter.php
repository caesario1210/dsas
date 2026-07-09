<?php

namespace App\Services\Cleaning;

use DateTime;

class DateFormatter
{
    public function format(array $row): array
    {
        if (isset($row['transaction_date'])) {
            $row['transaction_date'] = $this->formatDate($row['transaction_date']);
        }

        return $row;
    }

    private function formatDate(?string $date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'Y/m/d',
            'd.m.Y',
        ];

        foreach ($formats as $format) {
            $dateObj = DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj->format('Y-m-d');
            }
        }

        return $date;
    }
}
