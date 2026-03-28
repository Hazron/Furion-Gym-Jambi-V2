@extends('Owner.OwnerTemplate')

@section('title', 'Aktivitas Admin')

@section('header-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 no-print">
        <div>
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Aktivitas Admin</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pantau kinerja dan kelola akun admin.</p>
        </div>
        <button onclick="window.openCreateModal()"
            class="flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-2.5 rounded-xl transition shadow-lg shadow-gray-200 text-xs sm:text-sm font-bold active:scale-95 hover:-translate-y-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Admin
        </button>
    </div>
@endsection

@section('content')

    {{-- SECTION 1: LIST ADMIN (Cards & Filter) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8 no-print">

        {{-- Kartu "SEMUA ADMIN" --}}
        <div onclick="window.filterTable('all')" id="card-all"
            class="admin-card cursor-pointer bg-blue-50 border-2 border-blue-500 p-4 rounded-2xl shadow-sm hover:shadow-md transition relative group flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-sm">Semua Aktivitas</h4>
                <p class="text-xs text-gray-500">Tampilkan total gabungan</p>
            </div>
        </div>

        {{-- Loop Data Admin (Variabel $admins dikirim dari Controller) --}}
        @foreach($admins as $admin)
            <div id="card-{{ $admin->id }}"
                class="admin-card cursor-pointer bg-white border border-gray-200 p-4 rounded-2xl shadow-sm hover:shadow-md transition relative group">

                {{-- Area Klik untuk Filter --}}
                <div onclick="window.filterTable({{ $admin->id }})" class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-lg font-bold border border-gray-200 shrink-0">
                        {{ substr($admin->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm truncate">{{ $admin->name }}</h4>
                        <p class="text-xs text-gray-500 truncate">{{ $admin->email }}</p>
                    </div>
                </div>

                {{-- Tombol Edit/Delete --}}
                <div
                    class="absolute top-3 right-3 flex gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity bg-white/80 backdrop-blur-sm pl-2 rounded-bl-lg">
                    <button onclick="window.openEditModal({{ $admin->id }}, '{{ $admin->name }}', '{{ $admin->email }}', event)"
                        class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Edit Admin">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <form action="{{ route('owner.admin.delete', $admin->id) }}" method="POST"
                        onsubmit="return confirm('Hapus admin ini? Data transaksi tidak akan hilang, tapi akun akan terhapus.')"
                        class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                            title="Hapus Admin">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- SECTION 2: TABEL AKTIVITAS --}}
    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 no-print overflow-hidden">

        {{-- HEADER: JUDUL & FILTER TANGGAL --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
            <h3 class="text-lg font-bold text-gray-800" id="table-title">Riwayat Aktivitas: Semua Admin</h3>

            {{-- FILTER WAKTU DINAMIS --}}
            <div class="flex flex-col sm:flex-row gap-3 items-center w-full xl:w-auto">

                {{-- Mode Pilihan --}}
                <div
                    class="inline-flex bg-gray-100/80 rounded-xl p-1 shadow-inner w-full sm:w-auto overflow-x-auto no-scrollbar">
                    <button type="button" onclick="window.setFilterMode('today')" id="btn-filter-today"
                        class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-900 transition-colors whitespace-nowrap">Hari
                        Ini</button>
                    <button type="button" onclick="window.setFilterMode('week')" id="btn-filter-week"
                        class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-900 transition-colors whitespace-nowrap">Minggu
                        Ini</button>
                    <button type="button" onclick="window.setFilterMode('month')" id="btn-filter-month"
                        class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg bg-white shadow-sm text-blue-600 transition-colors whitespace-nowrap">Bulan</button>
                    <button type="button" onclick="window.setFilterMode('year')" id="btn-filter-year"
                        class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-900 transition-colors whitespace-nowrap">Tahun</button>
                </div>

                {{-- Kontrol Navigasi (Mundur / Maju) - Hanya untuk Bulan & Tahun --}}
                <div id="dynamic-filter-controls"
                    class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                    <button type="button" onclick="window.navigateFilter(-1)"
                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <span id="filter-label" class="text-xs font-bold text-gray-700 min-w-[95px] text-center"></span>
                    <button type="button" onclick="window.navigateFilter(1)"
                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto no-scrollbar">
            <table id="aktivitasAdminTable" class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider rounded-tl-xl">Waktu
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider rounded-tr-xl text-center">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700"></tbody>
            </table>
        </div>
    </div>

    {{-- MODAL 1: CRUD ADMIN (Create/Edit) --}}
    <div id="adminModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeAdminModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all duration-300 scale-95">
                <form id="adminForm" method="POST" action="">
                    @csrf
                    <div id="methodField"></div>

                    <div class="bg-white px-6 pb-6 pt-6">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 mb-5" id="adminModalTitle">Tambah Admin Baru</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama
                                    Lengkap</label>
                                <input type="text" name="name" id="inputName" required
                                    class="w-full rounded-xl border-gray-200 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Email</label>
                                <input type="email" name="email" id="inputEmail" required
                                    class="w-full rounded-xl border-gray-200 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Password</label>
                                <input type="password" name="password" id="inputPassword"
                                    class="w-full rounded-xl border-gray-200 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-[10px] text-gray-400 mt-1" id="passHelp">Minimal 6 karakter.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">Simpan</button>
                        <button type="button" onclick="window.closeAdminModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: DETAIL AKTIVITAS --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeDetailModal()"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all duration-300 scale-95">
                <div class="bg-white px-6 pb-6 pt-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="mt-0 text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Aktivitas</h3>
                            {{-- Loading --}}
                            <div id="modalLoading" class="mt-6 flex justify-center py-4">
                                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                            {{-- Content --}}
                            <div id="modalContent" class="mt-4 text-sm text-gray-600 hidden"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="window.closeDetailModal()"
                        class="w-full inline-flex justify-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Aktifkan kembali CDN ini --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- Global Variables for JS --}}
    <script>
        window.routeAktivitasAdmin = "{{ route('owner.aktivitasadmin') }}";
        window.routeAktivitasDetail = "{{ route('owner.aktivitas-detail') }}";
        window.routeAdminStore = "{{ route('owner.admin.store') }}";
        window.routeAdminUpdate = "{{ route('owner.admin.update', ':id') }}";
    </script>
    
    {{-- Memanggil File JS Menggunakan Vite --}}
    @vite('resources/js/Owner/AktivitasAdmin.js')
@endpush