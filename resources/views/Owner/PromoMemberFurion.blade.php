@extends('Owner.OwnerTemplate')

@section('title', 'Promo Paket Member')

@section('header-content')
    <div class="flex flex-col justify-center">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Paket Member Promo</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola promo membership, diskon, dan broadcast notifikasi.</p>
    </div>
@endsection

@section('content')

    {{-- 1. ALERT ERROR HANDLING --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-bold">Gagal Menyimpan:</p>
                <ul class="list-disc ml-5 text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- 2. GRAFIK & STATISTIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 mb-8 sm:mb-12">

        {{-- Chart Section dengan Navigasi Tahun --}}
        <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-3xl border border-gray-100 shadow-sm order-2 lg:order-1">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-base sm:text-lg text-gray-900">Grafik Peminat Promo</h3>
                    <p class="text-xs text-gray-400">Tren pendaftar paket promo</p>
                </div>
                {{-- Navigasi Tahun (AJAX) --}}
                <div class="flex items-center gap-3 bg-gray-50 p-1.5 rounded-2xl border border-gray-100">
                    <button onclick="changeYear(-1)"
                        class="p-1.5 hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90 text-gray-400 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <span id="displayYear"
                        class="text-sm font-bold text-blue-600 px-3 min-w-[3rem] text-center">{{ $year }}</span>
                    <button onclick="changeYear(1)"
                        class="p-1.5 hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90 text-gray-400 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="promoChart" class="w-full h-64"></div>
        </div>

        {{-- Statistik Cards --}}
        <div class="flex flex-col gap-4 order-1 lg:order-2">
            {{-- Card Aktif --}}
            <div
                class="flex-1 bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-3xl shadow-lg shadow-blue-200 text-white relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full group-hover:scale-110 transition duration-500">
                </div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-1">Campaign Aktif</p>
                    <h3 class="text-3xl sm:text-4xl font-black">{{ $promos->where('status', 'aktif')->count() }}</h3>
                    <p class="text-xs text-blue-200 mt-2">Periode promo sedang berjalan</p>
                </div>
            </div>
            {{-- Card Member Opt-Out (Updated Design) --}}
            <div
                class="flex-1 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all duration-300">

                <div class="absolute -right-2 -top-2 text-red-50 group-hover:text-red-100 transition-colors duration-300">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                </div>

                <div class="relative">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.15em]">Subscription Status</p>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-gray-900 tracking-tight">
                            {{ $members->where('is_opt_out', 1)->count() }}
                        </h3>
                        <span class="text-gray-400 font-medium text-sm italic">Members</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-50 relative">
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Stop menerima promosi (Opt-out)
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Filter Data --}}
    @php
        $now = \Carbon\Carbon::now();
        $activePromos = $promos->filter(fn($p) => $p->status == 'aktif' && $now->lte(\Carbon\Carbon::parse($p->tanggal_selesai)));
        $expiredPromos = $promos->filter(fn($p) => $p->status != 'aktif' || $now->gt(\Carbon\Carbon::parse($p->tanggal_selesai)));
    @endphp

    {{-- 3. TOMBOL AKSI UTAMA --}}
    <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3 mb-8">
        <button onclick="openModal('pushNotifModal')"
            class="group flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-600 hover:text-blue-600 hover:border-blue-200 px-5 py-2.5 rounded-xl sm:rounded-2xl font-bold text-xs sm:text-sm shadow-sm hover:shadow-md transition-all active:scale-95">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                </path>
            </svg>
            Push Notifikasi
        </button>
        <button onclick="openModal('addPromoModal')"
            class="group flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl sm:rounded-2xl font-bold text-xs sm:text-sm shadow-lg shadow-gray-200 transition-all hover:-translate-y-0.5 active:scale-95">
            <svg class="w-4 h-4 bg-white/20 rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Campaign Baru
        </button>
    </div>

    {{-- PROGRESS BAR BROADCAST (Menggunakan Tailwind agar serasi dengan tema) --}}
    <div id="broadcast-progress-container" style="display: none;"
        class="mb-8 bg-white border border-blue-200 shadow-lg shadow-blue-100/50 rounded-3xl overflow-hidden animate-fade-in-down">
        {{-- Header Status --}}
        <div
            class="bg-blue-600 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-white">
            <h5 class="font-bold flex items-center gap-2 text-sm sm:text-base">
                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                    </path>
                </svg>
                <span>Mengirim Broadcast:</span> <span id="bp-nama" class="font-black">Memuat...</span>
            </h5>
            <span id="bp-status-badge"
                class="bg-yellow-400 text-yellow-900 text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm">
                <span class="w-2 h-2 bg-yellow-600 rounded-full animate-ping"></span> Berjalan
            </span>
        </div>

        <div class="p-5 sm:p-6">
            {{-- Progress Line --}}
            <div class="flex justify-between text-xs sm:text-sm font-bold text-gray-500 mb-2">
                <span>Progress Pengiriman: <span id="bp-terproses" class="text-blue-600 font-black">0</span> dari <span
                        id="bp-total" class="text-gray-900">0</span> Pesan</span>
                <span id="bp-persen-text" class="text-blue-600 font-black">0%</span>
            </div>
            <div class="w-full bg-blue-50 rounded-full h-3 sm:h-4 mb-6 overflow-hidden border border-blue-100">
                <div id="bp-progress-bar"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-700 ease-out relative"
                    style="width: 0%">
                    <div class="absolute inset-0 bg-white/20"
                        style="background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent); background-size: 1rem 1rem;">
                    </div>
                </div>
            </div>

            {{-- Statistik Kotak Angka --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-3 sm:p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center shadow-sm">
                    <p class="text-[10px] sm:text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">⏳ Antrian</p>
                    <h3 id="bp-pending" class="text-xl sm:text-2xl font-black text-gray-700">0</h3>
                </div>
                <div class="p-3 sm:p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-center shadow-sm">
                    <p class="text-[10px] sm:text-xs text-indigo-600 font-bold uppercase tracking-wider mb-1">📤 Terkirim
                    </p>
                    <h3 id="bp-sent" class="text-xl sm:text-2xl font-black text-indigo-700">0</h3>
                </div>
                <div class="p-3 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center shadow-sm">
                    <p class="text-[10px] sm:text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">👁️ Dibaca
                    </p>
                    <h3 id="bp-read" class="text-xl sm:text-2xl font-black text-emerald-700">0</h3>
                </div>
                <div class="p-3 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-center shadow-sm">
                    <p class="text-[10px] sm:text-xs text-rose-600 font-bold uppercase tracking-wider mb-1">❌ Gagal</p>
                    <h3 id="bp-failed" class="text-xl sm:text-2xl font-black text-rose-700">0</h3>
                </div>
            </div>
        </div>
    </div>
    {{-- END PROGRESS BAR --}}


    {{-- 4. DAFTAR CAMPAIGN PROMO AKTIF --}}
    <div class="mb-12">
        <h3 class="font-bold text-lg sm:text-xl text-gray-800 mb-6 flex items-center gap-3">
            <span class="w-1.5 h-6 sm:h-8 bg-blue-600 rounded-full"></span> Campaign Sedang Aktif
        </h3>

        @if($activePromos->isEmpty())
            <div
                class="w-full py-12 bg-white rounded-[2rem] border border-dashed border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-gray-900 font-bold mb-1 text-sm sm:text-base">Belum ada event aktif</h4>
                <p class="text-gray-500 text-xs sm:text-sm">Buat campaign promo baru untuk menarik minat member.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($activePromos as $promo)
                    <div
                        class="group relative bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col h-full">
                        {{-- Gambar & Status --}}
                        <div class="relative h-44 w-full bg-gray-200 overflow-hidden">
                            @if($promo->gambar_banner)
                                <img src="{{ asset('storage/' . $promo->gambar_banner) }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3">
                                <span
                                    class="bg-emerald-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm">AKTIF</span>
                            </div>
                        </div>

                        {{-- Info Campaign --}}
                        <div class="p-5 flex-1 flex flex-col">
                            <h4 class="font-bold text-gray-900 text-lg leading-tight mb-1">{{ $promo->nama_campaign }}</h4>
                            <p class="text-[10px] text-gray-400 mb-4 font-mono">ID EVENT: #{{ $promo->id_campaign }}</p>

                            {{-- Tanggal --}}
                            <div class="bg-blue-50/50 rounded-xl p-3 border border-blue-100/50 text-xs text-gray-600 mb-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span
                                        class="font-bold">{{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}</span>
                                    <span class="text-gray-400">-</span>
                                    <span
                                        class="font-bold">{{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}</span>
                                </div>
                            </div>

                            {{-- Performa Penjualan / Transaksi --}}
                            <div class="mb-5">
                                <p class="text-[10px] font-bold text-gray-400 mb-2 tracking-wider">PERFORMA TRANSAKSI:</p>
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-green-50 border border-green-100 rounded-lg py-2">
                                        <div class="text-lg font-black text-green-600">{{ $promo->registrasi_count ?? 0 }}</div>
                                        <div class="text-[9px] font-bold text-green-700 uppercase">Registrasi</div>
                                    </div>
                                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg py-2">
                                        <div class="text-lg font-black text-indigo-600">{{ $promo->perpanjang_count ?? 0 }}</div>
                                        <div class="text-[9px] font-bold text-indigo-700 uppercase">Perpanjang</div>
                                    </div>
                                    <div class="bg-purple-50 border border-purple-100 rounded-lg py-2">
                                        <div class="text-lg font-black text-purple-600">{{ $promo->reaktivasi_count ?? 0 }}</div>
                                        <div class="text-[9px] font-bold text-purple-700 uppercase">Reaktivasi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- 5. RIWAYAT / EXPIRED PROMO --}}
    <div class="mb-12">
        <h3 class="font-bold text-lg sm:text-xl text-gray-500 mb-6 flex items-center gap-3">
            <span class="w-1.5 h-6 bg-gray-300 rounded-full"></span> Riwayat / Expired
        </h3>
        @if($expiredPromos->isEmpty())
            <div class="text-gray-400 text-sm ml-4 italic">Belum ada riwayat event promo.</div>
        @else
            <div
                class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 opacity-80 hover:opacity-100 transition duration-300">
                @foreach($expiredPromos as $promo)
                    @php $isExpiredDate = $now->gt(\Carbon\Carbon::parse($promo->tanggal_selesai)); @endphp
                    <div
                        class="group relative bg-white rounded-3xl border border-gray-200 shadow-sm flex flex-col h-full grayscale hover:grayscale-0 hover:shadow-lg transition">
                        <div class="relative h-32 w-full bg-gray-100 overflow-hidden">
                            @if($promo->gambar_banner)
                                <img src="{{ asset('storage/' . $promo->gambar_banner) }}"
                                    class="w-full h-full object-cover opacity-70 group-hover:opacity-100 transition">
                            @endif
                            <div class="absolute top-3 right-3">
                                <span
                                    class="bg-gray-700/90 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg">{{ $isExpiredDate ? 'BERAKHIR' : 'NON-AKTIF' }}</span>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h4 class="font-bold text-gray-600 mb-2 line-clamp-1">{{ $promo->nama_campaign }}</h4>
                            <div class="text-xs text-gray-400 mb-4 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                            </div>

                            {{-- Simple Stats untuk Expired --}}
                            <div class="flex gap-2 text-[10px] font-bold mb-4">
                                <span class="bg-green-50 text-green-600 px-2 py-1 rounded">{{ $promo->registrasi_count ?? 0 }}
                                    Registrasi</span>
                                <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded">{{ $promo->perpanjang_count ?? 0 }}
                                    Perpanjang</span>
                                <span class="bg-purple-50 text-purple-600 px-2 py-1 rounded">{{ $promo->reaktivasi_count ?? 0 }}
                                    Reaktivasi</span>
                            </div>

                            <div class="mt-auto pt-3 border-t border-gray-100 flex gap-2">
                                {{-- Tombol Edit aktif meskipun promo sudah nonaktif atau berakhir --}}
                                <button onclick="openEditModal({{ json_encode($promo) }})"
                                    class="flex-1 py-2 rounded-xl border border-gray-100 text-xs font-bold text-gray-600 hover:border-blue-200 hover:text-blue-600 transition-colors bg-gray-50">Edit</button>

                                <form action="{{ route('owner.promo.toggle', $promo->id_campaign) }}" method="POST">
                                    @csrf @method('PATCH')
                                    {{-- Tombol Aktifkan hanya muncul jika promo BELUM MELEWATI tanggal selesai --}}
                                    @if(!$isExpiredDate)
                                        <button type="button"
                                            onclick="confirmStatusChange('{{ $promo->id_campaign }}', '{{ $promo->nama_campaign }}', 'aktifkan')"
                                            class="w-10 h-full flex items-center justify-center bg-emerald-50 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-white transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ====================================================================== --}}
    {{-- AREA MODALS (INLINE) --}}
    {{-- ====================================================================== --}}

    {{-- A. MODAL TAMBAH PROMO --}}
    <div id="addPromoModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('addPromoModal')">
        </div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-3xl transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-gray-50 text-left shadow-2xl transition-all duration-300 scale-95 border border-gray-200">

                <div
                    class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Buat Campaign Promo Baru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Atur detail event dan centang variasi paket.</p>
                    </div>
                    <button onclick="closeModal('addPromoModal')"
                        class="text-gray-400 hover:bg-gray-100 p-2 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('owner.promo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-6 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">

                        {{-- Section 1: Informasi Dasar --}}
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                            <h4
                                class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4 flex items-center gap-2">
                                <span
                                    class="bg-blue-600 text-white w-5 h-5 flex items-center justify-center rounded-full text-xs">1</span>
                                Detail Event
                            </h4>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Gambar
                                    Banner</label>
                                <label for="dropzone-file-add"
                                    class="relative flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition-all overflow-hidden group">
                                    <div id="upload-placeholder-add"
                                        class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-7 h-7 mb-2 text-gray-400 group-hover:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-xs text-gray-500 group-hover:text-blue-600 font-medium">Klik untuk
                                            upload gambar banner</p>
                                    </div>
                                    <img id="upload-preview-add"
                                        class="absolute inset-0 w-full h-full object-cover hidden" />
                                    <input id="dropzone-file-add" name="gambar_promo" type="file" class="hidden" required
                                        onchange="previewImage(this, 'upload-preview-add', 'upload-placeholder-add')" />
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Tema Event /
                                        Campaign</label>
                                    <input type="text" name="nama_paket" required
                                        class="w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors"
                                        placeholder="Contoh: Mega Diskon Akhir Tahun 2026">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Berlaku
                                        Mulai</label>
                                    <input type="date" name="tanggal_mulai" required
                                        class="w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Berakhir
                                        Pada</label>
                                    <input type="date" name="tanggal_selesai" required
                                        class="w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Varian Harga --}}
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-2 mb-4">
                                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                    <span
                                        class="bg-blue-600 text-white w-5 h-5 flex items-center justify-center rounded-full text-xs">2</span>
                                    Setup Durasi & Harga Varian
                                </h4>
                                <button type="button" onclick="addDurationBlock()"
                                    class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg flex items-center gap-1 transition-colors active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Durasi
                                </button>
                            </div>
                            <div id="duration-container" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                        </div>

                    </div>

                    <div
                        class="bg-white px-6 py-4 flex justify-between items-center border-t border-gray-200 rounded-b-3xl">
                        <p class="text-xs text-gray-400 hidden sm:block">*Pastikan setidaknya memilih 1 durasi
                            (Reguler/Couple)</p>
                        <div class="flex gap-3 w-full sm:w-auto justify-end">
                            <button type="button" onclick="closeModal('addPromoModal')"
                                class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-7 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Rilis Campaign
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- B. MODAL EDIT CAMPAIGN --}}
    <div id="editPromoModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            onclick="closeModal('editPromoModal')"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all duration-300 scale-95 border border-gray-200">
                <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Edit Campaign Promo</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Ubah banner atau periode event.</p>
                    </div>
                    <button onclick="closeModal('editPromoModal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="px-6 py-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Ganti Banner
                                (Opsional)</label>
                            <input name="gambar_promo" type="file"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Campaign</label>
                            <input type="text" id="edit_nama_campaign" name="nama_campaign" required
                                class="w-full rounded-xl border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mulai</label>
                                <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" required
                                    class="w-full rounded-xl border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors">
                            </div>
                            <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Selesai</label>
                                <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" required
                                    class="w-full rounded-xl border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 transition-colors">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <button type="button" onclick="closeModal('editPromoModal')"
                            class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Update
                            Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- C. MODAL PUSH NOTIFIKASI --}}
    <div id="pushNotifModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            onclick="closeModal('pushNotifModal')"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all duration-300 scale-95 border border-gray-200">
                <div
                    class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 border-b border-blue-500 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-lg font-bold">Push Notifikasi</h3>
                        <p class="text-xs text-blue-100 mt-0.5">Broadcast promo ke member.</p>
                    </div>
                    <button onclick="closeModal('pushNotifModal')"
                        class="text-blue-100 hover:text-white bg-white/10 p-2 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('push.notification') }}" method="POST"
                    onsubmit="document.getElementById('btnSubmitPromo').disabled = true; document.getElementById('btnSubmitPromo').innerText = 'Sedang Memproses...';">
                    @csrf
                    <div class="px-6 py-6 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Target
                                Audience</label>
                            <select name="target" id="targetSelect" onchange="toggleManualInput(this.value)"
                                class="w-full rounded-xl border-2 border-gray-300 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="all">Semua Member</option>
                                <option value="active">Member Aktif</option>
                                <option value="expired">Member Expired</option>
                                <option value="promo_users">Pengguna Promo</option>
                                <option value="manual" class="font-bold text-blue-600">🎯 Manual (Tes Nomor)</option>
                            </select>
                        </div>

                        <div id="manualInputContainer" class="hidden animate-fade-in-down">
                            <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nomor WhatsApp
                                Tujuan</label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-gray-400 text-sm pointer-events-none">Example:
                                    0812...</span>
                                <input type="text" name="manual_number"
                                    class="w-full rounded-xl border-2 border-blue-300 bg-blue-50 text-sm py-2.5 px-4 pl-36 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="081234567890">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Promo
                                (Campaign)</label>
                            <select id="campaignSelect" onchange="generatePromoMessage(this)" required
                                class="w-full rounded-xl border-2 border-gray-300 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="" disabled selected>-- Pilih Campaign Promo Aktif --</option>
                                @foreach($activePromos as $promo)
                                    @php
                                        $paketArray = [];
                                        foreach ($promo->paketMembers as $paket) {
                                            $jenis = ucfirst(str_replace('promo ', '', $paket->jenis));
                                            $harga = number_format($paket->harga, 0, ',', '.');
                                            $paketArray[] = "✅ {$paket->durasi} {$jenis} : Rp {$harga}";
                                        }
                                    @endphp
                                    <option value="{{ $promo->id_campaign }}" data-nama="{{ $promo->nama_campaign }}"
                                        data-mulai="{{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}"
                                        data-selesai="{{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}"
                                        data-paket="{{ json_encode($paketArray) }}">
                                        {{ $promo->nama_campaign }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="subject" id="hiddenSubject">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 flex justify-between">
                                <span>Isi Pesan (Bisa Diedit)</span>
                                <span class="text-blue-500 font-normal normal-case">*Otomatis terisi</span>
                            </label>
                            <textarea name="message" id="promoMessage" rows="7" required
                                class="w-full rounded-xl border-2 border-gray-300 text-sm py-2.5 px-4 resize-none focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50"
                                placeholder="Pilih promo di atas, teks akan otomatis muncul di sini..."></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 rounded-b-3xl">
                        <button type="button" onclick="closeModal('pushNotifModal')"
                            class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" id="btnSubmitPromo"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">Kirim
                            Notifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ======================= LOGIC CHART AJAX =======================
        let currentYear = {{ $year }};
        let chart;

        function initChart(data) {
            const options = {
                series: [{ name: 'Penggunaan Promo', data: data }],
                chart: { type: 'area', height: 250, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, zoom: { enabled: false } },
                colors: ['#3b82f6'],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1, stops: [0, 90, 100] } },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#9ca3af', fontSize: '11px' } } },
                yaxis: { show: false },
                grid: { borderColor: '#f3f4f6', strokeDashArray: 4, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
                tooltip: { y: { formatter: function (val) { return val + " Member" } } }
            };

            if (chart) chart.destroy();
            chart = new ApexCharts(document.querySelector("#promoChart"), options);
            chart.render();
        }

        function changeYear(offset) {
            currentYear += offset;
            document.getElementById('displayYear').innerText = currentYear;

            fetch(`{{ route('owner.promomemberfurion') }}?year=${currentYear}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
                .then(response => response.json())
                .then(data => {
                    chart.updateSeries([{ data: data.promoStats }]);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // ======================= LOGIC MODAL & UI =======================
        function openModal(modalID) {
            const modal = document.getElementById(modalID);
            if (!modal) return;
            const panel = modal.querySelector('.transform') || modal.children[1].children[0];
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                if (panel) { panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }
            }, 10);
        }

        function closeModal(modalID) {
            const modal = document.getElementById(modalID);
            if (!modal) return;
            const panel = modal.querySelector('.transform') || modal.children[1].children[0];
            modal.classList.add('opacity-0');
            if (panel) { panel.classList.remove('scale-100'); panel.classList.add('scale-95'); }
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openEditModal(data) {
            if (document.getElementById('edit_nama_campaign')) document.getElementById('edit_nama_campaign').value = data.nama_campaign;
            if (document.getElementById('edit_tanggal_mulai')) document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
            if (document.getElementById('edit_tanggal_selesai')) document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;

            let url = "{{ route('owner.promo.update', ':id') }}";
            url = url.replace(':id', data.id_campaign);
            const form = document.getElementById('editForm');
            if (form) {
                form.action = url;
                openModal('editPromoModal');
            }
        }

        function confirmStatusChange(idCampaign, namaCampaign, action) {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Anda akan " + action + " campaign " + namaCampaign,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'nonaktifkan' ? '#ef4444' : '#3b82f6',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-toggle-' + idCampaign).submit();
                }
            })
        }

        function toggleManualInput(value) {
            const container = document.getElementById('manualInputContainer');
            const input = container.querySelector('input');
            if (value === 'manual') {
                container.classList.remove('hidden');
                if (input) input.required = true;
            } else {
                container.classList.add('hidden');
                if (input) { input.required = false; input.value = ''; }
            }
        }

        // ======================= LOGIC DYNAMIC FORM ADD PROMO =======================
        let blockIdCounter = 0;

        function addDurationBlock() {
            blockIdCounter++;
            const container = document.getElementById('duration-container');
            const existingInputs = document.querySelectorAll('.month-input-visible');
            let nextMonthValue = 1;

            if (existingInputs.length > 0) {
                const lastValue = parseInt(existingInputs[existingInputs.length - 1].value) || 0;
                nextMonthValue = lastValue + 1;
            }

            const html = `
                            <div class="duration-block border-2 border-gray-100 rounded-xl p-4 hover:border-blue-200 transition-colors bg-gray-50/50 relative group animate-fade-in-down" id="duration_block_${blockIdCounter}">
                                <button type="button" onclick="removeDurationBlock(${blockIdCounter})" class="btn-remove-block absolute top-3 right-3 text-gray-300 hover:text-red-500 transition-colors" title="Hapus Durasi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <div class="flex items-center gap-2 mb-4">
                                    <input type="number" min="1" max="24" class="month-input-visible w-16 h-10 rounded-lg border-2 border-gray-200 text-center font-black text-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                        value="${nextMonthValue}" 
                                        onchange="updateHiddenDuration(this, ${blockIdCounter})">
                                    <span class="text-sm font-bold text-gray-700">Bulan</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                        <label class="flex items-center justify-between cursor-pointer mb-2">
                                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">👤 Reguler</span>
                                            <input type="checkbox" name="promos[${blockIdCounter}_reguler][is_selected]" value="1"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                                onchange="
                                                    const wrapper = document.getElementById('wrapper_${blockIdCounter}_reguler');
                                                    const input = document.getElementById('harga_${blockIdCounter}_reguler');
                                                    wrapper.classList.toggle('hidden', !this.checked);
                                                    input.required = this.checked;
                                                ">
                                        </label>
                                        <div id="wrapper_${blockIdCounter}_reguler" class="hidden relative mt-2">
                                            <span class="absolute left-3 top-2 text-gray-400 text-sm font-bold pointer-events-none">Rp</span>
                                            <input type="number" name="promos[${blockIdCounter}_reguler][harga]" id="harga_${blockIdCounter}_reguler"
                                                class="w-full pl-9 pr-3 py-2 text-sm border-2 border-blue-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 bg-blue-50/30 transition-colors placeholder:text-gray-300"
                                                placeholder="0">
                                            <input type="hidden" name="promos[${blockIdCounter}_reguler][durasi]" id="durasi_${blockIdCounter}_reguler" value="${nextMonthValue} Bulan">
                                            <input type="hidden" name="promos[${blockIdCounter}_reguler][jenis]" value="promo">
                                        </div>
                                    </div>
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                        <label class="flex items-center justify-between cursor-pointer mb-2">
                                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">👥 Couple</span>
                                            <input type="checkbox" name="promos[${blockIdCounter}_couple][is_selected]" value="1"
                                                class="w-4 h-4 text-pink-500 bg-gray-100 border-gray-300 rounded focus:ring-pink-500 cursor-pointer"
                                                onchange="
                                                    const wrapper = document.getElementById('wrapper_${blockIdCounter}_couple');
                                                    const input = document.getElementById('harga_${blockIdCounter}_couple');
                                                    wrapper.classList.toggle('hidden', !this.checked);
                                                    input.required = this.checked;
                                                ">
                                        </label>
                                        <div id="wrapper_${blockIdCounter}_couple" class="hidden relative mt-2">
                                            <span class="absolute left-3 top-2 text-gray-400 text-sm font-bold pointer-events-none">Rp</span>
                                            <input type="number" name="promos[${blockIdCounter}_couple][harga]" id="harga_${blockIdCounter}_couple"
                                                class="w-full pl-9 pr-3 py-2 text-sm border-2 border-pink-200 rounded-lg focus:border-pink-500 focus:ring-pink-500 bg-pink-50/30 transition-colors placeholder:text-gray-300"
                                                placeholder="0">
                                            <input type="hidden" name="promos[${blockIdCounter}_couple][durasi]" id="durasi_${blockIdCounter}_couple" value="${nextMonthValue} Bulan">
                                            <input type="hidden" name="promos[${blockIdCounter}_couple][jenis]" value="promo couple">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
            container.insertAdjacentHTML('beforeend', html);
            checkDeleteButtons();
        }

        function updateHiddenDuration(inputElement, id) {
            const val = inputElement.value + ' Bulan';
            document.getElementById(`durasi_${id}_reguler`).value = val;
            document.getElementById(`durasi_${id}_couple`).value = val;
        }

        function removeDurationBlock(id) {
            const block = document.getElementById(`duration_block_${id}`);
            if (block) {
                block.remove();
                checkDeleteButtons();
            }
        }

        function checkDeleteButtons() {
            const blocks = document.querySelectorAll('.duration-block');
            const deleteButtons = document.querySelectorAll('.btn-remove-block');
            if (blocks.length <= 1) {
                deleteButtons.forEach(btn => btn.classList.add('hidden'));
            } else {
                deleteButtons.forEach(btn => btn.classList.remove('hidden'));
            }
        }

        function generatePromoMessage(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption.value) return;

            const namaCampaign = selectedOption.getAttribute('data-nama');
            const tanggalMulai = selectedOption.getAttribute('data-mulai');
            const tanggalSelesai = selectedOption.getAttribute('data-selesai');
            const paketArray = JSON.parse(selectedOption.getAttribute('data-paket'));

            document.getElementById('hiddenSubject').value = namaCampaign;

            let pesanPromo = `Halo Member Furion! 🔥\nFurion Gym sedang ada promo spesial nih:\n`;
            pesanPromo += `*${namaCampaign}*\n`;
            pesanPromo += `_(Periode Promo: ${tanggalMulai} s.d ${tanggalSelesai})_\n\n`;
            pesanPromo += `Berikut adalah daftar paket yang tersedia:\n`;

            paketArray.forEach(paket => {
                pesanPromo += `${paket}\n`;
            });

            pesanPromo += `\nYuk buruan daftar/perpanjang sebelum periode promo berakhir!\n`;

            const messageBox = document.getElementById('promoMessage');
            messageBox.value = pesanPromo;

            messageBox.classList.remove('bg-gray-50');
            messageBox.classList.add('bg-blue-50/30');
        }

        // ======================= LOGIC PROGRESS BAR AJAX =======================
        let progressInterval;

        function fetchBroadcastProgress() {
            fetch("{{ route('owner.promo.progress') }}") // Pastikan URL endpoint ini valid
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('broadcast-progress-container');

                    if (data.aktif) {
                        container.style.display = 'block';

                        document.getElementById('bp-nama').innerText = data.nama_campaign;
                        document.getElementById('bp-total').innerText = data.total;
                        document.getElementById('bp-terproses').innerText = data.terproses;
                        document.getElementById('bp-persen-text').innerText = data.persentase + '%';

                        const progressBar = document.getElementById('bp-progress-bar');
                        progressBar.style.width = data.persentase + '%';

                        document.getElementById('bp-pending').innerText = data.detail.pending;
                        document.getElementById('bp-sent').innerText = parseInt(data.detail.sent) + parseInt(data.detail.delivered);
                        document.getElementById('bp-read').innerText = data.detail.read;
                        document.getElementById('bp-failed').innerText = data.detail.failed;

                        const statusBadge = document.getElementById('bp-status-badge');

                        if (data.status_global === 'selesai' || data.persentase >= 100) {
                            progressBar.classList.replace('from-blue-500', 'from-emerald-400');
                            progressBar.classList.replace('to-blue-600', 'to-emerald-500');
                            statusBadge.className = 'bg-emerald-100 text-emerald-700 text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm';
                            statusBadge.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Selesai';

                            // Stop the interval if completed
                            clearInterval(progressInterval);
                        } else {
                            progressBar.classList.replace('from-emerald-400', 'from-blue-500');
                            progressBar.classList.replace('to-emerald-500', 'to-blue-600');
                            statusBadge.className = 'bg-yellow-400 text-yellow-900 text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm';
                            statusBadge.innerHTML = '<span class="w-2 h-2 bg-yellow-600 rounded-full animate-ping"></span> Berjalan';
                        }
                    } else {
                        container.style.display = 'none';
                        clearInterval(progressInterval);
                    }
                })
                .catch(error => console.error('Gagal mengambil data progress:', error));
        }

        // ======================= INIT ALL (Hanya Satu DOMContentLoaded) =======================
        document.addEventListener("DOMContentLoaded", function () {
            // Init Chart
            initChart(@json(array_values($promoStats ?? [])));

            // Init Dynamic Form Promo
            addDurationBlock();

            // Lock End Date input
            const inputMulai = document.querySelector('input[name="tanggal_mulai"]');
            const inputSelesai = document.querySelector('input[name="tanggal_selesai"]');
            if (inputMulai && inputSelesai) {
                inputMulai.addEventListener('change', function () {
                    inputSelesai.min = this.value;
                    if (inputSelesai.value && inputSelesai.value < this.value) {
                        inputSelesai.value = '';
                    }
                });
            }

            // Init & Start Polling for Progress Bar
            fetchBroadcastProgress();
            progressInterval = setInterval(fetchBroadcastProgress, 3000); // Polling tiap 3 detik
        });

        // ======================= TOAST ALERTS =======================
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}", confirmButtonColor: '#ef4444' });
        @endif
    </script>
@endpush