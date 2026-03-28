<?php

namespace App\Http\Controllers;

use App\Models\CampaignPromo;
use App\Models\PaketMember;
use Illuminate\Http\Request;
use App\Models\paket_promo; // Pastikan Model di-import
use Carbon\Carbon;

class landingPageFurionController extends Controller
{
    // app/Http/Controllers/landingPageFurionController.php
    public function index()
    {
        $allPromos = CampaignPromo::with([
            'paketMembers' => function ($query) {
                $query->where('status', 'aktif'); // Hanya ambil paket yang aktif
            }
        ])
            ->where("status", "aktif")
            ->orderBy("created_at", "desc")
            ->whereDate("tanggal_mulai", "<=", Carbon::now())
            ->whereDate("tanggal_selesai", ">=", Carbon::now())
            ->get();

        $promo = $allPromos->first();

        return view('landingPageFurion', compact('promo', 'allPromos'));
    }
}