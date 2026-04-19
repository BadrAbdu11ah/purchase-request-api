<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_estimated_price', 'notes'];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

