<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtlLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'rows_uploaded',
        'rows_imported',
        'rows_duplicate',
        'rows_missing',
        'rows_failed',
        'validation_errors',
    ];

    protected function casts(): array
    {
        return [
            'validation_errors' => 'array',
        ];
    }

    public function period()
    {
        return $this->belongsTo(SalesPeriod::class, 'period_id');
    }
}
