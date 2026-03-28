<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class membershipPayment extends Model
{
    protected $table = 'membership_payment';

    protected $fillable = [
        'member_id',
        'paket_id',
        'jenis_transaksi', //membership
        'nomor_invoice',
        'tanggal_transaksi',
        'metode_pembayaran',
        'nominal',
        'status_pembayaran', //completed
        'admin_id',
        'keterangan'
    ];

    // ==========================================
    // TAMBAHKAN RELASI INI
    // ==========================================

    /**
     * Relasi ke Model Member
     * (Setiap pembayaran milik satu member)
     */
    public function member()
    {
        // Parameter: (Model Tujuan, Foreign Key di tabel ini, Primary Key di tabel tujuan)
        return $this->belongsTo(members::class, 'member_id', 'id_members');
    }

    /**
     * Relasi ke Model Paket
     * (Setiap pembayaran terkait satu paket)
     */
    public function paket()
    {
        return $this->belongsTo(PaketMember::class, 'paket_id', 'id_paket');
    }
    
    public function admin()
{
    return $this->belongsTo(User::class, 'admin_id', 'id');
}
}