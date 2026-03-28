<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// HAPUS: use App\Models\order_item; (Tidak diperlukan karena satu folder/namespace)

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'deskripsi_produk',
        'harga_produk',
        'stok_produk',
        'gambar_produk',
        'status_produk',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'produk_id', 'id_produk');
    }
}