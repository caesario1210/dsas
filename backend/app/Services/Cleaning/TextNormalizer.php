<?php

namespace App\Services\Cleaning;

class TextNormalizer
{
    public function normalize(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            if ($this->shouldNormalize($key)) {
                $normalized[$key] = $this->normalizeText($value);
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    private function shouldNormalize(string $key): bool
    {
        $textFields = [
            'dealer_name',
            'dealer_code',
            'branch',
            'product_name',
            'product_code',
            'sales_person',
        ];

        return in_array($key, $textFields);
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = $this->normalizeCase($value);

        return $value;
    }

    private function normalizeCase(string $value): string
    {
        $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        
        $exceptions = ['PT', 'CV', 'UD', 'PD'];
        foreach ($exceptions as $exception) {
            $value = preg_replace('/\b' . strtolower($exception) . '\b/i', $exception, $value);
        }

        return $value;
    }
}
