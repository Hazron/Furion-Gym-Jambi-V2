<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\members;
use App\Models\membershipPayment;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class MemberFurionController extends Controller
{

    public function index(Request $request)
    {
        $now = Carbon::now();

        if ($request->ajax()) {
            $data = members::with(['paket', 'promo', 'membershipPayments.paket'])
                ->select('members.*')
                ->latest('updated_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lengkap', function ($row) {
                    $initials = strtoupper(substr($row->nama_lengkap, 0, 2));
                    return '
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-9 w-9"> <div class="h-9 w-9 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center">
                                <span class="text-xs font-bold text-blue-600">' . $initials . '</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-semibold text-gray-800">' . $row->nama_lengkap . '</div>
                            <div class="text-[11px] text-gray-500 font-medium">ID: <span class="tracking-wide text-gray-400">' . $row->id_members . '</span></div>
                        </div>
                    </div>';
                })
                ->addColumn('paket_members', function ($row) {
                    if ($row->paket)
                        return '<span class="text-blue-600 font-medium">' . $row->paket->nama_paket . '</span>';
                    elseif ($row->promo)
                        return '<span class="text-purple-600 font-medium">' . $row->promo->nama_paket . ' <span class="text-xs bg-purple-100 px-1 rounded">PROMO</span></span>';
                    return '<span class="text-gray-400">-</span>';
                })
                ->addColumn('sisa_waktu', function ($row) {
                    $formattedDate = $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
                    if (!$row->tanggal_selesai || $row->status == 'inactive') {
                        return '
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-xs font-bold text-gray-700 tracking-wide">' . $formattedDate . '</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-gray-500 border border-red-200">Masa Aktif Habis</span>
                        </div>';
                    }

                    $sisaHari = Carbon::now()->diffInDays(Carbon::parse($row->tanggal_selesai), false);
                    $sisaHari = ceil($sisaHari);

                    if ($sisaHari < 0) {
                        $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                        $badgeText = 'Expired';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    } elseif ($sisaHari <= 3) {
                        $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                        $badgeText = $sisaHari . ' Hari Lagi';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    } else {
                        $badgeClass = 'bg-green-50 text-green-600 border-green-100';
                        $badgeText = $sisaHari . ' Hari Lagi';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    }

                    return '
                    <div class="flex flex-col items-start gap-1">
                        <span class="text-xs font-bold text-gray-700 tracking-wide">' . $formattedDate . '</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border ' . $badgeClass . '">' . $icon . $badgeText . '</span>
                    </div>';
                })

                ->addColumn('aksi', function ($row) {
                    $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    $iconMata = '<svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
                    $detailButton = '<button onclick="openDetailModal(' . $jsonData . ')" class="group flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 border border-blue-100 shadow-sm" title="Lihat Detail">' . $iconMata . '</button>';

                    return '<div class="flex justify-end items-center gap-1">' . $detailButton . '</div>';
                })
                ->editColumn('tanggal_daftar', function ($row) {
                    return $row->tanggal_daftar ? date('d M Y', strtotime($row->tanggal_daftar)) : '-';
                })
                ->editColumn('tanggal_selesai', function ($row) {
                    return $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
                })
                ->rawColumns(['aksi', 'status', 'sisa_waktu', 'nama_lengkap', 'paket_members'])
                ->make(true);
        }


        // Logic untuk Card Statistik 
        $totalMember = members::count();
        $newMemberThisMonth = members::whereMonth('created_at', now()->month)->count();
        $memberAktif = members::where('status', 'active')->count();
        $retentionRate = $totalMember > 0 ? round(($memberAktif / $totalMember) * 100, 1) : 0;
        $expiredMember = members::where('status', '!=', 'active')->count();
        $expiredThisWeek = members::whereBetween('tanggal_selesai', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $renewalThisMonth = MembershipPayment::where('jenis_transaksi', 'renewal')->whereMonth('created_at', now()->month)->count();

        $memberBaruBulanIni = members::whereMonth('tanggal_daftar', $now->month)
            ->whereYear('tanggal_daftar', $now->year)
            ->count();
        return view('owner.memberfurion', compact('totalMember', 'newMemberThisMonth', 'memberAktif', 'retentionRate', 'expiredMember', 'expiredThisWeek', 'renewalThisMonth', 'memberBaruBulanIni'));
    }

    public function getMemberDetail($id)
    {
        $member = members::where('id_members', $id)
            ->with([
                'paket',
                'promo',
                'membershipPayments.paket'
            ])
            ->first();

        if (!$member) {
            return response()->json(['error' => 'Member tidak ditemukan'], 404);
        }

        $attendances = \App\Models\Absen::where('member_id', $id)
            ->orderBy('waktu_masuk', 'desc')
            ->pluck('waktu_masuk')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        // 3. Return JSON
        return response()->json([
            'member' => $member,
            'attendances' => $attendances
        ]);
    }
}
