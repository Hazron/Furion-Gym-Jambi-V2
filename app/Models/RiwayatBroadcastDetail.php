<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RiwayatBroadcast;

class RiwayatBroadcastDetail extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = ['riwayat_broadcast_id', 'no_wa', 'status', 'fonnte_message_id'];

    public function broadcast()
    {
        return $this->belongsTo(RiwayatBroadcast::class, 'riwayat_broadcast_id');
    }
}
