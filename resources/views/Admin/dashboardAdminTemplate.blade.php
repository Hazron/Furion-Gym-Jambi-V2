<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Dynamic Title & Favicon (Konsisten dengan halaman Owner) --}}
    <title>Furion Gym Admin - @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon-furion.png') }}">

    {{-- 2. Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- 3. DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    {{-- 4. VITE: Pengganti Tailwind CDN (Otomatis memuat CSS & JS yang sudah di-minify) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 5. Alpine & SweetAlert (Bisa tetap pakai CDN, tapi tambahkan defer agar loading web tidak tertahan) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

{{-- 6. Hapus tag <style> manual, gunakan utility class Tailwind (bg-[#EAEEF1] dan antialiased untuk font lebih halus) --}}
<body class="bg-[#EAEEF1] text-gray-800 font-sans antialiased">

    {{-- NAVBAR --}}
    @include('layout.navbar')

    {{-- MAIN WRAPPER --}}
    <div class="p-4 sm:ml-72">
        <div class="p-8 mt-4">

            {{-- HEADER: JUDUL DI KIRI, PROFIL DI KANAN --}}
            <div class="flex justify-between items-start mb-10">

                {{-- KIRI: Tempat Judul Halaman --}}
                <div class="w-full">
                    @yield('header-content')
                </div>

                {{-- KANAN: User Profile & Notif --}}
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
                </div>

            </div>

            {{-- MAIN CONTENT --}}
            @yield('content')

        </div>
    </div>

    {{-- 7. Pindahkan jQuery & DataTables JS ke DALAM tag body bagian bawah --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- 8. Stack scripts HARUS berada di bawah jQuery agar child view bisa menggunakan jQuery ($) --}}
    @stack('scripts')

</body>
</html>