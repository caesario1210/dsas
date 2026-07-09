<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
    ];

    public function transactions()
    {
        return $this->hasMany(SalesTransaction::class, 'product_id');
    }
}
