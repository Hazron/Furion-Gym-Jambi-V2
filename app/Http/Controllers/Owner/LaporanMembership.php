<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MembershipPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanMembership extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulan');
        // Tangkap tanggal dari request, default ke hari ini
        $dateParam = $request->get('date', now()->format('Y-m-d'));
        $targetDate = Carbon::parse($dateParam);

        $query = MembershipPayment::with('member', 'paket');

        // 1. LOGIKA FILTER & NAVIGASI
        $navLabel = "";
        $prevLink = "";
        $nextLink = "";
        $disableNext = false;

        if ($periode == 'hari') {
            $query->whereDate('tanggal_transaksi', now());
        } elseif ($periode == 'minggu') {
            $query->whereBetween('tanggal_transaksi', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode == 'bulan') {
            // Filter berdasarkan bulan yang dipilih
            $query->whereMonth('tanggal_transaksi', $targetDate->month)
                ->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->translatedFormat('F Y');
            $prevLink = $targetDate->copy()->subMonth()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addMonth()->format('Y-m-d');

            if ($targetDate->copy()->startOfMonth()->isSameMonth(now()->startOfMonth()))
                $disableNext = true;

        } elseif ($periode == 'tahun') {
            // Filter berdasarkan tahun yang dipilih
            $query->whereYear('tanggal_transaksi', $targetDate->year);

            $navLabel = $targetDate->format('Y');
            $prevLink = $targetDate->copy()->subYear()->format('Y-m-d');
            $nextLink = $targetDate->copy()->addYear()->format('Y-m-d');

            if ($targetDate->year >= now()->year)
                $disableNext = true;
        }

        $laporanData = $query->latest('tanggal_transaksi')->get();

        // Summary Counts (Gunakan data yang sudah difilter di atas)
        $countRegistrasi = $laporanData->where('jenis_transaksi', 'membership')->count();
        $countRenewal = $laporanData->where('jenis_transaksi', 'renewal')->count();
        $countReactivation = $laporanData->where('jenis_transaksi', 'reactivation')->count();

        // Paket Terlaris
        $paketStats = $laporanData->groupBy(function ($item) {
            return $item->nama_paket_snapshot ?? ($item->paket->nama_paket ?? 'Lainnya');
        })->map->count()->sortDesc()->take(5);

        $chartPaketLabels = $paketStats->keys()->toArray();
        $chartPaketValues = $paketStats->values()->toArray();

        // 2. LOGIKA GRAFIK DINAMIS (Mengikuti Target Date)
        $chartLabels = [];
        $chartRegistrasi = [];
        $chartRenewal = [];
        $chartReactivation = [];

        if ($periode == 'bulan') {
            // Tampilkan per Hari dalam bulan tersebut
            $daysInMonth = $targetDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $currentDay = $targetDate->copy()->day($d);
                $chartLabels[] = $currentDay->format('d M');
                $baseQuery = MembershipPayment::whereDate('tanggal_transaksi', $currentDay->format('Y-m-d'));
                $chartRegistrasi[] = (clone $baseQuery)->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = (clone $baseQuery)->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = (clone $baseQuery)->where('jenis_transaksi', 'reactivation')->count();
            }
        } elseif ($periode == 'tahun') {
            // Tampilkan per Bulan dalam tahun tersebut
            for ($m = 1; $m <= 12; $m++) {
                $currentMonth = $targetDate->copy()->month($m);
                $chartLabels[] = $currentMonth->translatedFormat('M');
                $baseQuery = MembershipPayment::whereYear('tanggal_transaksi', $targetDate->year)->whereMonth('tanggal_transaksi', $m);
                $chartRegistrasi[] = (clone $baseQuery)->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = (clone $baseQuery)->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = (clone $baseQuery)->where('jenis_transaksi', 'reactivation')->count();
            }
        } else {
            // Default Fallback (Sama seperti kode Anda)
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('d M');
                $baseQuery = MembershipPayment::whereDate('tanggal_transaksi', $date->format('Y-m-d'));
                $chartRegistrasi[] = (clone $baseQuery)->where('jenis_transaksi', 'membership')->count();
                $chartRenewal[] = (clone $baseQuery)->where('jenis_transaksi', 'renewal')->count();
                $chartReactivation[] = (clone $baseQuery)->where('jenis_transaksi', 'reactivation')->count();
            }
        }

        return view('owner.laporanMembership', compact(
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