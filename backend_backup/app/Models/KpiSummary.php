<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiSummary extends Model
{
    use HasFactory;

    protected $table = 'kpi_summary';

    protected $fillable = [
        'period_id',
        'kpi_name',
        'kpi_value',
        'filter_type',
        'filter_value',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'calculated_at' => 'datetime',
        ];
    }

    public function period()
    {
        return $this->belongsTo(SalesPeriod::class, 'period_id');
    }
}
