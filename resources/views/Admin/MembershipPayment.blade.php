@extends('Admin.dashboardAdminTemplate')

@section('header-content')
<div class="flex flex-col md:flex-row justify-between items-center gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Riwayat Payment</h2>
        <p class="text-gray-500 text-sm mt-1">Laporan pendapatan dan riwayat transaksi member.</p>
    </div>
</div>
@endsection

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Pendapatan (Filter)</p>
            <h3 class="text-2xl font-bold text-gray-800" id="statTotalPendapatan">Rp 0</h3>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Transaksi</p>
            <h3 class="text-2xl font-bold text-gray-800" id="statTotalTransaksi">0</h3>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="p-3 bg-green-50 text-green-600 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Pendapatan Hari Ini</p>
            <h3 class="text-2xl font-bold text-gray-800" id="statTodayIncome">Rp 0</h3>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">

    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-6">
        <div class="relative w-full md:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="customSearch" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Cari invoice/nama...">
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 p-1 rounded-xl">
                <input type="date" id="startDate" class="bg-transparent border-0 text-gray-600 text-sm focus:ring-0 cursor-pointer">
                <span class="text-gray-400">-</span>
                <input type="date" id="endDate" class="bg-transparent border-0 text-gray-600 text-sm focus:ring-0 cursor-pointer">
            </div>

            <button id="btnFilter" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter Data
            </button>

            <button id="btnReset" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                Reset
            </button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl">
        <table id="paymentTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4 rounded-tl-xl">No</th>
                    <th scope="col" class="px-6 py-4">No. Invoice</th>
                    <th scope="col" class="px-6 py-4">Nama Member</th>
                    <th scope="col" class="px-6 py-4">Paket</th>
                    <th scope="col" class="px-6 py-4">Jenis Transaksi</th>
                    <th scope="col" class="px-6 py-4">Tanggal Transaksi</th>
                    <th scope="col" class="px-6 py-4 text-right rounded-tr-xl">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white"></tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        window.routePaymentData = "{{ route('payment.data') }}";
        window.routePaymentStats = "{{ route('payment.stats') }}";
    </script>

    @vite('resources/js/Admin/MembershipPayment.js')
@endsection