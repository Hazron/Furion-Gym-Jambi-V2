<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// HAPUS: use App\Http\Controllers\Admin\ProdukController; (Ini salah alamat)

// PERBAIKAN 1: Nama class wajib PascalCase
class OrderItem extends Model
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

    public function order()
    {
        // PERBAIKAN 2: order::class diubah menjadi Order::class (Huruf 'O' kapital)
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function produk()
    {
        // Hubungkan ke MODEL Produk. 
        // Karena Produk.php ada di folder yang sama (App\Models), ini langsung jalan!
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }
}