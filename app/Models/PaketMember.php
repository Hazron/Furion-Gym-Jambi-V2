<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketMember extends Model
{
    protected $table = 'paket_members'; 
    protected $primaryKey = 'id_paket';
    
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_paket',
        'jenis',            
        'durasi',           
        'harga',
        'jenis',
        'status',           
        'campaign_id',           
        'deskripsi'
    ];

    public function campaign()
    {
        // Parameter: (Nama Model Parent, Foreign Key di tabel ini, Owner Key di tabel Parent)
        return $this->belongsTo(CampaignPromo::class, 'campaign_id', 'id_campaign');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeReguler($query)
    {
        return $query->where('jenis', 'reguler');
    }

    public function scopeCouple($query)
    {
        return $query->where('jenis', 'couple');
    }

    public function scopePromo($query)
    {
        return $query->where('jenis', 'promo');
    }
    
    public function scopePromoCouple($query){
        return $query->where('jenis', 'promo couple');
    }
}