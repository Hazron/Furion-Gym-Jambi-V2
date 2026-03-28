<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furion Gym Jambi - Fitness Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Teko:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Typography */
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1,
        h2,
        h3,
        .brand-font {
            font-family: 'Teko', sans-serif;
            letter-spacing: 0.05em;
        }

        /* Color Palette */
        .bg-brand-blue {
            background-color: #0026e6;
        }

        .text-brand-blue {
            color: #0026e6;
        }

        .border-brand-blue {
            border-color: #0026e6;
        }

        .bg-brand-yellow {
            background-color: #fbcc16;
        }

        .text-brand-yellow {
            color: #fbcc16;
        }

        .hover-brand-yellow:hover {
            background-color: #e5b80b;
        }

        /* Utilities */
        .clip-slant-bottom {
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }

        .clip-slant-top {
            clip-path: polygon(0 10%, 100% 0, 100% 100%, 0 100%);
        }

        /* [PERBAIKAN]: Penyesuaian shadow agar lebih kontras dengan background */
        .card-shadow {
            box-shadow: 0 10px 30px -10px rgba(0, 38, 230, 0.1);
        }

        .card-pop {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans overflow-x-hidden">

    <nav
        class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-2">
                    <div class="bg-brand-blue p-1.5 rounded-sm transform -skew-x-12 shadow-md">
                        <i data-lucide="zap" class="text-brand-yellow w-5 h-5 fill-current"></i>
                    </div>
                    <span class="brand-font text-3xl font-bold tracking-wider text-slate-900">FURION <span
                            class="text-brand-blue">GYM</span></span>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-slate-600 hover:text-brand-blue font-semibold transition">Home</a>
                        <a href="#why" class="text-slate-600 hover:text-brand-blue font-semibold transition">Kenapa
                            Kami</a>
                        <a href="#facilities"
                            class="text-slate-600 hover:text-brand-blue font-semibold transition">Fasilitas</a>
                        <a href="#pricing"
                            class="text-slate-600 hover:text-brand-blue font-semibold transition">Harga</a>
                        <a href="#contact"
                            class="bg-brand-blue text-white hover:bg-blue-800 px-6 py-2 rounded-sm font-bold skew-x-[-10deg] transition transform hover:scale-105 inline-block shadow-lg shadow-blue-500/20">
                            <span class="skew-x-[10deg] block">GABUNG SEKARANG</span>
                        </a>
                    </div>
                </div>

                <div class="md:hidden">
                    <button class="text-slate-800 hover:text-brand-blue">
                        <i data-lucide="menu" class="w-8 h-8"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <section id="home" class="relative h-screen flex items-center justify-center clip-slant-bottom">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/landing-page/bg gym.png') }}" alt="Gym Background"
                class="w-full h-full object-cover">
            <div
                class="absolute inset-0 bg-gradient-to-r from-slate-900/95 via-slate-900/80 to-brand-blue/30 mix-blend-multiply">
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 text-center sm:text-left w-full mt-16">
            <div
                class="inline-block bg-brand-yellow text-slate-900 font-bold px-3 py-1 mb-4 transform -skew-x-12 text-sm tracking-wide shadow-lg">
                <span class="block skew-x-12">#1 FITNESS CENTER DI JAMBI</span>
            </div>
            <h1 class="text-6xl md:text-8xl font-bold uppercase leading-tight italic drop-shadow-2xl text-white">
                UNLOCK YOUR <br>
                <span class="bg-clip-text bg-gradient-to-r from-brand-yellow to-white">FULL POTENTIAL</span>
            </h1>
            <p class="mt-6 text-xl text-gray-200 max-w-2xl font-light leading-relaxed">
                Transformasi dimulai dari sini. Fasilitas premium, lingkungan suportif, dan hasil nyata.
            </p>
            <div
                class="mt-10 bg-white/10 backdrop-blur-md p-2 rounded-lg border border-white/20 inline-block max-w-md w-full shadow-2xl">
                <form action="{{ route('member') }}" method="GET" class="flex gap-2">
                    <input type="text" name="member_id" id="landing_member_id" placeholder="MASUKKAN ID MEMBER..."
                        class="flex-1 bg-transparent text-white placeholder-blue-200 px-4 py-3 outline-none font-mono font-bold uppercase border-r border-white/20"
                        required>
                    <button type="submit"
                        class="bg-brand-yellow text-slate-900 px-6 py-2 rounded font-bold hover:bg-yellow-400 transition uppercase tracking-wider">
                        Cek
                    </button>
                </form>
            </div>
        </div>
    </section>

    <div
        class="bg-brand-blue py-10 shadow-2xl relative z-20 -mt-16 mx-4 md:mx-auto max-w-6xl rounded-2xl border-b-4 border-brand-yellow">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-blue-400/30">
            <div>
                <h3 class="text-4xl font-bold text-brand-yellow drop-shadow-sm">400+</h3>
                <p class="text-blue-100 text-sm uppercase tracking-widest font-medium">Member Aktif</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-brand-yellow drop-shadow-sm">20+</h3>
                <p class="text-blue-100 text-sm uppercase tracking-widest font-medium">Pelatih Pro</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-brand-yellow drop-shadow-sm">50+</h3>
                <p class="text-blue-100 text-sm uppercase tracking-widest font-medium">Alat Modern</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-brand-yellow drop-shadow-sm">4.9/5</h3>
                <p class="text-blue-100 text-sm uppercase tracking-widest font-medium">Rating Google</p>
            </div>
        </div>
    </div>

    <section id="why" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-brand-blue font-bold tracking-widest uppercase text-sm">KEUNGGULAN KAMI</span>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mt-2 uppercase italic">MENGAPA <span
                        class="text-brand-blue">FURION GYM?</span></h2>
                <div class="w-20 h-1.5 bg-brand-yellow mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <div
                    class="group p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-brand-blue transition duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-white rounded-xl flex items-center justify-center mb-6 text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition duration-300 shadow-sm border border-slate-100">
                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 uppercase italic">LOKASI STRATEGIS</h3>
                    <p class="text-slate-600 leading-relaxed">Terletak di pusat kota Jambi, akses mudah, dan parkiran
                        luas yang aman untuk kendaraan Anda.</p>
                </div>

                <div
                    class="group p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-brand-blue transition duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-white rounded-xl flex items-center justify-center mb-6 text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition duration-300 shadow-sm border border-slate-100">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 uppercase italic">ALAT TERAWAT</h3>
                    <p class="text-slate-600 leading-relaxed">Kami menjamin kebersihan dan fungsi alat. Maintenance
                        rutin dilakukan agar latihan Anda tidak terganggu.</p>
                </div>

                <div
                    class="group p-8 rounded-2xl bg-slate-50 border border-slate-200 hover:border-brand-blue transition duration-300 hover:shadow-xl hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-white rounded-xl flex items-center justify-center mb-6 text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition duration-300 shadow-sm border border-slate-100">
                        <i data-lucide="users" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 uppercase italic">KOMUNITAS POSITIF</h3>
                    <p class="text-slate-600 leading-relaxed">Lingkungan yang ramah bagi pemula. Tidak ada intimidasi,
                        semua member saling mendukung.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="facilities" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-brand-blue font-bold tracking-widest uppercase text-sm">FASILITAS KAMI</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mt-2 uppercase italic">WORLD CLASS <span
                            class="text-brand-blue">EQUIPMENT</span></h2>
                </div>
                <a href="#" class="hidden md:flex items-center gap-2 text-brand-blue font-bold hover:underline">Lihat
                    Galeri Lengkap <i data-lucide="arrow-right" class="w-5 h-5"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="group relative h-72 overflow-hidden rounded-xl shadow-xl border border-slate-200 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1637666062717-1c6bcfa4a4df?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                        alt="Free Weights">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent flex flex-col justify-end p-6">
                        <h3 class="text-2xl font-bold text-white uppercase italic mb-1">Free Weight Area</h3>
                        <p class="text-gray-300 text-sm">Dumbbell set lengkap & Bench Press</p>
                    </div>
                </div>
                <div
                    class="group relative h-72 overflow-hidden rounded-xl shadow-xl border border-slate-200 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1576678927484-cc907957088c?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover transition duration-700 group-hover:scale-110" alt="Cardio">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent flex flex-col justify-end p-6">
                        <h3 class="text-2xl font-bold text-white uppercase italic mb-1">Cardio Zone</h3>
                        <p class="text-gray-300 text-sm">Treadmill, Bike, & Elliptical</p>
                    </div>
                </div>
                <div
                    class="group relative h-72 overflow-hidden rounded-xl shadow-xl border border-slate-200 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1596357395217-80de13130e92?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover transition duration-700 group-hover:scale-110" alt="Lockers">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent flex flex-col justify-end p-6">
                        <h3 class="text-2xl font-bold text-white uppercase italic mb-1">Locker & Shower</h3>
                        <p class="text-gray-300 text-sm">Bersih, Aman, & Nyaman</p>
                    </div>
                </div>
                <div
                    class="group relative h-72 overflow-hidden rounded-xl shadow-xl border border-slate-200 cursor-pointer md:col-span-3">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&q=80&w=1200"
                        class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                        alt="Functional Training">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent flex flex-col justify-end p-6">
                        <h3 class="text-2xl font-bold text-white uppercase italic mb-1">Functional & Crossfit Area</h3>
                        <p class="text-gray-300 text-sm">Area luas untuk HIIT dan Agility Training</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-24 bg-brand-blue clip-slant-top clip-slant-bottom relative">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold uppercase italic text-white">OUR PROGRAMS</h2>
                <div class="w-20 h-1.5 bg-brand-yellow mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div
                    class="bg-white p-8 rounded-xl shadow-2xl border-l-8 border-brand-yellow flex gap-6 group hover:-translate-y-2 transition duration-300">
                    <div class="shrink-0">
                        <div
                            class="bg-blue-50 p-4 rounded-full text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition">
                            <i data-lucide="users" class="w-8 h-8"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900 uppercase italic">Personal Trainer</h3>
                        <p class="text-slate-600 mt-2 leading-relaxed">Pendampingan intensif one-on-one untuk memastikan
                            teknik yang benar dan hasil maksimal.</p>
                    </div>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-2xl border-l-8 border-brand-yellow flex gap-6 group hover:-translate-y-2 transition duration-300">
                    <div class="shrink-0">
                        <div
                            class="bg-blue-50 p-4 rounded-full text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition">
                            <i data-lucide="flame" class="w-8 h-8"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900 uppercase italic">Fat Loss Program</h3>
                        <p class="text-slate-600 mt-2 leading-relaxed">Program kardio dan beban terukur untuk membakar
                            lemak tubuh secara efisien.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-brand-blue font-bold tracking-widest uppercase text-sm">APA KATA MEREKA</span>
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mt-2 mb-16 uppercase italic">TESTIMONI <span
                    class="text-brand-blue">MEMBER</span></h2>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200 relative text-left">
                    <i data-lucide="quote" class="absolute top-8 right-8 text-brand-blue/10 w-12 h-12 fill-current"></i>
                    <div class="flex items-center gap-4 mb-6 relative z-10">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg"
                            class="w-14 h-14 rounded-full object-cover border-2 border-brand-blue shadow-sm">
                        <div>
                            <h4 class="font-bold text-slate-900 text-lg">Rudi Hartono</h4>
                            <div class="flex text-brand-yellow">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-600 italic relative z-10">"Alat-alatnya lengkap banget dan selalu bersih.
                        Coach-nya juga ramah, gak pelit ilmu buat ngajarin pemula kayak saya. Rekomen banget!"</p>
                </div>
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200 relative text-left transform md:-translate-y-4">
                    <i data-lucide="quote" class="absolute top-8 right-8 text-brand-blue/10 w-12 h-12 fill-current"></i>
                    <div class="flex items-center gap-4 mb-6 relative z-10">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg"
                            class="w-14 h-14 rounded-full object-cover border-2 border-brand-blue shadow-sm">
                        <div>
                            <h4 class="font-bold text-slate-900 text-lg">Sari Mawar</h4>
                            <div class="flex text-brand-yellow">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-600 italic relative z-10">"Ikut program fat loss disini hasilnya nyata. Dalam 3
                        bulan turun 10kg! Suasana gymnya juga enak, cewek gak ngerasa risih latihan disini."</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200 relative text-left">
                    <i data-lucide="quote" class="absolute top-8 right-8 text-brand-blue/10 w-12 h-12 fill-current"></i>
                    <div class="flex items-center gap-4 mb-6 relative z-10">
                        <img src="https://randomuser.me/api/portraits/men/85.jpg"
                            class="w-14 h-14 rounded-full object-cover border-2 border-brand-blue shadow-sm">
                        <div>
                            <h4 class="font-bold text-slate-900 text-lg">Dedi Kurniawan</h4>
                            <div class="flex text-brand-yellow">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-600 italic relative z-10">"Harga membernya sangat worth it dengan fasilitas
                        yang didapat. Parkiran luas dan aman, jadi tenang pas lagi workout."</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-brand-blue font-bold tracking-widest uppercase">INVESTASI KESEHATAN</span>
                <h2 class="text-4xl md:text-5xl font-bold uppercase italic text-slate-900 mt-2">PILIH PAKET <span
                        class="text-brand-blue">MEMBER</span></h2>
                <div class="w-20 h-1.5 bg-brand-yellow mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start max-w-6xl mx-auto">
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200 group hover:border-brand-blue transition h-full flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gray-200 group-hover:bg-brand-blue transition">
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 uppercase italic">SINGLE <span
                            class="text-brand-blue text-lg not-italic block font-sans font-medium text-slate-500">Short
                            Term</span></h3>

                    <div class="mt-6 space-y-6 flex-grow">
                        <div class="pb-4 border-b border-slate-100">
                            <p class="text-sm text-slate-500 mb-1">Durasi 1 Bulan</p>
                            <div class="flex items-end gap-1">
                                <span class="text-3xl font-bold text-slate-900">Rp 250.000</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Durasi 3 Bulan</p>
                            <div class="flex items-end gap-1">
                                <span class="text-3xl font-bold text-slate-900">Rp 600.000</span>
                            </div>
                            <span
                                class="text-xs text-green-700 bg-green-100 px-2 py-1 rounded mt-2 inline-block font-bold">Hemat
                                Rp 150.000</span>
                        </div>
                    </div>

                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20mau%20daftar%20member%20Single%20Short%20Term"
                        class="mt-8 block w-full py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-center font-bold hover:bg-slate-100 transition shadow-sm">PILIH
                        PAKET</a>
                </div>

                <div
                    class="bg-brand-blue p-8 rounded-2xl relative transform md:scale-105 shadow-2xl border-t-4 border-brand-yellow z-10 h-full flex flex-col">
                    <div
                        class="absolute top-0 right-0 bg-brand-yellow text-slate-900 text-xs font-bold px-3 py-1 rounded-bl-lg shadow-md">
                        BEST VALUE</div>
                    <h3 class="text-2xl font-bold text-white uppercase italic">SINGLE <span
                            class="text-blue-200 text-lg not-italic block font-sans font-medium">Long Term</span></h3>

                    <div class="mt-6 space-y-6 flex-grow">
                        <div class="pb-4 border-b border-blue-800/50">
                            <p class="text-sm text-blue-200 mb-1">Durasi 6 Bulan</p>
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-bold text-brand-yellow">Rp 1.050.000</span>
                            </div>
                            <span
                                class="text-xs text-brand-blue bg-blue-100 px-2 py-1 rounded mt-2 inline-block font-bold">Hemat
                                Rp 450.000</span>
                        </div>
                        <div>
                            <p class="text-sm text-blue-200 mb-1">Durasi 1 Tahun</p>
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-bold text-brand-yellow">Rp 1.900.000</span>
                            </div>
                            <span
                                class="text-xs text-brand-blue bg-brand-yellow px-2 py-1 rounded mt-2 inline-block font-bold">SUPER
                                HEMAT</span>
                        </div>
                    </div>

                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20mau%20daftar%20member%20Single%20Long%20Term"
                        class="mt-8 block w-full py-4 bg-brand-yellow text-slate-900 rounded-lg text-center font-bold hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">GABUNG
                        SEKARANG</a>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200 group hover:border-brand-blue transition h-full flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gray-200 group-hover:bg-brand-blue transition">
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 uppercase italic">COUPLE <span
                            class="text-brand-blue text-lg not-italic block font-sans font-medium text-slate-500">Berdua
                            Lebih Seru</span></h3>

                    <div class="mt-6 space-y-6 flex-grow">
                        <div class="pb-4 border-b border-slate-100">
                            <p class="text-sm text-slate-500 mb-1">Durasi 1 Bulan (2 Org)</p>
                            <div class="flex items-end gap-1">
                                <span class="text-3xl font-bold text-slate-900">Rp 400.000</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Rp 200rb / orang</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Durasi 3 Bulan (2 Org)</p>
                            <div class="flex items-end gap-1">
                                <span class="text-3xl font-bold text-slate-900">Rp 1.000.000</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Lebih hemat untuk partner gym!</p>
                        </div>
                    </div>

                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20mau%20daftar%20member%20Couple"
                        class="mt-8 block w-full py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-center font-bold hover:bg-slate-100 transition shadow-sm">AJAK
                        TEMAN</a>
                </div>
            </div>

            <div class="max-w-4xl mx-auto mt-12">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white border border-brand-blue/20 rounded-2xl shadow-lg p-8 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                    <div class="flex items-center gap-5 justify-center md:justify-start">
                        <div class="bg-brand-blue p-4 rounded-full text-white shadow-md">
                            <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-xl mb-1">KHUSUS PELAJAR / MAHASISWA</h4>
                            <p class="text-slate-600">Tunjukkan kartu identitas pelajar yang masih aktif.</p>
                        </div>
                    </div>
                    <div
                        class="bg-white px-8 py-3 rounded-xl border border-brand-blue/20 shadow-sm flex flex-col items-center">
                        <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Potongan
                            Harga</span>
                        <span class="text-3xl font-bold text-brand-blue">20% OFF</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold uppercase italic text-slate-900 mb-12">MEET THE <span
                    class="text-brand-blue">COACHES</span></h2>
            <div class="flex flex-wrap justify-center gap-10">
                <div
                    class="w-64 bg-white p-6 rounded-2xl shadow-xl border border-slate-200 transform transition hover:-translate-y-2">
                    <div
                        class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-brand-blue mb-4 shadow-md">
                        <img src="https://images.unsplash.com/photo-1567013127542-490d757e51fc?auto=format&fit=crop&q=80&w=400"
                            class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">COACH ANDI</h3>
                    <p class="text-brand-blue text-sm uppercase font-bold mt-1">Strength Specialist</p>
                </div>
                <div
                    class="w-64 bg-white p-6 rounded-2xl shadow-xl border border-slate-200 transform transition hover:-translate-y-2">
                    <div
                        class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-brand-blue mb-4 shadow-md">
                        <img src="https://images.unsplash.com/photo-1611672585731-fa1060a7a3c2?auto=format&fit=crop&q=80&w=400"
                            class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">COACH BUDI</h3>
                    <p class="text-brand-blue text-sm uppercase font-bold mt-1">Bodybuilding</p>
                </div>
                <div
                    class="w-64 bg-white p-6 rounded-2xl shadow-xl border border-slate-200 transform transition hover:-translate-y-2">
                    <div
                        class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-brand-blue mb-4 shadow-md">
                        <img src="https://images.unsplash.com/photo-1597347343908-2937e7dcc560?auto=format&fit=crop&q=80&w=400"
                            class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">COACH SITI</h3>
                    <p class="text-brand-blue text-sm uppercase font-bold mt-1">Cardio & Zumba</p>
                </div>
            </div>
        </div>
    </section>

    <section id="location" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-brand-blue font-bold tracking-widest uppercase text-sm">AKSES MUDAH</span>
                <h2 class="text-4xl md:text-5xl font-bold uppercase italic text-slate-900 mt-2">TEMUKAN LOKASI KAMI</h2>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl shadow-xl border border-slate-200">
                <div class="aspect-w-16 aspect-h-9 w-full h-96">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.627627471933!2d103.5934164!3d-1.6111162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2586e0c657a8a9%3A0x1d4a0468f7d98d2!2sJambi!5e0!3m2!1sen!2sid!4v1702220678235!5m2!1sen!2sid"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="rounded-lg shadow-inner"></iframe>
                </div>
                <div class="text-center mt-6 pb-2">
                    <p class="text-slate-700 font-medium text-lg">Jl. Jenderal Sudirman No. 12, Jambi Selatan.</p>
                    <p class="text-slate-500 mt-1">Akses mudah dari segala arah.</p>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="bg-slate-950 border-t-4 border-brand-yellow pt-16 pb-8 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <i data-lucide="zap" class="text-brand-yellow fill-current"></i>
                        <span class="brand-font text-2xl font-bold text-white">FURION <span
                                class="text-brand-blue">GYM</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Pusat kebugaran terbaik di Jambi dengan fasilitas modern dan komunitas yang solid.
                    </p>
                </div>
                <div>
                    <h4 class="text-brand-yellow font-bold mb-4 uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#why" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#facilities" class="hover:text-white transition">Fasilitas</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Harga</a></li>
                        <li><a href="#location" class="hover:text-white transition">Lokasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-brand-yellow font-bold mb-4 uppercase tracking-wider">Kontak</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-start gap-2"><i data-lucide="map-pin"
                                class="w-4 h-4 mt-1 text-brand-blue"></i> Jl. Jenderal Sudirman No. 12, Jambi</li>
                        <li class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4 text-brand-blue"></i>
                            +62 812-3456-7890</li>
                        <li class="flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4 text-brand-blue"></i>
                            info@furiongym.com</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-brand-yellow font-bold mb-4 uppercase tracking-wider">Jam Buka</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex justify-between border-b border-gray-800 pb-1"><span>Senin - Jumat:</span> <span
                                class="text-white">06:00 - 22:00</span></li>
                        <li class="flex justify-between border-b border-gray-800 pb-1"><span>Sabtu:</span> <span
                                class="text-white">07:00 - 20:00</span></li>
                        <li class="flex justify-between border-b border-gray-800 pb-1"><span>Minggu:</span> <span
                                class="text-white">08:00 - 18:00</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-600 text-sm">
                <p>© 2025 Furion Gym Jambi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <div id="checkMemberModal" class="relative z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div id="modalBackdrop"
            class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity duration-300 ease-out opacity-0"
            onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="modalPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 sm:my-8 sm:w-full sm:max-w-2xl flex flex-col max-h-[90vh]">
                    <div class="bg-blue-600 px-6 py-6 flex-shrink-0 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <i data-lucide="dumbbell" class="w-24 h-24 text-white -rotate-12"></i>
                        </div>
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                    <i data-lucide="activity" class="h-6 w-6 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white brand-font tracking-wide">CEK STATUS MEMBER
                                    </h3>
                                    <p class="text-blue-100 text-xs">Cek masa aktif & riwayat latihanmu</p>
                                </div>
                            </div>
                            <button onclick="closeModal()"
                                class="text-white/70 hover:text-white transition bg-white/10 hover:bg-white/20 rounded-full p-1">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <form id="checkForm" onsubmit="handleCheck(event)" class="mt-6 relative z-20">
                            <div class="relative flex items-center">
                                <i data-lucide="search" class="absolute left-4 w-5 h-5 text-blue-300"></i>
                                <input type="text" id="member_id" name="member_id"
                                    placeholder="MASUKKAN ID MEMBER (CONTOH: FR-2025)"
                                    class="w-full pl-12 pr-4 py-3.5 rounded-xl border-none ring-2 ring-blue-500/30 focus:ring-white bg-blue-700/50 text-white placeholder-blue-300 font-mono uppercase font-bold outline-none transition shadow-inner">
                                <button type="submit"
                                    class="absolute right-2 bg-brand-yellow text-blue-900 px-4 py-1.5 rounded-lg font-bold text-sm hover:bg-yellow-300 transition shadow-lg">CEK</button>
                            </div>
                        </form>
                    </div>
                    <div id="result-area" class="px-6 py-6 overflow-y-auto bg-slate-50 hidden min-h-[300px]"></div>
                    <div id="initial-state" class="px-6 py-12 text-center text-slate-400">
                        <i data-lucide="search-check" class="w-16 h-16 mx-auto mb-3 text-slate-200"></i>
                        <p>Silakan masukkan ID Member Anda di atas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($promo)
        <div id="promoModal" class="fixed inset-0 z-[150] hidden" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity opacity-0 duration-300"
                id="promoBackdrop" onclick="closePromo()"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div id="promoPanel"
                        class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all duration-500 opacity-0 scale-90 sm:my-8 sm:w-full sm:max-w-3xl border-4 border-brand-yellow">

                        <button type="button" onclick="closePromo()"
                            class="absolute top-4 right-4 z-20 bg-black/20 hover:bg-black/40 text-white rounded-full p-1 backdrop-blur-md transition">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>

                        <div class="relative h-48 sm:h-60 overflow-hidden bg-gray-200">
                            <img src="{{ asset('storage/' . $promo->gambar_banner) }}" alt="{{ $promo->nama_campaign }}"
                                class="w-full h-full object-cover object-center">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent opacity-80">
                            </div>

                            <div class="absolute bottom-0 left-0 p-6 w-full">
                                <span
                                    class="bg-brand-yellow text-slate-900 text-[10px] font-black px-3 py-1 rounded shadow-lg uppercase tracking-widest mb-2 inline-block italic">
                                    LIMITED TIME OFFER
                                </span>
                                <h3
                                    class="text-3xl sm:text-4xl font-bold text-white uppercase italic drop-shadow-md leading-none brand-font">
                                    {{ $promo->nama_campaign }}
                                </h3>
                            </div>
                        </div>

                        <div class="px-6 py-8 bg-white relative">
                            <i data-lucide="zap"
                                class="absolute right-[-10px] top-[-10px] w-32 h-32 text-slate-50 -rotate-12 z-0"></i>

                            <div class="relative z-10">
                                <div class="flex justify-start mb-6">
                                    <div
                                        class="flex items-center gap-2 text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 shadow-sm">
                                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                        <span class="text-xs font-bold uppercase tracking-tight">
                                            Berakhir:
                                            {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $singlePromos = $promo->paketMembers->where('jenis', 'promo');
                                    $couplePromos = $promo->paketMembers->where('jenis', 'promo couple');
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                                    <div>
                                        <h4
                                            class="text-slate-900 font-black text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-brand-blue"></span>
                                            Promo Member Single
                                        </h4>
                                        <div class="space-y-3">
                                            @forelse($singlePromos as $paket)
                                                <div
                                                    class="p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-brand-blue transition-all group shadow-sm relative overflow-hidden">
                                                    <div class="flex justify-between items-center relative z-10">
                                                        <div class="flex flex-col text-left">
                                                            <span
                                                                class="text-[10px] font-bold text-brand-blue uppercase tracking-tighter">{{ $paket->durasi }}</span>
                                                            <span
                                                                class="text-sm font-bold text-slate-800 leading-tight">{{ $paket->nama_paket }}</span>
                                                        </div>
                                                        <div class="text-right">
                                                            <span
                                                                class="text-lg font-black text-slate-900 block leading-none">Rp
                                                                {{ number_format($paket->harga, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-xs text-slate-400 italic">Tidak ada promo single aktif.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="border-t md:border-t-0 md:border-l border-slate-100 pt-6 md:pt-0 md:pl-8">
                                        <h4
                                            class="text-pink-600 font-black text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span
                                                class="w-2 h-2 rounded-full bg-pink-500 shadow-[0_0_8px_rgba(236,72,153,0.5)]"></span>
                                            Promo Member Couple
                                        </h4>
                                        <div class="space-y-3">
                                            @forelse($couplePromos as $paket)
                                                <div
                                                    class="p-4 rounded-2xl border-2 border-pink-50 bg-pink-50/20 hover:border-pink-500 transition-all group shadow-sm relative overflow-hidden">
                                                    <div class="flex justify-between items-center relative z-10">
                                                        <div class="flex flex-col text-left">
                                                            <span
                                                                class="text-[10px] font-bold text-pink-500 uppercase tracking-tighter">{{ $paket->durasi }}</span>
                                                            <span
                                                                class="text-sm font-bold text-slate-800 leading-tight">{{ $paket->nama_paket }}</span>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="text-lg font-black text-pink-700 block leading-none">Rp
                                                                {{ number_format($paket->harga, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    <i data-lucide="users"
                                                        class="absolute right-[-5px] bottom-[-5px] w-12 h-12 text-pink-200/30 -rotate-12"></i>
                                                </div>
                                            @empty
                                                <p class="text-xs text-slate-400 italic text-left">Tidak ada promo couple aktif.
                                                </p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-10 space-y-4">
                                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20tertarik%20klaim%20promo%20dari%20campaign:%20{{ urlencode($promo->nama_campaign) }}"
                                        target="_blank"
                                        class="group flex items-center justify-center gap-3 w-full bg-brand-blue hover:bg-blue-800 text-white text-center py-4 rounded-2xl font-bold uppercase tracking-widest transition-all shadow-xl shadow-blue-500/30 transform hover:-translate-y-1 active:scale-95">
                                        <span>KLAIM PROMO VIA WHATSAPP</span>
                                        <i data-lucide="send"
                                            class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                    </a>
                                    <p class="text-[10px] text-center text-slate-400 font-medium italic">
                                        *Syarat dan ketentuan berlaku. Harga couple berlaku untuk pendaftaran 2 orang
                                        sekaligus.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="floatingPromoBtn"
            class="fixed bottom-6 right-6 z-[90] hidden transition-all duration-500 transform translate-y-20 opacity-0">
            <button onclick="openPromo()"
                class="group bg-brand-yellow hover:bg-yellow-400 text-slate-900 font-bold py-3 px-5 rounded-full shadow-2xl flex items-center gap-3 border-4 border-white/50 backdrop-blur-sm transition-all hover:scale-105 hover:-translate-y-1">
                <div class="bg-white p-1.5 rounded-full text-brand-blue group-hover:rotate-12 transition">
                    <i data-lucide="gift" class="w-5 h-5"></i>
                </div>
                <div class="text-left">
                    <span class="block text-[10px] uppercase tracking-wider text-slate-600 leading-none mb-0.5">Jangan
                        Lewatkan</span>
                    <span class="block text-sm leading-none">KLAIM PROMO</span>
                </div>
            </button>
        </div>
    @endif

    <script>
        lucide.createIcons();

        // LOGIC SCRIPT BAWAAN - Tidak diubah, tetap berfungsi seperti sebelumnya
        const modal = document.getElementById('checkMemberModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');
        const body = document.body;

        function openModal() {
            modal.classList.remove('hidden');
            body.style.overflow = 'hidden';
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                panel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            }, 10);
            const input = document.getElementById('member_id');
            if (!input.value) setTimeout(() => input.focus(), 100);
        }

        function closeModal() {
            const url = new URL(window.location);
            url.searchParams.delete('member_id');
            window.history.pushState({}, '', url);

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            panel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            panel.classList.add('opacity-0', 'scale-95', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
                body.style.overflow = 'auto';
                document.getElementById('result-area').classList.add('hidden');
                document.getElementById('member_id').value = '';
            }, 300);
        }

        function handleCheck(e) {
            if (e) e.preventDefault();
            const inputVal = document.getElementById('member_id').value.toUpperCase();
            const resultArea = document.getElementById('result-area');
            const initialState = document.getElementById('initial-state');
            const submitBtn = document.querySelector('#checkForm button');

            if (!inputVal) return;

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin w-4 h-4"></i>';
            submitBtn.disabled = true;
            lucide.createIcons();

            setTimeout(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                initialState.classList.add('hidden');
                resultArea.classList.remove('hidden');

                const url = new URL(window.location);
                url.searchParams.set('member_id', inputVal);
                window.history.pushState({}, '', url);

                const isFound = inputVal.length > 3;

                if (isFound) {
                    const historyRows = `
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition group">
                            <td class="py-3 px-3 text-sm text-slate-600 font-medium">12 Des 2025</td>
                            <td class="py-3 px-3 text-sm font-mono text-slate-500">16:30</td>
                            <td class="py-3 px-3 text-sm text-slate-700">Gym Floor Access</td>
                            <td class="py-3 px-3 text-right">
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded border border-blue-200 uppercase tracking-wide">Check-in</span>
                            </td>
                        </tr>
                        `;

                    resultArea.innerHTML = `
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden mb-8 transform transition hover:scale-[1.01] duration-300">
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="bg-white/10 backdrop-blur-md px-3 py-1 rounded-lg border border-white/20">
                                        <p class="text-[10px] text-blue-100 uppercase tracking-widest font-bold">MEMBER CARD</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center justify-end gap-2 text-brand-yellow font-bold">
                                            <i data-lucide="check-circle" class="w-4 h-4 fill-current"></i><span>ACTIVE</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                                    <div>
                                        <p class="text-blue-100 text-xs mb-1">Nama Member</p>
                                        <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white drop-shadow-sm">BUDI SANTOSO</h2>
                                        <p class="font-mono text-blue-200 mt-1 tracking-wider text-sm opacity-80">${inputVal}</p>
                                    </div>
                                    <div class="bg-white/10 rounded-xl p-3 border border-white/10 backdrop-blur-sm min-w-[140px]">
                                        <p class="text-[10px] text-blue-200 uppercase mb-1">Masa Berlaku</p>
                                        <p class="font-bold text-white text-lg">12 Feb 2026</p>
                                        <p class="text-[10px] text-brand-yellow mt-1 font-medium">Single 3 Bulan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-6 px-1">
                            <h4 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-600 p-1.5 rounded-md"><i data-lucide="history" class="w-4 h-4"></i></span>
                                Riwayat Kunjungan
                            </h4>
                            <button onclick="copyLink()" id="btnCopy" class="text-xs font-medium flex items-center gap-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1.5 rounded-full transition border border-transparent hover:border-blue-100">
                                <i data-lucide="link" class="w-3 h-3"></i> Salin Link
                            </button>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-3">Tanggal</th><th class="py-3 px-3">Jam</th><th class="py-3 px-3">Aktivitas</th><th class="py-3 px-3 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">${historyRows}</tbody>
                                </table>
                            </div>
                        </div>
                    `;
                } else {
                    resultArea.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="bg-red-50 p-4 rounded-full mb-4">
                                <i data-lucide="user-x" class="w-8 h-8 text-red-500"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Member Tidak Ditemukan</h3>
                            <p class="text-slate-500 text-sm max-w-xs mx-auto mt-1">ID <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1 rounded">${inputVal}</span> tidak terdaftar.</p>
                        </div>
                    `;
                }
                lucide.createIcons();
            }, 600);
        }

        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.getElementById('btnCopy');
                const original = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="check" class="w-3 h-3"></i> Link Tersimpan!';
                btn.classList.replace('bg-blue-50', 'bg-green-100');
                btn.classList.replace('text-brand-blue', 'text-green-700');
                lucide.createIcons();
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.replace('bg-green-100', 'bg-blue-50');
                    btn.classList.replace('text-green-700', 'text-brand-blue');
                    lucide.createIcons();
                }, 2000);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const memberIdParam = urlParams.get('member_id');
            if (memberIdParam) {
                document.getElementById('member_id').value = memberIdParam;
                openModal();
                handleCheck();
            }
        });
    </script>
    <script>
        function redirectToMember(e) {
            e.preventDefault();
            const id = document.getElementById('landing_member_id').value;
            if (id) {
                window.location.href = `member.html?id=${id}`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const promoModal = document.getElementById('promoModal');
            if (promoModal) {
                setTimeout(() => openPromo(), 1500);
            }
        });

        function openPromo() {
            const elModal = document.getElementById('promoModal');
            const elBackdrop = document.getElementById('promoBackdrop');
            const elPanel = document.getElementById('promoPanel');
            const elFloatingBtn = document.getElementById('floatingPromoBtn');
            const body = document.body;

            if (!elModal) return;

            elModal.classList.remove('hidden');
            body.style.overflow = 'hidden';

            if (elFloatingBtn) {
                elFloatingBtn.classList.add('opacity-0', 'translate-y-20');
                setTimeout(() => elFloatingBtn.classList.add('hidden'), 500);
            }

            setTimeout(() => {
                elBackdrop.classList.remove('opacity-0');
                elPanel.classList.remove('opacity-0', 'scale-90');
                elPanel.classList.add('scale-100');
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function closePromo() {
            const elModal = document.getElementById('promoModal');
            const elBackdrop = document.getElementById('promoBackdrop');
            const elPanel = document.getElementById('promoPanel');
            const elFloatingBtn = document.getElementById('floatingPromoBtn');
            const body = document.body;

            if (!elModal) return;

            elBackdrop.classList.add('opacity-0');
            elPanel.classList.remove('scale-100');
            elPanel.classList.add('opacity-0', 'scale-90');

            setTimeout(() => {
                elModal.classList.add('hidden');
                body.style.overflow = 'auto';
                if (elFloatingBtn) {
                    elFloatingBtn.classList.remove('hidden');
                    setTimeout(() => {
                        elFloatingBtn.classList.remove('opacity-0', 'translate-y-20');
                        elFloatingBtn.classList.add('opacity-100', 'translate-y-0');
                    }, 50);
                }
            }, 300);
        }
    </script>
</body>

</html>