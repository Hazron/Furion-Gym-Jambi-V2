@extends('Owner.OwnerTemplate')

@section('title', 'Laporan Membership Furion Gym Jambi')

@section('header-content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Laporan Membership</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Analisis pertumbuhan, perpanjangan, dan minat paket member.
            </p>
        </div>
    </div>
@endsection

@section('content')

    <style>
        .dataTables_wrapper .dataTables_length select {
            @apply rounded-lg border-gray-200 text-sm;
        }

        .dataTables_wrapper .dataTables_filter input {
            @apply rounded-lg border-gray-200 text-sm px-4 py-2;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-gray-900 text-white border-none rounded-lg !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            @apply bg-gray-100 border-none rounded-lg !important;
        }

        /* Styling tambahan agar tombol filter bisa digeser rapi di HP */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        @media print {
            @page {
                margin: 0.5cm;
                size: landscape;
            }

            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
            }

            .no-print,
            nav,
            aside,
            header,
            .sidebar,
            #sidebar-backdrop {
                display: none !important;
            }

            .main-content,
            .p-4,
            .sm\:ml-72 {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .shadow-sm,
            .shadow-lg,
            .border,
            .shadow {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .bg-gradient-to-br {
                background: #f3f4f6 !important;
                color: black !important;
            }
        }
    </style>

    {{-- WRAPPER UNTUK MENGATUR POSISI FILTER & CARD (RESPONSIVE ORDERING) --}}
    <div class="flex flex-col">

        {{-- FILTER WAKTU --}}
        {{-- Mobile: order-2 (di bawah card), Desktop: sm:order-1 (di atas card) --}}
        <div class="order-2 sm:order-1 mb-8 sm:mb-6 no-print">
            <form action="{{ route('owner.laporan-Membership') }}" method="GET">
                <div
                    class="inline-flex flex-nowrap items-center bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-full sm:w-auto overflow-x-auto custom-scrollbar pb-1.5 sm:pb-1.5">
                    <div class="flex flex-nowrap gap-1 w-max sm:w-auto">
                        @foreach(['hari' => 'Hari Ini', 'minggu' => 'Minggu ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun', 'seluruh' => 'Seluruh'] as $val => $label)
                            <button type="submit" name="periode" value="{{ $val }}"
                                class="px-4 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-200 whitespace-nowrap
                                                {{ ($periode ?? 'bulan') == $val ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- SUMMARY CARDS --}}
        {{-- Mobile: order-1 (di atas filter), Desktop: sm:order-2 (di bawah filter) --}}
        <div class="order-1 sm:order-2 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4 sm:mb-8 print:grid-cols-4">

            {{-- Card 1: Total Aktivitas --}}
            <div
                class="col-span-2 sm:col-span-1 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-3xl p-4 sm:p-6 text-white shadow-lg shadow-blue-200 print:bg-white print:text-black print:border">
                <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider truncate">Total Aktivitas</p>
                <h3 class="text-xl sm:text-3xl font-black mt-1 truncate">{{ count($laporanData) }} <span
                        class="text-xs sm:text-sm font-normal">Transaksi</span></h3>
                <p class="text-[9px] sm:text-[10px] text-blue-100/80 mt-2 sm:mt-3 uppercase tracking-tighter truncate">
                    {{ $periode ?? 'Bulan' }} Ini</p>
            </div>

            {{-- Card 2: Registrasi --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">
                        Registrasi</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $countRegistrasi }}</h3>
                    <p class="text-[9px] sm:text-xs text-blue-600 mt-1 sm:mt-2 font-medium truncate">Member Baru</p>
                </div>
            </div>

            {{-- Card 3: Renewal --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">Renewal
                    </p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $countRenewal }}</h3>
                    <p class="text-[9px] sm:text-xs text-green-600 mt-1 sm:mt-2 font-medium truncate">Perpanjangan</p>
                </div>
            </div>

            {{-- Card 4: Reactivation --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider truncate">
                        Re-Aktifasi</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">{{ $countReactivation }}</h3>
                    <p class="text-[9px] sm:text-xs text-orange-500 mt-1 sm:mt-2 font-medium truncate">Aktif Kembali</p>
                </div>
            </div>
        </div>

    </div> {{-- End of Wrapper Order --}}


    {{-- GRAFIK & PIE CHART --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-8 print:block">
        {{-- BAGIAN KIRI: Tren --}}
        <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 print:mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h4 class="text-lg font-bold text-gray-800">Tren Pertumbuhan Member</h4>
                    <p class="text-xs text-gray-400">Analisis aktivitas {{ ucfirst($periode ?? 'Bulan') }}</p>
                </div>
                @if(isset($periode) && $periode != 'seluruh' && $periode != 'hari' && $periode != 'minggu')
                    <div class="flex justify-center w-full sm:w-auto no-print">
                        <div
                            class="inline-flex items-center bg-white p-1 rounded-xl border border-gray-200 shadow-sm w-full sm:w-auto justify-between">
                            {{-- Tombol Mundur --}}
                            <a href="{{ route('owner.laporan-Membership', ['periode' => $periode, 'date' => $prevLink]) }}"
                                class="p-1.5 rounded-lg hover:bg-gray-50 text-gray-400 hover:text-blue-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>

                            {{-- Label --}}
                            <span
                                class="px-3 text-xs sm:text-sm font-bold text-gray-800 uppercase tracking-tight text-center truncate">
                                {{ $navLabel }}
                            </span>

                            {{-- Tombol Maju --}}
                            @if($disableNext)
                                <button disabled class="p-1.5 text-gray-200 cursor-not-allowed bg-transparent">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <a href="{{ route('owner.laporan-Membership', ['periode' => $periode, 'date' => $nextLink]) }}"
                                    class="p-1.5 rounded-lg hover:bg-gray-50 text-gray-400 hover:text-blue-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div id="membershipTrendChart" class="w-full h-64 sm:h-72"></div>
        </div>

        {{-- BAGIAN KANAN: Paket Terlaris --}}
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
            <div class="mb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-1">Paket Terlaris</h4>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Paling Banyak Diminati</p>
            </div>

            <div class="flex-grow space-y-5 sm:space-y-6">
                @forelse($chartPaketLabels as $index => $label)
                    @php
                        $maxValue = !empty($chartPaketValues) ? max($chartPaketValues) : 1;
                        $percentage = ($chartPaketValues[$index] / ($maxValue ?: 1)) * 100;
                    @endphp
                    <div class="group">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <div class="flex items-start gap-2.5 sm:gap-3 flex-1 min-w-0">
                                <span
                                    class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-50 text-[10px] font-black text-gray-400 border border-gray-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-xs font-bold text-gray-700 leading-snug truncate">
                                    {{ $label }}
                                </span>
                            </div>
                            <span class="text-xs font-black text-blue-600 shrink-0 text-right">
                                {{ $chartPaketValues[$index] }} <span class="text-[10px] text-gray-400 font-medium">User</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-50">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-1000"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full py-10 opacity-30 grayscale">
                        <p class="text-xs font-black uppercase tracking-widest text-gray-500">Belum Ada Data</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-gray-50">
                <p class="text-[9px] text-gray-400 font-medium leading-tight italic">
                    *Data dihitung berdasarkan pemilihan paket pada setiap transaksi.
                </p>
            </div>
        </div>
    </div>

    {{-- DATA HOLDER FOR JS --}}
    <div id="chartData" data-labels='{!! json_encode($chartLabels) !!}' data-reg='{!! json_encode($chartRegistrasi) !!}'
        data-renew='{!! json_encode($chartRenewal) !!}' data-react='{!! json_encode($chartReactivation) !!}'
        data-paket-labels='{!! json_encode($chartPaketLabels) !!}'
        data-paket-values='{!! json_encode($chartPaketValues) !!}'>
    </div>

@endsection

@push('scripts')
    {{-- Memuat Library CSS & JS Eksternal via CDN --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Memanggil File JS Custom via Vite --}}
    @vite('resources/js/Owner/LaporanMembership.js')
@endpush