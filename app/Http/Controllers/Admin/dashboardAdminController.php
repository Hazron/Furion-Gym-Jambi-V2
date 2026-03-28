<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Models\MembershipPayment;
use App\Models\Order;
use App\Models\Absen; // Pastikan Model Absen huruf besar/kecil sesuai file Anda
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function dashboardAdmin()
    {
        // ================================================================
        // 1. STATISTIK DASAR (KARTU ATAS)
        // ================================================================
        $totalMembers    = Members::count();
        $activeMembers   = Members::where('status', 'active')->count();
        $inactiveMembers = Members::where('status', 'inactive')->count();

        $todayVisits     = 0;
        // ================================================================
        // 2. DATA CHART (MEMBER & REVENUE)
        // ================================================================

        // A. MEMBER CHART DATA (30 Hari Terakhir)
        $rawMember30 = Members::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->pluck('total', 'date');

        // B. REVENUE CHART DATA (Order + Membership)
        // 1. Revenue Hari Ini (Per Jam)
        $rawOrderToday = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_payment) as total'))
            ->whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid')
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $rawMemberPayToday = MembershipPayment::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(nominal) as total'))
            ->whereDate('created_at', Carbon::today())
            ->where('status_pembayaran', 'completed')
            ->groupBy('hour')
            ->pluck('total', 'hour');

        // 2. Revenue 30 Hari Terakhir (Per Hari)
        $rawOrder30 = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_payment) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->pluck('total', 'date');

        $rawMemberPay30 = MembershipPayment::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(nominal) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('status_pembayaran', 'completed')
            ->groupBy('date')
            ->pluck('total', 'date');

        // --- MENYUSUN ARRAY AWAL (Member & Revenue) ---

        // 1. Chart Data: 7 Hari Terakhir
        $chart7Days = [
            'labels'  => [],
            'Members' => [],
            'revenue' => [],
            'visits'  => [] // Kita siapkan slot kosong
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayLabel = Carbon::now()->subDays($i)->isoFormat('ddd'); // Sen, Sel

            $chart7Days['labels'][]  = $dayLabel;
            $chart7Days['Members'][] = $rawMember30[$date] ?? 0;

            $revOrder = $rawOrder30[$date] ?? 0;
            $revMemb  = $rawMemberPay30[$date] ?? 0;
            $chart7Days['revenue'][] = $revOrder + $revMemb;
        }

        // 2. Chart Data: 30 Hari Terakhir
        $chart30Days = [
            'labels'  => [],
            'Members' => [],
            'revenue' => [],
            'visits'  => []
        ];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $label = Carbon::now()->subDays($i)->format('d M');

            $chart30Days['labels'][]  = $label;
            $chart30Days['Members'][] = $rawMember30[$date] ?? 0;

            $revOrder = $rawOrder30[$date] ?? 0;
            $revMemb  = $rawMemberPay30[$date] ?? 0;
            $chart30Days['revenue'][] = $revOrder + $revMemb;
        }

        // 3. Chart Data: Hari Ini (Per Jam)
        $chartToday = [
            'labels'  => [],
            'revenue' => [],
            'Members' => [], // Kosongkan saja untuk per jam
            'visits'  => []
        ];

        for ($i = 7; $i <= 22; $i++) {
            $hourLabel = sprintf("%02d:00", $i);
            $chartToday['labels'][] = $hourLabel;

            $revOrder = $rawOrderToday[$i] ?? 0;
            $revMemb  = $rawMemberPayToday[$i] ?? 0;
            $chartToday['revenue'][] = $revOrder + $revMemb;
        }

        // ================================================================
        // 3. Aktivitas Member (GABUNGAN PAYMENT + ABSEN)
        // ================================================================

        // A. Ambil Data Payment (Regis & Renew)
        $paymentActivities = MembershipPayment::with('member')
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->map(function ($item) {
                $jenis = strtolower($item->jenis_transaksi);
                $type  = 'other';
                $color = 'gray';
                $desc  = $item->keterangan ?? 'Transaksi Membership';

                if (str_contains($jenis, 'membership')) {
                    $type  = 'regis';
                    $color = 'blue';
                    $desc  = 'Registrasi Member Baru';
                } elseif (str_contains($jenis, 'renew')) {
                    $type  = 'renew';
                    $color = 'green';
                    $desc  = 'Perpanjangan Paket';
                } elseif (str_contains($jenis, 'reactive') || str_contains($jenis, 'aktif')) {
                    $type  = 'reactive';
                    $color = 'orange';
                    $desc  = 'Reaktivasi Member';
                }

                return [
                    'type'  => $type,
                    'name'  => $item->member->nama_lengkap ?? 'Member Terhapus',
                    'desc'  => $desc,
                    'time'  => $item->created_at,
                    'color' => $color
                ];
            });

        // B. Ambil Data Absen (Visit) -- BAGIAN BARU
        $visitActivities = Absen::with('member')
            ->whereDate('waktu_masuk', Carbon::today())
            ->get()
            ->map(function ($item) {
                return [
                    'type'  => 'visit',           // Tipe khusus untuk filter tab
                    'name'  => $item->member->nama_lengkap ?? 'Member Terhapus',
                    'desc'  => 'Melakukan Absensi (Check-in)',
                    'time'  => $item->waktu_masuk, // Gunakan waktu_masuk
                    'color' => 'purple'           // Sesuai class CSS bg-purple-600
                ];
            });

        // C. Gabungkan Kedua Data & Urutkan Waktu Terbaru
        $activities = $paymentActivities->concat($visitActivities)->sortByDesc('time')->values();
        // Tabel Pending Payment
        $pendingPayments = Order::with('member')
            ->where('payment_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // ================================================================
        // 4. Hitungan Total Revenue (Card Stats)
        // ================================================================
        $revenueDay = Order::whereDate('created_at', Carbon::today())->where('payment_status', 'paid')->sum('total_payment')
            + MembershipPayment::whereDate('created_at', Carbon::today())->where('status_pembayaran', 'completed')->sum('nominal');

        $revenueWeek  = array_sum($chart7Days['revenue']);
        $revenueMonth = array_sum($chart30Days['revenue']);

        $revenueTotal = Order::where('payment_status', 'paid')->sum('total_payment')
            + MembershipPayment::where('status_pembayaran', 'completed')->sum('nominal');

        // ================================================================
        // 5. DATA ABSEN / VISIT (PERBAIKAN UTAMA DISINI)
        // ================================================================

        // Statistik Visit Hari Ini (Untuk Card)
        $visitHariIni = Absen::whereDate('waktu_masuk', Carbon::today())->count();
        $todayVisits  = $visitHariIni; // Update dummy variable di atas

        // Statistik Rata-rata Visit
        $totalVisitBulanIni = Absen::whereMonth('waktu_masuk', Carbon::now()->month)
            ->whereYear('waktu_masuk', Carbon::now()->year)
            ->count();
        $hariBerjalan = Carbon::now()->day;
        $rataRata = $hariBerjalan > 0 ? round($totalVisitBulanIni / $hariBerjalan, 1) : 0;

        // --- PENGISIAN DATA CHART VISIT (INJECT KE ARRAY YANG SUDAH ADA) ---
        $visitTodayData = [];
        for ($i = 7; $i <= 22; $i++) {
            $visitTodayData[] = Absen::whereDate('waktu_masuk', Carbon::today())
                ->whereTime('waktu_masuk', '>=', sprintf("%02d:00:00", $i))
                ->whereTime('waktu_masuk', '<=', sprintf("%02d:59:59", $i))
                ->count();
        }
        $chartToday['visits'] = $visitTodayData; // <--- INJECT DISINI

        // B. Visit 7 Hari Terakhir -> Masuk ke $chart7Days['visits']
        $visitWeekData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $visitWeekData[] = Absen::whereDate('waktu_masuk', $date->format('Y-m-d'))->count();
        }
        $chart7Days['visits'] = $visitWeekData; // <--- INJECT DISINI

        // C. Visit 30 Hari Terakhir -> Masuk ke $chart30Days['visits']
        $visitMonthData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $visitMonthData[] = Absen::whereDate('waktu_masuk', $date->format('Y-m-d'))->count();
        }
        $chart30Days['visits'] = $visitMonthData; // <--- INJECT DISINI

        // ================================================================
        // 6. RETURN VIEW
        // ================================================================
        return view('Admin.dashboardAdmin', compact(
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'todayVisits',
            'chart7Days',
            'chart30Days',
            'chartToday',
            'activities',
            'pendingPayments',
            'revenueDay',
            'revenueWeek',
            'revenueMonth',
            'revenueTotal',
            'visitHariIni',
            'rataRata'
        ));
    }
}
