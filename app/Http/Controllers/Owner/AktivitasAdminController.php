<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MembershipPayment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AktivitasAdminController extends Controller
{
public function index(Request $request)
    {
        if ($request->ajax()) {

            $filterUser = $request->filter_user_id;
            // Tangkap tanggal mulai dan akhir dari DataTables
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $ordersQuery = Order::query()
                ->select(['created_at', 'kasir_id as user_id', 'invoice_code', 'total_payment as amount', 'payment_method', 'order_id as source_id', 'payment_status'])
                ->addSelect(DB::raw("'order' as type"))
                ->addSelect(DB::raw("'Penjualan' as action_type"))
                ->whereNotNull('kasir_id');

            // --- QUERY MEMBERSHIP ---
            $membershipQuery = MembershipPayment::query()
                ->select(['created_at', 'admin_id as user_id', 'nomor_invoice as invoice_code', 'nominal as amount', 'jenis_transaksi', 'member_id', 'id as source_id', 'status_pembayaran'])
                ->addSelect(DB::raw("'membership' as type"))
                ->addSelect(DB::raw("jenis_transaksi as action_type"));

            // Terapkan Filter User
            if ($filterUser && $filterUser != 'all') {
                $ordersQuery->where('kasir_id', $filterUser);
                $membershipQuery->where('admin_id', $filterUser);
            }

            // Terapkan Filter Range Waktu (Jika ada inputan startDate & endDate)
            if ($startDate && $endDate) {
                // Konkatenasi jam agar mencakup transaksi sepanjang hari penuh
                $start = $startDate . ' 00:00:00';
                $end = $endDate . ' 23:59:59';
                
                $ordersQuery->whereBetween('created_at', [$start, $end]);
                $membershipQuery->whereBetween('created_at', [$start, $end]);
            }

            $orders = $ordersQuery->get();
            $memberships = $membershipQuery->get();

            // Gabungkan kedua hasil query
            $data = $orders->toBase()->merge($memberships->toBase())->sortByDesc('created_at');

            return DataTables::of($data)
                ->addColumn('admin_name', function ($row) {
                    $user = User::find($row->user_id);
                    return $user ? $user->name : 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y H:i');
                })
                ->addColumn('badge_action', function ($row) {
                    $type = strtolower($row->action_type);
                    if ($type === 'penjualan')
                        return '<span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Penjualan</span>';
                    return '<span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">' . ucfirst($type) . '</span>';
                })
                ->addColumn('description', function ($row) {
                    $rawStatus = $row->status_pembayaran ?? $row->payment_status;
                    $status = strtolower($rawStatus);

                    $statusBadge = match ($status) {
                        'paid', 'completed', 'lunas', 'settlement', 'success' => '<strong class="text-green-600">Lunas</strong>',
                        'pending', 'menunggu' => '<strong class="text-yellow-600">Pending</strong>',
                        'failed', 'gagal', 'cancel', 'deny', 'expire' => '<strong class="text-red-600">Gagal</strong>',
                        default => '<strong>' . ucfirst($status) . '</strong>',
                    };

                    $rupiah = "Rp " . number_format($row->amount, 0, ',', '.');

                    if ($row->type === 'order') {
                        $method = ucfirst($row->payment_method ?? 'Cash');
                        $mainText = "Penjualan (<strong>{$method}</strong>) senilai <strong>{$rupiah}</strong> status {$statusBadge}";
                    } else {
                        $jenis = ucfirst($row->jenis_transaksi);
                        $mainText = "Transaksi <strong>{$jenis}</strong> senilai <strong>{$rupiah}</strong> status {$statusBadge}";
                    }

                    $invoiceText = "<div class='text-xs text-gray-400 mt-1 font-mono'>#{$row->invoice_code}</div>";

                    return $mainText . $invoiceText;
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" data-id="' . $row->source_id . '" data-type="' . $row->type . '" class="btn-detail text-indigo-600 hover:text-indigo-900 text-sm font-medium hover:underline">Lihat Detail</button>';
                })
                ->rawColumns(['badge_action', 'description', 'action'])
                ->make(true);
        }

        $admins = User::where('role', 'admin')->get();

        // Kita tidak mengirim $activityDate lagi, cukup list Admin saja
        return view('owner.aktivitasadmin', compact('admins'));
    }

    public function getAktivitasDetail(Request $request)
    {
        $type = $request->type;
        $id = $request->id;

        if ($type == 'order') {
            // Ambil Order beserta Order Item dan Produknya
            $order = Order::with('orderItems.produk')->find($id);

            if (!$order)
                return response()->json(['error' => 'Data tidak ditemukan'], 404);

            $html = '<div class="mb-4">
                    <h4 class="font-bold text-gray-700">Detail Produk Terjual</h4>
                    <p class="text-xs text-gray-500">Invoice: ' . $order->invoice_code . '</p>
                 </div>
                 <table class="w-full text-sm text-left text-gray-500 border">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Harga</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($order->orderItems as $item) {
                $namaProduk = $item->produk ? $item->produk->nama_produk : 'Produk Terhapus';
                $html .= '<tr class="border-b">
                        <td class="px-4 py-2 font-medium text-gray-900">' . $namaProduk . '</td>
                        <td class="px-4 py-2 text-center">' . $item->qty . '</td>
                        <td class="px-4 py-2 text-right">' . number_format($item->price, 0, ',', '.') . '</td>
                        <td class="px-4 py-2 text-right font-bold">' . number_format($item->total, 0, ',', '.') . '</td>
                      </tr>';
            }

            $html .= '  </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-bold">Grand Total</td>
                            <td class="px-4 py-2 text-right font-bold text-indigo-600">Rp ' . number_format($order->total_payment, 0, ',', '.') . '</td>
                        </tr>
                    </tfoot>
                  </table>';

            return response()->json(['html' => $html]);
        } elseif ($type == 'membership') {
            $payment = MembershipPayment::with('member')->find($id);

            if (!$payment)
                return response()->json(['error' => 'Data tidak ditemukan'], 404);

            $namaMember = $payment->member ? $payment->member->nama_lengkap : 'Member Tidak Ditemukan';

            $html = '<div class="space-y-3">
                    <div class="border-b pb-2">
                        <span class="text-xs text-gray-500 uppercase block">Jenis Transaksi</span>
                        <span class="font-bold text-gray-800 text-lg">' . ucfirst($payment->jenis_transaksi) . '</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500 uppercase block">Nama Member</span>
                            <span class="font-medium text-gray-800">' . $namaMember . '</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase block">Nominal</span>
                            <span class="font-medium text-indigo-600">Rp ' . number_format($payment->nominal, 0, ',', '.') . '</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase block">Metode Bayar</span>
                            <span class="font-medium text-gray-800">' . ($payment->metode_pembayaran ?? '-') . '</span>
                        </div>
                         <div>
                            <span class="text-xs text-gray-500 uppercase block">Tanggal</span>
                            <span class="font-medium text-gray-800">' . Carbon::parse($payment->tanggal_transaksi)->format('d M Y') . '</span>
                        </div>
                    </div>
                    <div class="mt-4 bg-gray-50 p-3 rounded text-sm text-gray-600">
                        <span class="font-bold">Keterangan:</span> <br>
                        ' . ucfirst($payment->jenis_transaksi) . ' - ' . ($payment->paket->nama_paket ?? 'Paket Tidak Ditemukan') . '
                    </div>
                 </div>';

            return response()->json(['html' => $html]);
        }
    }

    // --- CRUD ADMIN METHODS ---

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Admin berhasil ditambahkan');
    }

    public function updateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data Admin berhasil diperbarui');
    }

    public function deleteAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Admin berhasil dihapus');
    }
}