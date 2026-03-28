@extends('Owner.OwnerTemplate')

@section('title', 'Monitoring Etalase')

@section('header-content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 tracking-tight">Monitoring Etalase</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pantau ketersediaan stok produk penjualan.</p>
        </div>
    </div>
@endsection

@section('content')

    {{-- 1. STATS CARD (Desain Konsisten) --}}
    {{-- 1. STATS CARD (Diperbesar: 2 Kolom per Baris di Desktop) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

        {{-- Total Produk --}}
        <div
            class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-6 -mt-6 transition group-hover:scale-110">
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-2">Total Produk</p>
                    <h3 class="text-4xl font-black text-gray-900 mb-2">{{ $totalProduk }}</h3>
                    <span class="text-xs text-blue-600 mt-2 font-medium">
                        SKU Terdaftar
                    </span>
                </div>
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Estimasi Aset --}}
        <div
            class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-6 -mt-6 transition group-hover:scale-110">
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-2">Estimasi Aset</p>
                    <h3 class="text-4xl font-black mb-2 flex items-baseline gap-1">
                        <span class="text-2xl font-black text-gray-800 mt-1">Rp</span>
                        {{ number_format($totalAset / 1000000, 1, ',', '.') }}<span
                            class="text-2xl font-black text-gray-800 mt-1">Jt</span>
                    </h3>
                    <span class="text-xs text-emerald-600 mt-2 font-medium">
                        Modal Barang
                    </span>
                </div>
                <div
                    class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Perlu Restock --}}
        <div
            class="bg-white p-8 rounded-[2rem] border shadow-sm hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-6 -mt-6 transition group-hover:scale-110">
            </div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-2">Perlu Restock</p>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $stokMenipis }}</h3>
                    <span class="text-xs text-amber-600/80 mt-2 font-medium">
                        Stok Menipis
                    </span>
                </div>
                <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-3xl flex items-center justify-center shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Habis Total --}}
        <div
            class="bg-white p-8 rounded-[2rem] border shadow-sm hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-6 -mt-6 transition group-hover:scale-110">
            </div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-2">Habis Total</p>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $stokHabis }}</h3>
                    <span class="text-xs text-red-600/80 mt-2 font-medium">
                        Segera Isi!
                    </span>
                </div>
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-3xl flex items-center justify-center shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- 2. FILTER & SEARCH --}}
    <div
        class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        {{-- Filter Tabs --}}
        <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto p-1 no-scrollbar">
            @php
                $tabs = ['all' => 'Semua Produk', 'menipis' => 'Stok Menipis', 'habis' => 'Stok Habis'];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('owner.monitoringEtalase', ['filter' => $key]) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap
                                               {{ $filter == $key ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <form action="{{ route('owner.monitoringEtalase') }}" method="GET" class="w-full md:w-64 relative">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="text" name="search" placeholder="Cari nama produk..." value="{{ request('search') }}"
                class="w-full bg-gray-50 border-none text-sm rounded-xl py-2.5 pl-10 focus:ring-2 focus:ring-blue-500">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>
    </div>

    {{-- 3. PRODUCT GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            {{-- Logic Visualisasi Stok --}}
            @php
                // Target ideal stok misal 50 pcs (untuk visualisasi bar)
                $maxStockVisual = 50;
                $percentage = min(($product->stok_produk / $maxStockVisual) * 100, 100);

                $statusColor = 'bg-emerald-500';
                $statusText = 'Aman';

                if ($product->stok_produk == 0) {
                    $statusColor = 'bg-red-500';
                    $statusText = 'Habis';
                } elseif ($product->stok_produk <= 5) {
                    $statusColor = 'bg-red-500';
                    $statusText = 'Kritis';
                } elseif ($product->stok_produk <= 15) {
                    $statusColor = 'bg-amber-500';
                    $statusText = 'Menipis';
                }
            @endphp

            <div
                class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 group overflow-hidden relative flex flex-col h-full">

                {{-- Status Badge --}}
                @if($product->stok_produk <= 5)
                    <div class="absolute top-3 right-3 z-10">
                        <span
                            class="px-2 py-1 {{ $product->stok_produk == 0 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }} rounded-lg text-[10px] font-bold border border-white shadow-sm flex items-center gap-1">
                            @if($product->stok_produk == 0) <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg> @endif
                            {{ $statusText }}
                        </span>
                    </div>
                @endif

                {{-- Image Placeholder --}}
                <div
                    class="h-40 bg-gray-50 flex items-center justify-center relative group-hover:bg-gray-100 transition overflow-hidden">
                    @if($product->gambar_produk)
                        <img src="{{ asset('produk/' . $product->gambar_produk) }}" class="h-full w-full object-cover">
                    @else
                        <div class="text-4xl font-black text-gray-200 select-none">
                            {{ strtoupper(substr($product->nama_produk, 0, 2)) }}
                        </div>
                        <svg class="w-12 h-12 text-gray-200 absolute opacity-20" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-5 flex flex-col flex-grow">
                    <div class="mb-auto">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 mb-1"
                            title="{{ $product->nama_produk }}">
                            {{ $product->nama_produk }}
                        </h4>
                        <p class="text-xs text-gray-400 font-mono mb-3">ID: #{{ $product->id_produk }}</p>
                    </div>

                    {{-- Stock Indicator Bar --}}
                    <div class="mb-4">
                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1">
                            <span>Sisa: <span
                                    class="{{ $product->stok_produk <= 5 ? 'text-red-600' : 'text-gray-900' }}">{{ $product->stok_produk }}</span></span>
                            <span>Target: {{ $maxStockVisual }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="{{ $statusColor }} h-2 rounded-full transition-all duration-500"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-dashed border-gray-100">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 uppercase font-bold">Harga</span>
                            <span class="text-sm font-black text-gray-900">Rp
                                {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-gray-900 font-bold text-lg">Tidak ada produk ditemukan</h3>
                <p class="text-gray-500 text-sm mt-1">Coba ubah filter status atau kata kunci pencarian.</p>
                <a href="{{ route('owner.monitoringEtalase') }}"
                    class="mt-4 text-blue-600 font-bold text-sm hover:underline">Reset
                    Filter</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $products->appends(['search' => request('search'), 'filter' => $filter])->links() }}
    </div>

@endsection