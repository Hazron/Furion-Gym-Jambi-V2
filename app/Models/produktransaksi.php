<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produktransaksi extends Model
{
    protected $table = 'produktransaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = [
        'id_produk',
        'qyt',
        'member_id',
        'total_harga',
        'status_pembayaran',
        'metode_pembayaran',
    ];
}
