<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'insight_type',
        'insight_text',
    ];

    public function period()
    {
        return $this->belongsTo(SalesPeriod::class, 'period_id');
    }
}
