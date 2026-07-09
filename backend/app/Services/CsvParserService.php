<?php

namespace App\Services;

class CsvParserService
{
    public function parse(string $filePath, int $previewLimit = 100): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('Failed to open file: ' . $filePath);
        }

        $columns = fgetcsv($handle);
        
        if ($columns === false) {
            fclose($handle);
            throw new \Exception('Failed to read CSV header');
        }

        $columns = array_map('trim', $columns);

        $preview = [];
        $rowCount = 0;

        while (($row = fgetcsv($handle)) !== false && $rowCount < $previewLimit) {
            if (count($row) === count($columns)) {
                $preview[] = array_combine($columns, array_map('trim', $row));
                $rowCount++;
            }
        }

        $totalRows = $rowCount;
        while (fgetcsv($handle) !== false) {
            $totalRows++;
        }

        fclose($handle);

        return [
            'columns' => $columns,
            'preview' => $preview,
            'total_rows' => $totalRows,
        ];
    }

    public function parseAll(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('Failed to open file: ' . $filePath);
        }

        $columns = fgetcsv($handle);
        
        if ($columns === false) {
            fclose($handle);
            throw new \Exception('Failed to read CSV header');
        }

        $columns = array_map('trim', $columns);

        $data = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($columns)) {
                $data[] = [
                    'line' => $rowNumber,
                    'data' => array_combine($columns, array_map('trim', $row)),
                ];
            }
            $rowNumber++;
        }

        fclose($handle);

        return [
            'columns' => $columns,
            'data' => $data,
            'total_rows' => count($data),
        ];
    }
}
