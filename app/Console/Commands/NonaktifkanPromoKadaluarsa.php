<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CampaignPromo;
use App\Models\PaketMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NonaktifkanPromoKadaluarsa extends Command
{
    /**
     * Nama perintah untuk dijalankan di terminal (contoh: php artisan promo:nonaktifkan)
     */
    protected $signature = 'promo:nonaktifkan';

    /**
     * Deskripsi dari perintah
     */
    protected $description = 'Menonaktifkan otomatis campaign promo dan paket member yang sudah melewati batas tanggal selesai';

    public function handle()
    {
        $hariIni = Carbon::today();

        $idCampaignKadaluarsa = CampaignPromo::where('status', 'aktif')
            ->whereDate('tanggal_selesai', '<', $hariIni)
            ->pluck('id_campaign');

        if ($idCampaignKadaluarsa->isNotEmpty()) {
            
            CampaignPromo::whereIn('id_campaign', $idCampaignKadaluarsa)
                ->update(['status' => 'nonaktif']);

            PaketMember::whereIn('campaign_id', $idCampaignKadaluarsa)
                ->update(['status' => 'nonaktif']);

            $jumlah = $idCampaignKadaluarsa->count();
            Log::info("TUGAS TERJADWAL SUKSES: {$jumlah} Campaign Promo beserta paketnya telah dinonaktifkan otomatis karena kadaluarsa.");
            
            $this->info("{$jumlah} Campaign Promo berhasil dinonaktifkan secara otomatis!");
        } else {
            $this->info('Tidak ada campaign promo yang kadaluarsa hari ini.');
        }
    }
}