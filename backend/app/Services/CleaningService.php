<?php

namespace App\Services;

use App\Services\Cleaning\TextNormalizer;
use App\Services\Cleaning\DateFormatter;
use App\Services\Cleaning\NumericFormatter;
use App\Services\Cleaning\DealerNameNormalizer;

class CleaningService
{
    private TextNormalizer $textNormalizer;
    private DateFormatter $dateFormatter;
    private NumericFormatter $numericFormatter;
    private DealerNameNormalizer $dealerNameNormalizer;

    public function __construct(
        TextNormalizer $textNormalizer,
        DateFormatter $dateFormatter,
        NumericFormatter $numericFormatter,
        DealerNameNormalizer $dealerNameNormalizer
    ) {
        $this->textNormalizer = $textNormalizer;
        $this->dateFormatter = $dateFormatter;
        $this->numericFormatter = $numericFormatter;
        $this->dealerNameNormalizer = $dealerNameNormalizer;
    }

    public function cleanAll(array $validRows, string $tempPath): array
    {
        $cleaned = [];
        $cleaningErrors = [];

        foreach ($validRows as $index => $row) {
            try {
                $cleanedRow = $this->cleanRow($row);
                $cleaned[] = $cleanedRow;
            } catch (\Exception $e) {
                $cleaningErrors[] = [
                    'line' => $index + 2,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $cleanedPath = $this->saveCleanedData($cleaned, $tempPath);

        return [
            'status' => count($cleaningErrors) > 0 ? 'partial' : 'success',
            'summary' => [
                'total_rows' => count($validRows),
                'cleaned_rows' => count($cleaned),
                'failed_rows' => count($cleaningErrors),
            ],
            'cleaned_data' => $cleaned,
            'cleaned_path' => $cleanedPath,
            'errors' => $cleaningErrors,
        ];
    }

    private function cleanRow(array $row): array
    {
        $row = $this->textNormalizer->normalize($row);
        $row = $this->dateFormatter->format($row);
        $row = $this->numericFormatter->format($row);
        $row = $this->dealerNameNormalizer->normalize($row);

        return $row;
    }

    private function saveCleanedData(array $cleaned, string $tempPath): string
    {
        $cleanedPath = str_replace('upload_', 'cleaned_', $tempPath);
        $fullPath = storage_path('app/' . $cleanedPath);

        $handle = fopen($fullPath, 'w');
        
        if (!empty($cleaned)) {
            fputcsv($handle, array_keys($cleaned[0]));
        }

        foreach ($cleaned as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $cleanedPath;
    }
}
