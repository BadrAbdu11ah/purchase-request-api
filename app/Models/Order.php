<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status', 'total_estimated_price', 'notes'];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
