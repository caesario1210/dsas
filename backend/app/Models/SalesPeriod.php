<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'start_date',
        'end_date',
        'status',
        'uploaded_by',
        'uploaded_at',
        'total_rows',
        'imported_rows',
        'failed_rows',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'uploaded_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class);
    }
}
