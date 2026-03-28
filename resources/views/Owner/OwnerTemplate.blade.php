<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furion Gym Owner - @yield('title', 'Dashboard')</title>
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#EAEEF1">

    <link rel="apple-touch-icon" href="{{ asset('icons/ios/180.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/ios/152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('icons/ios/167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/ios/180.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Furion Gym">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind Config --}}
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

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* PWA: Menghilangkan highlight biru saat tap di mobile */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-brand-bg text-gray-800">

    {{-- 1. SIDEBAR (Desktop) --}}
    @include('layout.navbar')

    {{-- ============================================================== --}}
    {{-- 2. MOBILE HEADER BAR (FIXED) --}}
    {{-- Persis seperti dashboard: Fixed di atas, Hamburger Besar, Avatar Kanan --}}
    {{-- ============================================================== --}}
    <div
        class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 px-4 py-3 sm:hidden flex items-center justify-between shadow-sm h-16">
        <div class="flex items-center gap-3">
            {{-- TOMBOL HAMBURGER --}}
            <button onclick="toggleSidebar()"
                class="text-gray-500 hover:text-blue-600 focus:outline-none transition p-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <span class="font-extrabold text-lg text-gray-900 tracking-tight">Furion Gym</span>
        </div>

        {{-- USER DROPDOWN (MOBILE) --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="focus:outline-none flex items-center">
                <img class="w-8 h-8 rounded-full border border-gray-100 object-cover"
                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Owner' }}&background=0D8ABC&color=fff"
                    alt="User">
            </button>

            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
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

    {{-- ============================================================== --}}
    {{-- 3. MAIN WRAPPER --}}
    {{-- ============================================================== --}}
    {{-- pt-20 di mobile agar konten tidak tertutup header fixed --}}
    <div class="pt-20 p-3 sm:pt-4 sm:p-4 sm:ml-72 transition-all duration-300">
        <div class="p-2 sm:p-8 mt-0 sm:mt-4">

            {{-- HEADER HALAMAN (Desktop) --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">

                {{-- Judul Halaman (Dinamis dari @yield) --}}
                <div class="w-full sm:w-auto">
                    @yield('header-content')
                </div>

                {{-- User Profile Desktop (Hidden di Mobile karena sudah ada di Header Fixed) --}}
                <div class="hidden sm:flex items-center justify-end w-full sm:w-auto gap-5">
                    <div class="relative ml-auto sm:ml-0" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 bg-white pl-2 pr-4 py-2 rounded-full shadow-sm hover:shadow-md transition border border-gray-100">
                            <img class="w-9 h-9 rounded-full border border-gray-100 object-cover"
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

            {{-- MAIN CONTENT --}}
            @yield('content')

        </div>
    </div>

    {{-- OVERLAY BACKDROP --}}
    <div id="sidebar-backdrop" onclick="toggleSidebar()"
        class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm hidden transition-opacity opacity-0"
        aria-hidden="true"></div>

    {{-- Global Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- Script Toggle Sidebar --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('logo-sidebar'); // Pastikan ID ini ada di navbar.blade.php
            const backdrop = document.getElementById('sidebar-backdrop');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.remove('opacity-0'); }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => { backdrop.classList.add('hidden'); }, 300);
            }
        }
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log('PWA: Service Worker Terdaftar (Scope: ' + reg.scope + ')'))
                    .catch(err => console.error('PWA: Registrasi Gagal', err));
            });
        }
    </script>

    @stack('scripts')

</body>

</html>