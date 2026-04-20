<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\members;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BroadcastController extends Controller
{
    public function index()
    {
        $logs = $this->getBroadcastLogs();

        return view('Owner.Broadcast', compact('logs'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target_audience' => 'required|in:all,active,expired',
            'message' => 'required|string|min:5',
        ]);

        $query = members::with('paket');

        if ($request->target_audience == 'active') {
            $query->whereDate('tanggal_selesai', '>=', Carbon::now());
        } elseif ($request->target_audience == 'expired') {
            $query->whereDate('tanggal_selesai', '<', Carbon::now());
        }

        $members = $query->whereNotNull('no_telepon')->get();

        if ($members->isEmpty()) {
            return back()->with('error', 'Tidak ada member ditemukan untuk kategori ini.');
        }

        $token = env('FONNTE_TOKEN');
        if (!$token) {
            return back()->with('error', 'Token Fonnte belum dikonfigurasi di .env');
        }

        $successCount = 0;
        $failCount = 0;

        Log::info("Manual Broadcast: Memulai pengiriman ke " . $members->count() . " member.");

        foreach ($members as $member) {
            
            $targetPhone = $this->formatPhoneNumber($member->no_telepon);
            
            $namaMember = $member->nama_member ?? $member->nama_lengkap ?? 'Member';
            $namaPaket = $member->paket->nama_paket ?? '-';
            $tglExp = $member->tanggal_selesai ? Carbon::parse($member->tanggal_selesai)->format('d-m-Y') : '-';

            $personalMessage = str_replace(
                ['{name}', '{paket}', '{expired}'],
                [$namaMember, $namaPaket, $tglExp],
                $request->message
            );

            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->withoutVerifying()->post('https://api.fonnte.com/send', [
                    'target' => $targetPhone,
                    'message' => $personalMessage,
                    'countryCode' => '62',
                ]);

                $resBody = $response->json();

                if ($response->successful() && ($resBody['status'] ?? false)) {
                    $successCount++;
                    Log::info("Manual Broadcast Sukses: {$namaMember} ({$targetPhone})");
                } else {
                    $failCount++;
                    $reason = $resBody['reason'] ?? 'Unknown Error';
                    Log::error("Manual Broadcast Gagal: {$namaMember} ({$targetPhone}). Reason: {$reason}");
                }

                sleep(15); 

            } catch (\Exception $e) {
                $failCount++;
                Log::error("Manual Broadcast Exception: {$namaMember}. Error: " . $e->getMessage());
            }
        }

        Log::info("Manual Broadcast Selesai. Sukses: {$successCount}, Gagal: {$failCount}");

        return back()->with('success', "Broadcast selesai! Berhasil: $successCount, Gagal: $failCount");
    }

    private function getBroadcastLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $file = file($logFile);
            $file = array_reverse($file);
            $count = 0;

            foreach ($file as $line) {
                if (strpos($line, 'Scheduler:') !== false || 
                    strpos($line, 'Broadcast') !== false || 
                    strpos($line, 'Fonnte') !== false) {

                    preg_match('/^\[(?<date>.*)\] \w+\.(?<level>\w+): (?<message>.*)/', $line, $matches);

                    if (!empty($matches)) {
                        $logs[] = [
                            'date' => Carbon::parse($matches['date'])->diffForHumans(),
                            'raw_date' => $matches['date'],
                            'level' => $matches['level'], // INFO, ERROR
                            'message' => $matches['message']
                        ];
                    }
                    $count++;
                }

                if ($count >= 20) break; // Batasi 20 log terakhir
            }
        }

        return $logs;
    }

    // Fungsi Helper: Ubah 08 jadi 628
    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (substr($number, 0, 2) === '08') {
            return '62' . substr($number, 1);
        }

        if (substr($number, 0, 1) === '8') {
            return '62' . $number;
        }

        return $number;
    }
}