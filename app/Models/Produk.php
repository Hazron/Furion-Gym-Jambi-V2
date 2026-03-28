<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\order_item;

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

    // Relasi ke item transaksi
    public function orderItems()
    {
        return $this->hasMany(order_item::class, 'produk_id', 'id_produk');
    }

    
}   
