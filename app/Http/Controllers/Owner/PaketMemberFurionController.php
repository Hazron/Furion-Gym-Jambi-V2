<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PaketMember;
use Illuminate\Http\Request;

class PaketMemberFurionController extends Controller
{
    public function index()
    {
        $pakets = PaketMember::orderBy('durasi', 'asc')->get();
        return view('Owner.PaketMemberFurion', compact('pakets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'jenis' => 'required|in:reguler,couple,Reguler,Couple',
        ]);

        $data = $request->all();

        $data['status'] = 'aktif';
        PaketMember::create($data);

        return redirect()->back()->with('success', 'Paket berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'durasi' => 'required|integer', // Sesuai model: durasi
        ]);

        $paket = PaketMember::findOrFail($id);
        $paket->update($request->all());

        return redirect()->back()->with('success', 'Paket berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $paket = PaketMember::findOrFail($id);
        $paket->delete();

        return redirect()->back()->with('success', 'Paket berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $paket = PaketMember::findOrFail($id);

        if ($paket->status == 'aktif') {
            $paket->status = 'nonaktif';
            $msg = 'dinonaktifkan';
        } else {
            $paket->status = 'aktif';
            $msg = 'diaktifkan';
        }

        $paket->save();

        return redirect()->back()->with('success', "Paket berhasil $msg!");
    }
}
