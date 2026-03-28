<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\members;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KirimBroadcastMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kirim-broadcast-member';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim notifikasi WhatsApp otomatis via Fonnte ke member (H-7, H-3, H-1)';

    protected $token;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai proses broadcast otomatis...');
        Log::info('Scheduler: Memulai broadcast member...');

        $this->token = env('FONNTE_TOKEN');

        if (empty($this->token)) {
            $this->error('❌ Token FONNTE_TOKEN belum diatur di file .env');
            Log::error('Scheduler: Token Fonnte kosong.');
            return Command::FAILURE;
        }

        $this->info('🔍 Mengecek member H-7...');
        $membersH7 = $this->getMembersByDaysRemaining(7);
        $this->processBroadcast($membersH7, 7);

        $this->info('🔍 Mengecek member H-3...');
        $membersH3 = $this->getMembersByDaysRemaining(3);
        $this->processBroadcast($membersH3, 3);

        $this->info('🔍 Mengecek member H-1...');
        $membersH1 = $this->getMembersByDaysRemaining(1);
        $this->processBroadcast($membersH1, 1);

        $this->info('✅ Broadcast selesai dijalankan.');
        Log::info('Scheduler: Broadcast selesai.');

        return Command::SUCCESS;
    }

    private function getMembersByDaysRemaining($days)
    {
        return members::where('status', 'active')
            ->whereDate('tanggal_selesai', Carbon::now()->addDays($days))
            ->with('paket')
            ->get();
    }

    private function processBroadcast($members, $sisaHari)
    {
        if ($members->isEmpty()) {
            $this->line("   - Tidak ada member untuk H-{$sisaHari}");
            return;
        }

        foreach ($members as $member) {
            $tglSelesai = Carbon::parse($member->tanggal_selesai)->translatedFormat('d F Y');
            $namaPaket = $member->paket->nama_paket ?? 'Membership';
            $namaMember = $member->nama_member ?? $member->nama_lengkap;
            $nomorHp = $member->nomor_telepon ?? $member->no_telepon;

            // Template Pesan
            $pesan = "Halo Kak *{$namaMember}*! 👋\n\n";
            $pesan .= "Gak kerasa nih, *{$namaPaket}* kakak di *Furion Gym Jambi* tinggal *{$sisaHari} hari lagi* lho (Jatuh tempo: {$tglSelesai}). 😱\n\n";
            $pesan .= "Jangan biarkan semangat latihanmu kendor ya! Yuk perpanjang sekarang biar goals body idaman kakak makin cepat tercapai! 🚀💪🔥\n";
            $pesan .= "\n_Note: Pesan ini dikirim otomatis oleh sistem_";

            $result = $this->sendFonnte($nomorHp, $pesan);

            Log::info("Broadcast ke {$namaMember} (H-{$sisaHari}): " . json_encode($result));

            if ($result['status']) {
                $this->info("   ✅ Sukses kirim ke {$namaMember} ({$nomorHp})");
            } else {
                $this->error("   ❌ Gagal kirim ke {$namaMember} ({$nomorHp}). Alasan: " . $result['reason']);
            }
            sleep(2);
        }
    }

    private function sendFonnte($target, $message)
    {
        if (!$target)
            return ['status' => false, 'reason' => 'Nomor Kosong'];

        $target = $this->formatWa($target);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            return ['status' => false, 'reason' => "Curl Error: $curlError"];
        }

        $json = json_decode($response, true);

        if ($httpCode == 200) {
            if (isset($json['status']) && $json['status'] == true) {
                return ['status' => true, 'reason' => 'OK'];
            } else {
                $reason = $json['reason'] ?? 'Unknown Fonnte Error';
                return ['status' => false, 'reason' => $reason];
            }
        }

        return ['status' => false, 'reason' => "HTTP Error: $httpCode"];
    }

    private function formatWa($nomor)
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        if (substr($nomor, 0, 2) == '08') {
            return '62' . substr($nomor, 1);
        }
        if (substr($nomor, 0, 2) == '62') {
            return $nomor;
        }
        return '62' . $nomor;
    }
}