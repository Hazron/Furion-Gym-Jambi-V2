<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\members; // Pastikan nama model huruf kapital jika di file-nya kapital (Members)
use App\Models\RiwayatBroadcast;
use App\Models\RiwayatBroadcastDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    // =======================================================
    // 1. FUNGSI UNTUK INBOX (Balasan Member seperti "STOP")
    // =======================================================
    public function handleInbox(Request $request)
    {
        $sender = $request->input('sender'); // Contoh: 6289628275663
        $message = strtolower(trim($request->input('message', '')));

        if ($message === 'stop' || $message === 'berhenti') {

            $cleanPhone = preg_replace('/[^0-9]/', '', $sender);

            $lastDigits = substr($cleanPhone, -9);

            $member = members::where('no_telepon', 'LIKE', '%' . $lastDigits)
                ->first();

            if ($member) {
                $member->update(['is_opt_out' => 1]); // Gunakan integer 1 untuk boolean di DB

                Log::info("🚫 OPT-OUT SUCCESS: Member {$member->nama_member} ({$sender})");

                $this->kirimBalasanStop($sender);
            } else {
                Log::warning("⚠️ OPT-OUT FAILED: Nomor {$sender} mengirim STOP tapi tidak terdaftar di database.");
            }
        }

        return response()->json(['status' => true]);
    }

    // =======================================================
    // 2. FUNGSI UNTUK WEBHOOK STATUS DARI FONNTE
    // =======================================================
    public function handleStatus(Request $request)
    {
        Log::info("📥 RAW WEBHOOK FONNTE STATUS: ", $request->all());

        // Ambil kunci-kunci dari payload
        $stateId = $request->input('stateid'); // Contoh: 3EB024243F3250A2094010
        $processId = $request->input('id');    // Contoh: 146012637
        $rawStatus = strtolower($request->input('state') ?? $request->input('status') ?? '');
        $nomorTarget = $request->input('target') ?? $request->input('phone') ?? $request->input('recipient');

        // Fonnte terkadang mengirim status dalam bentuk angka (1=sent, 2=delivered, 3=read, 4=failed)
        $statusMap = [
            '0' => 'pending',
            '1' => 'sent',
            '2' => 'delivered',
            '3' => 'read',
            '4' => 'failed'
        ];
        $status_kirim = $statusMap[$rawStatus] ?? $rawStatus;

        if (!$status_kirim) {
            return response()->json(['status' => 'success']);
        }

        $detail = null;

        if ($processId && is_numeric($processId)) {
            $detail = RiwayatBroadcastDetail::where('fonnte_message_id', $processId)->first();

            // JIKA KETEMU, TIMPA ID ANGKA DENGAN ID HURUF!
            if ($detail && $stateId) {
                $detail->fonnte_message_id = $stateId; // Update ke 3EB0...
                $detail->save();
                Log::info("🔄 SINKRONISASI ID: Angka {$processId} -> Huruf {$stateId} (WA: {$detail->no_wa})");
            }
        }

        elseif ($stateId && !is_numeric($stateId)) {
            $detail = RiwayatBroadcastDetail::where('fonnte_message_id', $stateId)->first();
        }
        elseif ($nomorTarget) {
            $formattedPhone = $this->formatPhone($nomorTarget);
            $detail = RiwayatBroadcastDetail::whereIn('no_wa', [$formattedPhone, $nomorTarget])
                ->whereIn('status', ['pending', 'sent', 'delivered'])
                ->latest()
                ->first();
        }

        if ($detail) {
            if ($detail->status === 'read' && in_array($status_kirim, ['sent', 'delivered'])) {
            } else {
                $detail->update(['status' => $status_kirim]);
                Log::info("✅ UPDATE PROGRESS: Pesan (WA: {$detail->no_wa}) -> {$status_kirim}");
            }

            $broadcastUtama = RiwayatBroadcast::find($detail->riwayat_broadcast_id);
            if ($broadcastUtama && $broadcastUtama->status !== 'selesai') {
                $broadcastUtama->update(['status' => 'berjalan']);

                $sisaPending = RiwayatBroadcastDetail::where('riwayat_broadcast_id', $detail->riwayat_broadcast_id)
                    ->whereNotIn('status', ['sent', 'delivered', 'read', 'failed'])
                    ->count();

                if ($sisaPending == 0) {
                    $broadcastUtama->update(['status' => 'selesai']);
                    Log::info("🎉 BROADCAST SELESAI 100%");
                }
            }
        } else {
            Log::warning("❌ STATUS GAGAL: Data tidak ditemukan di database untuk webhook ini (StateID: {$stateId}).");
        }

        return response()->json(['status' => 'success']);
    }

    // =======================================================
    // FUNGSI-FUNGSI BANTUAN (HELPERS)
    // =======================================================
    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            return '62' . substr($phone, 1);
        }
        return $phone;
    }

    private function kirimBalasanStop($target)
    {
        try {
            Http::withoutVerifying()
                ->withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => "Anda telah berhasil berhenti berlangganan info promo Furion Gym Jambi. Kami tidak akan mengirimkan pesan broadcast lagi kepada Anda. Terima kasih.",
                    'delay' => (string) rand(15, 30),
                ]);
        } catch (\Exception $e) {
            Log::error("Gagal mengirim balasan STOP ke {$target}: " . $e->getMessage());
        }
    }
}