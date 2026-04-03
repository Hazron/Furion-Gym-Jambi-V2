<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $primaryKey = 'id_members';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $fillable = [
        'id_members',
        'nama_lengkap',
        'alamat',
        'no_telepon',
        'email',
        'jenis_kelamin',
        'tanggal_daftar',
        'tanggal_selesai',
        'paket_id',
        'partner_id',
        'is_opt_out',
        'status',
        'target_latihan',
    ];

    protected $casts = [
        'is_opt_out' => 'boolean',
    ];

    public function paket()
    {
        return $this->belongsTo(PaketMember::class, 'paket_id', 'id_paket');
    }

    public function promo()
    {
        return $this->belongsTo(PaketPromo::class, 'promo_id', 'id_paket_promo'); 
    }

    public function partner()
    {
        return $this->hasOne(Members::class, 'id_members', 'partner_id');
    }

    public function getNamaPaketAktifAttribute()
    {
        if ($this->paket) {
            return $this->paket->nama_paket;
        } elseif ($this->promo) {
            return $this->promo->nama_paket . ' (PROMO)';
        }
        return 'Tanpa Paket';
    }

    public function membershipPayments()
    {
        return $this->hasMany(MembershipPayment::class, 'member_id', 'id_members');
    }
}