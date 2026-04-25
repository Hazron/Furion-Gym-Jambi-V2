<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Models\MembershipPayment;
use App\Models\Order;
use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function dashboardAdmin()
    {
        $memberStats = Members::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $activeMembers   = $memberStats['active'] ?? 0;
        $inactiveMembers = $memberStats['inactive'] ?? 0;
        $totalMembers    = $activeMembers + $inactiveMembers;

        $today = Carbon::today();
        $sub30Days = Carbon::now()->subDays(30);

        $rawMember30 = Members::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $sub30Days)
            ->groupBy('date')
            ->pluck('total', 'date');

        $rawOrderToday = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_payment) as total'))
            ->whereDate('created_at', $today)->where('payment_status', 'paid')
            ->groupBy('hour')->pluck('total', 'hour');

        $rawMemberPayToday = MembershipPayment::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(nominal) as total'))
            ->whereDate('created_at', $today)->where('status_pembayaran', 'completed')
            ->groupBy('hour')->pluck('total', 'hour');

        $rawOrder30 = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_payment) as total'))
            ->where('created_at', '>=', $sub30Days)->where('payment_status', 'paid')
            ->groupBy('date')->pluck('total', 'date');

        $rawMemberPay30 = MembershipPayment::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(nominal) as total'))
            ->where('created_at', '>=', $sub30Days)->where('status_pembayaran', 'completed')
            ->groupBy('date')->pluck('total', 'date');

        $rawVisitToday = Absen::select(DB::raw('HOUR(waktu_masuk) as hour'), DB::raw('count(*) as total'))
            ->whereDate('waktu_masuk', $today)
            ->groupBy('hour')->pluck('total', 'hour');

        $rawVisit30 = Absen::select(DB::raw('DATE(waktu_masuk) as date'), DB::raw('count(*) as total'))
            ->where('waktu_masuk', '>=', $sub30Days)
            ->groupBy('date')->pluck('total', 'date');

        $chart7Days = ['labels' => [], 'members' => [], 'revenue' => [], 'visits' => []];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $date = $dateObj->format('Y-m-d');
            
            $chart7Days['labels'][]  = $dateObj->isoFormat('ddd');
            $chart7Days['members'][] = $rawMember30[$date] ?? 0;
            $chart7Days['revenue'][] = ($rawOrder30[$date] ?? 0) + ($rawMemberPay30[$date] ?? 0);
            $chart7Days['visits'][]  = $rawVisit30[$date] ?? 0;
        }

        $chart30Days = ['labels' => [], 'members' => [], 'revenue' => [], 'visits' => []];
        for ($i = 29; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $date = $dateObj->format('Y-m-d');
            
            $chart30Days['labels'][]  = $dateObj->format('d M');
            $chart30Days['members'][] = $rawMember30[$date] ?? 0;
            $chart30Days['revenue'][] = ($rawOrder30[$date] ?? 0) + ($rawMemberPay30[$date] ?? 0);
            $chart30Days['visits'][]  = $rawVisit30[$date] ?? 0;
        }

        $chartToday = ['labels' => [], 'revenue' => [], 'members' => [], 'visits' => []];
        for ($i = 7; $i <= 22; $i++) {
            $chartToday['labels'][]  = sprintf("%02d:00", $i);
            $chartToday['revenue'][] = ($rawOrderToday[$i] ?? 0) + ($rawMemberPayToday[$i] ?? 0);
            $chartToday['visits'][]  = $rawVisitToday[$i] ?? 0;
        }

        $paymentActivities = MembershipPayment::with('member:id,nama_lengkap')
            ->whereDate('created_at', $today)
            ->get()
            ->map(function ($item) {
                $jenis = strtolower($item->jenis_transaksi);
                $type  = 'other';
                $color = 'gray';
                $desc  = $item->keterangan ?? 'Transaksi Membership';

                if (str_contains($jenis, 'membership')) {
                    $type = 'regis'; $color = 'blue'; $desc = 'Registrasi Member Baru';
                } elseif (str_contains($jenis, 'renew')) {
                    $type = 'renew'; $color = 'green'; $desc = 'Perpanjangan Paket';
                } elseif (str_contains($jenis, 'reactive') || str_contains($jenis, 'aktif')) {
                    $type = 'reactive'; $color = 'orange'; $desc = 'Reaktivasi Member';
                }

                return [
                    'type'  => $type,
                    'name'  => $item->member->nama_lengkap ?? 'Member Terhapus',
                    'desc'  => $desc,
                    'time'  => $item->created_at,
                    'color' => $color
                ];
            });

        $visitActivities = Absen::with('member:id,nama_lengkap')
            ->whereDate('waktu_masuk', $today)
            ->get()
            ->map(function ($item) {
                return [
                    'type'  => 'visit',
                    'name'  => $item->member->nama_lengkap ?? 'Member Terhapus',
                    'desc'  => 'Melakukan Absensi (Check-in)',
                    'time'  => $item->waktu_masuk,
                    'color' => 'purple'
                ];
            });

        $activities = $paymentActivities->concat($visitActivities)->sortByDesc('time')->values();

        $pendingPayments = Order::with('member:id,nama_lengkap')
            ->where('payment_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $revenueDay   = array_sum($chartToday['revenue']);
        $revenueWeek  = array_sum($chart7Days['revenue']);
        $revenueMonth = array_sum($chart30Days['revenue']);

        $revenueTotal = Order::where('payment_status', 'paid')->sum('total_payment')
            + MembershipPayment::where('status_pembayaran', 'completed')->sum('nominal');

        $visitHariIni = array_sum($chartToday['visits']);
        $todayVisits  = $visitHariIni;

        $totalVisitBulanIni = array_sum($chart30Days['visits']);
        $hariBerjalan = Carbon::now()->day;
        $rataRata = $hariBerjalan > 0 ? round($totalVisitBulanIni / $hariBerjalan, 1) : 0;

        return view('Admin.dashboardAdmin', compact(
            'totalMembers', 'activeMembers', 'inactiveMembers', 'todayVisits',
            'chart7Days', 'chart30Days', 'chartToday',
            'activities', 'pendingPayments',
            'revenueDay', 'revenueWeek', 'revenueMonth', 'revenueTotal',
            'visitHariIni', 'rataRata'
        ));
    }
}