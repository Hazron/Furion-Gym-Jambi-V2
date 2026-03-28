<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\orderItem;
use App\Models\order_item;
use App\Models\members;
use App\Models\User;

class order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'member_id',
        'kasir_id',
        'invoice_code',
        'subtotal',
        'discount',
        'total_payment',
        'payment_method',
        'payment_status',
        'bukti_transfer'
    ];

    // Relasi ke OrderItem (1 order punya banyak item)
    public function items()
    {
        return $this->hasMany(order_item::class, 'order_id', 'order_id');
    }

    // Relasi ke member
    public function member()
    {
        return $this->belongsTo(members::class, 'member_id', 'id_members');
    }

    // Relasi ke kasir/admin
    public function cashier()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'id');
    }

    // Di dalam model App\Models\Order
    public function orderItems()
    {
        // Mengarah ke model order_item yang Anda berikan
        return $this->hasMany(order_item::class, 'order_id', 'order_id');
    }
}
