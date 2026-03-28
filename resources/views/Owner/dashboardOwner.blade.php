<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - Furion Gym</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#EAEEF1">

    <link rel="apple-touch-icon" href="{{ asset('icons/ios/180.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/ios/152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('icons/ios/167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/ios/180.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Furion Gym">

    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'brand-bg': '#EAEEF1',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #EAEEF1;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-[#EAEEF1] text-gray-800">

    @include('layout.navbar')

    <div
        class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 px-4 py-3 sm:hidden flex items-center justify-between shadow-sm h-16">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()"
                class="text-gray-500 hover:text-blue-600 focus:outline-none transition p-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <span class="font-extrabold text-lg text-gray-900 tracking-tight">Furion Gym</span>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="focus:outline-none flex items-center">
                <img class="w-8 h-8 rounded-full border border-gray-100 object-cover"
                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Owner' }}&background=0D8ABC&color=fff"
                    alt="User">
            </button>

            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                style="display: none;">

                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                    <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'Owner' }}</p>
                    <p class="text-[10px] text-gray-500 uppercase">{{ Auth::user()->role ?? 'Owner' }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors rounded-b-xl">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="pt-20 p-3 sm:pt-4 sm:p-4 sm:ml-72 transition-all duration-300">
        <div class="p-2 sm:p-8 mt-0 sm:mt-4">

            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4 hidden sm:flex">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-black tracking-tight">Overview</h1>
                    <p class="text-gray-500 mt-1 font-medium text-xs sm:text-sm">
                        Selamat datang kembali, {{ Auth::user()->name ?? 'Owner' }}!
                    </p>
                </div>

                <div class="flex items-center justify-between w-full sm:w-auto gap-5">
                    <div class="relative ml-auto sm:ml-0" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 bg-white pl-2 pr-4 py-2 rounded-full shadow-sm hover:shadow-md transition border border-gray-100">
                            <img class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-gray-100 object-cover"
                                src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Owner' }}&background=0D8ABC&color=fff"
                                alt="User Avatar">
                            <div class="text-left hidden md:block">
                                <p class="text-xs font-bold text-gray-700">{{ Auth::user()->name ?? 'Deky' }}</p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ Auth::user()->role ?? 'Owner' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                            style="display: none;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors first:rounded-t-xl last:rounded-b-xl">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
                <div
                    class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition cursor-default group">
                    <div class="flex flex-col sm:flex-row justify-between items-start">
                        <div class="w-full">
                            <p
                                class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                                Pendapatan</p>
                            <h3 class="text-base sm:text-2xl font-extrabold text-gray-900 mt-1 truncate">
                                <span class="block sm:hidden">
                                    Rp {{ number_format($totalPendapatan / 1000, 0, ',', '.') }}k
                                    <span class="text-[10px] text-gray-400 font-medium">/bln</span>
                                </span>

                                <span class="hidden sm:block">
                                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    <span class="text-sm text-gray-400 font-medium">/bulan</span>
                                </span>
                            </h3>
                            @if($percentageChange >= 0)
                                <div
                                    class="flex items-center mt-2 text-emerald-500 text-[9px] sm:text-xs font-bold bg-emerald-50 w-fit px-1.5 sm:px-2 py-1 rounded-lg">
                                    +{{ number_format($percentageChange, 0) }}%
                                </div>
                            @else
                                <div
                                    class="flex items-center mt-2 text-rose-500 text-[9px] sm:text-xs font-bold bg-rose-50 w-fit px-1.5 sm:px-2 py-1 rounded-lg">
                                    {{ number_format(abs($percentageChange), 0) }}%
                                </div>
                            @endif
                        </div>
                        <div
                            class="hidden sm:block p-2 sm:p-3 bg-blue-50 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition mt-2 sm:mt-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition cursor-default group">
                    <div class="flex flex-col sm:flex-row justify-between items-start">
                        <div class="w-full">
                            <p
                                class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                                Member Aktif</p>
                            <h3 class="text-base sm:text-2xl font-extrabold text-gray-900 mt-1">
                                {{ number_format($totalMemberAktif, 0, ',', '.') }}
                            </h3>
                            <div
                                class="flex items-center mt-2 text-green-500 text-[9px] sm:text-xs font-bold bg-green-50 w-fit px-1.5 sm:px-2 py-1 rounded-lg">
                                +{{ $newMemberThisMonth }}, <span class="hidden sm:inline"> Orang</span>
                            </div>
                        </div>
                        <div
                            class="hidden sm:block p-2 sm:p-3 bg-cyan-50 rounded-2xl text-cyan-600 group-hover:bg-cyan-500 group-hover:text-white transition mt-2 sm:mt-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition cursor-default group">
                    <div class="flex flex-col sm:flex-row justify-between items-start">
                        <div class="w-full">
                            <p
                                class="text-[9px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider truncate">
                                Visit /Hari</p>
                            <h3 class="text-base sm:text-2xl font-extrabold text-gray-900 mt-1">
                                {{ $visitToday }}
                            </h3>
                            <div
                                class="flex items-center mt-2 text-gray-500 text-[9px] sm:text-xs font-medium bg-gray-50 w-fit px-1.5 sm:px-2 py-1 rounded-lg border border-gray-100">
                                Rata-rata: {{ $averageDailyVisit }}
                            </div>
                        </div>
                        <div
                            class="hidden sm:block p-2 sm:p-3 bg-purple-50 rounded-2xl text-purple-600 group-hover:bg-purple-500 group-hover:text-white transition mt-2 sm:mt-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div onclick="openModal('modalPending')"
                    class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 h-full w-1 bg-orange-500 opacity-0 group-hover:opacity-100 transition-all duration-300">
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start">
                        <div class="w-full">
                            <p class="text-[9px] sm:text-[11px] font-bold text-gray-400 tracking-wider truncate">PENDING
                            </p>
                            <h3 class="text-base sm:text-2xl font-extrabold text-gray-900 mt-1 truncate">
                                <span class="block sm:hidden">Rp
                                    {{ number_format($totalPending / 1000, 0, ',', '.') }}k</span>
                                <span class="hidden sm:block">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
                            </h3>
                            @if($totalCountPending > 0)
                                <div
                                    class="flex items-center mt-2 text-orange-500 text-[9px] sm:text-xs font-bold bg-orange-50 w-fit px-1.5 sm:px-2 py-1 rounded-lg">
                                    {{ $totalCountPending }} transaksi
                                </div>
                            @else
                                <div
                                    class="flex items-center mt-2 text-emerald-500 text-xs font-bold bg-emerald-50 w-fit px-2 py-1 rounded-lg">
                                    Lunas
                                </div>
                            @endif
                        </div>
                        <div
                            class="hidden sm:block p-2 sm:p-3 bg-orange-50 rounded-2xl text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition shadow-sm mt-2 sm:mt-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 sm:mb-8">
                <div class="lg:col-span-2 bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900" id="chartTitle">Statistik</h3>
                            <p class="text-xs sm:text-sm text-gray-400" id="chartSubtitle">Data Tahunan</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                            <select id="dataTypeFilter" onchange="handleTypeChange()"
                                class="w-full sm:w-auto bg-gray-50 border-none text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold outline-none cursor-pointer">
                                <option value="member">Data Member</option>
                                <option value="revenue">Total Revenue</option>
                                <option value="visit">Kunjungan</option>
                            </select>
                            <select id="timeFilter" onchange="updateChart()"
                                class="w-full sm:w-auto bg-white border border-gray-200 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 shadow-sm outline-none cursor-pointer">
                                <option value="year">Tahun Ini</option>
                                <option value="month">Bulan Ini</option>
                                <option value="week" selected>7 Hari Terakhir</option>
                                <option value="day" id="optionDay" disabled>Hari Ini (Jam)</option>
                            </select>
                        </div>
                    </div>
                    <div id="dynamicChart" class="w-full h-72 sm:h-80"></div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-3xl border border-gray-100 shadow-sm h-full flex flex-col"
                    x-data="{ activeTab: 'all' }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-800">Aktivitas Hari Ini</h3>
                        <span class="text-[10px] bg-red-100 text-red-600 px-2 py-1 rounded-full font-bold">
                            {{ \Carbon\Carbon::now()->format('d M Y') }}
                        </span>
                    </div>

                    <div class="flex gap-1 mb-4 overflow-x-auto pb-2 no-scrollbar w-full">
                        <button @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-500'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">
                            Semua
                        </button>
                        <button @click="activeTab = 'membership'"
                            :class="activeTab === 'membership' ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">
                            Regis
                        </button>
                        <button @click="activeTab = 'renewal'"
                            :class="activeTab === 'renewal' ? 'bg-green-600 text-white' : 'bg-gray-50 text-gray-500'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">
                            Renew
                        </button>
                        <button @click="activeTab = 'reactivation'"
                            :class="activeTab === 'reactivation' ? 'bg-orange-500 text-white' : 'bg-gray-50 text-gray-500'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">
                            Reactive
                        </button>
                        <button @click="activeTab = 'visit'"
                            :class="activeTab === 'visit' ? 'bg-purple-600 text-white' : 'bg-gray-50 text-gray-500'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">
                            Absen
                        </button>
                    </div>

                    <div class="space-y-3 overflow-y-auto max-h-[300px] pr-1 custom-scrollbar flex-1">
                        @forelse($todaysActivities as $activity)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $activity['type'] }}'"
                                class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 
                                            {{ $activity['type'] == 'membership' ? 'bg-blue-100 text-blue-600' : '' }}
                                            {{ $activity['type'] == 'renewal' ? 'bg-green-100 text-green-600' : '' }}
                                            {{ $activity['type'] == 'reactivation' ? 'bg-orange-100 text-orange-600' : '' }}
                                            {{ $activity['type'] == 'visit' ? 'bg-purple-100 text-purple-600' : '' }}">
                                    @if($activity['type'] == 'membership')
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    @elseif($activity['type'] == 'renewal')
                                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    @elseif($activity['type'] == 'reactivation')
                                        <i data-lucide="zap" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold text-gray-800 truncate pr-2 capitalize">
                                            {{ strtolower($activity['name']) }}
                                        </p>
                                        <span class="text-[9px] font-medium text-gray-400 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($activity['time'])->format('H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $activity['desc'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                                <i data-lucide="calendar-off" class="w-8 h-8 mb-2 opacity-50"></i>
                                <p class="text-xs">Belum ada aktivitas hari ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg shadow-gray-100/50 border border-gray-100 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-xl text-gray-800 tracking-tight">Transaksi Terakhir</h3>
                        <p class="text-sm text-gray-500 mt-1">Log aktivitas pembayaran terbaru.</p>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Member /
                                    Pelanggan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis
                                    Transaksi</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Admin
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentTransactions as $trx)
                                <tr class="hover:bg-gray-50/80 transition duration-150 ease-in-out group">
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex items-center gap-4">
                                            <div class="relative shrink-0">
                                                <div
                                                    class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold ring-2 ring-white shadow-sm {{ $trx['source'] == 'order' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600' }}">
                                                    {{ strtoupper(substr($trx['name'], 0, 2)) }}
                                                </div>
                                                <div
                                                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center {{ $trx['source'] == 'order' ? 'bg-orange-500' : 'bg-blue-500' }}">
                                                    <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        @if($trx['source'] == 'order')
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                                                            </path>
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        @endif
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <span
                                                    class="block font-bold text-gray-800 text-sm truncate max-w-[140px]">{{ $trx['name'] }}</span>
                                                <span
                                                    class="block text-[11px] text-gray-400 font-medium">#{{ $trx['id_members'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex items-start gap-2.5">
                                            <span
                                                class="mt-1.5 w-1.5 h-1.5 rounded-full shrink-0 {{ $trx['source'] == 'order' ? 'bg-orange-500' : 'bg-blue-500' }}"></span>
                                            <span
                                                class="text-sm font-medium text-gray-700 leading-snug">{{ $trx['type_label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex items-center gap-2 text-gray-500">
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <span class="text-sm">{{ $trx['admin'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-gray-700 text-xs">{{ \Carbon\Carbon::parse($trx['date'])->translatedFormat('d M Y') }}</span>
                                            <span
                                                class="text-[10px] text-gray-400 mt-0.5 font-medium">{{ \Carbon\Carbon::parse($trx['date'])->format('H:i') }}
                                                WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <span class="font-bold text-gray-900 text-sm font-mono tracking-tight">
                                            Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        @if($trx['status'] == 'completed' || $trx['status'] == 'paid')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas
                                            </span>
                                        @elseif($trx['status'] == 'pending')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg> Gagal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                    </path>
                                                </svg>
                                            </div>
                                            <span class="text-gray-500 font-medium text-sm">Belum ada transaksi
                                                terbaru</span>
                                            <p class="text-gray-400 text-xs mt-1">Transaksi akan muncul di sini setelah
                                                pembayaran berhasil.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="modalPending" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity opacity-0"
                    id="modalBackdrop" onclick="closeModal('modalPending')"></div>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div
                            class="relative transform overflow-hidden rounded-t-2xl sm:rounded-2xl bg-white text-left shadow-xl transition-all w-full sm:max-w-4xl border border-gray-100">
                            <div
                                class="bg-white px-4 pb-4 pt-5 sm:p-6 border-b border-gray-100 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0">
                                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold leading-6 text-gray-900">Tagihan Pending</h3>
                                </div>
                                <button type="button" onclick="closeModal('modalPending')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="bg-gray-50/50 px-4 py-6 sm:p-6 max-h-[70vh] overflow-y-auto">
                                <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                    Member</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                    Tanggal</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">
                                                    Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($pendingTransactions as $item)
                                                <tr>
                                                    <td
                                                        class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $item->member->nama_lengkap ?? 'Umum' }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $item->created_at->format('d M') }}
                                                    </td>
                                                    <td
                                                        class="px-4 py-4 whitespace-nowrap text-sm text-right font-bold text-orange-600">
                                                        {{ number_format($item->total_payment, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-10 text-center text-gray-500">Tidak ada
                                                        tagihan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                                <button type="button" onclick="closeModal('modalPending')"
                                    class="w-full inline-flex justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="sidebar-backdrop" onclick="toggleSidebar()"
        class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm hidden transition-opacity opacity-0"
        aria-hidden="true"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('logo-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }
    </script>

    <script>
        window.dashboardData = @json($chartData);
    </script>

    <script src="{{ asset('js/Owner/dashboard-owner.js') }}" defer></script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .catch(err => console.error(err));
            });
        }
    </script>
</body>

</html>