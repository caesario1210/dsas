<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'invoice_no',
        'dealer_id',
        'product_id',
        'quantity',
        'unit_price',
        'revenue',
        'cost',
        'target',
        'sales_person',
        'sales_month',
        'period_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'revenue' => 'decimal:2',
            'cost' => 'decimal:2',
            'profit' => 'decimal:2',
            'profit_margin' => 'decimal:2',
            'target' => 'decimal:2',
        ];
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function period()
    {
        return $this->belongsTo(SalesPeriod::class, 'period_id');
    }
}
