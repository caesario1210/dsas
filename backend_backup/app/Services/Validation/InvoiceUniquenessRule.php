<?php

namespace App\Services\Validation;

use App\Models\SalesTransaction;

class InvoiceUniquenessRule
{
    public function validate(array $row): ?array
    {
        $invoiceNo = trim($row['data']['invoice_no'] ?? '');

        if (empty($invoiceNo)) {
            return [
                'rule' => 'invoice_uniqueness',
                'field' => 'invoice_no',
                'message' => 'Invoice number is required',
                'action' => 'rejected',
            ];
        }

        $exists = SalesTransaction::where('invoice_no', $invoiceNo)->exists();

        if ($exists) {
            return [
                'rule' => 'invoice_uniqueness',
                'field' => 'invoice_no',
                'value' => $invoiceNo,
                'message' => "Invoice number '{$invoiceNo}' already exists (Q2-A: Global unique constraint)",
                'action' => 'rejected',
            ];
        }

        return null;
    }

    public function getDescription(): string
    {
        return 'Q2-A: Invoice must be globally unique across all periods';
    }
}
