<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class MonitoringEtalaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $filter = $request->filter ?? 'all';
        if ($filter == 'menipis') {
            $query->where('stok_produk', '<=', 5)->where('stok_produk', '>', 0);
        } elseif ($filter == 'habis') {
            $query->where('stok_produk', 0);
        }

        $products = $query->orderBy('stok_produk', 'asc')->paginate(12);

        $allProducts = Produk::all();
        
        $totalProduk = $allProducts->count();
        $totalAset = $allProducts->sum(fn($p) => $p->harga_produk * $p->stok_produk);
        $stokMenipis = $allProducts->filter(fn($p) => $p->stok_produk <= 5 && $p->stok_produk > 0)->count();
        $stokHabis = $allProducts->filter(fn($p) => $p->stok_produk == 0)->count();

        return view("Owner.MonitoringEtalase", compact(
            'products', 
            'totalProduk', 
            'totalAset', 
            'stokMenipis', 
            'stokHabis',
            'filter'
        ));
    }
}