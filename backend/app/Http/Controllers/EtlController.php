<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ValidationService;
use App\Services\CleaningService;
use App\Services\ImportService;
use App\Services\CsvParserService;
use App\Services\ExcelParserService;
use App\Services\AuditService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EtlController extends Controller
{
    protected $validationService;
    protected $cleaningService;
    protected $importService;
    protected $csvParser;
    protected $excelParser;
    protected $auditService;

    public function __construct(
        ValidationService $validationService,
        CleaningService $cleaningService,
        ImportService $importService,
        CsvParserService $csvParser,
        ExcelParserService $excelParser,
        AuditService $auditService
    ) {
        $this->validationService = $validationService;
        $this->cleaningService = $cleaningService;
        $this->importService = $importService;
        $this->csvParser = $csvParser;
        $this->excelParser = $excelParser;
        $this->auditService = $auditService;
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

            $this->auditService->log('etl.validate', 'file', null, "Validation: {$validationResult['summary']['valid_rows']} valid, {$validationResult['summary']['invalid_rows']} invalid", null, null, $request);

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
        try {
            $request->validate([
                'temp_path' => 'required|string',
                'valid_rows' => 'required|array',
            ]);

            $tempPath = $request->input('temp_path');
            $validRowLines = $request->input('valid_rows');
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

            $validRows = array_filter($parsedData['data'], function($row) use ($validRowLines) {
                return in_array($row['line'], $validRowLines);
            });

            $validRowsData = array_map(fn($r) => $r['data'], $validRows);

            $cleaningResult = $this->cleaningService->cleanAll($validRowsData, $tempPath);

            $this->auditService->log('etl.clean', 'file', null, "Cleaned: {$cleaningResult['summary']['cleaned_rows']} rows", null, null, $request);

            return response()->json([
                'status' => 'success',
                'message' => 'Cleaning completed',
                'data' => $cleaningResult,
            ]);

        } catch (\Exception $e) {
            Log::error('ETL Cleaning failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Cleaning failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'cleaned_path' => 'required|string',
            ]);

            $cleanedPath = $request->input('cleaned_path');
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $cleanedPath);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cleaned file not found. Please repeat the cleaning step.',
                ], 404);
            }

            $parsedData = $this->csvParser->parseAll($fullPath);
            $cleanedData = array_map(fn($r) => $r['data'], $parsedData['data']);

            $result = $this->importService->import($cleanedData, $cleanedPath);

            Cache::flush();

            $this->auditService->log('etl.import', 'file', null, "Import: {$result['summary']['imported']} imported, {$result['summary']['skipped']} skipped, {$result['summary']['failed']} failed", null, null, $request);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('ETL Import failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function summary($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Summary endpoint - Coming in Slice 6',
        ]);
    }
}
