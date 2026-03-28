@extends('Admin.dashboardAdminTemplate')

{{-- ======================================================================== --}}
{{-- 1. LOGIC TANGGAL & NAVIGASI (PHP)                                        --}}
{{-- ======================================================================== --}}
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $periode = request('periode', 'hari');
    $refDate = request('date', date('Y-m-d'));
    
    $carbonDate = Carbon::parse($refDate);
    $startDate  = $carbonDate->copy();
    $endDate    = $carbonDate->copy();

    $prevDateLink = '';
    $nextDateLink = '';
    $labelPeriode = '';
    
    // Variabel bantu untuk navigasi
    $p = $carbonDate->copy();
    $n = $carbonDate->copy();

    switch ($periode) {
        case 'minggu':
            $startDate = $carbonDate->copy()->startOfWeek();
            $endDate   = $carbonDate->copy()->endOfWeek();
            
            $prevDateLink = route('listPaymentBarang', ['periode' => 'minggu', 'date' => $p->subWeek()->format('Y-m-d')]);
            $nextDateLink = route('listPaymentBarang', ['periode' => 'minggu', 'date' => $n->addWeek()->format('Y-m-d')]);
            
            $labelPeriode = $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y');
            break;

        case 'bulan':
            $startDate = $carbonDate->copy()->startOfMonth();
            $endDate   = $carbonDate->copy()->endOfMonth();

            $prevDateLink = route('listPaymentBarang', ['periode' => 'bulan', 'date' => $p->subMonth()->format('Y-m-d')]);
            $nextDateLink = route('listPaymentBarang', ['periode' => 'bulan', 'date' => $n->addMonth()->format('Y-m-d')]);

            $labelPeriode = $carbonDate->translatedFormat('F Y');
            break;

        case 'tahun':
            $startDate = $carbonDate->copy()->startOfYear();
            $endDate   = $carbonDate->copy()->endOfYear();

            $prevDateLink = route('listPaymentBarang', ['periode' => 'tahun', 'date' => $p->subYear()->format('Y-m-d')]);
            $nextDateLink = route('listPaymentBarang', ['periode' => 'tahun', 'date' => $n->addYear()->format('Y-m-d')]);

            $labelPeriode = $carbonDate->translatedFormat('Y');
            break;

        case 'semua':
            $labelPeriode = 'Semua Data';
            $prevDateLink = '#'; 
            $nextDateLink = '#'; 
            break;

        default: // 'hari'
            $prevDateLink = route('listPaymentBarang', ['periode' => 'hari', 'date' => $p->subDay()->format('Y-m-d')]);
            $nextDateLink = route('listPaymentBarang', ['periode' => 'hari', 'date' => $n->addDay()->format('Y-m-d')]);
            
            $labelPeriode = $carbonDate->translatedFormat('l, d F Y');
            break;
    }
@endphp

@section('header-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">List Payment Barang</h2>
            <p class="text-gray-500 text-sm mt-1">Pantau arus kas masuk dari transaksi barang.</p>
        </div>
    </div>

    {{-- CSS DataTables Custom --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            background-image: none; padding: .4rem 2rem .4rem 1rem; border-radius: 0.75rem; border-color: #e5e7eb;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.75rem; border-color: #e5e7eb; padding: .4rem 1rem; margin-left: 10px;
        }
        table.dataTable.no-footer { border-bottom: 1px solid #f3f4f6; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563EB !important; color: white !important; border: none !important; border-radius: 0.5rem;
        }
    </style>
@endsection

@section('content')

    {{-- ======================================================================== --}}
    {{-- 2. FILTER TOOLBAR (GABUNGAN FILTER & NAVIGASI)                           --}}
    {{-- ======================================================================== --}}
    <div class="mb-8 no-print">
        <div class="bg-white p-2 rounded-2xl border border-gray-200 shadow-sm inline-flex flex-col sm:flex-row gap-4 items-center">
            
            <div class="flex bg-gray-100 p-1 rounded-xl">
                @foreach(['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun'] as $key => $val)
                <a href="{{ route('listPaymentBarang', ['periode' => $key, 'date' => $refDate]) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 
                   {{ $periode == $key ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $val }}
                </a>
                @endforeach
            </div>

            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            <div class="flex items-center gap-2">
                <a href="{{ $prevDateLink }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 transition-colors {{ $periode == 'semua' ? 'pointer-events-none opacity-50' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>

                <div class="relative group">
                    @if($periode == 'hari')
                        <form action="{{ route('listPaymentBarang') }}" method="GET" class="absolute inset-0 opacity-0 cursor-pointer">
                            <input type="hidden" name="periode" value="hari">
                            <input type="date" name="date" value="{{ $refDate }}" class="w-full h-full cursor-pointer" onchange="this.form.submit()">
                        </form>
                        <div class="flex flex-col items-center cursor-pointer px-2">
                            <span class="text-sm font-bold text-gray-800 flex items-center gap-1">
                                {{ $labelPeriode }}
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </div>
                    @else
                        <div class="text-center px-4">
                            <span class="text-sm font-bold text-gray-800 block whitespace-nowrap">{{ $labelPeriode }}</span>
                        </div>
                    @endif
                </div>

                <a href="{{ $nextDateLink }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 transition-colors {{ $periode == 'semua' ? 'pointer-events-none opacity-50' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            @if($refDate != date('Y-m-d'))
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
            <a href="{{ route('listPaymentBarang') }}" class="text-xs font-semibold text-blue-600 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                Ke Hari Ini
            </a>
            @endif

        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 3. STATS CARDS                                                           --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">Total Transaksi</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_transaksi']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">Income (Lunas)</p>
                <h3 class="text-xl font-bold text-gray-800">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">Pending (Rp)</p>
                <h3 class="text-xl font-bold text-gray-800">Rp {{ number_format($stats['pendapatan_pending'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">Jml. Pending</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_pending']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">Trx Hari Ini</p>
                <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['transaksi_hari_ini']) }}</h3>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 4. TABEL TRANSAKSI                                                       --}}
    {{-- ======================================================================== --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Detail Transaksi</h3>
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="customSearch" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 placeholder-gray-400" placeholder="Cari invoice, nama...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="tableTransaksi" class="w-full text-left border-collapse stripe">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider font-bold border-b border-gray-100">
                        <th class="px-6 py-4 rounded-l-xl">Invoice</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Metode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @foreach($orders as $order)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-blue-600 text-xs">{{ $order->invoice_code }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs shrink-0 border border-gray-200">
                                    {{ substr($order->member->nama_lengkap ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-700 line-clamp-1 text-sm">{{ $order->member->nama_lengkap ?? 'Guest / Tamu' }}</p>
                                    <p class="text-[10px] text-gray-400">Kasir: {{ $order->cashier->name ?? 'Admin' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                            Rp {{ number_format($order->total_payment, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 text-[10px] font-bold uppercase border border-gray-200 tracking-wide">{{ $order->payment_method }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($order->payment_status == 'paid')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-[10px] font-bold border border-green-100 cursor-default">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Lunas
                                </span>
                            @else
                                {{-- Tombol Pending: Memanggil fungsi JS Global --}}
                                <div class="relative group inline-block">
                                    <button onclick="window.openPaymentModal('{{ $order->invoice_code }}', '{{ route('order.updatePayment', $order->order_id ?? $order->id) }}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-600 text-[10px] font-bold border border-yellow-100 hover:bg-yellow-100 hover:text-yellow-700 transition-colors cursor-pointer shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pending
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                            <span class="font-semibold block text-gray-700">{{ $order->created_at->format('d M Y') }}</span>
                            <span class="text-[10px]">{{ $order->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="window.showDetailOrder(JSON.parse(this.dataset.items), '{{ $order->invoice_code }}')"
                                    data-items="{{ json_encode($order->items->load('produk')) }}"
                                    class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:shadow-sm transition-all" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                
                                @if($order->payment_status == 'paid' && isset($order->member))
                                <form action="{{ route('order.sendInvoice', $order->order_id ?? $order->id) }}" method="POST" onsubmit="return confirm('Kirim Invoice WhatsApp ke {{ $order->member->nama_lengkap }}?');">
                                    @csrf
                                    <button type="submit" class="p-2 bg-green-50 border border-green-200 rounded-lg text-green-600 hover:bg-green-500 hover:text-white hover:shadow-md transition-all" title="Kirim Invoice WA">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 5. MODAL DETAIL ORDER (Hidden Default)                                   --}}
    {{-- ======================================================================== --}}
    <div id="modalDetailOrder" class="hidden fixed inset-0 z-[70] overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modalDetailOrder').classList.add('hidden')"></div>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden transform transition-all text-left">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Detail Pesanan</h2>
                        <p id="detailInvoiceCode" class="text-xs text-blue-600 font-bold tracking-wider font-mono mt-0.5">INV-XXX</p>
                    </div>
                    <button onclick="document.getElementById('modalDetailOrder').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 p-2 rounded-full shadow-sm border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-0 overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <table class="w-full text-left">
                        <tbody id="detailItemsList" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                    <button onclick="document.getElementById('modalDetailOrder').classList.add('hidden')" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-gray-800 w-full transition-transform active:scale-95">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 6. MODAL PELUNASAN (Baru ditambahkan di luar tabel)                      --}}
    {{-- ======================================================================== --}}
    <div id="modalPayment" class="hidden fixed inset-0 z-[70] overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="window.closePaymentModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-sm w-full p-6">
                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1">Konfirmasi Pelunasan</h3>
                <p class="text-sm text-gray-500 mb-4">Invoice <b id="paymentInvoiceCode" class="text-blue-600 font-mono"></b> akan ditandai Lunas.</p>
                
                <form id="paymentForm" action="" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Metode Bayar</label>
                        <select name="payment_method" required class="block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="edc">Mesin EDC</option>
                        </select>
                    </div>
                    <div class="flex flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-green-500/30 px-4 py-2 bg-green-600 text-base font-bold text-white hover:bg-green-700 sm:w-auto sm:text-sm transition-colors">Bayar Sekarang</button>
                        <button type="button" onclick="window.closePaymentModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CONFIG JS GLOBAL --}}
    <script>
        window.assetProduk = "{{ asset('produk') }}/";
    </script>

    {{-- SCRIPT CUSTOM --}}
    @vite('resources/js/Admin/ListPaymentBarang.js')

@endsection