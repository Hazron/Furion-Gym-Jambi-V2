<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Members;
use Illuminate\Support\Str;
use App\Models\order;
use App\Models\order_item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::all();
        $members = Members::all();
        return view('Admin.OrderBarang', compact('produks', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'payment_status' => 'required|string',
            'payment_method' => 'required|string|in:cash,qris,transfer',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048', 
        ]);

        DB::beginTransaction();

        try {
            $invoiceCode = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer_produk', 'public');
            }

            $order = order::create([
                'member_id'      => $request->member_id ?: null,
                'kasir_id'       => Auth::id(),
                'invoice_code'   => $invoiceCode,
                'subtotal'       => $request->total_amount,
                'discount'       => 0,
                'total_payment'  => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'bukti_transfer' => $buktiPath,
            ]);

            foreach ($request->items as $item) {
                $produk = Produk::where('id_produk', $item['id'])->first();
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }
                if ($produk->stok_produk < $item['qty']) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi.");
                }
                
                order_item::create([
                    'order_id'   => $order->order_id ?? $order->id,
                    'produk_id'  => $produk->id_produk,
                    'qty'        => $item['qty'],
                    'price'      => $produk->harga_produk,
                    'total'      => $produk->harga_produk * $item['qty'],
                ]);

                $produk->stok_produk -= $item['qty'];
                $produk->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan!',
                'invoice' => $invoiceCode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambahProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga_produk'       => 'required|numeric',
            'stok_produk'        => 'required|numeric',
            'gambar_produk'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status_produk'     => 'active',
        ]);

        $gambarName = null;
        if ($request->hasFile('gambar_produk')) {
            $gambar      = $request->file('gambar_produk');
            $gambarName  = time() . '_' . Str::random(8) . '.' . $gambar->getClientOriginalExtension();
            $gambar->storeAs('produk', $gambarName, 'public');
        }

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'harga_produk'       => $request->harga_produk,
            'stok_produk'        => $request->stok_produk,
            'gambar_produk'      => $gambarName,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function editProduk(Request $request, $id)
    {
        $request->validate([
            'nama_produk'   => 'required|string|max:255',
            'harga_produk'  => 'required|numeric|min:0',
            'stok_produk'   => 'required|integer|min:0',
            'stok_masuk'    => 'nullable|integer|min:0',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $produk = Produk::where('id_produk', $id)->firstOrFail();

            $produk->nama_produk = $request->nama_produk;
            $produk->harga_produk = $request->harga_produk;

            $totalStok = $request->stok_produk;
            if ($request->filled('stok_masuk')) {
                $totalStok += $request->stok_masuk;
            }
            $produk->stok_produk = $totalStok;

            if ($request->hasFile('gambar_produk')) {
                if ($produk->gambar_produk && Storage::disk('public')->exists('produk/' . $produk->gambar_produk)) {
                    Storage::disk('public')->delete('produk/' . $produk->gambar_produk);
                }

                $file = $request->file('gambar_produk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('produk', $filename, 'public');
                $produk->gambar_produk = $filename;
            }

            $produk->save();

            return redirect()->back()->with('success', 'Produk berhasil diperbarui! Stok bertambah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $produk = Produk::where('id_produk', $id)->firstOrFail();

            if ($produk->gambar_produk && Storage::disk('public')->exists('produk/' . $produk->gambar_produk)) {
                Storage::disk('public')->delete('produk/' . $produk->gambar_produk);
            }

            $produk->delete();

            return redirect()->back()->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $produk = Produk::where('id_produk', $id)->firstOrFail();

            $newStatus = ($produk->status_produk == 'active') ? 'inactive' : 'active';

            $produk->status_produk = $newStatus;
            $produk->save();

            $message = ($newStatus == 'active')
                ? 'Produk sekarang TERSEDIA!'
                : 'Produk dinonaktifkan (TIDAK TERSEDIA).';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'new_state' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengganti status: ' . $e->getMessage()
            ], 500);
        }
    }
}