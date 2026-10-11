<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'company', 'phone', 'email', 'address'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}