<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ListPaymentController extends Controller
{
    public function index(Request $request)
    {
        // 1. SETUP FILTER PERIODE
        // DEFAULT KE 'BULAN' JIKA TIDAK ADA REQUEST
        $periode = $request->input('periode', 'bulan');
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        switch ($periode) {
            case 'hari':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'minggu':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'tahun':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'semua':
                // TIDAK ADA FILTER TANGGAL
                $startDate = null;
                $endDate = null;
                break;
            case 'bulan':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        // 2. QUERY DATA (BERDASARKAN RENTANG TANGGAL)
        $query = Order::with(['member', 'cashier'])->latest();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // AMBIL DATA UNTUK TABEL
        $orders = $query->get();

        $stats = [
            'total_transaksi' => $orders->count(),
            'total_pemasukan' => $orders->where('payment_status', 'paid')->sum('total_payment'),
            'total_pending' => $orders->where('payment_status', 'pending')->count(),
            'pendapatan_pending' => $orders->where('payment_status', 'pending')->sum('total_payment'),
            'transaksi_hari_ini' => Order::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('Admin.listPaymentBarang', compact('orders', 'stats', 'periode'));
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($id);

        $order->payment_status = 'paid';
        $order->payment_method = $request->payment_method;
        $order->save();

        return redirect()->back()->with('success', 'STATUS PEMBAYARAN BERHASIL DIPERBARUI MENJADI LUNAS.');
    }

    public function sendInvoice($id)
    {
        try {
            // 1. AMBIL DATA ORDER BESERTA MEMBER DAN ITEM PRODUKNYA
            $order = Order::with(['member', 'items.produk'])->findOrFail($id);

            $nomorTujuan = $order->member->no_telepon ?? null;

            if (!$nomorTujuan) {
                return back()->with('error', 'GAGAL: NOMOR TELEPON MEMBER TIDAK DITEMUKAN.');
            }

            $target = preg_replace('/[^0-9]/', '', $nomorTujuan);
            if (substr($target, 0, 2) == '08') {
                $target = '628' . substr($target, 2);
            }

            if (empty($target) || substr($target, 0, 2) != '62') {
                return back()->with('error', 'FORMAT NOMOR WHATSAPP TIDAK VALID.');
            }

            $pesan = "*INVOICE PEMBELIAN* 🧾\n";
            $pesan .= "FURION GYM STORE\n\n";

            $pesan .= "NO. INVOICE : {$order->invoice_code}\n";
            $pesan .= "TANGGAL : " . Carbon::parse($order->created_at)->format('d M Y H:i') . "\n";
            $pesan .= "PEMBELI : " . ($order->member->nama_lengkap ?? 'GUEST') . "\n";
            $pesan .= "STATUS : LUNAS ✅\n";
            $pesan .= "--------------------------------\n";
            $pesan .= "*DETAIL BARANG:*\n";

            foreach ($order->items as $item) {
                $namaProduk = $item->produk->nama_produk ?? 'PRODUK DIHAPUS';
                $qty = $item->qty;
                $totalItem = number_format($item->total, 0, ',', '.');

                $pesan .= "- {$namaProduk} (X{$qty}) : RP {$totalItem}\n";
            }

            $pesan .= "--------------------------------\n";
            $pesan .= "*TOTAL BAYAR : RP " . number_format($order->total_payment, 0, ',', '.') . "*\n";
            $pesan .= "--------------------------------\n\n";
            $pesan .= "TERIMA KASIH TELAH BERBELANJA DI FURION GYM! 💪";

            $tokenFonnte = "uyH3RdWC5A7yoKvu2zaU"; // TOKEN ANDA

            $response = Http::withoutVerifying() // BYPASS SSL LOCALHOST
                ->withHeaders(['Authorization' => $tokenFonnte])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $pesan,
                    'countryCode' => '62',
                    'delay' => (string) rand(15, 30),
                ]);

            // CEK RESPON FONNTE (OPSIONAL)
            $hasil = $response->json();
            if (isset($hasil['status']) && $hasil['status'] == false) {
                return back()->with('error', 'GAGAL KIRIM WA: ' . ($hasil['reason'] ?? 'UNKNOWN ERROR'));
            }

            return back()->with('success', 'INVOICE BERHASIL DIKIRIM KE WHATSAPP PEMBELI!');

        } catch (\Exception $e) {
            return back()->with('error', 'TERJADI KESALAHAN SISTEM: ' . $e->getMessage());
        }
    }
}