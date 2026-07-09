<?php

namespace App\Services;

use App\Services\Validation\RevenueConsistencyRule;
use App\Services\Validation\InvoiceUniquenessRule;
use App\Services\Validation\MasterDataConsistencyRule;
use App\Services\Validation\SinglePeriodRule;
use App\Services\Validation\DatePeriodMatchRule;
use App\Services\Validation\DuplicateDetector;
use App\Services\Validation\MissingValueDetector;

class ValidationService
{
    protected $revenueRule;
    protected $invoiceRule;
    protected $masterDataRule;
    protected $singlePeriodRule;
    protected $datePeriodRule;
    protected $duplicateDetector;
    protected $missingValueDetector;

    public function __construct(
        RevenueConsistencyRule $revenueRule,
        InvoiceUniquenessRule $invoiceRule,
        MasterDataConsistencyRule $masterDataRule,
        SinglePeriodRule $singlePeriodRule,
        DatePeriodMatchRule $datePeriodRule,
        DuplicateDetector $duplicateDetector,
        MissingValueDetector $missingValueDetector
    ) {
        $this->revenueRule = $revenueRule;
        $this->invoiceRule = $invoiceRule;
        $this->masterDataRule = $masterDataRule;
        $this->singlePeriodRule = $singlePeriodRule;
        $this->datePeriodRule = $datePeriodRule;
        $this->duplicateDetector = $duplicateDetector;
        $this->missingValueDetector = $missingValueDetector;
    }

    public function validateAll(array $rows, array $columns): array
    {
        $batchError = $this->singlePeriodRule->validateBatch($rows);
        
        if ($batchError) {
            return [
                'status' => 'failed',
                'batch_error' => $batchError,
                'summary' => [
                    'total_rows' => count($rows),
                    'valid_rows' => 0,
                    'invalid_rows' => count($rows),
                    'duplicates_within_csv' => 0,
                    'missing_values' => 0,
                ],
                'errors' => [],
                'duplicates' => [],
                'missing_values' => [],
            ];
        }

        $duplicates = $this->duplicateDetector->detect($rows);
        $missingValues = $this->missingValueDetector->detect($rows);

        $errors = [];
        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $row) {
            $rowErrors = $this->validateRow($row);

            if (!empty($rowErrors)) {
                $invalidRows[] = [
                    'line' => $row['line'],
                    'data' => $row['data'],
                    'errors' => $rowErrors,
                ];

                foreach ($rowErrors as $error) {
                    $errors[] = array_merge([
                        'line' => $row['line'],
                        'invoice_no' => $row['data']['invoice_no'] ?? 'N/A',
                    ], $error);
                }
            } else {
                $validRows[] = $row;
            }
        }

        return [
            'status' => count($invalidRows) === 0 && empty($duplicates) && empty($missingValues) ? 'passed' : 'failed',
            'summary' => [
                'total_rows' => count($rows),
                'valid_rows' => count($validRows),
                'invalid_rows' => count($invalidRows),
                'duplicates_within_csv' => count($duplicates),
                'missing_values' => count($missingValues),
            ],
            'errors' => $errors,
            'duplicates' => $duplicates,
            'missing_values' => $missingValues,
            'valid_rows' => array_map(fn($r) => $r['line'], $validRows),
        ];
    }

    protected function validateRow(array $row): array
    {
        $errors = [];

        $missingError = $this->checkMissingValues($row);
        if ($missingError) {
            $errors[] = $missingError;
            return $errors;
        }

        $revenueError = $this->revenueRule->validate($row);
        if ($revenueError) {
            $errors[] = $revenueError;
        }

        $invoiceError = $this->invoiceRule->validate($row);
        if ($invoiceError) {
            $errors[] = $invoiceError;
        }

        $masterDataError = $this->masterDataRule->validate($row);
        if ($masterDataError) {
            $errors[] = $masterDataError;
        }

        $datePeriodError = $this->datePeriodRule->validate($row);
        if ($datePeriodError) {
            $errors[] = $datePeriodError;
        }

        return $errors;
    }

    protected function checkMissingValues(array $row): ?array
    {
        $requiredFields = [
            'transaction_date', 'invoice_no', 'dealer_code', 'dealer_name',
            'branch', 'product_code', 'product_name', 'quantity',
            'unit_price', 'revenue', 'cost', 'target', 'sales_month'
        ];

        $missing = [];
        foreach ($requiredFields as $field) {
            $value = $row['data'][$field] ?? null;
            if ($value === null || $value === '' || (is_string($value) && trim($value) === '')) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return [
                'rule' => 'required_fields',
                'fields' => $missing,
                'message' => 'Missing required fields: ' . implode(', ', $missing),
                'action' => 'rejected',
            ];
        }

        return null;
    }
}
