<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\members; // Pastikan Model diawali huruf besar (standar Laravel): Member
use App\Models\Order;
use App\Models\MembershipPayment;
use App\Models\absen; // Pastikan Model: Absen

class DashboardOwnerController extends Controller
{
    public function dashboardOwner()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonthDate = Carbon::now()->subMonth();

        // 1. LOGIC PENDAPATAN
        $orderThisMonth = Order::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('payment_status', 'paid')->sum('total_payment');

        $membershipThisMonth = MembershipPayment::whereMonth('tanggal_transaksi', $now->month)
            ->whereYear('tanggal_transaksi', $now->year)
            ->where('status_pembayaran', 'completed')->sum('nominal');

        $totalPendapatan = $orderThisMonth + $membershipThisMonth;

        $orderLastMonth = Order::whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)
            ->where('payment_status', 'paid')->sum('total_payment');

        $membershipLastMonth = MembershipPayment::whereMonth('tanggal_transaksi', $lastMonthDate->month)
            ->whereYear('tanggal_transaksi', $lastMonthDate->year)
            ->where('status_pembayaran', 'completed')->sum('nominal');

        $totalPendapatanBulanLalu = $orderLastMonth + $membershipLastMonth;

        $percentageChange = 0;
        if ($totalPendapatanBulanLalu > 0) {
            $percentageChange = (($totalPendapatan - $totalPendapatanBulanLalu) / $totalPendapatanBulanLalu) * 100;
        } else {
            $percentageChange = $totalPendapatan > 0 ? 100 : 0;
        }

        // 2. LOGIC MEMBER & VISIT
        $totalMemberAktif = members::where('status', 'active')->count();
        $newMemberThisMonth = members::where('status', 'active')
            ->whereMonth('tanggal_daftar', $now->month)
            ->whereYear('tanggal_daftar', $now->year)->count();

        $visitToday = absen::whereDate('waktu_masuk', Carbon::today())->count();
        $totalVisitLast30Days = absen::where('waktu_masuk', '>=', Carbon::now()->subDays(30))->count();
        $averageDailyVisit = $totalVisitLast30Days > 0 ? ceil($totalVisitLast30Days / 30) : 0;

        $pendingMembership = MembershipPayment::where('status_pembayaran', 'pending')->sum('nominal');
        $countMembershipPending = MembershipPayment::where('status_pembayaran', 'pending')->count();
        $pendingOrder = Order::where('payment_status', 'pending')->sum('total_payment');
        $countOrderPending = Order::where('payment_status', 'pending')->count();

        $totalPending = $pendingMembership + $pendingOrder;
        $totalCountPending = $countMembershipPending + $countOrderPending;

        // 3. LOGIKA CHART
        $getChartData = function ($model, $dateCol, $range, $colSum = null) use ($currentYear, $currentMonth) {
            $data = [];
            $labels = [];
            $now = Carbon::now();

            if ($range == 'year') {
                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = Carbon::create()->month($i)->translatedFormat('M');
                    $data[$i] = 0;
                }
                $query = $model::selectRaw("MONTH($dateCol) as time_unit, " . ($colSum ? "SUM($colSum)" : "COUNT(*)") . " as total")
                    ->whereYear($dateCol, $currentYear);
            } elseif ($range == 'month') {
                $daysInMonth = $now->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $labels[] = strval($i);
                    $data[$i] = 0;
                }
                $query = $model::selectRaw("DAY($dateCol) as time_unit, " . ($colSum ? "SUM($colSum)" : "COUNT(*)") . " as total")
                    ->whereMonth($dateCol, $currentMonth)->whereYear($dateCol, $currentYear);
            } elseif ($range == 'week') {
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('d M');
                    $data[$date->format('Y-m-d')] = 0;
                }
                $query = $model::selectRaw("DATE($dateCol) as time_unit, " . ($colSum ? "SUM($colSum)" : "COUNT(*)") . " as total")
                    ->where($dateCol, '>=', Carbon::now()->subDays(6));
            } elseif ($range == 'day') { // Fix logic day (per jam)
                for ($i = 6; $i <= 23; $i++) {
                    $labels[] = sprintf("%02d:00", $i);
                    $data[$i] = 0;
                }
                $query = $model::selectRaw("HOUR($dateCol) as time_unit, " . ($colSum ? "SUM($colSum)" : "COUNT(*)") . " as total")
                    ->whereDate($dateCol, Carbon::today());
            }

            if ($colSum == 'total_payment')
                $query->where('payment_status', 'paid');
            if ($colSum == 'nominal')
                $query->where('status_pembayaran', 'completed');

            $results = $query->groupBy('time_unit')->get();
            foreach ($results as $row) {
                if (isset($data[$row->time_unit])) {
                    $data[$row->time_unit] = (int) $row->total;
                }
            }
            return ['labels' => array_values($labels), 'data' => array_values($data)];
        };

        $visitData = [
            'year' => $getChartData(absen::class, 'waktu_masuk', 'year'),
            'month' => $getChartData(absen::class, 'waktu_masuk', 'month'),
            'week' => $getChartData(absen::class, 'waktu_masuk', 'week'),
        ];
        $memberData = [
            'year' => $getChartData(members::class, 'created_at', 'year'),
            'month' => $getChartData(members::class, 'created_at', 'month'),
            'week' => $getChartData(members::class, 'created_at', 'week'),
        ];

        $chartDataRevenue = [];
        foreach (['year', 'month', 'week', 'day'] as $r) {
            $dOrder = $getChartData(Order::class, 'created_at', $r, 'total_payment');
            $dMember = $getChartData(MembershipPayment::class, 'tanggal_transaksi', $r, 'nominal');
            $total = array_map(function ($x, $y) {
                return $x + $y;
            }, $dOrder['data'], $dMember['data']);
            $chartDataRevenue[$r] = ['labels' => $dOrder['labels'], 'data' => $total];
        }

        $chartData = [
            'year' => ['labels' => $visitData['year']['labels'], 'visit' => $visitData['year']['data'], 'member' => $memberData['year']['data'], 'revenue' => $chartDataRevenue['year']['data']],
            'month' => ['labels' => $visitData['month']['labels'], 'visit' => $visitData['month']['data'], 'member' => $memberData['month']['data'], 'revenue' => $chartDataRevenue['month']['data']],
            'week' => ['labels' => $visitData['week']['labels'], 'visit' => $visitData['week']['data'], 'member' => $memberData['week']['data'], 'revenue' => $chartDataRevenue['week']['data']],
            'day' => ['labels' => $getChartData(absen::class, 'waktu_masuk', 'day')['labels'], 'visit' => $getChartData(absen::class, 'waktu_masuk', 'day')['data'], 'member' => $getChartData(members::class, 'created_at', 'day')['data'], 'revenue' => $chartDataRevenue['day']['data']]
        ];

        // 4. LOGIKA AKTIVITAS HARI INI
        $absenToday = absen::with('member')->whereDate('waktu_masuk', Carbon::today())->get()
            ->map(function ($item) {
                return [
                    'type' => 'visit',
                    'name' => $item->member->nama_lengkap ?? 'Member Tidak Dikenal',
                    'time' => $item->waktu_masuk,
                    'desc' => 'Check-in Harian',
                    'sort_time' => $item->waktu_masuk
                ];
            });

        $transToday = MembershipPayment::with('member')->whereDate('tanggal_transaksi', Carbon::today())
            ->where('status_pembayaran', 'completed')->get()
            ->map(function ($item) {
                return [
                    'type' => $item->jenis_transaksi == 'renewal' ? 'renewal' : ($item->jenis_transaksi == 'reactivation' ? 'reactivation' : 'membership'),
                    'name' => $item->member->nama_lengkap ?? 'Umum',
                    'time' => $item->created_at,
                    'desc' => str_contains(strtolower($item->keterangan ?? ''), 'promo') ? 'Registrasi Promo' : 'Transaksi Membership',
                    'sort_time' => $item->created_at
                ];
            });

        $todaysActivities = collect()->merge($absenToday)->merge($transToday)->sortByDesc('sort_time')->values();

        // ==========================================================
        // 5. PERBAIKAN MAPPING DATA UNTUK MENGHINDARI ERROR VIEW
        // ==========================================================
        $membershipCollection = MembershipPayment::with(['member', 'admin', 'paket'])->latest()->take(50)->get()
            ->map(function ($item) {
                return [
                    // Mapping Key disesuaikan dengan View ($trx['name'], $trx['source'], dll)
                    'name' => $item->member ? $item->member->nama_lengkap : 'Guest',
                    'id_members' => $item->nomor_invoice ?? '-', // Menggunakan Invoice sebagai ID di tampilan
                    'source' => 'membership', // PENTING: Key ini sebelumnya hilang
                    'type_label' => ucfirst($item->jenis_transaksi),
                    'admin' => $item->admin ? $item->admin->name : 'System',
                    'date' => $item->created_at,
                    'amount' => $item->nominal,
                    'status' => $item->status_pembayaran,
                    // Key tambahan untuk keperluan lain jika butuh
                    'raw_date' => $item->created_at,
                ];
            });

        $penjualanCollection = Order::with(['member', 'cashier'])->latest()->take(50)->get()
            ->map(function ($item) {
                return [
                    // Mapping Key disesuaikan dengan View
                    'name' => $item->member ? $item->member->nama_lengkap : 'Non-Member',
                    'id_members' => $item->invoice_code,
                    'source' => 'order', // PENTING: Key ini sebelumnya hilang
                    'type_label' => 'Penjualan',
                    'admin' => $item->cashier ? $item->cashier->name : 'Kasir',
                    'date' => $item->created_at,
                    'amount' => $item->total_payment,
                    'status' => $item->payment_status,
                    'raw_date' => $item->created_at,
                ];
            });

        // Gabungkan dan Sortir
        $laporanData = $membershipCollection->concat($penjualanCollection)->sortByDesc('raw_date');

        $recentTransactions = $laporanData->take(5);
        $pendingTransactions = Order::with('member')->where('payment_status', 'pending')->latest()->get();

        return view('owner.dashboardOwner', compact(
            'totalPendapatan',
            'percentageChange',
            'totalMemberAktif',
            'newMemberThisMonth',
            'visitToday',
            'averageDailyVisit',
            'chartData',
            'todaysActivities',
            'recentTransactions',
            'pendingTransactions',
            'totalPending',
            'totalCountPending',
            'laporanData'
        ));
    }
}