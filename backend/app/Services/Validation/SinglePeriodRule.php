<?php

namespace App\Services\Validation;

class SinglePeriodRule
{
    protected $detectedPeriods = [];

    public function validateBatch(array $rows): ?array
    {
        return null;
    }

    public function getDescription(): string
    {
        return 'Q5-B: One upload must contain only one sales period';
    }
}
