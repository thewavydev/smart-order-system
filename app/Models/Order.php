<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total',
        'status',
        'source'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
//     public function orderItems()
// {
//     return $this->hasMany(OrderItem::class);
// }

    public function getOrderNumberAttribute()
    {
        return '#ORD' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

}