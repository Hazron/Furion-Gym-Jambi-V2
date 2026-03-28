@extends('Admin.dashboardAdminTemplate')

@section('header-content')
<div class="flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Etalase Produk</h2>
        <p class="text-gray-500 text-sm mt-1">Penjualan suplemen, minuman, dan merchandise gym.</p>

    </div>
    <div class="text-right">
        <div class="text-sm text-gray-500">Kasir Aktif</div>
        <div class="font-bold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</div>
    </div>
</div>
@endsection

@section('content')

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-180px)]">

    <div class="flex-1 flex flex-col bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-5 border-b border-gray-100 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
            <div class="flex gap-2 overflow-x-auto w-full xl:w-auto pb-2 xl:pb-0 no-scrollbar">
                <button class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium shadow-lg shadow-blue-200 whitespace-nowrap transition-transform active:scale-95">Semua</button>
                <button class="px-4 py-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 text-sm font-medium whitespace-nowrap transition-colors">Suplemen</button>
                <button class="px-4 py-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 text-sm font-medium whitespace-nowrap transition-colors">Minuman</button>
                <button class="px-4 py-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 text-sm font-medium whitespace-nowrap transition-colors">Gear & Alat</button>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-all" placeholder="Cari produk...">
                </div>

                <button onclick="document.getElementById('modalTambahProduk').classList.remove('hidden')"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-sm font-medium transition-all active:scale-95 shadow-lg shadow-gray-200 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Produk
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-5 bg-gray-50/50">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">

                @foreach($produks as $produk)
                <div class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition-all group cursor-pointer {{ $produk->status_produk == 'inactive' ? 'opacity-60 grayscale' : '' }}">

                    <div class="relative h-32 bg-gray-100 rounded-xl mb-3 overflow-hidden">
                        @if($produk->gambar_produk)
                        <img src="{{ asset('produk/' . $produk->gambar_produk) }}" class="w-full h-full object-cover" alt="{{ $produk->nama_produk }}">
                        @else
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        @endif

                        @if($produk->status_produk == 'active')
                        <span class="absolute top-2 right-2 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Ready</span>
                        @else
                        <span class="absolute top-2 right-2 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Non-Aktif</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-800 text-sm line-clamp-1">{{ $produk->nama_produk }}</h3>
                    <p class="text-xs text-gray-500 mb-3">Stock: {{ $produk->stok_produk }}</p>

                    <div class="flex justify-between items-center">
                        <span class="text-blue-600 font-bold text-sm">Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}</span>

                        <div class="flex items-center gap-2">

                            <div class="relative group/status">
                                <button
                                    type="button"
                                    onclick="toggleStatus('{{ $produk->id_produk }}', '{{ $produk->status_produk }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full transition-colors {{ $produk->status_produk == 'active' ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300' }}">
                                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </button>
                                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover/status:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                                    {{ $produk->status_produk == 'active' ? 'Non-aktifkan' : 'Aktifkan' }}
                                </div>
                            </div>

                            <div class="relative group/edit">
                                <button
                                    type="button"

                                    @if($produk->status_produk == 'active')
                                    data-produk="{{ $produk }}"
                                    onclick="openEditModal(JSON.parse(this.dataset.produk))"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 transition-colors"
                                    @else
                                    disabled
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-300 cursor-not-allowed"
                                    @endif
                                    >
                                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H7v-3a2 2 0 01.586-1.414z" />
                                    </svg>
                                </button>

                                @if($produk->status_produk == 'active')
                                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover/edit:opacity-100 transition-opacity pointer-events-none z-10">
                                    Edit
                                </div>
                                @endif
                            </div>

                            <button
                                type="button"
                                @if($produk->status_produk == 'active')
                                onclick="tambahKeKeranjang(this)"
                                class="bg-gray-100 hover:bg-blue-600 hover:text-white w-10 h-10 rounded-full flex items-center justify-center transition-colors shadow-sm"
                                @else
                                disabled
                                class="bg-gray-100 text-gray-300 w-10 h-10 rounded-full flex items-center justify-center cursor-not-allowed"
                                @endif

                                data-id="{{ $produk->id_produk }}"
                                data-name="{{ $produk->nama_produk }}"
                                data-price="{{ $produk->harga_produk }}"
                                data-image="{{ $produk->gambar_produk ? asset('produk/' . $produk->gambar_produk) : '' }}">

                                <svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>

                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="w-full lg:w-96 flex flex-col bg-white rounded-3xl shadow-xl border border-gray-100 h-full">

        <div class="p-5 border-b border-gray-100 bg-white rounded-t-3xl z-10">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Data Pembeli</label>
            <div class="relative w-full" id="customSelectMember">
                <div class="relative">
                    <input type="text" id="selectedMemberDisplay"
                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 pr-10 font-medium cursor-pointer transition-colors caret-transparent"
                        placeholder="Pilih Member..." value="Guest / Tamu Umum" readonly onclick="toggleDropdown()">
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div id="memberDropdownList" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-2xl overflow-hidden">
                    <div class="p-2 border-b border-gray-100 bg-gray-50/50">
                        <input type="text" id="memberSearchInput" onkeyup="filterMembers()"
                            class="block w-full p-2 text-sm text-gray-900 border border-gray-200 rounded-lg bg-white outline-none"
                            placeholder="Ketik nama member...">
                    </div>
                    <ul id="memberList" class="max-h-60 overflow-y-auto py-1">

                        <li>
                            <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors border-b border-gray-50 group"
                                onclick="selectMember('Guest / Tamu Umum', null)">

                                <div class="text-sm font-bold text-gray-700">Guest / Tamu Umum</div>
                            </div>
                        </li>

                        @foreach($members as $member)
                        <li>
                            <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors border-b border-gray-50 group"
                                onclick="selectMember('{{ $member->nama_lengkap }}', '{{ $member->id_members }}')">

                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-sm font-bold text-gray-700">{{ $member->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-400">{{ $member->no_telepon }}</div>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full border font-bold bg-gray-100 text-gray-600">{{ ucfirst($member->status) }}</span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div id="cart-items-container" class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-3 opacity-60">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-sm">Keranjang kosong</p>
            </div>
        </div>

        <div class="p-5 border-t border-gray-100 bg-gray-50/50 rounded-b-3xl">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span id="subtotal-display">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Diskon Member (0%)</span>
                    <span class="text-red-500">- Rp 0</span>
                </div>
                <div class="border-t border-dashed border-gray-200 my-2"></div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total Pembayaran</span>
                    <span id="total-payment" class="font-bold text-xl text-blue-600">Rp 0</span>
                </div>
            </div>

            <button onclick="processPayment()" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<div id="modalTambahProduk" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Tambah Produk Baru</h2>
                <p class="text-xs text-gray-400">Masukkan detail item untuk dijual.</p>
            </div>
            <button type="button" onclick="toggleModal('modalTambahProduk', false)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="formTambahProduk" action="{{ route('tambahProduk') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Contoh: Whey Protein Gold 5lbs..." required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="harga_produk" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stok Awal</label>
                        <input type="number" name="stok_produk" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Foto Produk</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-blue-50 transition-all relative overflow-hidden">
                            <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-xs text-gray-500">Klik untuk upload gambar</p>
                            </div>
                            <img id="image-preview" class="hidden absolute inset-0 w-full h-full object-cover opacity-80" />
                            <input id="file-upload" type="file" name="gambar_produk" class="hidden" accept="image/*" onchange="previewImage(this)" />
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                <button type="button" onclick="toggleModal('modalTambahProduk', false)" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-blue-700 transition-transform active:scale-95">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- start modalEditProduk -->
<div id="modalEditProduk" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transform transition-all">

        <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Edit Produk</h2>
                <p class="text-xs text-gray-400">Perbarui informasi produk.</p>
            </div>
            <button type="button" onclick="toggleModal('modalEditProduk', false)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="formEditProduk" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-5">

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Produk</label>
                    <input type="text" id="edit_nama_produk" name="nama_produk"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual (Rp)</label>
                        <input type="number" id="edit_harga_produk" name="harga_produk"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Manajemen Stok</label>

                        <div class="flex items-end gap-3">

                            <div class="flex-1">
                                <label class="text-[10px] text-gray-400 block mb-1">Stok saat ini:</label>
                                <input type="number"
                                    id="edit_stok_produk"
                                    name="stok_produk"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    required>
                            </div>

                            <div>
                                <label class="text-[10px] text-gray-400 block mb-1">Barang Masuk (+):</label>
                                <div class="flex items-center bg-white border border-gray-200 rounded-xl overflow-hidden">
                                    <input type="number"
                                        id="calc_tambah_stok"
                                        name="stok_masuk"
                                        placeholder="0"
                                        class="w-20 px-3 py-2.5 text-sm outline-none border-none focus:ring-0">
                                    <!-- <button type="button"
                                        onclick="tambahStokOtomatis()"
                                        class="px-3 py-2.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 border-l border-gray-200 transition-colors"
                                        title="Tambahkan ke Total">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button> -->
                                </div>
                            </div>

                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Update Foto (Opsional)</label>
                        <div class="flex gap-4 items-center">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 overflow-hidden border border-gray-200 flex-shrink-0">
                                <img id="edit_preview_img" src="" class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1">
                                <label class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                    <input type="file" name="gambar_produk" class="block w-full text-sm text-gray-500" accept="image/*">
                                </label>
                                <p class="text-[10px] text-gray-400 mt-1">*Biarkan kosong jika tidak ingin mengganti gambar.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" onclick="toggleModal('modalEditProduk', false)" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-yellow-500 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-yellow-600 transition-all transform active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Update Produk
                    </button>
                </div>
        </form>
    </div>
</div>
<!-- end modalEditProduk -->



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.routes = {
            tambahProduk: "{{ route('tambahProduk') }}",
            simpanTransaksi: "{{ route('transaksi.simpan') }}",
            baseProduk: "{{ url('/produk') }}", 
            assetProduk: "{{ asset('produk') }}/" 
        };

        window.currentUser = "{{ Auth::user()->name ?? 'Admin' }}";
    </script>

    @vite('resources/js/Admin/orderBarang.js')

@endsection