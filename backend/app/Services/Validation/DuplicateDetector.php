<?php

namespace App\Services\Validation;

class DuplicateDetector
{
    protected $seenInvoices = [];

    public function detect(array $rows): array
    {
        $this->seenInvoices = [];
        $duplicates = [];

        foreach ($rows as $row) {
            $invoiceNo = trim($row['data']['invoice_no'] ?? '');
            
            if (empty($invoiceNo)) {
                continue;
            }

            if (isset($this->seenInvoices[$invoiceNo])) {
                $duplicates[] = [
                    'line' => $row['line'],
                    'invoice_no' => $invoiceNo,
                    'first_seen_at_line' => $this->seenInvoices[$invoiceNo],
                    'message' => "Duplicate invoice within CSV: '{$invoiceNo}' first appeared at line {$this->seenInvoices[$invoiceNo]}",
                ];
            } else {
                $this->seenInvoices[$invoiceNo] = $row['line'];
            }
        }

        return $duplicates;
    }

    public function getCount(): int
    {
        return count($this->seenInvoices);
    }
}
