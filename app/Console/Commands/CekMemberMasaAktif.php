<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\members;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CekMemberMasaAktif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cek-member-masa-aktif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek member yang tanggal selesainya sudah lewat dan mengubah status menjadi inactive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredMembers = members::where('status', 'active')
            ->whereDate('tanggal_selesai', '<', Carbon::today())
            ->update(['status' => 'inactive']);

        if ($expiredMembers > 0) {
            Log::info("Scheduler Berjalan: Berhasil menonaktifkan {$expiredMembers} member yang masa aktifnya habis.");
            $this->info("Berhasil menonaktifkan {$expiredMembers} member.");
        } else {
            Log::info("Scheduler Berjalan: Tidak ada member yang expired hari ini.");
            $this->info("Tidak ada member yang expired hari ini.");
        }
    }
}
