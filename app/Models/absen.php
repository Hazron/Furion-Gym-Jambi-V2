<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class absen extends Model
{
    protected $table = 'absen';
    protected $primaryKey = 'id_absen'; // Sesuaikan dengan schema Anda

    protected $fillable = [
        'member_id',
        'waktu_masuk',
    ];

    // Relasi ke Member (opsional, jika nanti butuh nama member)
    public function member()
    {
        return $this->belongsTo(members::class, 'member_id', 'id_members');
    }
}
