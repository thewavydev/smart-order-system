<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
 
#[Fillable(['customer_name', 'customer_email', 'customer_phone'])]

class Order extends Model
{
    use HasFactory;
    use Fillable;
    use Hidden;

    public function casts(): array
    {
        return [
            'customer_name' => 'string',
            'customer_email' => 'string',
            'customer_phone' => 'string',
        ];
    }
}
