<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MembershipPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanMembership extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Parameter
        $periode = $request->get('periode', 'bulan');
        $dateParam = $request->get('date', now()->format('Y-m-d'));
        $targetDate = Carbon::parse($dateParam);

        // 2. Setup Query Dasar (Eager Loading untuk performa)
        $query = MembershipPayment::with(['member', 'paket']);

        // 3. Logika Filter & Navigasi
        $navLabel = "";
        $prevLink = "";
        $nextLink = "";
        $disableNext = false;

        if ($periode == 'hari') {
            $query->whereDate('tanggal_transaksi', $targetDate->format('Y-m-d'));
            $navLabel = $targetDate->translatedFormat('d F Y');
            $prevLink = $targetDate->copy()->subDay()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addDay()->format('Y-m-d');
            if ($targetDate->isToday()) $disableNext = true;

        } elseif ($periode == 'minggu') {
            $startOfWeek = $targetDate->copy()->startOfWeek();
            $endOfWeek = $targetDate->copy()->endOfWeek();
            $query->whereBetween('tanggal_transaksi', [$startOfWeek, $endOfWeek]);

            $navLabel = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');
            $prevLink = $targetDate->copy()->subWeek()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addWeek()->format('Y-m-d');
            if ($targetDate->copy()->endOfWeek()->isFuture()) $disableNext = true;

        } elseif ($periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $targetDate->month)
                  ->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->translatedFormat('F Y');
            $prevLink = $targetDate->copy()->subMonth()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addMonth()->format('Y-m-d');
            if ($targetDate->isSameMonth(now())) $disableNext = true;

        } elseif ($periode == 'tahun') {
            $query->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->format('Y');
            $prevLink = $targetDate->copy()->subYear()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addYear()->format('Y-m-d');
            if ($targetDate->year >= now()->year) $disableNext = true;

        } elseif ($periode == 'seluruh') {
            $navLabel = "Semua Waktu";
            $disableNext = true;
        }

        // 4. EKSEKUSI QUERY UTAMA (Hanya 1x panggil ke Database)
        $laporanData = $query->latest('tanggal_transaksi')->get();

        // 5. Hitung Summary Cards (Menggunakan terminologi yang sudah dikoreksi)
        $countRegistrasi = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'registrasi')->count();
        $countRenewal = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'perpanjang')->count();
        $countReactivation = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();

        // 6. Paket Terlaris (Top 5)
        $paketStats = $laporanData->groupBy(function ($item) {
            return $item->nama_paket_snapshot ?? ($item->paket->nama_paket ?? 'Lainnya');
        })->map->count()->sortDesc()->take(5);

        $chartPaketLabels = $paketStats->keys()->toArray();
        $chartPaketValues = $paketStats->values()->toArray();

        // 7. Logika Grafik Tren Pertumbuhan (Tanpa Query di dalam Loop)
        $chartLabels = [];
        $chartRegistrasi = [];
        $chartRenewal = [];
        $chartReactivation = [];

        if ($periode == 'bulan') {
            $daysInMonth = $targetDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $currentDayStr = $targetDate->copy()->day($d)->format('Y-m-d');
                $chartLabels[] = $targetDate->copy()->day($d)->format('d M');

                // Filter dari memory collection ($laporanData)
                $dayData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') == $currentDayStr);

                $chartRegistrasi[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'registrasi')->count();
                $chartRenewal[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'perpanjang')->count();
                $chartReactivation[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();
            }
        } elseif ($periode == 'tahun') {
            for ($m = 1; $m <= 12; $m++) {
                $chartLabels[] = Carbon::create()->month($m)->translatedFormat('M');

                $monthData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->month == $m && Carbon::parse($item->tanggal_transaksi)->year == $targetDate->year);

                $chartRegistrasi[] = $monthData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'registrasi')->count();
                $chartRenewal[] = $monthData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'perpanjang')->count();
                $chartReactivation[] = $monthData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();
            }
        } elseif ($periode == 'minggu') {
            $startOfWeek = $targetDate->copy()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $currentDay = $startOfWeek->copy()->addDays($i);
                $chartLabels[] = $currentDay->translatedFormat('D'); // Menampilkan nama hari (Sen, Sel, dll)
                
                $dayData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') == $currentDay->format('Y-m-d'));

                $chartRegistrasi[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'registrasi')->count();
                $chartRenewal[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'perpanjang')->count();
                $chartReactivation[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();
            }
        } elseif ($periode == 'hari') {
            // Tampilkan 1 titik data saja untuk hari tersebut
            $chartLabels[] = $targetDate->translatedFormat('d M Y');
            $chartRegistrasi[] = $countRegistrasi;
            $chartRenewal[] = $countRenewal;
            $chartReactivation[] = $countReactivation;
        } else {
            // Fallback (Seluruh Waktu) - Dikelompokkan per bulan/tahun
            $groupedData = $laporanData->groupBy(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('M Y'))->reverse();
            foreach ($groupedData as $monthYear => $group) {
                $chartLabels[] = $monthYear;
                $chartRegistrasi[] = $group->filter(fn($i) => strtolower($i->jenis_transaksi) == 'registrasi')->count();
                $chartRenewal[] = $group->filter(fn($i) => strtolower($i->jenis_transaksi) == 'perpanjang')->count();
                $chartReactivation[] = $group->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();
            }
        }

        return view('Owner.LaporanMembership', compact(
            'laporanData',
            'countRegistrasi',
            'countRenewal',
            'countReactivation',
            'periode',
            'chartLabels',
            'chartRegistrasi',
            'chartRenewal',
            'chartReactivation',
            'chartPaketLabels',
            'chartPaketValues',
            'navLabel',
            'prevLink',
            'nextLink',
            'disableNext'
        ));
    }
}