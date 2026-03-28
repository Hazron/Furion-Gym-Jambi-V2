<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PERBAIKAN 1: Nama class wajib PascalCase
class ProdukTransaksi extends Model
{
    // Nama tabel menggunakan huruf kecil semua sudah aman
    protected $table = 'produktransaksi';
    protected $primaryKey = 'id_transaksi';
    
    protected $fillable = [
        'id_produk',
        'qyt', // Catatan: Apakah ini typo dari 'qty'? Sesuaikan dengan nama kolom di database.
        'member_id',
        'total_harga',
        'status_pembayaran',
        'metode_pembayaran',
    ];

    // TAMBAHAN: Relasi ke model Produk
    public function produk()
    {
        // Pastikan memanggil class Produk dengan awalan kapital
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // TAMBAHAN: Relasi ke model Members
    public function member()
    {
        // Pastikan memanggil class Members dengan awalan kapital
        return $this->belongsTo(Members::class, 'member_id', 'id_members');
    }
}