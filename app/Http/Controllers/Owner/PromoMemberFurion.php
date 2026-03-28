<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\members; // Pastikan huruf kapital jika modelnya Members
use App\Models\PaketMember;
use App\Models\CampaignPromo;
use App\Models\membershipPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\RiwayatBroadcast;
use App\Models\RiwayatBroadcastDetail;

class PromoMemberFurion extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('year', Carbon::now()->year);
        $statistikPromo = array_fill(1, 12, 0);

        $transaksiPromo = membershipPayment::select(
            DB::raw('MONTH(tanggal_transaksi) as bulan'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('tanggal_transaksi', $tahun)
            ->where('status_pembayaran', 'completed')
            ->whereHas('paket', function ($q) {
                $q->whereIn('jenis', ['promo', 'promo couple']);
            })
            ->groupBy('bulan')
            ->get();

        foreach ($transaksiPromo as $baris) {
            $statistikPromo[$baris->bulan] = (int) $baris->total;
        }

        if ($request->ajax()) {
            return response()->json([
                'promoStats' => array_values($statistikPromo),
                'year' => $tahun
            ]);
        }

        // AMBIL DATA CAMPAIGN & HITUNG PERFORMA
        $promos = CampaignPromo::with('paketMembers')->latest('id_campaign')->get();

        // [OPTIMASI] Ambil data broadcast sekaligus untuk semua campaign aktif (Mencegah N+1 Query di Blade)
        $campaignNames = $promos->pluck('nama_campaign')->toArray();
        $broadcasts = RiwayatBroadcast::with('details')
            ->whereIn('nama_campaign', $campaignNames)
            ->get()
            ->groupBy('nama_campaign');

        foreach ($promos as $promo) {
            // Kumpulkan ID Paket yang terikat dengan Campaign ini
            $idPaketArray = $promo->paketMembers->pluck('id_paket');

            // Hitung metrik performa
            $promo->registrasi_count = membershipPayment::whereIn('paket_id', $idPaketArray)
                ->where('jenis_transaksi', 'membership')
                ->where('status_pembayaran', 'completed')
                ->count();

            $promo->perpanjang_count = membershipPayment::whereIn('paket_id', $idPaketArray)
                ->where('jenis_transaksi', 'renewal')
                ->where('status_pembayaran', 'completed')
                ->count();

            $promo->reaktivasi_count = membershipPayment::whereIn('paket_id', $idPaketArray)
                ->where('jenis_transaksi', 'reactivation')
                ->where('status_pembayaran', 'completed')
                ->count();

            // [SUNTIK DATA BROADCAST KE OBJECT PROMO]
            $latestBroadcast = isset($broadcasts[$promo->nama_campaign])
                ? $broadcasts[$promo->nama_campaign]->sortByDesc('created_at')->first()
                : null;

            if ($latestBroadcast) {
                $details = $latestBroadcast->details;
                $berhasil = $details->whereIn('status', ['sent', 'delivered', 'read'])->count();
                $gagal = $details->where('status', 'failed')->count();
                $antrian = $details->where('status', 'pending')->count();
                $total = $latestBroadcast->total_target;

                $terproses = $total - $antrian;
                $persentase = $total > 0 ? round(($terproses / $total) * 100) : 0;
                if ($persentase > 100)
                    $persentase = 100;

                $promo->broadcast_data = (object) [
                    'ada' => true,
                    'total' => $total,
                    'berhasil' => $berhasil,
                    'gagal' => $gagal,
                    'antrian' => $antrian,
                    'terproses' => $terproses,
                    'persentase' => $persentase,
                ];
            } else {
                $promo->broadcast_data = (object) ['ada' => false];
            }
        }

        $promoStats = $statistikPromo;
        $year = $tahun;

        $members = members::all();
        $totalOptOut = Members::where('is_opt_out', 1)->count();

        return view('Owner.PromoMemberFurion', compact('promos', 'promoStats', 'year', 'totalOptOut', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'gambar_promo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'promos' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $path = null;
            if ($request->hasFile('gambar_promo')) {
                $path = $request->file('gambar_promo')->store('promo-images', 'public');
            }

            $promoTerpilih = collect($request->promos)->filter(function ($promo) {
                return isset($promo['is_selected']) && $promo['is_selected'] == 1;
            });

            if ($promoTerpilih->isEmpty()) {
                throw new \Exception('Pilih minimal satu durasi promo (Reguler/Couple)!');
            }

            $campaign = CampaignPromo::create([
                'nama_campaign' => $request->nama_paket,
                'gambar_banner' => $path,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => 'aktif',
            ]);

            foreach ($promoTerpilih as $promo) {
                $labelJenis = ($promo['jenis'] === 'promo') ? 'Reguler' : 'Couple';
                $namaPaketLengkap = $request->nama_paket . ' (' . $promo['durasi'] . ') - ' . $labelJenis;

                PaketMember::create([
                    'campaign_id' => $campaign->id_campaign,
                    'nama_paket' => $namaPaketLengkap,
                    'jenis' => $promo['jenis'],
                    'durasi' => $promo['durasi'],
                    'harga' => $promo['harga'],
                    'status' => 'aktif',
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Campaign Promo berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $campaign = CampaignPromo::findOrFail($id);

        $request->validate([
            'nama_campaign' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'gambar_promo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = [
                'nama_campaign' => $request->nama_campaign,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ];

            if ($request->hasFile('gambar_promo')) {
                if ($campaign->gambar_banner && Storage::disk('public')->exists($campaign->gambar_banner)) {
                    Storage::disk('public')->delete($campaign->gambar_banner);
                }
                $path = $request->file('gambar_promo')->store('promo-images', 'public');
                $data['gambar_banner'] = $path;
            }

            $campaign->update($data);

            foreach ($campaign->paketMembers as $paket) {
                $labelJenis = ($paket->jenis === 'promo') ? 'Reguler' : 'Couple';
                $paket->update([
                    'nama_paket' => $request->nama_campaign . ' (' . $paket->durasi . ') - ' . $labelJenis
                ]);
            }

            return redirect()->back()->with('success', 'Detail Campaign berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $campaign = CampaignPromo::findOrFail($id);

        if ($campaign->status == 'aktif') {
            $campaign->status = 'nonaktif';
            $pesan = 'dinonaktifkan';
        } else {
            $campaign->status = 'aktif';
            $pesan = 'diaktifkan';
        }

        $campaign->save();

        PaketMember::where('campaign_id', $id)->update(['status' => $campaign->status]);

        return redirect()->back()->with('success', "Status campaign berhasil $pesan!");
    }

    public function pushNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
            'target' => 'required|in:active,expired,promo_users,all,manual',
            'manual_number' => 'required_if:target,manual',
        ]);

        Log::info("Admin memulai Broadcast. Target: {$request->target}, Judul: {$request->subject}");

        try {
            $nomorTujuan = [];

            // --- LOGIKA PENGAMBILAN NOMOR ---
            if ($request->target === 'manual') {
                $nomorMentah = explode(',', $request->manual_number);
                foreach ($nomorMentah as $mentah) {
                    $no = preg_replace('/[^0-9]/', '', trim($mentah));
                    $no08 = $no;
                    if (substr($no, 0, 2) == '62')
                        $no08 = '0' . substr($no, 2);
                    elseif (substr($no, 0, 1) == '8')
                        $no08 = '0' . $no;
                    if (substr($no, 0, 2) == '08')
                        $no = '628' . substr($no, 2);
                    elseif (substr($no, 0, 1) == '8')
                        $no = '62' . $no;

                    if (substr($no, 0, 2) == '62' && strlen($no) > 9) {
                        $sudahBerhenti = members::where(function ($q) use ($no, $no08) {
                            $q->where('no_telepon', $no)->orWhere('no_telepon', $no08);
                        })->where('is_opt_out', true)->exists();
                        if (!$sudahBerhenti)
                            $nomorTujuan[] = $no;
                    }
                }
            } else {
                $query = members::query()->where('is_opt_out', false);
                if ($request->target === "active")
                    $query->where('status', 'active');
                elseif ($request->target === "expired")
                    $query->where('status', '!=', 'active');
                elseif ($request->target === "promo_users") {
                    $query->whereHas('paket', function ($q) {
                        $q->where('jenis', 'promo');
                    });
                }
                $anggota = $query->get();
                if ($anggota->isEmpty())
                    return back()->with('error', 'Tidak ada member yang sesuai kriteria.');
                foreach ($anggota as $m) {
                    $no = preg_replace('/[^0-9]/', '', $m->no_telepon);
                    if (substr($no, 0, 2) == '08')
                        $no = '628' . substr($no, 2);
                    if (!empty($no) && substr($no, 0, 2) == '62')
                        $nomorTujuan[] = $no;
                }
            }

            $nomorTujuan = array_unique($nomorTujuan);
            if (empty($nomorTujuan))
                return back()->with('error', 'Tidak ada nomor telepon valid.');

            $token = env('FONNTE_TOKEN');
            if (!$token)
                return back()->with('error', 'Token Fonnte belum disetting!');

            $broadcast = RiwayatBroadcast::create([
                'nama_campaign' => $request->subject,
                'total_target' => count($nomorTujuan),
                'status' => 'berjalan'
            ]);

            // 2. Kirim dan Simpan Detail per Nomor
            foreach ($nomorTujuan as $no) {
                try {
                    $response = Http::withoutVerifying()
                        ->withHeaders(['Authorization' => $token])
                        ->post('https://api.fonnte.com/send', [
                            'target' => $no,
                            'message' => $request->message,
                            'countryCode' => '62',
                            'delay' => (string) rand(15, 30),
                        ]);

                    $resData = $response->json();

                    $processId = null;
                    if (isset($resData['id']) && is_array($resData['id']) && count($resData['id']) > 0) {
                        $processId = $resData['id'][0];
                    }

                    RiwayatBroadcastDetail::create([
                        'riwayat_broadcast_id' => $broadcast->id,
                        'no_wa' => $no,
                        'fonnte_message_id' => $processId,
                        'status' => $processId ? 'pending' : 'failed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("Gagal kirim ke $no: " . $e->getMessage());
                }
            }

            return back()->with('success', "Proses Broadcast Berjalan! Sinkronisasi antrian berhasil dibuat.");

        } catch (\Exception $e) {
            Log::error("Error saat push notification: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function progressBroadcast()
    {
        $broadcast = RiwayatBroadcast::latest()->first();

        if (!$broadcast || ($broadcast->status === 'selesai' && $broadcast->updated_at->diffInMinutes(now()) > 60)) {
            return response()->json(['aktif' => false]);
        }

        $details = RiwayatBroadcastDetail::where('riwayat_broadcast_id', $broadcast->id)->get();

        $pending = $details->where('status', 'pending')->count();
        $sent = $details->where('status', 'sent')->count();
        $delivered = $details->where('status', 'delivered')->count();
        $read = $details->where('status', 'read')->count();
        $failed = $details->where('status', 'failed')->count();

        $terproses = $broadcast->total_target - $pending;
        $persentase = $broadcast->total_target > 0 ? round(($terproses / $broadcast->total_target) * 100) : 0;
        if ($persentase > 100)
            $persentase = 100;

        return response()->json([
            'aktif' => true,
            'nama_campaign' => $broadcast->nama_campaign,
            'total' => $broadcast->total_target,
            'terproses' => $terproses,
            'persentase' => $persentase,
            'status_global' => $broadcast->status,
            'detail' => [
                'pending' => $pending,
                'sent' => $sent,
                'delivered' => $delivered,
                'read' => $read,
                'failed' => $failed
            ]
        ]);
    }
}