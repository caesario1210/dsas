<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_period_id',
        'dealer_id',
        'product_id',
        'branch_id',
        'transaction_date',
        'invoice_no',
        'quantity',
        'unit_price',
        'revenue',
        'cost',
        'profit',
        'discount',
        'target',
        'sales_person',
        'sales_month',
        'dealer_code',
        'dealer_name',
        'branch',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'revenue' => 'decimal:2',
        'cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'discount' => 'decimal:2',
        'target' => 'decimal:2',
    ];

    public function salesPeriod()
    {
        return $this->belongsTo(SalesPeriod::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
