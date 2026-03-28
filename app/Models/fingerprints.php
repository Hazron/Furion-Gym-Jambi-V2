<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PERBAIKAN 1: Gunakan huruf kapital di awal (PascalCase). 
// Idealnya nama model itu singular (Fingerprint), tapi kalau mau pakai plural (Fingerprints) tidak masalah, yang penting kapital.
class fingerprints extends Model
{
    protected $table = 'fingerprints';

    protected $fillable = [
        'member_id',
        'fingerprint_template',
        'device_id',
        'last_updated',
    ];

    public function member()
    {
        // Pastikan memanggil class Members dengan awalan kapital
        return $this->belongsTo(Members::class, 'member_id', 'id_members');
    }
}