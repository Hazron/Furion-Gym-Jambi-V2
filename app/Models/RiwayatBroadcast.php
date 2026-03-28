<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RiwayatBroadcastDetail;

class RiwayatBroadcast extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = ['nama_campaign', 'total_target', 'status'];

    public function details()
    {
        return $this->hasMany(RiwayatBroadcastDetail::class);
    }
}
