<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
    ];

    public function dealers()
    {
        return $this->hasMany(Dealer::class, 'branch_id');
    }
}
