@extends('Admin.dashboardAdminTemplate')

@section('header-content')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Paket Member</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola data harga dan jenis membership gym.</p>
        </div>
    </div>
@endsection

@section('content')

    @php
        $actives = $paketMember->where('status', 'aktif');
        $inactives = $paketMember->where('status', '!=', 'aktif');

        $promos = $actives->filter(function ($paket) {
            return strtolower($paket->jenis ?? '') === 'promo';
        });

        $regulars = $actives->reject(function ($paket) {
            return strtolower($paket->jenis ?? '') === 'promo';
        });
    @endphp

    {{-- ================================================================================== --}}
    {{-- BAGIAN 1: PAKET PROMO (HANYA YANG AKTIF) --}}
    {{-- ================================================================================== --}}
    @if($promos->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Paket Promo & Diskon</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Hot Deals</span>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-xl shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <span class="font-bold">Reminder:</span> Jangan lupa untuk promosikan paket ini ke member atau
                            non-member!
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($promos as $paket)
                    <div
                        class="group relative bg-white rounded-3xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl border border-yellow-400 shadow-yellow-100 ring-4 ring-yellow-50">

                        <div class="absolute -top-3 right-6 z-10">
                            <span
                                class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg shadow-orange-500/30 uppercase tracking-widest flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Special Promo
                            </span>
                        </div>

                        <div class="flex items-start justify-between mb-5">
                            <div class="p-3.5 rounded-2xl bg-yellow-100 text-yellow-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-600">
                                    {{ $paket->durasi }} Bulan
                                </span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1"
                                title="{{ $paket->nama_paket }}">
                                {{ $paket->nama_paket }}
                            </h3>

                            <div class="mb-4">
                                <p class="text-xs text-gray-400">Paket promo terbatas</p>
                                <p class="text-xs font-semibold text-orange-500 flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ date('d M Y', strtotime($paket->promo_mulai)) }} -
                                    {{ date('d M Y', strtotime($paket->promo_selesai)) }}
                                </p>
                            </div>

                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-semibold text-gray-500">Rp</span>
                                <span
                                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($paket->harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-8 border-t border-gray-100 pt-6">
                            <div class="flex items-center gap-3 text-sm text-gray-600 animate-pulse">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="font-bold text-yellow-700">DISKON</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Akses Gym</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================================================================================== --}}
    {{-- BAGIAN 2: PAKET REGULER (HANYA YANG AKTIF) --}}
    {{-- ================================================================================== --}}
    <div class="mb-12">
        <h3 class="text-xl font-bold text-gray-800 mb-4 pl-1 border-l-4 border-blue-600">Paket Reguler</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($regulars as $paket)
                <div
                    class="group relative bg-white rounded-3xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl border border-gray-100 shadow-sm hover:border-blue-100">

                    <div class="flex items-start justify-between mb-5">
                        <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <span
                                class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-500">
                                {{ $paket->durasi }} Bulan
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1"
                            title="{{ $paket->nama_paket }}">
                            {{ $paket->nama_paket }}
                        </h3>
                        <p class="text-xs text-gray-400 mb-4">Paket membership reguler</p>

                        <div class="flex items-baseline gap-1">
                            <span class="text-sm font-semibold text-gray-500">Rp</span>
                            <span
                                class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($paket->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-y-3 gap-x-2">

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                <span class="truncate">Akses Gym</span>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                <span class="truncate">Loker Room</span>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                <span class="truncate">Kamar Mandi</span>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                <span class="truncate">Handuk</span>
                            </div>

                        </div>
                    </div>

                </div>
            @empty
                @if($promos->isEmpty())
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-16 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <div class="bg-gray-50 p-4 rounded-full shadow-sm mb-4 animate-bounce">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Belum ada paket tersedia</h3>
                        <p class="text-gray-500 mt-2 mb-6 max-w-sm mx-auto">Mulai dengan menambahkan paket membership pertama Anda
                            agar member dapat mendaftar.</p>
                        <button
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                            Buat Paket Pertama
                        </button>
                    </div>
                @else
                    <div class="col-span-full py-8 text-center text-gray-400 italic">
                        Tidak ada paket reguler aktif saat ini.
                    </div>
                @endif
            @endforelse
        </div>
    </div>

    {{-- ================================================================================== --}}
    {{-- BAGIAN 3: PAKET NONAKTIF (HISTORY / ARCHIVE) --}}
    {{-- ================================================================================== --}}
    @if($inactives->isNotEmpty())
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex items-center gap-2 mb-6 opacity-70">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-500">Arsip Paket (Nonaktif)</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($inactives as $paket)
                    <div
                        class="bg-gray-50 rounded-xl p-4 border border-gray-200 opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300 hover:shadow-md cursor-not-allowed">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-red-500 border border-red-200 bg-red-50 px-2 py-0.5 rounded">
                                Nonaktif
                            </span>
                            @if(strtolower($paket->jenis) == 'promo')
                                <span class="text-[10px] font-bold text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded">Promo</span>
                            @else
                                <span class="text-[10px] font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded">Reguler</span>
                            @endif
                        </div>

                        <h4 class="font-bold text-gray-700 text-sm mb-1 line-clamp-1">{{ $paket->nama_paket }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ $paket->durasi }} Bulan</p>

                        <div class="text-sm font-bold text-gray-600">
                            Rp {{ number_format($paket->harga, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection