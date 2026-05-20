<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
 
// #[Fillable(['customer_name', 'customer_email', 'customer_phone'])]

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'product_name',
        'quantity',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {

            $year = now()->year;
            $random = random_int(1000, 9999); // 4 digits

            $order->order_number = 'ORD-' . $year . '-' . $random;
        });
    }

    protected $casts = [
        'created_at',
        'updated_at',
    ];
}
