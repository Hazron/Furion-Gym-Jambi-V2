<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PERBAIKAN: Ubah 'paket_promo' menjadi 'PaketPromo'
class PaketPromo extends Model
{
    // Nama tabel tetap 'paket_promo' huruf kecil, ini sudah benar
    protected $table = 'paket_promo';
    protected $primaryKey = 'id_paket_promo';
    
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'nama_paket',
        'durasi',
        'harga',
        'status', 
        'jenis',
        'campaign_id',
    ];
}