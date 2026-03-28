<?php

namespace App\Models;

use App\Http\Controllers\Admin\ProdukController;
use Illuminate\Database\Eloquent\Model;

class order_item extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'order_id',
        'produk_id',
        'qty',
        'price',
        'total',
    ];

    // Relasi ke Order
// Relasi ke Order
    public function order()
    {
        return $this->belongsTo(order::class, 'order_id', 'order_id');
    }

    // PERBAIKAN DISINI
    public function produk()
    {
        // Hubungkan ke MODEL Produk, bukan Controller
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }

    
}
