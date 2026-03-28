<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\MembershipPayment;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'bulan');
        $kategori = $request->input('kategori', 'all');
        $dateInput = $request->input('date', Carbon::now()->format('Y-m-d'));

        $selectedDate = Carbon::parse($dateInput);
        $now = Carbon::now();

        $navLabel = '';
        $prevLink = '';
        $nextLink = '';
        $disableNext = false;
        $startDate = null;
        $endDate = null;
        $dbFormat = '';
        $loopLimit = 0;

        switch ($periode) {
            case 'tahun':
                $startDate = $selectedDate->copy()->startOfYear();
                $endDate = $selectedDate->copy()->endOfYear();
                $navLabel = $selectedDate->format('Y');
                $prevLink = $selectedDate->copy()->subYear()->format('Y-m-d');
                $nextLink = $selectedDate->copy()->addYear()->format('Y-m-d');
                $disableNext = $selectedDate->year >= $now->year;
                $dbFormat = '%m';
                $loopLimit = 12;
                break;

            case 'seluruh':
                $firstMember = MembershipPayment::min('created_at');
                $firstOrder = Order::min('created_at');
                $baseDate = $firstMember ? Carbon::parse($firstMember) : ($firstOrder ? Carbon::parse($firstOrder) : $now);
                $startDate = $baseDate->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $navLabel = "Semua Waktu";
                $disableNext = true;
                $dbFormat = '%b %Y';
                break;

            case 'hari':
                $startDate = $selectedDate->copy()->startOfDay();
                $endDate = $selectedDate->copy()->endOfDay();
                $navLabel = $selectedDate->translatedFormat('d F Y');
                $prevLink = $selectedDate->copy()->subDay()->format('Y-m-d');
                $nextLink = $selectedDate->copy()->addDay()->format('Y-m-d');
                $disableNext = $selectedDate->isToday();
                $dbFormat = '%H';
                $loopLimit = 24;
                break;

            case 'minggu':
                $startDate = $selectedDate->copy()->startOfWeek();
                $endDate = $selectedDate->copy()->endOfWeek();
                $navLabel = $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y');
                $prevLink = $selectedDate->copy()->subWeek()->format('Y-m-d');
                $nextLink = $selectedDate->copy()->addWeek()->format('Y-m-d');
                $disableNext = $endDate->gte($now);
                $dbFormat = '%d';
                $loopLimit = 7;
                break;

            case 'bulan':
            default:
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
                $navLabel = $selectedDate->translatedFormat('F Y');
                $prevLink = $selectedDate->copy()->subMonth()->format('Y-m-d');
                $nextLink = $selectedDate->copy()->addMonth()->format('Y-m-d');
                $disableNext = $selectedDate->format('Y-m') >= $now->format('Y-m');
                $dbFormat = '%d';
                $loopLimit = $selectedDate->daysInMonth;
                break;
        }

        // ==========================================================
        // 3. QUERY DATA
        // ==========================================================

        $membershipGraph = collect([]);
        $penjualanGraph = collect([]);
        $totalMembership = 0;
        $totalPenjualan = 0;
        $membershipCollection = collect([]);
        $penjualanCollection = collect([]);

        // --- A. QUERY MEMBERSHIP ---
        if ($kategori == 'all' || $kategori == 'membership') {

            // Grafik Membership
            $membershipGraph = MembershipPayment::selectRaw("DATE_FORMAT(created_at, '{$dbFormat}') as unit, SUM(nominal) as total")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('unit')
                ->pluck('total', 'unit');

            // Total Card Membership
            $totalMembership = MembershipPayment::whereBetween('created_at', [$startDate, $endDate])
                ->sum('nominal');

            // List Tabel Membership
            $membershipCollection = MembershipPayment::with(['member', 'admin', 'paket'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($item) {
                    $rawType = strtolower($item->jenis_transaksi);
                    $labelTipe = match ($rawType) {
                        'renewal', 'perpanjang' => 'Perpanjang',
                        'reactivation', 'reaktivasi' => 'Reaktivasi',
                        'membership', 'registrasi', 'new' => 'Registrasi',
                        default => 'Registrasi',
                    };

                    return [
                        'tanggal_raw' => $item->created_at,
                        'tanggal' => $item->created_at,
                        'invoice' => $item->nomor_invoice ?? '-',
                        'member' => $item->member->nama_lengkap ?? 'Guest',
                        'jenis' => 'Membership',
                        'tipe_label' => $labelTipe,
                        'keterangan' => 'Membership (' . ($item->paket->nama_paket ?? '-') . ')',
                        'status' => $item->status_pembayaran,
                        'nominal' => $item->nominal,
                        'details' => []
                    ];
                });
        }

        // --- B. QUERY PENJUALAN (ORDER) ---
        if ($kategori == 'all' || $kategori == 'penjualan') {

            $penjualanGraph = Order::selectRaw("DATE_FORMAT(created_at, '{$dbFormat}') as unit, SUM(total_payment) as total")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('unit')
                ->pluck('total', 'unit');

            $totalPenjualan = Order::whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_payment');

            $penjualanCollection = Order::with(['member', 'items.produk'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($item) {
                    $detailBelanja = $item->items->map(function ($detail) {
                        return [
                            'nama_produk' => $detail->produk->nama_produk ?? 'Produk Dihapus',
                            'qty' => $detail->qty,
                            'harga' => $detail->price,
                            'total' => $detail->total
                        ];
                    });

                    return [
                        'tanggal_raw' => $item->created_at,
                        'tanggal' => $item->created_at,
                        'invoice' => $item->invoice_code,
                        'member' => $item->member->nama_lengkap ?? 'Non-Member',
                        'jenis' => 'Penjualan',
                        'tipe_label' => 'Penjualan',
                        'keterangan' => 'Pembelian Produk',
                        'status' => $item->payment_status,
                        'nominal' => $item->total_payment,
                        'details' => $detailBelanja
                    ];
                });
        }

        // --- 4. HITUNG PENDING (PERBAIKAN LOGIC) ---
        $pendingM = 0;
        $pendingP = 0;

        if ($kategori == 'all' || $kategori == 'membership') {
            $pendingM = MembershipPayment::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status_pembayaran', ['pending', 'unpaid', 'Pending', 'Unpaid', 'menunggu'])
                ->sum('nominal');
        }

        if ($kategori == 'all' || $kategori == 'penjualan') {
            $pendingP = Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('payment_status', ['pending', 'unpaid', 'Pending', 'Unpaid', 'menunggu'])
                ->sum('total_payment');
        }

        $totalPending = $pendingM + $pendingP;

        $subStart = ($periode == 'tahun') ? $startDate->copy()->subYear() : $startDate->copy()->subMonth();
        $subEnd = ($periode == 'tahun') ? $endDate->copy()->subYear() : $endDate->copy()->subMonth();

        $laluM = 0;
        $laluP = 0;
        if ($kategori == 'all' || $kategori == 'membership') {
            $laluM = MembershipPayment::whereBetween('created_at', [$subStart, $subEnd])
                ->sum('nominal');
        }
        if ($kategori == 'all' || $kategori == 'penjualan') {
            $laluP = Order::whereBetween('created_at', [$subStart, $subEnd])
                ->sum('total_payment');
        }
        $totalPemasukanBulanLalu = $laluM + $laluP;


        // ==========================================================
        // 4. MAPPING DATA KE GRAFIK
        // ==========================================================
        $chartLabels = [];
        $chartValues = [];

        if ($periode == 'seluruh') {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $key = $current->format('M Y');
                $chartLabels[] = $key;
                $valM = $membershipGraph->get($key, 0);
                $valP = $penjualanGraph->get($key, 0);
                $chartValues[] = (int) $valM + (int) $valP;
                $current->addMonth();
            }
        } else {
            $startLoop = ($periode == 'hari') ? 0 : 1;
            for ($i = $startLoop; $i <= ($periode == 'hari' ? 23 : $loopLimit); $i++) {
                $key = str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($periode == 'tahun') {
                    $chartLabels[] = Carbon::create()->month($i)->translatedFormat('M');
                } elseif ($periode == 'hari') {
                    $chartLabels[] = $key . ':00';
                } elseif ($periode == 'minggu') {
                    $dateCursor = $startDate->copy()->addDays($i - 1);
                    $chartLabels[] = $dateCursor->translatedFormat('D, d M');
                    $key = $dateCursor->format('d');
                } else {
                    $chartLabels[] = $i . ' ' . $selectedDate->format('M');
                }
                $valM = $membershipGraph->get($key, 0);
                $valP = $penjualanGraph->get($key, 0);
                $chartValues[] = (int) $valM + (int) $valP;
            }
        }

        $laporanData = $membershipCollection->concat($penjualanCollection)->sortByDesc('tanggal_raw')->values();

        return view('owner.laporankeuangan', [
            'laporanData' => $laporanData,
            'totalPemasukan' => $totalMembership + $totalPenjualan,
            'totalMembership' => $totalMembership,
            'totalPenjualan' => $totalPenjualan,
            'totalPending' => $totalPending,
            'totalPemasukanBulanLalu' => $totalPemasukanBulanLalu,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'navLabel' => $navLabel,
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
            'disableNext' => $disableNext,
            'periode' => $periode,
            'kategori' => $kategori
        ]);
    }
}