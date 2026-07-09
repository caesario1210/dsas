<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'upload_date',
        'uploaded_by',
        'total_rows',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'upload_date' => 'date',
        ];
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function transactions()
    {
        return $this->hasMany(SalesTransaction::class, 'period_id');
    }

    public function kpiSummaries()
    {
        return $this->hasMany(KpiSummary::class, 'period_id');
    }

    public function businessInsights()
    {
        return $this->hasMany(BusinessInsight::class, 'period_id');
    }

    public function etlLogs()
    {
        return $this->hasMany(EtlLog::class, 'period_id');
    }
}
