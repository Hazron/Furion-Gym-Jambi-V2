<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PERBAIKAN 1: Nama class diawali huruf kapital (Absen)
class Absen extends Model 
{
    protected $table = 'absen';
    protected $primaryKey = 'id_absen'; 

    protected $fillable = [
        'member_id',
        'waktu_masuk',
    ];

    public function member()
    {
        return $this->belongsTo(Members::class, 'member_id', 'id_members');
    }
}