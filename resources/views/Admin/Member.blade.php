@extends('Admin.dashboardAdminTemplate')

@section('header-content')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Data Member</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola data member gym anda di sini.</p>
        </div>
    </div>

    {{-- CSS DataTables & Select2 Custom --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Custom CSS Select2 agar menyatu dengan gaya Tailwind */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #d1d5db !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-dropdown {
            border-radius: 0.75rem !important;
            border: 1px solid #d1d5db !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            font-size: 0.875rem !important;
        }
    </style>
@endsection

@section('content')

    {{-- KOTAK PENCARIAN & TABEL UTAMA --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="customSearch"
                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-colors"
                    placeholder="Cari nama member...">
            </div>

            <div class="flex flex-wrap gap-3">
                <select id="filterStatus"
                    class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>

                <button onclick="openModal()"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-2xl shadow-lg shadow-blue-500/30 transition-all active:scale-95 font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Member
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl">
            <table id="memberTable" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 rounded-tl-xl">No</th>
                        <th scope="col" class="px-6 py-4">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Paket</th>
                        <th scope="col" class="px-6 py-4">Mulai / Update</th>
                        <th scope="col" class="px-6 py-4">Selesai</th>
                        <th scope="col" class="px-6 py-4 rounded-tr-xl text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white"></tbody>
            </table>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 1: TAMBAH MEMBER (REGISTER) --}}
    {{-- ================================================================= --}}
    <div id="modalTambahMember"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div id="modalBox"
            class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl transform opacity-0 scale-95 translate-y-4 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Tambah Member Baru</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('registerMember') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- 1. Info Pribadi Member Utama --}}
                    <div class="mb-8">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            Informasi Pribadi (Member Utama)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_member" value="{{ old('nama_member') }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                                    placeholder="Masukkan nama" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <input type="number" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                                    placeholder="08..." required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                                    placeholder="email@contoh.com" required>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <div class="flex gap-4 mt-1">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Laki-laki" class="peer sr-only"
                                            required>
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 hover:bg-gray-50 transition-all">
                                            Laki-laki</div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Perempuan" class="peer sr-only"
                                            required>
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-600 hover:bg-gray-50 transition-all">
                                            Perempuan</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Form Pasangan --}}
                    <div id="coupleMemberForm" class="hidden mb-8 bg-pink-50/50 p-6 rounded-2xl border border-pink-100">
                        <h3 class="text-xs font-bold text-pink-500 uppercase tracking-wider mb-4">Informasi Pasangan
                            (Couple)</h3>
                        <div class="mb-5 bg-white p-4 rounded-xl border border-pink-200 shadow-sm">
                            <label class="block text-xs font-bold text-pink-700 mb-2">Pilih Member Terdaftar
                                (Opsional)</label>
                            <select name="partner_id" id="inputPartnerTambah" class="select2-couple w-full text-sm">
                                <option value="" selected>-- Ketik Nama atau ID Pasangan --</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id_members }}">{{ $m->nama_lengkap }} ({{ $m->id_members }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1.5">*Pilih ini jika pasangan sudah terdaftar. Jika
                                belum, isi form di bawah.</p>
                        </div>
                        <div class="flex items-center justify-center mb-5">
                            <div class="h-px bg-pink-200 flex-1"></div>
                            <span class="px-3 text-[10px] font-bold text-pink-400 tracking-wider">ATAU BUAT AKUN BARU</span>
                            <div class="h-px bg-pink-200 flex-1"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasangan Baru</label>
                                <input type="text" name="nama_member_2" id="inputNama2"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-pink-100 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <input type="number" name="nomor_telepon_2" id="inputTelp2"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-pink-100 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email_2" id="inputEmail2"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-pink-100 focus:border-pink-500">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <div class="flex gap-4 mt-1">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin_2" value="Laki-laki"
                                            class="peer sr-only group-radio-2">
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-600 hover:bg-gray-50 transition-all">
                                            Laki-laki</div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin_2" value="Perempuan"
                                            class="peer sr-only group-radio-2">
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-600 hover:bg-gray-50 transition-all">
                                            Perempuan</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Detail Paket & Pembayaran --}}
                    <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100 mb-4">
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-4">Detail Membership</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-blue-900 mb-1">Mulai Membership</label>
                                <input type="date" name="tanggal_mulai" id="inputTanggalMulai"
                                    value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                    class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-gray-700"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-blue-900 mb-1">Pilih Paket</label>
                                <select name="id_paket" id="selectPaketTambah"
                                    onchange="handlePaketChange(this); updateKalkulasiTambah()" required
                                    class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-gray-700">
                                    <option value="" selected disabled>-- Pilih Paket --</option>
                                    <optgroup label="Paket Couple">
                                        @foreach ($paketCouple as $pc)
                                            <option value="{{ $pc->id_paket }}" data-tipe="couple" data-jenis="couple"
                                                data-harga="{{ $pc->harga ?? 0 }}" data-durasi="{{ $pc->durasi }}">
                                                {{ $pc->nama_paket }} ({{ $pc->durasi }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Paket Reguler">
                                        @foreach ($paketReguler as $pr)
                                            <option value="{{ $pr->id_paket }}" data-tipe="reguler" data-jenis="reguler"
                                                data-harga="{{ $pr->harga ?? 0 }}" data-durasi="{{ $pr->durasi }}">
                                                {{ $pr->nama_paket }} ({{ $pr->durasi }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Paket Promo">
                                        @foreach ($paketPromo as $pp)
                                            <option value="{{ $pp->id_paket }}" data-tipe="promo" data-jenis="promo"
                                                data-harga="{{ $pp->harga ?? 0 }}" data-durasi="{{ $pp->durasi }}">
                                                {{ $pp->nama_promo ?? $pp->nama_paket }} ({{ $pp->durasi }}) - PROMO</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Paket Promo Couple">
                                        @foreach ($paketPromoCouple as $ppc)
                                            <option value="{{ $ppc->id_paket }}" data-tipe="promo couple"
                                                data-harga="{{ $ppc->harga }}" data-durasi="{{ $ppc->durasi }}">
                                                {{ $ppc->nama_paket }} ({{ $ppc->durasi }}) - PROMO COUPLE</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <input type="hidden" name="tipe_paket" id="inputTipePaketTambah">
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4 border border-blue-100 shadow-sm mt-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Ringkasan Pembayaran
                            </h3>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500">Durasi Paket</span>
                                <span id="display_durasi_tambah_member" class="text-sm font-medium text-gray-800">-</span>
                            </div>
                            <div class="flex justify-between items-center mb-3 border-b border-dashed pb-3">
                                <span class="text-sm text-gray-500">Estimasi Selesai</span>
                                <span id="display_tanggal_selesai_tambah" class="text-sm font-bold text-blue-600">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-700">Total Tagihan</span>
                                <span id="display_total_tambah" class="text-2xl font-extrabold text-blue-700">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Upload Bukti Transfer
                            (Opsional)</label>
                        <input type="file" name="bukti_transfer"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded-xl">
                    </div>

                    {{-- FITUR BYPASS INVOICE --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-2xl border border-gray-200 mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <input type="checkbox" id="skip_invoice_tambah" name="skip_invoice" value="1"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer"
                                onchange="toggleOwnerPassword(this, 'owner_password_field_tambah')">
                            <label for="skip_invoice_tambah" class="text-sm font-bold text-gray-700 cursor-pointer">
                                Bypass Invoice & Laporan (Owner Only)
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-500 ml-6 mb-3">Centang jika pendaftaran ini digratiskan / tidak
                            masuk laporan keuangan.</p>

                        <div id="owner_password_field_tambah" class="hidden ml-6 animate-fade-in-down">
                            <label class="block text-xs font-bold text-red-600 mb-1">PASSWORD OWNER</label>
                            <input type="password" name="owner_password"
                                class="w-full bg-white border border-red-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-100 focus:border-red-500 transition-all"
                                placeholder="Masukkan password konfirmasi owner">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all transform active:scale-95 text-sm">Simpan
                            Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 2: EDIT MEMBER --}}
    {{-- ================================================================= --}}
    <div id="modalEditMember"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div id="modalBoxEdit"
            class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl transform opacity-0 scale-95 translate-y-4 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Edit Data Member</h2>
                <button onclick="closeEditModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="formEditMember" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" id="edit_id_member" name="id_member">
                    <div class="mb-8">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" id="edit_nama" name="nama_member"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-100 focus:border-yellow-500 transition-all"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <input type="number" id="edit_telepon" name="nomor_telepon"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-100 focus:border-yellow-500 transition-all"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="edit_email" name="email"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-500 rounded-xl px-4 py-2.5 text-sm cursor-not-allowed"
                                    readonly>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <div class="flex gap-4 mt-1">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Laki-laki" id="edit_gender_male"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 hover:bg-gray-50 transition-all">
                                            Laki-laki</div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="Perempuan" id="edit_gender_female"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2.5 rounded-xl border border-gray-200 text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-600 hover:bg-gray-50 transition-all">
                                            Perempuan</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-yellow-500 text-white font-medium rounded-xl shadow-lg shadow-yellow-500/30 hover:bg-yellow-600 transition-all transform active:scale-95 text-sm">Update
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 3: PERPANJANG MEMBER (RENEWAL) --}}
    {{-- ================================================================= --}}
    <div id="modalPerpanjangMember"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div id="modalBoxPerpanjang"
            class="bg-white w-full max-w-lg rounded-3xl shadow-2xl transform opacity-0 scale-95 translate-y-4 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Perpanjang Masa Aktif</h2>
                <button onclick="closePerpanjangModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="formPerpanjangMember" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="perpanjang_id_member" name="id_members">
                    <input type="hidden" name="tipe_paket" id="inputTipePaketPerpanjang">

                    <div class="mb-6 border border-gray-200 p-4 rounded-xl">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Informasi Member Aktif
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b border-gray-100 pb-1">
                                <span class="text-gray-600">Nama Member</span>
                                <span id="display_perpanjang_nama" class="font-semibold text-gray-800">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Selesai Saat Ini</span>
                                <span id="display_tanggal_selesai_saat_ini" class="font-semibold text-red-600">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50/50 rounded-2xl p-5 border border-green-100 mb-6">
                        <h3 class="text-xs font-bold text-green-800 uppercase tracking-wider mb-4">Pilih Durasi Tambahan
                        </h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-green-900 mb-1">Paket Perpanjangan</label>
                            <select name="id_paket" id="selectPaketPerpanjang"
                                class="w-full bg-white border border-green-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500 text-gray-700"
                                required>
                                <option value="" disabled selected>Pilih durasi paket...</option>
                                <optgroup label="Paket Couple (2 Orang)">
                                    @foreach ($paketCouple as $pc)
                                        <option value="{{ $pc->id_paket }}" data-tipe="couple"
                                            data-harga="{{ $pc->harga ?? 0 }}" data-durasi="{{ $pc->durasi }}">
                                            {{ $pc->nama_paket }} ({{ $pc->durasi }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Paket Reguler">
                                    @foreach ($paketReguler as $p)
                                        <option value="{{ $p->id_paket }}" data-tipe="reguler" data-harga="{{ $p->harga ?? 0 }}"
                                            data-durasi="{{ $p->durasi }}">{{ $p->nama_paket }} ({{ $p->durasi }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Paket Promo">
                                    @foreach ($paketPromo as $pp)
                                        <option value="{{ $pp->id_paket }}" data-tipe="promo" data-harga="{{ $pp->harga ?? 0 }}"
                                            data-durasi="{{ $pp->durasi }}">{{ $pp->nama_promo ?? $pp->nama_paket }}
                                            ({{ $pp->durasi }}) - PROMO</option>
                                    @endforeach
                                <optgroup label="Paket Promo Couple">
                                    @foreach ($paketPromoCouple as $ppc)
                                        <option value="{{ $ppc->id_paket }}" data-tipe="promo couple"
                                            data-harga="{{ $ppc->harga }}" data-durasi="{{ $ppc->durasi }}">
                                            {{ $ppc->nama_paket }} ({{ $ppc->durasi }}) - PROMO COUPLE</option>
                                    @endforeach
                                </optgroup>
                                </optgroup>
                            </select>
                        </div>

                        <div id="renewCoupleSection" class="hidden animate-fade-in-down mb-4">
                            <div class="bg-white p-3 rounded-xl border border-green-200 shadow-sm">
                                <label class="block text-xs font-bold text-green-700 mb-2">Pilih Pasangan (Wajib)</label>
                                <select name="partner_id" id="inputPartnerRenew" class="select2-couple w-full text-sm">
                                    <option value="" selected disabled>-- Ketik Nama/ID Pasangan --</option>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id_members }}">{{ $m->nama_lengkap }} ({{ $m->id_members }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-500 mt-1">*Masa aktif pasangan akan ikut diperpanjang.</p>
                            </div>
                        </div>

                        <input type="date" name="tanggal_mulai" id="inputTanggalMulaiPerpanjang" class="hidden">

                        <div class="bg-white rounded-xl p-4 border border-green-200 shadow-sm mt-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Ringkasan Pembayaran
                            </h3>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500">Durasi Tambahan</span>
                                <span id="display_durasi_tambah" class="text-sm font-medium text-gray-800">-</span>
                            </div>
                            <div class="flex justify-between items-center mb-3 border-b border-dashed pb-3">
                                <span class="text-sm text-gray-500">Tanggal Selesai Baru</span>
                                <span id="display_tanggal_selesai_baru" class="text-sm font-bold text-green-600">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-700">Total Tagihan</span>
                                <span id="display_total_perpanjang" class="text-2xl font-extrabold text-green-700">Rp
                                    0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Upload Bukti Transfer
                            (Opsional)</label>
                        <input type="file" name="bukti_transfer"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-200 rounded-xl">
                    </div>

                    {{-- FITUR BYPASS INVOICE PERPANJANGAN --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200 mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <input type="checkbox" id="skip_invoice_perpanjang" name="skip_invoice" value="1"
                                class="w-4 h-4 text-green-600 rounded focus:ring-green-500 cursor-pointer"
                                onchange="toggleOwnerPassword(this, 'owner_password_field_perpanjang')">
                            <label for="skip_invoice_perpanjang" class="text-sm font-bold text-gray-700 cursor-pointer">
                                Bypass Invoice & Laporan (Owner Only)
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-500 ml-6 mb-3">Centang jika perpanjangan ini tidak masuk laporan
                            keuangan.</p>

                        <div id="owner_password_field_perpanjang" class="hidden ml-6 animate-fade-in-down">
                            <label class="block text-xs font-bold text-red-600 mb-1">PASSWORD OWNER</label>
                            <input type="password" name="owner_password"
                                class="w-full bg-white border border-red-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-100 focus:border-red-500 transition-all"
                                placeholder="Masukkan password konfirmasi owner">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closePerpanjangModal()"
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-xl shadow-lg shadow-green-500/30 hover:bg-green-700 transition-all transform active:scale-95 text-sm">Proses
                            Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 4: REAKTIVASI MEMBER (INACTIVE TO ACTIVE) --}}
    {{-- ================================================================= --}}
    <div id="modalReaktifasiMember"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div id="modalBoxReaktifasi"
            class="bg-white w-full max-w-lg rounded-3xl shadow-2xl transform opacity-0 scale-95 translate-y-4 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Re-aktifasi Member</h2>
                <button onclick="closeReaktifasiModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="formReaktifasiMember" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="reaktifasi_id_member" name="id_member">

                    <div class="mb-6 border border-gray-200 p-4 rounded-xl bg-red-50/50">
                        <h3 class="text-xs font-bold text-red-700 uppercase tracking-wider mb-3">Member Tidak Aktif</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b border-gray-100 pb-1">
                                <span class="text-gray-600">Nama Member</span>
                                <span id="display_reaktifasi_nama" class="font-semibold text-gray-800">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status Terakhir</span>
                                <span class="font-bold text-red-600">INACTIVE</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50/50 rounded-2xl p-5 border border-yellow-100 mb-6">
                        <h3 class="text-xs font-bold text-yellow-800 uppercase tracking-wider mb-4">Pilih Paket Baru (Mulai
                            Hari Ini)</h3>
                        <label class="block text-sm font-medium text-yellow-900 mb-1">Paket Membership</label>
                        <select name="paket_id" id="selectPaketReaktifasi"
                            class="w-full bg-white border border-yellow-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-200 focus:border-yellow-500 text-gray-700"
                            required>
                            <option value="" disabled selected>Pilih paket re-aktifasi...</option>
                            <optgroup label="Paket Couple">
                                @foreach ($paketCouple as $pc)
                                    <option value="{{ $pc->id_paket }}" data-tipe="couple" data-harga="{{ $pc->harga ?? 0 }}"
                                        data-durasi="{{ $pc->durasi }}">{{ $pc->nama_paket }} ({{ $pc->durasi }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Paket Reguler">
                                @foreach ($paketReguler as $p)
                                    <option value="{{ $p->id_paket }}" data-tipe="reguler" data-harga="{{ $p->harga ?? 0 }}"
                                        data-durasi="{{ $p->durasi }}">{{ $p->nama_paket }} ({{ $p->durasi }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Paket Promo">
                                @foreach ($paketPromo as $pp)
                                    <option value="{{ $pp->id_paket }}" data-tipe="promo" data-harga="{{ $pp->harga ?? 0 }}"
                                        data-durasi="{{ $pp->durasi }}">{{ $pp->nama_promo ?? $pp->nama_paket }}
                                        ({{ $pp->durasi }}) - PROMO</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Paket Promo Couple">
                                @foreach ($paketPromoCouple as $ppc)
                                    <option value="{{ $ppc->id_paket }}" data-tipe="promo couple" data-harga="{{ $ppc->harga }}"
                                        data-durasi="{{ $ppc->durasi }}">{{ $ppc->nama_paket }} ({{ $ppc->durasi }}) - PROMO
                                        COUPLE</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <input type="hidden" name="tipe_paket" id="inputTipePaketReaktifasi">

                        <div id="reactivateCoupleSection" class="hidden animate-fade-in-down mb-4 mt-4">
                            <div class="bg-white p-3 rounded-xl border border-yellow-200 shadow-sm">
                                <label class="block text-xs font-bold text-yellow-700 mb-2">Pilih Pasangan (Wajib)</label>
                                <select name="partner_id" id="inputPartnerReactivate" class="select2-couple w-full text-sm">
                                    <option value="" selected disabled>-- Ketik Nama/ID Pasangan --</option>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id_members }}">{{ $m->nama_lengkap }} ({{ $m->id_members }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-500 mt-1">*Masa aktif pasangan akan ikut diaktifkan.</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4 border border-yellow-200 shadow-sm mt-4">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Ringkasan Pembayaran
                            </h3>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500">Tanggal Mulai Baru</span>
                                <span id="display_tanggal_mulai_baru"
                                    class="text-sm font-medium text-gray-800">{{ date('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-3 border-b border-dashed pb-3">
                                <span class="text-sm text-gray-500">Tanggal Selesai Baru</span>
                                <span id="display_tanggal_selesai_reaktifasi"
                                    class="text-sm font-bold text-yellow-600">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-700">Total Tagihan</span>
                                <span id="display_total_reaktifasi" class="text-2xl font-extrabold text-yellow-700">Rp
                                    0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Upload Bukti Transfer
                            (Opsional)</label>
                        <input type="file" name="bukti_transfer"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 border border-gray-200 rounded-xl">
                    </div>

                    {{-- FITUR BYPASS INVOICE REAKTIVASI --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200 mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <input type="checkbox" id="skip_invoice_reaktifasi" name="skip_invoice" value="1"
                                class="w-4 h-4 text-yellow-600 rounded focus:ring-yellow-500 cursor-pointer"
                                onchange="toggleOwnerPassword(this, 'owner_password_field_reaktifasi')">
                            <label for="skip_invoice_reaktifasi" class="text-sm font-bold text-gray-700 cursor-pointer">
                                Bypass Invoice & Laporan (Owner Only)
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-500 ml-6 mb-3">Centang jika re-aktifasi ini tidak masuk laporan
                            keuangan.</p>

                        <div id="owner_password_field_reaktifasi" class="hidden ml-6 animate-fade-in-down">
                            <label class="block text-xs font-bold text-red-600 mb-1">PASSWORD OWNER</label>
                            <input type="password" name="owner_password"
                                class="w-full bg-white border border-red-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-100 focus:border-red-500 transition-all"
                                placeholder="Masukkan password konfirmasi owner">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeReaktifasiModal()"
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-yellow-600 text-white font-medium rounded-xl shadow-lg shadow-yellow-500/30 hover:bg-yellow-700 transition-all transform active:scale-95 text-sm">Proses
                            Re-aktifasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 5: DETAIL MEMBER (READ ONLY) --}}
    {{-- ================================================================= --}}
    <div id="detailMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div id="modalBoxDetail"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl opacity-0 scale-95 translate-y-4">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Detail Member</h3>
                    <button type="button" onclick="closeDetailModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors bg-white hover:bg-gray-100 p-1 rounded-full">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <div class="flex flex-col sm:flex-row items-start gap-6 mb-8">
                        <div class="flex-shrink-0 mx-auto sm:mx-0">
                            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 border border-blue-200 flex items-center justify-center text-blue-700 text-2xl font-black shadow-sm"
                                id="detail-initials"></div>
                        </div>
                        <div class="flex-grow w-full text-center sm:text-left">
                            <div class="flex flex-col sm:flex-row items-center gap-2 mb-2 justify-center sm:justify-start">
                                <h2 class="text-2xl font-bold text-gray-900 tracking-tight" id="detail-nama">-</h2>
                                <span id="detail-status"
                                    class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            </div>
                            <p class="text-sm font-medium text-gray-500 mb-5" id="detail-paket">-</p>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Kontak</p>
                                    <p class="text-sm font-semibold text-gray-900 truncate" id="detail-telp">-</p>
                                    <p class="text-xs text-gray-500 truncate" id="detail-email">-</p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Masa Aktif
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900" id="detail-join">Join: -</p>
                                    <p class="text-xs text-gray-500" id="detail-masa-aktif">Exp: -</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Riwayat Pembayaran
                            </h4>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-md"
                                id="total-transaksi-badge">0 Transaksi</span>
                        </div>
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500 sm:pl-6">
                                            Tanggal</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                                            Paket Transaksi</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-gray-500">
                                            Nominal</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                                            Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white" id="transaction-table-body"></tbody>
                            </table>
                        </div>
                        <div id="empty-transaction" class="hidden text-center py-8">
                            <p class="text-sm text-gray-500 italic">Belum ada riwayat transaksi.</p>
                        </div>
                        <div id="detail-pagination-container" class="px-4 py-3 bg-white"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                    <button type="button" onclick="closeDetailModal()"
                        class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div id="alertSuccess"
            class="fixed bottom-5 right-5 bg-white border-l-4 border-green-500 shadow-2xl rounded-lg p-4 z-[9999] flex items-center gap-3 animate-bounce">
            <div class="text-green-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg></div>
            <div>
                <h4 class="font-bold text-gray-800">Berhasil!</h4>
                <p class="text-sm text-gray-600">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('alertSuccess').remove()"
                class="text-gray-400 hover:text-gray-600 ml-2"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
        </div>
    @endif

    @if(session('error'))
        <div id="alertError"
            class="fixed bottom-5 right-5 bg-white border-l-4 border-red-500 shadow-2xl rounded-lg p-4 z-[9999] flex items-center gap-3 animate-bounce">
            <div class="text-red-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg></div>
            <div>
                <h4 class="font-bold text-red-600">Gagal!</h4>
                <p class="text-sm text-gray-600">{{ session('error') }}</p>
            </div>
            <button onclick="document.getElementById('alertError').remove()" class="text-gray-400 hover:text-gray-600 ml-2"><svg
                    class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
        </div>
    @endif

@endsection

{{-- SCRIPTS (DI-PUSH KE MASTER TEMPLATE) --}}
@push('scripts')
    {{-- Library Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        window.routeDataMember = "{{ route('member.data') }}";
        window.routeMemberUpdate = "{{ route('member.update', ':id') }}";
        window.routeMemberPerpanjang = "{{ route('member.perpanjang', 'ID_MEMBER_PLACEHOLDER') }}";
        window.routeMembershipReactivate = "{{ route('membership.reactivate', ':id') }}";

        // FUNGSI JAVASCRIPT UNTUK TOGGLE PASSWORD OWNER (SKIP INVOICE)
        function toggleOwnerPassword(checkbox, fieldId) {
            const field = document.getElementById(fieldId);
            const inputField = field.querySelector('input[type="password"]');

            if (checkbox.checked) {
                field.classList.remove('hidden');
                inputField.setAttribute('required', 'required'); // Wajib isi jika dicentang
            } else {
                field.classList.add('hidden');
                inputField.removeAttribute('required'); // Tidak wajib jika tidak dicentang
                inputField.value = ''; // Kosongkan nilainya
            }
        }
    </script>

    {{-- File Custom JS --}}
    @vite('resources/js/Admin/Member.js')
@endpush