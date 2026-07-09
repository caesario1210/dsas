<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ValidationService;
use App\Services\CsvParserService;
use App\Services\ExcelParserService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EtlController extends Controller
{
    protected $validationService;
    protected $csvParser;
    protected $excelParser;

    public function __construct(
        ValidationService $validationService,
        CsvParserService $csvParser,
        ExcelParserService $excelParser
    ) {
        $this->validationService = $validationService;
        $this->csvParser = $csvParser;
        $this->excelParser = $excelParser;
    }

    public function validate(Request $request)
    {
        try {
            $request->validate([
                'temp_path' => 'required|string',
            ]);

            $tempPath = $request->input('temp_path');
            $fullPath = storage_path('app/' . $tempPath);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found. Please upload again.',
                ], 404);
            }

            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
            
            $parsedData = $extension === 'csv' 
                ? $this->csvParser->parseAll($fullPath)
                : $this->excelParser->parseAll($fullPath);

            $validationResult = $this->validationService->validateAll(
                $parsedData['data'],
                $parsedData['columns']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Validation completed',
                'data' => $validationResult,
            ]);

        } catch (\Exception $e) {
            Log::error('ETL Validation failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function clean(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cleaning endpoint - Coming in Slice 4',
        ]);
    }

    public function import(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Import endpoint - Coming in Slice 5',
        ]);
    }

    public function summary($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Summary endpoint - Coming in Slice 6',
        ]);
    }
}
