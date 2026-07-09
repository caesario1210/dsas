<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx|max:10240',
            ]);

            $file = $request->file('file');
            
            $result = $this->fileService->validateAndParseFile($file);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded and parsed successfully',
                'data' => [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'rows_count' => $result['total_rows'],
                    'preview' => $result['preview'],
                    'columns' => $result['columns'],
                    'temp_path' => $result['temp_path'],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function template()
    {
        try {
            $filePath = storage_path('templates/sales_template.csv');
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Template file not found',
                ], 404);
            }

            return response()->download($filePath, 'sales_template.csv');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
