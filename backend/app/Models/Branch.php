<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
        'branch_code',
        'address',
        'city',
        'province',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function dealers()
    {
        return $this->hasMany(Dealer::class);
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class);
    }
}
