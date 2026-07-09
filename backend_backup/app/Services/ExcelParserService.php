<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelParserService
{
    public function parse(string $filePath, int $previewLimit = 100): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            $columns = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                $columns[] = trim((string)$cellValue);
            }

            $preview = [];
            $previewRows = min($previewLimit, $highestRow - 1);

            for ($row = 2; $row <= $previewRows + 1; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $rowData[$columns[$col - 1]] = trim((string)$cellValue);
                }
                $preview[] = $rowData;
            }

            $totalRows = $highestRow - 1;

            return [
                'columns' => $columns,
                'preview' => $preview,
                'total_rows' => $totalRows,
            ];

        } catch (\Exception $e) {
            throw new \Exception('Failed to parse Excel file: ' . $e->getMessage());
        }
    }

    public function parseAll(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            $columns = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                $columns[] = trim((string)$cellValue);
            }

            $data = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $rowData[$columns[$col - 1]] = trim((string)$cellValue);
                }
                $data[] = [
                    'line' => $row - 1,
                    'data' => $rowData,
                ];
            }

            return [
                'columns' => $columns,
                'data' => $data,
                'total_rows' => count($data),
            ];

        } catch (\Exception $e) {
            throw new \Exception('Failed to parse Excel file: ' . $e->getMessage());
        }
    }
}
