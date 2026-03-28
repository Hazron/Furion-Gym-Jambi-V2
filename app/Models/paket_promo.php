<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class paket_promo extends Model
{
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
