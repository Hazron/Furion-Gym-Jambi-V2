@extends('Owner.OwnerTemplate')

@section('title', 'Laporan Keuangan Furion Gym Jambi')

@section('header-content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Laporan Keuangan</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pantau arus kas Membership & Penjualan Gym.</p>
        </div>
        </div>
@endsection

@section('content')

    {{-- ====================================================================== --}}
    {{--                            STYLE KHUSUS (CSS)                          --}}
    {{-- ====================================================================== --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        /* DataTables Default */
        .dataTables_wrapper .dataTables_length select { @apply rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500; }
        .dataTables_wrapper .dataTables_filter input { @apply rounded-lg border-gray-200 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 outline-none; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { @apply bg-gray-900 text-white border-none rounded-lg !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { @apply bg-gray-100 border-none rounded-lg text-gray-800 !important; }
        
        /* Mobile Table */
        #mobileTable thead { display: none; }
        #mobileTable td { padding: 0; border: none; }
        #mobileTable tr { background: transparent !important; }

        /* ==================== PRINT LAYOUT (KOP SURAT) ==================== */
        @media print {
            @page { margin: 1cm; size: A4; }
            body { background: white !important; font-family: 'Times New Roman', Times, serif; color: black !important; -webkit-print-color-adjust: exact; }
            .no-print, nav, aside, header, .sidebar, #sidebar-backdrop, .dataTables_wrapper, .mobile-card-view, .desktop-table-view, #incomeChart, #sourceChart { display: none !important; }
            .main-content, .p-4, .sm\:ml-72 { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            #print-area { display: block !important; }
            .print-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
            .print-table th, .print-table td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
            .print-table th { background-color: #f0f0f0 !important; font-weight: bold; text-align: center; }
            .text-right { text-align: right !important; }
            .text-center { text-align: center !important; }
            .signature-section { margin-top: 50px; display: flex; justify-content: flex-end; page-break-inside: avoid; }
            .signature-box { text-align: center; width: 200px; }
            .signature-line { margin-top: 60px; border-top: 1px solid #000; }
        }
    </style>

    <div class="no-print">
        
        <div class="mb-6">
            <form action="{{ route('owner.laporankeuangan') }}" method="GET">
                <input type="hidden" name="periode" value="{{ $periode ?? 'bulan' }}">
                <input type="hidden" name="kategori" value="{{ $kategori ?? 'all' }}">

                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Periode --}}
                    <div class="inline-flex flex-wrap items-center bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-full sm:w-auto overflow-x-auto custom-scrollbar">
                        @foreach(['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun', 'seluruh' => 'Semua'] as $val => $label)
                            <button type="submit" name="periode" value="{{ $val }}"
                                class="flex-1 sm:flex-none px-3 py-2 text-xs sm:text-sm font-semibold rounded-xl whitespace-nowrap transition-all duration-200 
                                {{ ($periode ?? 'bulan') == $val ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Kategori --}}
                    <div class="inline-flex flex-wrap items-center bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm w-full sm:w-auto">
                        <button type="submit" name="kategori" value="all" class="flex-1 px-3 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-200 {{ ($kategori ?? 'all') == 'all' ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'text-gray-500 hover:bg-gray-50' }}">Semua</button>
                        <button type="submit" name="kategori" value="membership" class="flex-1 px-3 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-200 {{ ($kategori ?? 'all') == 'membership' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'text-gray-500 hover:bg-gray-50' }}">Membership</button>
                        <button type="submit" name="kategori" value="penjualan" class="flex-1 px-3 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-200 {{ ($kategori ?? 'all') == 'penjualan' ? 'bg-green-50 text-green-600 border border-green-100' : 'text-gray-500 hover:bg-gray-50' }}">Penjualan</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <div class="col-span-2 sm:col-span-1 bg-gradient-to-br from-blue-600 to-blue-500 rounded-3xl p-4 sm:p-6 text-white shadow-lg shadow-blue-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0">
                        <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider truncate">Pendapatan</p>
                        <h3 class="text-xl sm:text-3xl font-black mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                    </div>
                    @php
                        $pendapatanLalu = $totalPemasukanBulanLalu ?? 0;
                        $persentase = ($pendapatanLalu > 0) ? (($totalPemasukan - $pendapatanLalu) / $pendapatanLalu) * 100 : (($totalPemasukan > 0) ? 100 : 0);
                        $isNaik = $persentase >= 0;
                    @endphp
                    <div class="bg-white/20 px-1.5 py-1 rounded-lg backdrop-blur-sm border border-white/10 flex items-center gap-0.5 ml-2 shrink-0">
                        <svg class="w-2.5 h-2.5 {{ $isNaik ? 'text-green-300' : 'text-red-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isNaik ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"></path></svg>
                        <span class="text-[10px] sm:text-xs font-bold">{{ number_format(abs($persentase), 0) }}%</span>
                    </div>
                </div>
                <p class="text-[9px] sm:text-[10px] text-blue-100/80 mt-2 sm:mt-3">Periode Ini</p>
            </div>

            {{-- Card Membership --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Membership</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">Rp {{ number_format($totalMembership, 0, ',', '.') }}</h3>
                    <p class="text-[10px] sm:text-xs text-blue-600 mt-1 sm:mt-2 font-medium">Layanan Gym</p>
                </div>
            </div>

            {{-- Card Penjualan --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Penjualan</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                    <p class="text-[10px] sm:text-xs text-green-600 mt-1 sm:mt-2 font-medium">Produk</p>
                </div>
            </div>

            {{-- Card Pending --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-16 h-16 sm:w-24 sm:h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Pending</p>
                    <h3 class="text-lg sm:text-2xl font-black text-gray-800 mt-1 truncate">Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                    <p class="text-[10px] sm:text-xs text-orange-500 mt-1 sm:mt-2 font-medium">Menunggu</p>
                </div>
            </div>
        </div>

        {{-- GRAFIK & PIE CHART --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Grafik Tren --}}
            <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">Tren Pendapatan</h4>
                        <p class="text-xs text-gray-400">Analisis per {{ ucfirst($periode ?? 'Bulan') }}</p>
                    </div>
                    @if(isset($periode) && $periode != 'seluruh' && $periode != 'hari' && $periode != 'minggu')
                        <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-200 shadow-sm self-end sm:self-auto">
                            <a href="{{ route('owner.laporankeuangan', ['periode' => $periode, 'date' => $prevLink, 'kategori' => $kategori]) }}" class="p-1.5 rounded-lg hover:bg-white hover:shadow-sm text-gray-500 hover:text-gray-900 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                            <span class="px-3 text-xs sm:text-sm font-bold text-gray-800 min-w-[120px] text-center select-none uppercase tracking-tight">{{ $navLabel }}</span>
                            @if($disableNext)
                                <button disabled class="p-1.5 rounded-lg text-gray-300 cursor-not-allowed bg-transparent"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                            @else
                                <a href="{{ route('owner.laporankeuangan', ['periode' => $periode, 'date' => $nextLink, 'kategori' => $kategori]) }}" class="p-1.5 rounded-lg hover:bg-white hover:shadow-sm text-gray-500 hover:text-gray-900 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                            @endif
                        </div>
                    @endif
                </div>
                <div id="incomeChart" class="w-full h-64 sm:h-72"></div>
            </div>

            {{-- Pie Chart --}}
            <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-1">Sumber Dana</h4>
                    <p class="text-xs text-gray-400">Proporsi Pemasukan</p>
                </div>
                <div class="flex-grow flex items-center justify-center relative min-h-[250px]">
                    <div id="sourceChart" class="w-full flex justify-center"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-100">
                        <span class="block text-[10px] text-gray-500 uppercase font-bold">Member</span>
                        <span class="block font-black text-blue-600 text-sm sm:text-base">{{ number_format(($totalMembership / ($totalPemasukan ?: 1)) * 100, 0) }}%</span>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center border border-green-100">
                        <span class="block text-[10px] text-gray-500 uppercase font-bold">Jual</span>
                        <span class="block font-black text-green-600 text-sm sm:text-base">{{ number_format(($totalPenjualan / ($totalPemasukan ?: 1)) * 100, 0) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DAFTAR TRANSAKSI (Mobile Card & Desktop Table) --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Rincian Transaksi</h4>
                    <p class="text-xs text-gray-500">Data detail transaksi masuk.</p>
                </div>
            </div>

            {{-- 1. MOBILE (CARD VIEW) --}}
            <div class="block sm:hidden mobile-card-view p-4">
                <table id="mobileTable" class="w-full">
                    <thead><tr><th></th></tr></thead>
                    <tbody>
                        @foreach($laporanData as $row)
                            @php
                                $status = strtolower($row['status']);
                                $statusColor = 'text-gray-500 bg-gray-100';
                                $statusLabel = $row['status'];
                                if (in_array($status, ['paid', 'completed', 'lunas'])) { $statusColor = 'text-emerald-600 bg-emerald-50'; $statusLabel = 'Lunas'; }
                                elseif (in_array($status, ['pending', 'unpaid'])) { $statusColor = 'text-orange-600 bg-orange-50'; $statusLabel = 'Pending'; }
                                elseif (in_array($status, ['failed', 'cancel'])) { $statusColor = 'text-red-600 bg-red-50'; $statusLabel = 'Gagal'; }
                            @endphp
                            <tr>
                                <td>
                                    <div class="mb-4 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm relative overflow-hidden border-b-4 border-b-gray-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-700">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}</span>
                                                <span class="text-[10px] text-gray-400 font-mono">#{{ $row['invoice'] }}</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $statusColor }}">{{ $statusLabel }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <h4 class="text-sm font-bold text-gray-900">{{ $row['member'] }}</h4>
                                            <p class="text-xs text-gray-500 line-clamp-1">{{ $row['keterangan'] }}</p>
                                        </div>
                                       <div class="flex justify-between items-center bg-gray-50/50 p-2 rounded-lg">
                                            @if($row['jenis'] == 'Membership')
                                                @php
                                                    $tipeColor = 'bg-gray-50 text-gray-600 border-gray-100'; // Default
                                                    if ($row['tipe_label'] == 'Registrasi') {
                                                        $tipeColor = 'bg-green-50 text-green-600 border-green-100'; // Ijo
                                                    } elseif ($row['tipe_label'] == 'Perpanjang') {
                                                        $tipeColor = 'bg-blue-50 text-blue-600 border-blue-100'; // Biru
                                                    } elseif ($row['tipe_label'] == 'Reaktivasi') {
                                                        $tipeColor = 'bg-yellow-50 text-yellow-600 border-yellow-100'; // Kuning
                                                    }
                                                @endphp
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase {{ $tipeColor }}">{{ $row['tipe_label'] }}</span>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase">{{ $row['tipe_label'] }}</span>
                                                    <button onclick='window.showDetailBelanja(@json($row["details"] ?? []))' class="text-xs text-gray-400 hover:text-gray-600 underline">Detail</button>
                                                </div>
                                            @endif
                                            <span class="text-sm font-black text-gray-800">Rp {{ number_format($row['nominal'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 2. DESKTOP (TABLE VIEW) --}}
            <div class="hidden sm:block desktop-table-view p-6">
                <table id="transactionTable" class="display w-full text-sm text-gray-700">
                    <thead>
                        <tr class="bg-gray-50 uppercase text-xs font-bold text-gray-500">
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Invoice</th>
                            <th class="px-6 py-3 text-left">Member</th>
                            <th class="px-6 py-3 text-left">Tipe</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporanData as $row)
                            <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-0 transition">
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($row['tanggal'])->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 align-middle">{{ $row['invoice'] }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 align-middle">{{ $row['member'] }}</td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex flex-col items-start gap-1">
                                        @if($row['jenis'] == 'Membership')
                                            @php
                                                $tipeColorDesk = 'bg-gray-50 text-gray-600 border-gray-100'; // Default
                                                if ($row['tipe_label'] == 'Registrasi') {
                                                    $tipeColorDesk = 'bg-green-50 text-green-600 border-green-100'; // Ijo
                                                } elseif ($row['tipe_label'] == 'Perpanjang') {
                                                    $tipeColorDesk = 'bg-blue-50 text-blue-600 border-blue-100'; // Biru
                                                } elseif ($row['tipe_label'] == 'Reaktivasi') {
                                                    $tipeColorDesk = 'bg-yellow-50 text-yellow-600 border-yellow-100'; // Kuning
                                                }
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border {{ $tipeColorDesk }}">{{ $row['tipe_label'] }}</span>
                                            <span class="text-[11px] text-gray-500 font-medium ml-1">{{ $row['keterangan'] }}</span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border bg-emerald-50 text-emerald-600 border-emerald-100">{{ $row['tipe_label'] }}</span>
                                                <button onclick='window.showDetailBelanja(@json($row["details"] ?? []))' class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded border border-gray-200 transition flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Lihat Item
                                                </button>
                                            </div>
                                            <span class="text-[11px] text-gray-500 font-medium ml-1">{{ $row['keterangan'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    @php
                                        $status = strtolower($row['status']);
                                        $style = 'bg-gray-100 text-gray-600 border-gray-200';
                                        $label = $row['status'];
                                        if (in_array($status, ['paid', 'completed', 'lunas'])) { $style = 'bg-emerald-50 text-emerald-600 border-emerald-100'; $label = 'Lunas'; }
                                        elseif (in_array($status, ['pending', 'unpaid'])) { $style = 'bg-orange-50 text-orange-600 border-orange-100'; $label = 'Pending'; }
                                        elseif (in_array($status, ['failed', 'cancel'])) { $style = 'bg-red-50 text-red-600 border-red-100'; $label = 'Gagal'; }
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border {{ $style }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 align-middle">Rp {{ number_format($row['nominal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden transition-all duration-300 ease-in-out opacity-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeModal('detailModal')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all scale-95 transform">
                <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Rincian Belanja</h3>
                    <button onclick="window.closeModal('detailModal')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-5 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50"><tr><th class="px-3 py-2 rounded-l-lg">Produk</th><th class="px-3 py-2 text-center">Qty</th><th class="px-3 py-2 text-right rounded-r-lg">Total</th></tr></thead>
                        <tbody id="detailContent"></tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-5 py-3 text-right"><button onclick="window.closeModal('detailModal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100">Tutup</button></div>
            </div>
        </div>
    </div>

    {{-- DATA HOLDER UNTUK JS CHART --}}
    <div id="chartData" data-labels='@json($chartLabels)' data-values='@json($chartValues)' data-member="{{ $totalMembership ?? 0 }}" data-sales="{{ $totalPenjualan ?? 0 }}"></div>

@endsection

@push('scripts')
    {{-- CSS DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    {{-- CDN Library Eksternal --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Pemanggilan File JS Custom via Vite --}}
    @vite('resources/js/Owner/LaporanKeuangan.js')
@endpush