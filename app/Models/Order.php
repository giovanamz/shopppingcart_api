<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const ACCEPTED = '1';
    const PENDING  = '0';
    const REJECTED = '2';

    protected $fillable = [
    	'user_id',
		'ammount',
		'shipping_address',
		'order_address',
		'order_email',
		'order_status'
    ];

    public function order_details()
    {
    	// la orden puede tener muchos detalles
    	return $this->hasMany(OrderDetail::class, 'order_id');
    }

}
