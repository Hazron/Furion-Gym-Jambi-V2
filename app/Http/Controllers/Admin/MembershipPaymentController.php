<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipPayment;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MembershipPaymentController extends Controller
{
    public function view()
    {
        return view('Admin.MembershipPayment');
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Query Dasar dengan Relasi
            // Tambahkan ->orderBy('tanggal_transaksi', 'desc') agar yang terbaru ada di atas
            $query = MembershipPayment::with(['member', 'paket'])
                ->select('membership_payment.*')
                ->orderBy('tanggal_transaksi', 'desc'); // <--- TAMBAHKAN BARIS INI

            // --- FILTER TANGGAL (Tetap Sama) ---
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_transaksi', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tanggal_transaksi', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y H:i');
                })
                ->editColumn('nominal', function ($row) {
                    return 'Rp ' . number_format($row->nominal, 0, ',', '.');
                })
                ->addColumn('nama_member', function ($row) {
                    return $row->member ? $row->member->nama_lengkap : 'Member Terhapus';
                })
                ->addColumn('nama_paket', function ($row) {
                    return $row->paket ? $row->paket->nama_paket : '-';
                })
                ->editColumn('jenis_transaksi', function ($row) {
                    // Badge warna warni sesuai tipe
                    if ($row->jenis_transaksi == 'membership')
                        return '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">New Member</span>';
                    if ($row->jenis_transaksi == 'renewal')
                        return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Perpanjangan</span>';
                    if ($row->jenis_transaksi == 'reactivation')
                        return '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold">Re-aktifasi</span>';
                    return $row->jenis_transaksi;
                })
                ->rawColumns(['jenis_transaksi'])
                ->make(true);
        }
    }

    public function getStats(Request $request)
    {
        try {
            $query = MembershipPayment::query();

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Coba debug dulu
            $test = $query->first();
            if (!$test) {
                return response()->json([
                    'error' => 'Tidak ada data payment ditemukan.',
                    'debug' => $request->all()
                ], 500);
            }

            $totalPendapatan = $query->sum('nominal') ?? 0;
            $totalTransaksi = $query->count() ?? 0;

            $todayIncome = MembershipPayment::whereDate('created_at', Carbon::today())
                ->sum('nominal') ?? 0;

            return response()->json([
                'total_pendapatan' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
                'total_transaksi' => $totalTransaksi,
                'today_income' => 'Rp ' . number_format($todayIncome, 0, ',', '.'),
                'debug' => $test   // ← menunjukkan record pertama
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
