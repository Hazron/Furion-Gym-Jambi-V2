@extends('Owner.OwnerTemplate')

@section('title', 'Data Member Furion Gym Jambi')

@section('header-content')
    <div class="flex flex-col justify-center">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 tracking-tight">Data Member</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data member, status membership, dan perpanjangan.</p>
    </div>
@endsection

@section('content')
    {{-- CSS Tailwind untuk DataTables & Scrollbar Mobile --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 5px;
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_length select {
            @apply rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500;
        }

        .dataTables_wrapper .dataTables_filter input {
            @apply rounded-lg border-gray-200 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 outline-none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-gray-900 text-white border-none rounded-lg !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            @apply bg-gray-100 border-none rounded-lg text-gray-800 !important;
        }

        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 8px;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left !important;
            }
        }
    </style>

    {{-- 1. INFOBOX STATISTIK --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8 print:grid-cols-4">
        <div
            class="bg-gradient-to-br from-indigo-600 to-blue-500 rounded-3xl p-4 sm:p-6 text-white shadow-lg shadow-blue-200 print:bg-white print:text-black print:border">
            <p class="text-blue-100 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">Total Member</p>
            <h3 class="text-xl sm:text-3xl font-black mt-1 truncate">{{ $totalMember ?? 0 }} <span
                    class="text-[10px] sm:text-sm font-normal">Member</span></h3>
        </div>
        <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
            </div>
            <div class="relative z-10">
                <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">Member Aktif
                </p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $memberAktif ?? 0 }}</h3>
                <p class="text-[9px] sm:text-xs text-green-600 mt-1 sm:mt-2 font-medium flex items-center gap-1 truncate">
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-green-500"></span> Status Active</p>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
            </div>
            <div class="relative z-10">
                <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">Member
                    NonAktif</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $expiredMember ?? 0 }}</h3>
                <p class="text-[9px] sm:text-xs text-red-500 mt-1 sm:mt-2 font-medium flex items-center gap-1 truncate">
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-red-500"></span> Status Inactive</p>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
            </div>
            <div class="relative z-10">
                <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">Registrasi
                    Baru</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $memberBaruBulanIni ?? 0 }}</h3>
                <p class="text-[9px] sm:text-xs text-orange-500 mt-1 sm:mt-2 font-medium truncate">Bulan Ini</p>
            </div>
        </div>
    </div>

    {{-- 2. KOTAK PENCARIAN & TABEL UTAMA --}}
    <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4 sm:mb-6">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="customSearch"
                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-colors"
                    placeholder="Cari nama member...">
            </div>
            <div class="flex w-full sm:w-auto">
                <select id="filterStatus"
                    class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-50">
            <table id="membersTable" class="w-full text-sm text-left text-gray-500" >
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-4 rounded-tl-xl w-10 text-center">No</th>
                        <th scope="col" class="px-4 py-4">Nama Lengkap</th>
                        <th scope="col" class="px-4 py-4 text-center">Status</th>
                        <th scope="col" class="px-4 py-4 hidden md:table-cell">Paket</th>
                        <th scope="col" class="px-4 py-4 hidden lg:table-cell">Mulai</th>
                        <th scope="col" class="px-4 py-4 hidden sm:table-cell">Selesai</th>
                        <th scope="col" class="px-4 py-4 rounded-tr-xl text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white"></tbody>
            </table>
        </div>
    </div>

    {{-- 3. MODAL DETAIL MEMBER (MOBILE BOTTOM SHEET & DESKTOP POPUP) --}}
    <div id="detailMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeDetailModal()">
        </div>

        {{-- items-end di mobile agar jadi bottom sheet, items-center di desktop agar di tengah --}}
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-left">
            <div id="modalBoxDetail"
                class="relative transform overflow-hidden bg-white shadow-2xl transition-all w-full max-w-3xl opacity-0 scale-95 translate-y-full sm:translate-y-4 rounded-t-3xl sm:rounded-3xl mt-12 sm:mt-0 flex flex-col max-h-[90vh]">

                {{-- Handle drag visual untuk mobile --}}
                <div class="w-full flex justify-center pt-3 pb-1 sm:hidden">
                    <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="flex justify-between items-center px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-white">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Detail Member</h3>
                    <button type="button" onclick="window.closeDetailModal()"
                        class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="px-5 sm:px-6 py-5 overflow-y-auto custom-scrollbar bg-gray-50/30 flex-grow">

                    {{-- Profil Wrapper (Horizontal di HP agar irit space) --}}
                    <div class="flex items-center sm:items-start gap-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-14 w-14 sm:h-20 sm:w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl sm:text-3xl font-black shadow-md shadow-blue-200"
                                id="detail-initials"></div>
                        </div>
                        <div class="flex-grow w-full">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1">
                                <h2 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight leading-tight"
                                    id="detail-nama">-</h2>
                                <div><span id="detail-status"
                                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-[10px] sm:text-xs font-bold text-green-700 border border-green-200">Active</span>
                                </div>
                            </div>
                            <p class="text-[10px] sm:text-xs font-bold text-blue-600 bg-blue-50 inline-block px-2 py-1 rounded-md mt-1"
                                id="detail-paket">-</p>
                        </div>
                    </div>

                    {{-- Grid Info --}}
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
                        <div>
                            <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">
                                Kontak</p>
                            <p class="text-sm font-bold text-gray-800 truncate" id="detail-telp">-</p>
                            <p class="text-xs text-gray-500 truncate" id="detail-email">-</p>
                        </div>
                        <div class="hidden sm:block w-px bg-gray-100 h-full"></div> {{-- Divider desktop --}}
                        <div class="border-t border-gray-100 pt-3 sm:border-0 sm:pt-0">
                            <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Masa
                                Aktif</p>
                            <p class="text-sm font-bold text-gray-800" id="detail-join">Join: -</p>
                            <p class="text-xs text-gray-500" id="detail-masa-aktif">Exp: -</p>
                        </div>
                    </div>

                    {{-- Riwayat --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Riwayat Pembayaran
                            </h4>
                            <span
                                class="text-[10px] sm:text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2 py-1 rounded-lg"
                                id="total-transaksi-badge">0 Transaksi</span>
                        </div>

                        <div class="overflow-x-auto bg-white border border-gray-100 rounded-2xl shadow-sm custom-scrollbar">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead
                                    class="bg-gray-50/80 border-b border-gray-100 text-[10px] sm:text-xs text-gray-500 uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-4">Transaksi</th>
                                        <th class="py-3 px-4 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50" id="transaction-table-body"></tbody>
                            </table>
                        </div>
                        <div id="empty-transaction"
                            class="hidden text-center py-6 bg-white border border-gray-100 rounded-2xl mt-2">
                            <p class="text-xs sm:text-sm text-gray-400 font-medium">Belum ada riwayat transaksi.</p>
                        </div>
                        <div id="detail-pagination-container" class="mt-3"></div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="bg-white px-5 sm:px-6 py-4 border-t border-gray-100 sm:rounded-b-3xl">
                    <button type="button" onclick="window.closeDetailModal()"
                        class="w-full sm:w-auto float-right rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gray-200 hover:bg-gray-800 transition-all active:scale-95">Tutup
                        Detail</button>
                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- CDN --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    {{-- Variables untuk Vite JS --}}
    <script>
        window.routeMemberFurion = "{{ route('owner.memberFurion') }}";
    </script>

    {{-- Script Vite JS --}}
    @vite('resources/js/Owner/MemberFurion.js')
@endpush