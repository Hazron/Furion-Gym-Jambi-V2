<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignPromo extends Model
{
    protected $table = 'campaign_promo';
    protected $primaryKey = 'id_campaign';

    protected $fillable = [
        'nama_campaign',
        'gambar_banner',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    public function paketMembers()
    {
        return $this->hasMany(PaketMember::class, 'campaign_id', 'id_campaign');
    }
}