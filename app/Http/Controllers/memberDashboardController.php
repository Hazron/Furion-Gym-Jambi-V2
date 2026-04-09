<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members; 
use App\Models\Absen;   
use App\Models\MembershipPayment;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class memberDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'member_id' => 'required|string',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer'
        ]);

        // 2. Cari Data Member
        $id = strtoupper($request->member_id);
        $member = members::where('id_members', $id)->first();

        if (!$member) {
            return redirect('/')->with('error', 'ID Member tidak ditemukan!');
        }

        // 3. Pengaturan Tanggal (Navigasi Kalender)
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        // 4. Hitung Sisa Masa Aktif Paket
        $today = Carbon::now();
        $expiredDate = Carbon::parse($member->tanggal_selesai);
        $remainingDays = (int) ceil($today->diffInDays($expiredDate, false));

        // 5. Ambil Data Absensi (Sesuai Bulan & Tahun yang Dipilih)
        $monthlyAttendances = absen::where('member_id', $member->id_members)
            ->whereMonth('waktu_masuk', $month)
            ->whereYear('waktu_masuk', $year)
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        // Array tanggal latihan untuk kalender view
        $trainingDates = $monthlyAttendances->map(function ($item) {
            return Carbon::parse($item->waktu_masuk)->day;
        })->toArray();

        // 6. LOGIKA TARGET LATIHAN (DINAMIS)
        $totalSessions = count($trainingDates);

        // Ambil level dari database, default ke 'beginner' jika kosong
        $level = $member->target_latihan ?? 'beginner';

        switch ($level) {
            case 'advance':
                $targetSessions = 20; 
                $levelLabel = "Advance";
                break;
            case 'intermediate':
                $targetSessions = 16;
                $levelLabel = "Intermediate";
                break;
            case 'beginner':
            default:
                $targetSessions = 12;
                $levelLabel = "Beginner";
                break;
        }

        // 7. Hitung Persentase Konsistensi
        $progressPercent = ($targetSessions > 0) ? min(($totalSessions / $targetSessions) * 100, 100) : 0;
        $displayPercent = ($targetSessions > 0) ? round(($totalSessions / $targetSessions) * 100) : 0;

        // 8. Generate Feedback Cerdas
        if ($displayPercent >= 100) {
            $feedback = "🔥 LUAR BIASA! Kamu berlatih layaknya atlet " . ucfirst($levelLabel) . "!";
            $feedbackColor = "bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.7)]";
        } elseif ($displayPercent >= 75) {
            $feedback = "✨ KEEP PUSHING! Sedikit lagi target " . ucfirst($levelLabel) . " tercapai.";
            $feedbackColor = "bg-brand-yellow";
        } elseif ($displayPercent >= 50) {
            $feedback = "👍 GOOD JOB! Kamu sudah separuh jalan di level ini.";
            $feedbackColor = "bg-blue-400";
        } else {
            $feedback = "⚠️ AYO FOKUS! Kejar target " . ucfirst($levelLabel) . " kamu ($targetSessions sesi).";
            $feedbackColor = "bg-red-500";
        }

        // 9. AMBIL RIWAYAT TRANSAKSI (BARU)
        // Pastikan nama kolom relasi ('member_id') sesuai dengan yang ada di tabel membership_payments kamu
        $riwayatTransaksi = MembershipPayment::with('paket')
            ->where('member_id', $member->id_members)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // 10. Return View
        return view('member', compact(
            'member',
            'monthlyAttendances',
            'trainingDates',
            'totalSessions',
            'targetSessions', 
            'level',          
            'progressPercent',
            'displayPercent', 
            'feedback',
            'feedbackColor',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'remainingDays',
            'riwayatTransaksi' // <-- Variabel riwayat dilempar ke Blade
        ));
    }

    public function downloadStory(Request $request, $member_id)
    {
        // ... (Kode downloadStory sama seperti sebelumnya) ...
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $member = members::where('id_members', $member_id)->first();
        if (!$member) {
            return abort(404, 'Member not found');
        }

        $currentMonth = Carbon::createFromDate($year, $month, 1);

        $monthlyAttendances = absen::where('member_id', $member->id_members)
            ->whereMonth('waktu_masuk', $month)
            ->whereYear('waktu_masuk', $year)
            ->get();

        $trainingDates = $monthlyAttendances->map(function ($item) {
            return Carbon::parse($item->waktu_masuk)->day;
        })->toArray();

        $totalSessions = count($trainingDates);
        $targetSessions = 12;

        $progressPercent = min(($totalSessions / $targetSessions) * 100, 100);

        if ($totalSessions >= 12) {
            $feedback = "🔥 LUAR BIASA! Konsistensi kamu top banget.";
        } elseif ($totalSessions >= 6) {
            $feedback = "👍 BAGUS! Pertahankan ritme latihanmu.";
        } else {
            $feedback = "⚠️ AYO SEMANGAT! Jangan biarkan ototmu Kendor.";
        }

        $htmlContent = view('story_preview', compact(
            'member',
            'currentMonth',
            'trainingDates',
            'totalSessions',
            'progressPercent',
            'feedback'
        ))->render();

        $monthName = $currentMonth->format('F-Y');
        $fileName = "furion-story-{$member_id}-{$monthName}.png";
        $path = public_path('storage/stories/' . $fileName);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        Browsershot::html($htmlContent)
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->windowSize(414, 736)
            ->deviceScaleFactor(2)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'member_id' => 'required',
            'target_latihan' => 'required|in:beginner,intermediate,advance',
        ]);

        $member = members::where('id_members', $request->member_id)->first();

        if ($member) {
            $member->update([
                'target_latihan' => $request->target_latihan
            ]);
            return redirect()->back()->with('success', 'Target latihan berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Member tidak ditemukan.');
    }

    public function downloadRiwayatPDF($member_id)
    {
        $member = Members::where('id_members', $member_id)->first();
        
        if (!$member) {
            return abort(404, 'Member tidak ditemukan');
        }

        $riwayatTransaksi = MembershipPayment::with('paket')
            ->where('member_id', $member->id_members)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Buat nama file
        $fileName = 'Riwayat_Transaksi_' . $member->id_members . '_' . date('Ymd') . '.pdf';

        $pdf = Pdf::loadView('pdf.riwayat_transaksi_pdf', compact('member', 'riwayatTransaksi'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($fileName);
    }

    public function downloadTransaksiSinglePDF($id)
    {
        $transaksi = MembershipPayment::with(['member', 'paket'])->findOrFail($id);
        
        $fileName = 'Struk_Furion_' . $transaksi->member->id_members . '_' . date('Ymd', strtotime($transaksi->tanggal_transaksi)) . '.pdf';

        $pdf = Pdf::loadView('pdf.transaksi_single_pdf', compact('transaksi'));
        
        $pdf->setPaper('A5', 'landscape'); 

        // Download file PDF
        return $pdf->download($fileName);
    }
}