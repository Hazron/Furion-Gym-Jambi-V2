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

        // 2. Setup Query Dasar
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
            if ($targetDate->isToday())
                $disableNext = true;

        } elseif ($periode == 'minggu') {
            $startOfWeek = $targetDate->copy()->startOfWeek();
            $endOfWeek = $targetDate->copy()->endOfWeek();
            $query->whereBetween('tanggal_transaksi', [$startOfWeek, $endOfWeek]);

            $navLabel = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');
            $prevLink = $targetDate->copy()->subWeek()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addWeek()->format('Y-m-d');
            if ($targetDate->copy()->endOfWeek()->isFuture())
                $disableNext = true;

        } elseif ($periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $targetDate->month)
                ->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->translatedFormat('F Y');
            $prevLink = $targetDate->copy()->subMonth()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addMonth()->format('Y-m-d');
            if ($targetDate->isSameMonth(now()))
                $disableNext = true;

        } elseif ($periode == 'tahun') {
            $query->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->format('Y');
            $prevLink = $targetDate->copy()->subYear()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addYear()->format('Y-m-d');
            if ($targetDate->year >= now()->year)
                $disableNext = true;

        } elseif ($periode == 'seluruh') {
            $navLabel = "Semua Waktu";
            $disableNext = true;
        }

        $laporanData = $query->latest('tanggal_transaksi')->get();

        $countRegistrasi = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'membership')->count();
        $countRenewal = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'renewal')->count();
        $countReactivation = $laporanData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();

        if ($periode == 'bulan') {
            $daysInMonth = $targetDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $currentDayStr = $targetDate->copy()->day($d)->format('Y-m-d');
                $chartLabels[] = $targetDate->copy()->day($d)->format('d M');

                $dayData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') == $currentDayStr);

                // Gunakan strtolower juga di sini
                $chartRegistrasi[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'membership')->count();
                $chartRenewal[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'renewal')->count();
                $chartReactivation[] = $dayData->filter(fn($i) => strtolower($i->jenis_transaksi) == 'reaktivasi')->count();
            }
        }
        // ... sisa kode lainnya ...

        // 5. Paket Terlaris (Top 5)
        $paketStats = $laporanData->groupBy(function ($item) {
            return $item->nama_paket_snapshot ?? ($item->paket->nama_paket ?? 'Lainnya');
        })->map->count()->sortDesc()->take(5);

        $chartPaketLabels = $paketStats->keys()->toArray();
        $chartPaketValues = $paketStats->values()->toArray();

        // 6. Logika Grafik (Tanpa Query di dalam Loop)
        $chartLabels = [];
        $chartRegistrasi = [];
        $chartRenewal = [];
        $chartReactivation = [];

        if ($periode == 'bulan') {
            $daysInMonth = $targetDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $currentDayStr = $targetDate->copy()->day($d)->format('Y-m-d');
                $chartLabels[] = $targetDate->copy()->day($d)->format('d M');

                // Filter dari koleksi yang sudah ada di memory (Cepat)
                $dayData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') == $currentDayStr);

                $chartRegistrasi[] = $dayData->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = $dayData->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = $dayData->where('jenis_transaksi', 'reaktivasi')->count();
            }
        } elseif ($periode == 'tahun') {
            for ($m = 1; $m <= 12; $m++) {
                $chartLabels[] = Carbon::create()->month($m)->translatedFormat('M');

                $monthData = $laporanData->filter(fn($item) => Carbon::parse($item->tanggal_transaksi)->month == $m);

                $chartRegistrasi[] = $monthData->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = $monthData->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = $monthData->where('jenis_transaksi', 'reaktivasi')->count();
            }
        } else {
            // Default 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('d M');

                $dayData = MembershipPayment::whereDate('tanggal_transaksi', $date->format('Y-m-d'))->get();

                $chartRegistrasi[] = $dayData->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = $dayData->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = $dayData->where('jenis_transaksi', 'reaktivasi')->count();
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