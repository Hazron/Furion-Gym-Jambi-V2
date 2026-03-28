@extends('Owner.OwnerTemplate')

@section('title', 'Kelola Paket Member Reguler')

@section('header-content')
    <div class="flex flex-col justify-center">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Paket Member Reguler</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola harga dan durasi membership.</p>
    </div>
@endsection

@section('content')

    {{-- 1. ACTION BAR --}}
    <div class="flex justify-start mb-6 sm:mb-8">
        <button onclick="openModal('addPaketModal')"
            class="group flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl sm:rounded-2xl font-bold text-xs sm:text-sm shadow-lg shadow-gray-200 transition-all hover:-translate-y-0.5 active:scale-95">
            <div
                class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            Buat Paket Baru
        </button>
    </div>

    {{-- 2. GRID CARD PAKET --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10 items-start">
        @php
            $paketReguler = $pakets->where('jenis', 'reguler');
            $paketcouple = $pakets->where('jenis', 'couple');

            $regulerActive = $paketReguler->where('status', 'aktif');
            $regulerInactive = $paketReguler->where('status', '!=', 'aktif');

            $coupleActive = $paketcouple->where('status', 'aktif');
            $coupleInactive = $paketcouple->where('status', '!=', 'aktif');
        @endphp

        {{-- ================================================= --}}
        {{-- ========== KOLOM KIRI (PAKET REGULER) =========== --}}
        {{-- ================================================= --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 mb-1 px-1">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800 leading-tight">Paket Reguler</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-semibold">Keanggotaan Perorangan</p>
                </div>
            </div>

            {{-- AKTIF REGULER --}}
            @foreach($regulerActive as $paket)
                <div
                    class="group relative rounded-2xl p-4 sm:p-5 border shadow-sm transition-all duration-300 overflow-hidden flex flex-col bg-white border-blue-100 hover:shadow-md hover:-translate-y-1">
                    <div class="absolute top-4 right-4 z-10">
                        <span
                            class="bg-emerald-50 text-emerald-600 border-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider">Aktif</span>
                    </div>
                    <div class="absolute left-0 top-6 bottom-6 w-1 rounded-r-full bg-blue-500"></div>
                    <div class="mb-3 pl-3 relative pr-14">
                        <h3 class="text-base font-black text-gray-800 group-hover:text-blue-600 transition leading-tight">
                            {{ $paket->nama_paket }}
                        </h3>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durasi {{ $paket->durasi }}
                            Bulan</span>
                    </div>
                    <div class="mb-3 pl-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-[10px] font-bold text-gray-400">Rp</span>
                            <span
                                class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ number_format($paket->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="rounded-xl p-3 mb-4 border bg-gray-50/50 border-gray-100 text-gray-600 flex-1">
                        <p class="text-xs leading-relaxed line-clamp-2">{{ $paket->deskripsi ?? 'Akses penuh fasilitas gym.' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 mt-auto">
                        <button
                            onclick="openEditModal({{ json_encode($paket) }}, '{{ route('owner.paket.update', $paket->id_paket) }}')"
                            class="flex-1 py-2 rounded-lg font-bold text-[11px] border bg-white border-gray-200 text-gray-600 hover:border-blue-600 hover:text-blue-600 shadow-sm transition uppercase tracking-wide">
                            Edit
                        </button>
                        {{-- PERBAIKAN: Menambahkan ID Form untuk SweetAlert --}}
                        <form id="toggle-form-{{ $paket->id_paket }}"
                            action="{{ route('owner.paket.toggle', $paket->id_paket) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="button"
                                onclick="confirmStatusChange('{{ $paket->id_paket }}', '{{ $paket->nama_paket }}', 'nonaktifkan')"
                                class="w-9 h-9 bg-red-50 text-red-500 border-red-100 hover:bg-red-500 flex items-center justify-center rounded-lg transition border hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- PEMISAH NON-AKTIF REGULER --}}
            @if($regulerInactive->count())
                <div class="pt-3 pb-1 border-t border-gray-100 mt-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Arsip Paket Non-Aktif</span>
                </div>
                @foreach($regulerInactive as $paket)
                    <div
                        class="relative rounded-2xl p-4 border bg-gray-50 border-gray-200 opacity-75 hover:opacity-100 transition-opacity flex flex-col">
                        <div class="absolute top-4 right-4 z-10">
                            <span
                                class="bg-gray-200 text-gray-500 border-gray-300 text-[9px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider">Non-Aktif</span>
                        </div>
                        <div class="absolute left-0 top-5 bottom-5 w-1 rounded-r-full bg-gray-300"></div>
                        <div class="mb-3 pl-3 relative pr-16">
                            <h3 class="text-sm font-black text-gray-500 leading-tight mb-0.5">{{ $paket->nama_paket }}</h3>
                            <span class="text-[10px] font-bold text-gray-400">Durasi {{ $paket->durasi }} Bulan</span>
                        </div>
                        {{-- PERBAIKAN: Menambahkan ID Form untuk SweetAlert --}}
                        <form id="toggle-form-{{ $paket->id_paket }}" action="{{ route('owner.paket.toggle', $paket->id_paket) }}"
                            method="POST" class="mt-auto">
                            @csrf @method('PATCH')
                            <button type="button"
                                onclick="confirmStatusChange('{{ $paket->id_paket }}', '{{ $paket->nama_paket }}', 'aktifkan')"
                                class="w-full py-2 bg-white text-emerald-600 border-emerald-200 hover:bg-emerald-500 hover:text-white rounded-lg transition border font-bold text-[11px] uppercase tracking-wide shadow-sm">
                                Aktifkan
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ================================================= --}}
        {{-- ========== KOLOM KANAN (PAKET COUPLE) =========== --}}
        {{-- ================================================= --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 mb-1 px-1">
                <div class="p-2 bg-pink-50 text-pink-500 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800 leading-tight">Paket Couple</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-semibold">Membership Pasangan</p>
                </div>
            </div>

            {{-- AKTIF COUPLE --}}
            @foreach($coupleActive as $paket)
                <div
                    class="group relative rounded-2xl p-4 sm:p-5 border shadow-sm transition-all duration-300 overflow-hidden flex flex-col bg-white border-pink-100 hover:shadow-md hover:-translate-y-1">
                    <div class="absolute top-4 right-4 z-10">
                        <span
                            class="bg-emerald-50 text-emerald-600 border-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider">Aktif</span>
                    </div>
                    <div class="absolute left-0 top-6 bottom-6 w-1 rounded-r-full bg-pink-500"></div>
                    <div class="mb-3 pl-3 relative pr-14">
                        <h3 class="text-base font-black text-gray-800 group-hover:text-pink-600 transition leading-tight">
                            {{ $paket->nama_paket }}
                        </h3>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durasi {{ $paket->durasi }}
                            Bulan</span>
                    </div>
                    <div class="mb-3 pl-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-[10px] font-bold text-gray-400">Rp</span>
                            <span
                                class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ number_format($paket->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="rounded-xl p-3 mb-4 border bg-gray-50/50 border-gray-50 text-gray-600 flex-1">
                        <p class="text-xs leading-relaxed line-clamp-2">{{ $paket->deskripsi ?? 'Akses fasilitas gym berdua.' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 mt-auto">
                        <button
                            onclick="openEditModal({{ json_encode($paket) }}, '{{ route('owner.paket.update', $paket->id_paket) }}')"
                            class="flex-1 py-2 rounded-lg font-bold text-[11px] border bg-white border-gray-200 text-gray-600 hover:border-pink-500 hover:text-pink-600 shadow-sm transition uppercase tracking-wide">
                            Edit
                        </button>
                        {{-- PERBAIKAN: Menambahkan ID Form untuk SweetAlert --}}
                        <form id="toggle-form-{{ $paket->id_paket }}"
                            action="{{ route('owner.paket.toggle', $paket->id_paket) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="button"
                                onclick="confirmStatusChange('{{ $paket->id_paket }}', '{{ $paket->nama_paket }}', 'nonaktifkan')"
                                class="w-9 h-9 bg-red-50 text-red-500 border-red-100 hover:bg-red-500 flex items-center justify-center rounded-lg transition border hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- PEMISAH NON-AKTIF COUPLE --}}
            @if($coupleInactive->count())
                <div class="pt-3 pb-1 border-t border-gray-100 mt-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Arsip Paket Non-Aktif</span>
                </div>
                @foreach($coupleInactive as $paket)
                    <div
                        class="relative rounded-2xl p-4 border bg-gray-50 border-gray-200 opacity-75 hover:opacity-100 transition-opacity flex flex-col">
                        <div class="absolute top-4 right-4 z-10">
                            <span
                                class="bg-gray-200 text-gray-500 border-gray-300 text-[9px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider">Non-Aktif</span>
                        </div>
                        <div class="absolute left-0 top-5 bottom-5 w-1 rounded-r-full bg-gray-300"></div>
                        <div class="mb-3 pl-3 relative pr-16">
                            <h3 class="text-sm font-black text-gray-500 leading-tight mb-0.5">{{ $paket->nama_paket }}</h3>
                            <span class="text-[10px] font-bold text-gray-400">Durasi {{ $paket->durasi }} Bulan</span>
                        </div>
                        {{-- PERBAIKAN: Menambahkan ID Form untuk SweetAlert --}}
                        <form id="toggle-form-{{ $paket->id_paket }}" action="{{ route('owner.paket.toggle', $paket->id_paket) }}"
                            method="POST" class="mt-auto">
                            @csrf @method('PATCH')
                            <button type="button"
                                onclick="confirmStatusChange('{{ $paket->id_paket }}', '{{ $paket->nama_paket }}', 'aktifkan')"
                                class="w-full py-2 bg-white text-emerald-600 border-emerald-200 hover:bg-emerald-500 hover:text-white rounded-lg transition border font-bold text-[11px] uppercase tracking-wide shadow-sm">
                                Aktifkan
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

    {{-- ================= MODALS ================= --}}
    {{-- 1. MODAL TAMBAH --}}
    <div id="addPaketModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('addPaketModal')">
        </div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all duration-300 scale-95">

                {{-- Header --}}
                <div
                    class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Buat Paket Baru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Tambahkan opsi membership untuk pelanggan.</p>
                    </div>
                    <button onclick="closeModal('addPaketModal')"
                        class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 flex items-center justify-center transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('owner.paket.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-6 space-y-6">

                        {{-- 1. PILIHAN JENIS PAKET (VISUAL CARDS) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 ml-1">Jenis
                                Membership</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="jenis" value="reguler" class="peer sr-only" checked>
                                    <div
                                        class="rounded-2xl border-2 border-gray-100 bg-white p-4 flex flex-col items-center justify-center gap-2 transition-all duration-200 hover:border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 peer-checked:shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center transition-colors peer-checked:bg-blue-600 peer-checked:text-white">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span
                                            class="text-sm font-bold text-gray-600 peer-checked:text-blue-700">Reguler</span>
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-blue-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </label>

                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="jenis" value="couple" class="peer sr-only">
                                    <div
                                        class="rounded-2xl border-2 border-gray-100 bg-white p-4 flex flex-col items-center justify-center gap-2 transition-all duration-200 hover:border-pink-200 peer-checked:border-pink-500 peer-checked:bg-pink-50/50 peer-checked:shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center transition-colors peer-checked:bg-pink-600 peer-checked:text-white">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span
                                            class="text-sm font-bold text-gray-600 peer-checked:text-pink-700">Couple</span>
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity text-pink-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- 2. NAMA PAKET --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Nama
                                Paket</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                </div>
                                <input type="text" name="nama_paket" required
                                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition outline-none placeholder:text-gray-400"
                                    placeholder="Contoh: Gold Membership">
                            </div>
                        </div>

                        {{-- 3. HARGA & DURASI (Side by Side) --}}
                        <div class="grid grid-cols-2 gap-5">
                            {{-- Harga --}}
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Harga</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="harga" required
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm font-bold focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition outline-none"
                                        placeholder="0">
                                </div>
                            </div>

                            {{-- Durasi --}}
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Durasi</label>
                                <div class="relative">
                                    <input type="number" name="durasi" required min="1" max="36"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm font-bold focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition outline-none"
                                        placeholder="1">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-xs font-bold text-gray-400 uppercase">Bulan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-3xl border-t border-gray-100">
                        <button type="button" onclick="closeModal('addPaketModal')"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-800 transition shadow-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 shadow-lg shadow-blue-200 transition transform active:scale-95">
                            Simpan Paket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. MODAL EDIT --}}
    <div id="editPaketModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            onclick="closeModal('editPaketModal')"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-xl transition-all duration-300 scale-95">

                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Edit Paket</h3>
                    <button type="button" onclick="closeModal('editPaketModal')"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama
                                Paket</label>
                            <input type="text" id="edit_nama" name="nama_paket" required
                                class="w-full border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm py-3 px-4">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Harga
                                    (Rp)</label>
                                <input type="number" id="edit_harga" name="harga" required
                                    class="w-full border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm py-3 px-4 font-bold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Durasi
                                    (Bulan)</label>
                                <input type="number" id="edit_durasi" name="durasi" required
                                    class="w-full border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm py-3 px-4">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" onclick="closeModal('editPaketModal')"
                            class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200">Simpan
                            Perubahan</button>
                    </div>
                        <input type="hidden" id="edit_id">
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // FUNGSI MODAL
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            // Delay sedikit agar transisi opacity berjalan halus
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div[class*="transform"]').classList.remove('scale-95');
                modal.querySelector('div[class*="transform"]').classList.add('scale-100');
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('opacity-0');
            modal.querySelector('div[class*="transform"]').classList.remove('scale-100');
            modal.querySelector('div[class*="transform"]').classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300); // Sesuaikan dengan durasi transition CSS
        }

        // FUNGSI EDIT MODAL
        // FUNGSI EDIT MODAL
        function openEditModal(paket, updateUrl) {
            document.getElementById('edit_id').value = paket.id_paket;
            document.getElementById('edit_nama').value = paket.nama_paket;
            document.getElementById('edit_harga').value = paket.harga;

            let durasiHanyaAngka = paket.durasi ? String(paket.durasi).replace(/\D/g, '') : '';
            document.getElementById('edit_durasi').value = durasiHanyaAngka;

            // Set action form secara dinamis
            document.getElementById('editForm').action = updateUrl;

            openModal('editPaketModal');
        }

        // FUNGSI KONFIRMASI STATUS (SweetAlert2)
        function confirmStatusChange(id, nama, aksi) {
            let warnaTombol = aksi === 'nonaktifkan' ? '#d33' : '#10b981';
            let teksKonfirmasi = aksi === 'nonaktifkan' ?
                "Paket ini tidak akan bisa dipilih lagi." :
                "Paket ini akan tersedia kembali.";

            Swal.fire({
                title: `Yakin ${aksi} paket?`,
                text: `${nama} - ${teksKonfirmasi}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: warnaTombol,
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Ya, ${aksi}!`,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                width: '320px' // Agar pas di mobile
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('toggle-form-' + id).submit();
                }
            });
        }

        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif
    </script>
@endpush