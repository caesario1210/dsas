<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    protected $csvParser;
    protected $excelParser;

    protected $requiredColumns = [
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
        'sales_person',
        'sales_month',
    ];

    public function __construct(CsvParserService $csvParser, ExcelParserService $excelParser)
    {
        $this->csvParser = $csvParser;
        $this->excelParser = $excelParser;
    }

    public function validateAndParseFile(UploadedFile $file): array
    {
        $this->validateFileFormat($file);
        
        $tempPath = $this->storeTemporarily($file);
        
        $data = $this->parseFile($file, $tempPath);
        
        $this->validateColumns($data['columns']);
        
        return [
            'temp_path' => $tempPath,
            'total_rows' => $data['total_rows'],
            'preview' => $data['preview'],
            'columns' => $data['columns'],
        ];
    }

    protected function validateFileFormat(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, ['csv', 'xlsx'])) {
            throw new \Exception('Invalid file format. Only CSV and XLSX are allowed.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \Exception('File size exceeds 10MB limit.');
        }
    }

    protected function storeTemporarily(UploadedFile $file): string
    {
        $filename = 'upload_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('temp', $filename, 'local');
        
        return $path;
    }

    protected function parseFile(UploadedFile $file, string $tempPath): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fullPath = storage_path('app/' . $tempPath);

        if ($extension === 'csv') {
            return $this->csvParser->parse($fullPath);
        } elseif ($extension === 'xlsx') {
            return $this->excelParser->parse($fullPath);
        }

        throw new \Exception('Unsupported file format.');
    }

    protected function validateColumns(array $columns): void
    {
        $normalizedColumns = array_map('trim', array_map('strtolower', $columns));
        $normalizedRequired = array_map('strtolower', $this->requiredColumns);

        $missing = array_diff($normalizedRequired, $normalizedColumns);

        if (!empty($missing)) {
            throw new \Exception(
                'Missing required columns: ' . implode(', ', $missing)
            );
        }

        if (count($normalizedColumns) !== 14) {
            throw new \Exception(
                'Invalid column count. Expected 14 columns, got ' . count($normalizedColumns)
            );
        }
    }

    public function deleteTemporaryFile(string $tempPath): void
    {
        if (Storage::disk('local')->exists($tempPath)) {
            Storage::disk('local')->delete($tempPath);
        }
    }
}
