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
        // Default ke 'bulan' jika tidak ada request
        $periode = $request->input('periode', 'bulan'); 
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        switch ($periode) {
            case 'hari':
                $startDate = $now->copy()->startOfDay();
                $endDate   = $now->copy()->endOfDay();
                break;
            case 'minggu':
                $startDate = $now->copy()->startOfWeek();
                $endDate   = $now->copy()->endOfWeek();
                break;
            case 'tahun':
                $startDate = $now->copy()->startOfYear();
                $endDate   = $now->copy()->endOfYear();
                break;
            case 'semua':
                // Tidak ada filter tanggal
                $startDate = null; 
                $endDate   = null;
                break;
            case 'bulan':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate   = $now->copy()->endOfMonth();
                break;
        }

        // 2. QUERY DATA (Berdasarkan Rentang Tanggal)
        $query = Order::with(['member', 'cashier'])->latest();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Ambil data untuk tabel
        $orders = $query->get();

        $stats = [
            'total_transaksi'    => $orders->count(),
            'total_pemasukan'    => $orders->where('payment_status', 'paid')->sum('total_payment'),
            'total_pending'      => $orders->where('payment_status', 'pending')->count(),
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

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui menjadi Lunas.');
    }

    public function sendInvoice($id)
{
    try {
        // 1. Ambil Data Order beserta Member dan Item Produknya
        $order = Order::with(['member', 'items.produk'])->findOrFail($id);

        $nomorTujuan = $order->member->no_telepon ?? null;

        if (!$nomorTujuan) {
            return back()->with('error', 'Gagal: Nomor telepon member tidak ditemukan.');
        }

        $target = preg_replace('/[^0-9]/', '', $nomorTujuan);
        if (substr($target, 0, 2) == '08') {
            $target = '628' . substr($target, 2);
        }

        if (empty($target) || substr($target, 0, 2) != '62') {
            return back()->with('error', 'Format nomor WhatsApp tidak valid.');
        }

        $pesan = "*INVOICE PEMBELIAN* 🧾\n";
        $pesan .= "Furion Gym Store\n\n";
        
        $pesan .= "No. Invoice : {$order->invoice_code}\n";
        $pesan .= "Tanggal : " . Carbon::parse($order->created_at)->format('d M Y H:i') . "\n";
        $pesan .= "Pembeli : " . ($order->member->nama_lengkap ?? 'Guest') . "\n";
        $pesan .= "Status : LUNAS ✅\n";
        $pesan .= "--------------------------------\n";
        $pesan .= "*Detail Barang:*\n";

        foreach ($order->items as $item) {
            $namaProduk = $item->produk->nama_produk ?? 'Produk dihapus';
            $qty = $item->qty;
            $totalItem = number_format($item->total, 0, ',', '.');
            
            $pesan .= "- {$namaProduk} (x{$qty}) : Rp {$totalItem}\n";
        }

        $pesan .= "--------------------------------\n";
        $pesan .= "*TOTAL BAYAR : Rp " . number_format($order->total_payment, 0, ',', '.') . "*\n";
        $pesan .= "--------------------------------\n\n";
        $pesan .= "Terima kasih telah berbelanja di Furion Gym! 💪";

        $tokenFonnte = "uyH3RdWC5A7yoKvu2zaU"; // Token Anda

        $response = Http::withoutVerifying() // Bypass SSL Localhost
            ->withHeaders(['Authorization' => $tokenFonnte])
            ->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $pesan,
                'countryCode' => '62',
                'delay'       => (string) rand(15, 30),
            ]);

        // Cek Respon Fonnte (Opsional)
        $hasil = $response->json();
        if (isset($hasil['status']) && $hasil['status'] == false) {
             return back()->with('error', 'Gagal kirim WA: ' . ($hasil['reason'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Invoice berhasil dikirim ke WhatsApp Pembeli!');

    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}
}