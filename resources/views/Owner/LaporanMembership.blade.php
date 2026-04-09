@extends('Owner.OwnerTemplate')

@section('title', 'Laporan Membership Furion Gym Jambi')

@section('header-content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Laporan Membership</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Analisis pertumbuhan, perpanjangan, dan minat paket member.</p>
        </div>
    </div>
@endsection

@section('content')

    {{-- CSS CUSTOM UNTUK PRINT & DATATABLES --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        @media print {
            @page { margin: 0.5cm; size: landscape; }
            body { background: white !important; -webkit-print-color-adjust: exact !important; }
            .no-print, nav, aside, header, .sidebar, #sidebar-backdrop { display: none !important; }
            .main-content, .p-4, .sm\:ml-72 { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .shadow-sm, .border { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>

    <div class="flex flex-col">

        {{-- 1. FILTER PERIODE (Mobile: Bawah, Desktop: Atas) --}}
        <div class="order-2 sm:order-1 mb-8 sm:mb-6 no-print">
            <form action="{{ route('owner.laporan-Membership') }}" method="GET">
                <div class="inline-flex flex-nowrap items-center bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-full sm:w-auto overflow-x-auto custom-scrollbar">
                    <div class="flex flex-nowrap gap-1">
                        @foreach(['hari' => 'Hari Ini', 'minggu' => 'Minggu ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun', 'seluruh' => 'Seluruh'] as $val => $label)
                            <button type="submit" name="periode" value="{{ $val }}"
                                class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-200 whitespace-nowrap
                                {{ ($periode ?? 'bulan') == $val ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. SUMMARY CARDS --}}
        <div class="order-1 sm:order-2 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4 sm:mb-8 print:grid-cols-4">
            {{-- Card Total --}}
            <div class="col-span-2 sm:col-span-1 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-3xl p-4 sm:p-6 text-white shadow-lg shadow-blue-200 print:text-black print:border">
                <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider">Total Aktivitas</p>
                <h3 class="text-xl sm:text-3xl font-black mt-1">{{ count($laporanData) }} <span class="text-xs sm:text-sm font-normal">Transaksi</span></h3>
            </div>

            {{-- Card Registrasi --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider">Registrasi</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1">{{ $countRegistrasi }}</h3>
                    <p class="text-[9px] sm:text-xs text-blue-600 mt-1 font-medium">Member Baru</p>
                </div>
            </div>

            {{-- Card Perpanjang --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider">Renewal</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1">{{ $countRenewal }}</h3>
                    <p class="text-[9px] sm:text-xs text-green-600 mt-1 font-medium">Perpanjangan</p>
                </div>
            </div>

            {{-- Card Reaktivasi --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider">Re-Aktifasi</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1">{{ $countReactivation }}</h3>
                    <p class="text-[9px] sm:text-xs text-orange-500 mt-1 font-medium">Aktif Kembali</p>
                </div>
            </div>
        </div>

    </div>

    {{-- 3. GRAFIK TREN & PAKET TERLARIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-8 print:block">
        {{-- Sisi Kiri: Tren Pertumbuhan --}}
        <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h4 class="text-lg font-bold text-gray-800">Tren Pertumbuhan Member</h4>
                    <p class="text-xs text-gray-400">Analisis aktivitas {{ ucfirst($periode ?? 'Bulan') }}</p>
                </div>
                
                {{-- Navigasi Waktu --}}
                @if(isset($periode) && in_array($periode, ['bulan', 'tahun']))
                    <div class="flex items-center bg-gray-50 p-1 rounded-xl border border-gray-200 no-print">
                        <a href="{{ route('owner.laporan-Membership', ['periode' => $periode, 'date' => $prevLink]) }}" class="p-1.5 text-gray-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                        </a>
                        <span class="px-3 text-xs font-bold text-gray-800 uppercase tracking-tight">{{ $navLabel }}</span>
                        @if($disableNext)
                            <span class="p-1.5 text-gray-200 cursor-not-allowed"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg></span>
                        @else
                            <a href="{{ route('owner.laporan-Membership', ['periode' => $periode, 'date' => $nextLink]) }}" class="p-1.5 text-gray-400 hover:text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            <div id="membershipTrendChart" class="w-full h-64 sm:h-72"></div>
        </div>

        {{-- Sisi Kanan: Paket Terlaris (Visual List) --}}
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="mb-6">
                <h4 class="text-lg font-bold text-gray-800">Paket Terlaris</h4>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Paling Banyak Diminati</p>
            </div>

            <div class="space-y-6">
                @forelse($chartPaketLabels as $index => $label)
                    @php
                        $maxValue = !empty($chartPaketValues) ? max($chartPaketValues) : 1;
                        $percentage = ($chartPaketValues[$index] / ($maxValue ?: 1)) * 100;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-700 truncate pr-4">{{ $label }}</span>
                            <span class="text-xs font-black text-blue-600">{{ $chartPaketValues[$index] }} <span class="text-[10px] text-gray-400">User</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 opacity-30 italic text-sm">Belum ada data paket</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 4. DATA HOLDER (Sangat Penting untuk JavaScript) --}}
    <div id="chartData" 
        data-labels='{!! json_encode($chartLabels) !!}' 
        data-reg='{!! json_encode($chartRegistrasi) !!}'
        data-renew='{!! json_encode($chartRenewal) !!}' 
        data-react='{!! json_encode($chartReactivation) !!}'
        data-paket-labels='{!! json_encode($chartPaketLabels) !!}'
        data-paket-values='{!! json_encode($chartPaketValues) !!}'>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    @vite('resources/js/Owner/LaporanMembership.js')
@endpush