<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Furion Gym</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F3F4F6;
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
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
</head>

<body class="text-gray-800">

    @include('layout.navbar') <div class="p-4 sm:ml-72 min-h-screen">
        <div class="p-4 mt-4">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
                    <p class="text-gray-500 mt-1 text-sm font-medium">Selamat datang kembali, Admin Furion!</p>
                </div>
                <div class="flex items-center gap-5 ml-4 shrink-0">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 bg-white pl-2 pr-4 py-2 rounded-full shadow-sm hover:shadow-md transition border border-gray-100">
                            <img class="w-9 h-9 rounded-full border border-gray-100"
                                src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Admin">
                            <div class="text-left hidden md:block">
                                <p class="text-xs font-bold text-gray-700">
                                    Admin {{ Auth::user()->name }}
                                </p>

                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                            style="display: none;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Sign
                                    out</button>
                            </form>
                        </div>
                    </div>


                    {{-- Notification Icon --}}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <!-- TOTAL MEMBER -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Member Aktif</p>
                            <h2 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $activeMembers ?? 0 }}</h2>
                        </div>
                        <div class="p-3 bg-green-50 text-green-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <span
                        class="text-[10px] text-green-600 font-bold bg-green-50 px-2 py-1 rounded-full mt-4 inline-block flex w-fit items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Masa Aktif
                    </span>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Member Non-Aktif</p>
                            <h2 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $inactiveMembers ?? 0 }}</h2>
                        </div>
                        <div class="p-3 bg-red-50 text-red-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="text-[10px] text-red-600 font-bold bg-red-50 px-2 py-1 rounded-full mt-4 inline-block">
                        Masa Aktif Habis
                    </span>
                </div>
                <!-- VISIT HARI INI -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Visit Hari Ini</p>

                            {{-- Logika Tampilan Angka --}}
                            <h2
                                class="text-3xl font-extrabold mt-1 {{ $visitHariIni > 0 ? 'text-gray-800' : 'text-gray-400' }}">
                                {{ $visitHariIni > 0 ? $visitHariIni : '-' }}
                            </h2>
                        </div>

                        {{-- Icon --}}
                        <div
                            class="p-3 {{ $visitHariIni > 0 ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400' }} rounded-2xl transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    {{-- Badge Status Bawah --}}
                    @if($visitHariIni > 0)
                        {{-- Logika Warna: Hijau jika performa hari ini diatas rata-rata, Kuning jika dibawah --}}
                        @php
                            $isAboveAverage = $visitHariIni >= $rataRata;
                            $colorClass = $isAboveAverage ? 'text-green-600 bg-green-100' : 'text-orange-600 bg-orange-100';
                        @endphp

                        <div class="flex flex-col mt-4">
                            {{-- Teks Utama (Member Masuk) --}}
                            <span
                                class="text-[10px] {{ $colorClass }} font-bold px-2 py-1 rounded-full inline-block w-max mb-1">
                                {{ $visitHariIni }} Member Masuk
                            </span>

                            {{-- Teks Rata-rata --}}
                            <span class="text-[10px] text-gray-400 font-medium ml-1">
                                Rata-rata: {{ $rataRata }} / hari
                            </span>
                        </div>
                    @else
                        <span
                            class="text-[10px] text-gray-500 font-bold bg-gray-100 px-2 py-1 rounded-full mt-4 inline-block">
                            Belum ada data
                        </span>
                    @endif
                </div>

                <!-- REVENUE -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
                    x-data="{ 
                         filter: 'total', 
                         amounts: { 
                             day: '{{ number_format($revenueDay ?? 0, 0, ',', '.') }}', 
                             week: '{{ number_format($revenueWeek ?? 0, 0, ',', '.') }}', 
                             month: '{{ number_format($revenueMonth ?? 0, 0, ',', '.') }}', 
                             total: '{{ number_format($revenueTotal ?? 0, 0, ',', '.') }}' 
                         },
                         labels: { day: 'Hari Ini', week: 'Minggu Ini', month: 'Bulan Ini', total: 'Total Semua' }
                     }">

                    <div class="flex justify-between items-start mb-1">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider"
                                x-text="'Revenue (' + labels[filter] + ')'">Revenue</p>
                            <h2 class="text-2xl font-extrabold text-gray-800 mt-1 flex items-baseline gap-1">
                                <span class="text-sm font-normal text-gray-500">Rp</span>
                                <span x-text="amounts[filter]" x-transition.opacity>0</span>
                            </h2>
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 origin-top-right"
                                style="display: none;">
                                <button @click="filter = 'day'; open = false"
                                    class="block w-full text-left px-4 py-2 text-xs font-bold text-gray-600 hover:bg-purple-50 hover:text-purple-600">Hari
                                    Ini</button>
                                <button @click="filter = 'week'; open = false"
                                    class="block w-full text-left px-4 py-2 text-xs font-bold text-gray-600 hover:bg-purple-50 hover:text-purple-600">Minggu
                                    Ini</button>
                                <button @click="filter = 'month'; open = false"
                                    class="block w-full text-left px-4 py-2 text-xs font-bold text-gray-600 hover:bg-purple-50 hover:text-purple-600">Bulan
                                    Ini</button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button @click="filter = 'total'; open = false"
                                    class="block w-full text-left px-4 py-2 text-xs font-bold text-gray-600 hover:bg-purple-50 hover:text-purple-600">Total
                                    Semua</button>
                            </div>
                        </div>
                    </div>
                    <span
                        class="text-[10px] text-purple-600 font-bold bg-purple-50 px-2 py-1 rounded-full mt-4 inline-block">Income
                        Bersih</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800" id="chartTitle">Statistik Pertumbuhan</h3>
                            <p class="text-xs text-gray-400 mt-1" id="chartSubtitle">Pantau data secara realtime</p>
                        </div>
                        <div class="flex gap-2">
                            <select id="chartTopicFilter"
                                class="bg-gray-50 border border-gray-200 text-xs font-bold text-gray-600 rounded-xl focus:ring-blue-500 block p-2 outline-none cursor-pointer">
                                <option value="members">Data Member</option>
                                <option value="revenue">Total Revenue</option>
                                <option value="visits">Kunjungan</option>
                            </select>
                            <select id="chartPeriodFilter"
                                class="bg-white border border-gray-200 text-xs font-bold text-gray-600 rounded-xl focus:ring-blue-500 block p-2 outline-none cursor-pointer">
                                <option value="today" id="optToday">Hari Ini (Jam)</option>
                                <option value="7days" selected>7 Hari Terakhir</option>
                                <option value="30days">30 Hari (Per Minggu)</option>
                            </select>
                        </div>
                    </div>
                    <div id="chartMemberGrowth" class="w-full"></div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm h-full flex flex-col"
                    x-data="{ activeTab: 'all' }">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Aktivitas Hari Ini</h3>

                    <div class="flex gap-1 mb-4 overflow-x-auto pb-1 no-scrollbar">
                        <button @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">Semua</button>

                        <button @click="activeTab = 'regis'"
                            :class="activeTab === 'regis' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">Regis</button>

                        {{-- Value tab diubah menjadi 'renew' agar sinkron dengan $activity['type'] == 'renew' --}}
                        <button @click="activeTab = 'renew'"
                            :class="activeTab === 'renew' ? 'bg-green-600 text-white shadow-md shadow-green-500/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">Perpanjang</button>

                        <button @click="activeTab = 'reaktivasi'"
                            :class="activeTab === 'reaktivasi' ? 'bg-yellow-500 text-white shadow-md shadow-yellow-500/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">Reaktivasi</button>

                        <button @click="activeTab = 'visit'"
                            :class="activeTab === 'visit' ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all whitespace-nowrap">Absen</button>
                    </div>

                    <div class="space-y-3 overflow-y-auto max-h-[300px] pr-1 custom-scrollbar flex-1">
                        @forelse($activities as $activity)
                            <div x-show="activeTab === 'all' || activeTab === '{{ $activity['type'] }}'"
                                class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">

                                {{-- Penyesuaian background dan warna text icon berdasarkan tipe --}}
                                <div
                                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 
                            {{ $activity['type'] == 'regis' ? 'bg-blue-100 text-blue-600' : '' }}
                            {{ $activity['type'] == 'renew' ? 'bg-green-100 text-green-600' : '' }}
                            {{ $activity['type'] == 'reaktivasi' ? 'bg-yellow-100 text-yellow-600' : '' }}
                            {{ $activity['type'] == 'visit' ? 'bg-purple-100 text-purple-600' : '' }}
                            {{ !in_array($activity['type'], ['regis', 'renew', 'reaktivasi', 'visit']) ? 'bg-gray-100 text-gray-600' : '' }}">

                                    @if($activity['type'] == 'regis')
                                        {{-- Icon Add User (Registrasi Baru) --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                    @elseif($activity['type'] == 'renew')
                                        {{-- Icon Refresh / Sync (Perpanjang Masa Aktif) --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                    @elseif($activity['type'] == 'reaktivasi')
                                        {{-- Icon Zap / Petir (Reaktivasi Akun) --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z">
                                            </path>
                                        </svg>
                                    @elseif($activity['type'] == 'visit')
                                        {{-- Icon Fingerprint (Absen / Check-in) --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4">
                                            </path>
                                        </svg>
                                    @else
                                        {{-- Default Icon (Dokumen) --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold text-gray-800 truncate pr-2">{{ $activity['name'] }}</p>
                                        <span
                                            class="text-[9px] font-medium text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($activity['time'])->format('H:i') }}</span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $activity['desc'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400 text-xs">Belum ada aktivitas hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span> Tagihan Pending
                        </h3>
                        <a href="{{ route('listPaymentBarang') }}"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition">Lihat
                            Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-100 text-xs uppercase">
                                    <th class="pb-3 pl-2 font-semibold">Invoice</th>
                                    <th class="pb-3 font-semibold">Member</th>
                                    <th class="pb-3 font-semibold text-right">Total</th>
                                    <th class="pb-3 font-semibold text-center">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($pendingPayments as $pending)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 pl-2 font-bold text-gray-800">{{ $pending->invoice_code }}</td>
                                        <td class="py-3 text-xs font-medium text-gray-600">
                                            {{ $pending->member->nama_lengkap ?? 'Guest' }}
                                        </td>
                                        <td class="py-3 text-right font-bold text-gray-800 text-xs">Rp
                                            {{ number_format($pending->total_payment, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-center text-[10px] text-gray-400">
                                            {{ $pending->created_at->diffForHumans(null, true) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-400 text-sm">Semua tagihan lunas!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-rows-2 gap-4">
                    <div
                        class="bg-blue-600 rounded-3xl p-6 shadow-lg text-white relative overflow-hidden group cursor-pointer hover:bg-blue-700 transition">
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg mb-1">Registrasi Member</h3>
                            <a href="#"
                                class="text-[10px] bg-white/20 px-2 py-1 rounded font-bold hover:bg-white hover:text-blue-600 transition">Buka
                                Menu &rarr;</a>
                        </div>
                        <svg class="w-20 h-20 absolute -right-2 -bottom-2 text-white/10 group-hover:scale-110 transition-transform"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z">
                            </path>
                        </svg>
                    </div>
                    <div
                        class="bg-gray-900 rounded-3xl p-6 shadow-lg text-white relative overflow-hidden group cursor-pointer hover:bg-gray-800 transition">
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg mb-1">Cek Paket</h3>
                            <a href="#"
                                class="text-[10px] bg-white/20 px-2 py-1 rounded font-bold hover:bg-white hover:text-gray-900 transition">Buka
                                Menu &rarr;</a>
                        </div>
                        <svg class="w-20 h-20 absolute -right-2 -bottom-2 text-white/10 group-hover:scale-110 transition-transform"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script>
        window.chartData = {
            today: @json($chartToday),
            week: @json($chart7Days),
            month: @json($chart30Days)
        };
    </script>

    @vite('resources/js/Admin/dashboardAdmin.js')
</body>

</html>