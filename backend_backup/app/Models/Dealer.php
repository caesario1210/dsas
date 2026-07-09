<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_code',
        'dealer_name',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transactions()
    {
        return $this->hasMany(SalesTransaction::class, 'dealer_id');
    }
}
