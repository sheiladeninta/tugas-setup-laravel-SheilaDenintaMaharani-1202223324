<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'total_items',
        'subtotal',
        'tax',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}