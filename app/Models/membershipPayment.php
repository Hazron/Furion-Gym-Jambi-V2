<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPayment extends Model
{
    protected $table = 'membership_payment';

    protected $fillable = [
        'member_id',
        'paket_id',
        'jenis_transaksi',
        'nomor_invoice',
        'tanggal_transaksi',
        'bukti_transfer',
        'metode_pembayaran',
        'nominal',
        'status_pembayaran',
        'admin_id',
        'keterangan'
    ];

    // ==========================================
    // TAMBAHKAN RELASI INI
    // ==========================================

    public function member()
    {
        // PERBAIKAN: Ubah 'members::class' menjadi 'Members::class'
        return $this->belongsTo(Members::class, 'member_id', 'id_members');
    }

    public function paket()
    {
        return $this->belongsTo(PaketMember::class, 'paket_id', 'id_paket');
    }
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }
}