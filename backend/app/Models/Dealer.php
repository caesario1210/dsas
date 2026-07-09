<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'dealer_name',
        'dealer_code',
        'address',
        'city',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class);
    }
}
