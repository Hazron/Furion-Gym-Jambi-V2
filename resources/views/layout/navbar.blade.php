@php
    // --- ADMIN ROUTES ---
    $isMemberRoute = Request::routeIs('memberAdmin', 'member.*', 'membership.*', 'admin.paketmember');
    $isPaymentRoute = Request::routeIs('membershipPayment', 'payment.*');
    $isParentMember = $isMemberRoute || $isPaymentRoute;
    $isBarangRoute = Request::routeIs('indexBarang', 'listPaymentBarang', 'barang.*');

    // --- OWNER ROUTES ---
    $isOwnerPaket = Request::routeIs('owner.paketmemberfurion', 'owner.promomemberfurion');
    $isOwnerLaporan = Request::routeIs('owner.laporankeuangan', 'owner.laporan-Membership');
    $isOwnerAdminLog = Request::routeIs('owner.aktivitasadmin');
    $isOwnerEtalase = Request::routeIs('owner.monitoringEtalase');
@endphp

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-50 w-72 h-screen bg-white border-r border-gray-100 transition-transform -translate-x-full sm:translate-x-0 font-inter shadow-lg sm:shadow-none"
    aria-label="Sidebar">

    <div class="h-full px-5 py-8 overflow-y-auto flex flex-col bg-white custom-scrollbar">

        <div class="flex items-center justify-between mb-10 pl-1">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl text-white font-bold shadow-lg shadow-blue-200">
                    FG
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-xl font-black text-gray-900 tracking-tighter">FURION</span>
                    <span class="text-xl font-black text-blue-600 tracking-tighter">GYM</span>
                    <span class="text-xl font-black text-blue-900 tracking-tighter">JAMBI</span>
                </div>
            </div>
            <button type="button" onclick="toggleSidebar()"
                class="sm:hidden text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <ul class="space-y-2 font-medium">

            {{-- ========================================================== --}}
            {{-- MENU ADMIN --}}
            {{-- ========================================================== --}}
            @if(Auth::user()->role == 'admin')
                <li class="px-3 mb-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Menu Admin</span>
                </li>

                <li>
                    <a href="{{ route('dashboardAdmin') }}"
                        class="flex items-center p-3.5 rounded-2xl transition-all group {{ request()->routeIs('dashboardAdmin') ? 'text-white bg-blue-600 shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="ml-3 text-sm font-bold">Dashboard</span>
                    </a>
                </li>

                <li x-data="{ open: {{ $isParentMember ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center w-full p-3.5 rounded-2xl transition-all {{ $isParentMember ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="flex-1 ml-3 text-sm font-bold text-left">Member</span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul x-show="open" class="mt-2 space-y-1 ml-9 border-l-2 border-blue-100 pl-4">
                        <li><a href="{{ route('memberAdmin') }}"
                                class="block py-2 text-sm {{ Request::routeIs('memberAdmin') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Data
                                Member</a></li>
                        <li><a href="{{ route('membershipPayment') }}"
                                class="block py-2 text-sm {{ Request::routeIs('membershipPayment') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Payment
                                Member</a></li>
                        <li><a href="{{route('admin.paketmember')}}"
                                class="block py-2 text-sm {{ Request::routeIs('admin.paketmember') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Paket
                                Member</a></li>
                    </ul>
                </li>

                <li x-data="{ open: {{ $isBarangRoute ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center w-full p-3.5 rounded-2xl transition-all {{ $isBarangRoute ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="flex-1 ml-3 text-sm font-bold text-left">Produk & Stok</span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul x-show="open" class="mt-2 space-y-1 ml-9 border-l-2 border-blue-100 pl-4">
                        <li><a href="{{ route('indexBarang') }}"
                                class="block py-2 text-sm {{ Request::routeIs('indexBarang') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Order
                                Produk</a></li>
                        <li><a href="{{ route('listPaymentBarang') }}"
                                class="block py-2 text-sm {{ Request::routeIs('listPaymentBarang') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Riwayat
                                Payment</a></li>
                    </ul>
                </li>

                {{-- ========================================================== --}}
                {{-- MENU OWNER --}}
                {{-- ========================================================== --}}
            @elseif(Auth::user()->role == 'owner')
                <li class="px-3 mb-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Menu Owner</span>
                </li>

                <li>
                    <a href="{{ route('owner.dashboard') }}"
                        class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('owner.dashboard') ? 'text-white bg-blue-600 shadow-lg' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="ml-3 text-sm font-bold">Dashboard Insight</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('owner.memberFurion') }}"
                        class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('owner.memberFurion') ? 'text-white bg-blue-600 shadow-lg' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="ml-3 text-sm font-bold">Data Semua Member</span>
                    </a>
                </li>

                <li x-data="{ open: {{ $isOwnerPaket ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center w-full p-3.5 rounded-2xl transition-all {{ $isOwnerPaket ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="flex-1 ml-3 text-sm font-bold text-left">Manajemen Paket</span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul x-show="open" class="mt-2 space-y-1 ml-9 border-l-2 border-blue-100 pl-4">
                        <li><a href="{{ route('owner.paketmemberfurion') }}"
                                class="block py-2 text-sm {{ Request::routeIs('owner.paketmemberfurion') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Paket
                                Reguler</a></li>
                        <li><a href="{{ route('owner.promomemberfurion') }}"
                                class="block py-2 text-sm {{ Request::routeIs('owner.promomemberfurion') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Paket
                                Promo</a></li>
                    </ul>
                </li>

                <li x-data="{ open: {{ $isOwnerLaporan ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center w-full p-3.5 rounded-2xl transition-all {{ $isOwnerLaporan ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="flex-1 ml-3 text-sm font-bold text-left">Laporan</span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul x-show="open" class="mt-2 space-y-1 ml-9 border-l-2 border-blue-100 pl-4">
                        <li><a href="{{ route('owner.laporankeuangan') }}"
                                class="block py-2 text-sm {{ Request::routeIs('owner.laporankeuangan') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Laporan Keuangan</a></li>
                        <li><a href="{{ route('owner.laporan-Membership') }}"
                                class="block py-2 text-sm {{ Request::routeIs('owner.laporan-Membership') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">Laporan Membership</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('owner.aktivitasadmin') }}"
                        class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('owner.aktivitasadmin') ? 'text-white bg-blue-600 shadow-lg' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 text-sm font-bold">Log Aktivitas Admin</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('owner.monitoringEtalase') }}"
                        class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('owner.monitoringEtalase') ? 'text-white bg-blue-600 shadow-lg' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="ml-3 text-sm font-bold">Monitoring Etalase</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</aside>