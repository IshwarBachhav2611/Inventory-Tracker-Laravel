<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'category', 'quantity', 'unit_price',
        'min_stock', 'supplier_id', 'is_active',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock;
    }
}