let table;

// --- 1. VARIABEL & LOGIKA FILTER WAKTU ---
let filterMode = 'month'; // Default ke bulan
let refDate = new Date(); // Titik referensi navigasi waktu
const monthNames = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

function formatDate(date) {
    const d = new Date(date);
    let month = '' + (d.getMonth() + 1);
    let day = '' + d.getDate();
    const year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('-');
}

function updateFilterUI() {
    // Reset styling semua tombol filter ke warna default
    $('.filter-btn').removeClass('bg-white shadow-sm text-blue-600').addClass('text-gray-500 hover:text-gray-900');
    // Beri warna biru pada tombol yang sedang aktif
    $(`#btn-filter-${filterMode}`).removeClass('text-gray-500 hover:text-gray-900').addClass('bg-white shadow-sm text-blue-600');

    let start, end;
    const targetDate = new Date(refDate);

    // Sembunyikan kontrol prev/next jika mode Hari Ini atau Minggu Ini
    if (filterMode === 'today' || filterMode === 'week') {
        $('#dynamic-filter-controls').hide();
    } else {
        $('#dynamic-filter-controls').show();
    }

    // Hitung Tanggal Awal dan Akhir berdasarkan mode
    if (filterMode === 'today') {
        const today = new Date();
        start = formatDate(today);
        end = formatDate(today);
    } else if (filterMode === 'week') {
        const today = new Date();
        const day = today.getDay(); 
        // Anggap Senin sebagai awal minggu (jika Minggu = 0, ubah ke -6)
        const diffToMonday = today.getDate() - day + (day === 0 ? -6 : 1);
        start = formatDate(new Date(today.setDate(diffToMonday)));
        end = formatDate(new Date(today.setDate(diffToMonday + 6)));
    } else if (filterMode === 'month') {
        const y = targetDate.getFullYear();
        const m = targetDate.getMonth();
        $('#filter-label').text(monthNames[m] + ' ' + y);
        start = formatDate(new Date(y, m, 1));
        end = formatDate(new Date(y, m + 1, 0)); // Hari terakhir di bulan tersebut
    } else if (filterMode === 'year') {
        const y = targetDate.getFullYear();
        $('#filter-label').text(y);
        start = formatDate(new Date(y, 0, 1));
        end = formatDate(new Date(y, 11, 31));
    }

    // Simpan nilai ke input hidden
    $('#startDateFilter').val(start);
    $('#endDateFilter').val(end);

    // Reload tabel jika sudah diinisialisasi
    if (table) {
        table.ajax.reload();
    }
}

// Ekspos ke global window agar bisa dipanggil dari atribut onclick di Blade HTML
window.setFilterMode = function(mode) {
    filterMode = mode;
    refDate = new Date(); // Reset kalender ke hari ini saat pindah mode
    updateFilterUI();
}

window.navigateFilter = function(direction) {
    if (filterMode === 'month') {
        refDate.setMonth(refDate.getMonth() + direction);
    } else if (filterMode === 'year') {
        refDate.setFullYear(refDate.getFullYear() + direction);
    }
    updateFilterUI();
}

// --- 2. INISIALISASI DATA TABLES ---
$(document).ready(function () {
    // Buat input tersembunyi untuk menyimpan range waktu dan user
    $('body').append('<input type="hidden" id="currentFilterUser" value="all">');
    $('body').append('<input type="hidden" id="startDateFilter">');
    $('body').append('<input type="hidden" id="endDateFilter">');
    
    // Set data awal filter sebelum DataTables Load
    updateFilterUI();

    // Pastikan ID tabel ada di DOM sebelum inisialisasi
    if ($('#aktivitasAdminTable').length) {
        table = $('#aktivitasAdminTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: window.routeAktivitasAdmin, // Menggunakan global variable
                data: function (d) {
                    d.filter_user_id = $('#currentFilterUser').val();
                    d.start_date = $('#startDateFilter').val(); // Kirim Tanggal Awal
                    d.end_date = $('#endDateFilter').val(); // Kirim Tanggal Akhir
                }
            },
            columns: [
                { data: 'created_at', name: 'created_at', class: 'px-6 py-4 whitespace-nowrap text-sm text-gray-500' },
                { 
                    data: 'admin_name', name: 'admin_name', class: 'px-6 py-4 font-medium text-gray-900',
                    render: function (data) {
                        let initial = data ? data.charAt(0).toUpperCase() : '?';
                        return `<div class="flex items-center gap-2"><div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">${initial}</div><span>${data}</span></div>`;
                    }
                },
                { data: 'badge_action', name: 'action_type', class: 'px-6 py-4 whitespace-nowrap' },
                { data: 'description', name: 'description', class: 'px-6 py-4 text-sm text-gray-600 leading-snug' },
                { data: 'action', name: 'action', orderable: false, class: 'px-6 py-4 text-center' }
            ],
            order: [[0, 'desc']],
            language: {
                search: "Cari:",
                zeroRecords: "Data tidak ditemukan pada rentang waktu ini",
                info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
                infoEmpty: "Data Kosong",
                paginate: { first: "«", last: "»", next: "›", previous: "‹" }
            }
        });
    }

    // Bind event delegation untuk tombol detail aktivitas (karena dirender oleh DataTables)
    $(document).on('click', '.btn-detail', function () {
        var id = $(this).data('id');
        var type = $(this).data('type');
        
        toggleModal('detailModal', true);
        $('#modalLoading').show();
        $('#modalContent').html('').addClass('hidden');

        $.ajax({
            url: window.routeAktivitasDetail, // Menggunakan global variable
            type: 'GET',
            data: { id: id, type: type },
            success: function (response) {
                $('#modalLoading').hide();
                $('#modalContent').html(response.html).removeClass('hidden');
            },
            error: function () {
                $('#modalLoading').hide();
                $('#modalContent').html('<p class="text-red-500 font-bold text-center">Gagal memuat data detail.</p>').removeClass('hidden');
            }
        });
    });
});

// --- 3. FILTER USER ADMIN LOGIC ---
window.filterTable = function(userId) {
    $('#currentFilterUser').val(userId);
    $('.admin-card').removeClass('bg-blue-50 border-blue-500 border-2').addClass('bg-white border-gray-200 border');
    if (userId === 'all') {
        $('#card-all').removeClass('bg-white border-gray-200 border').addClass('bg-blue-50 border-blue-500 border-2');
        $('#table-title').text('Riwayat Aktivitas: Semua Admin');
    } else {
        $('#card-' + userId).removeClass('bg-white border-gray-200 border').addClass('bg-blue-50 border-blue-500 border-2');
        let name = $('#card-' + userId).find('h4').text();
        $('#table-title').text('Riwayat Aktivitas: ' + name);
    }
    table.ajax.reload();
}

// --- 4. MODAL LOGIC (ANIMATED) ---
function toggleModal(modalId, show) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const panel = modal.querySelector('div[class*="transform"]');
    if (show) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }, 10);
    } else {
        modal.classList.add('opacity-0');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

// --- 5. CRUD ADMIN MODAL ---
window.openCreateModal = function() {
    toggleModal('adminModal', true);
    $('#adminForm').attr('action', window.routeAdminStore);
    $('#methodField').html('');
    $('#adminModalTitle').text('Tambah Admin Baru');
    $('#inputName').val('');
    $('#inputEmail').val('');
    $('#inputPassword').val('').attr('required', true);
    $('#passHelp').text('Minimal 6 karakter.');
}

window.openEditModal = function(id, name, email, event) {
    if (event) event.stopPropagation(); // Mencegah ter-kliknya div filter card
    
    toggleModal('adminModal', true);
    let url = window.routeAdminUpdate.replace(':id', id);
    $('#adminForm').attr('action', url);
    $('#methodField').html('<input type="hidden" name="_method" value="PUT">'); // Pakai input hidden agar lebih aman
    $('#adminModalTitle').text('Edit Data Admin');
    $('#inputName').val(name);
    $('#inputEmail').val(email);
    $('#inputPassword').val('').attr('required', false);
    $('#passHelp').text('Kosongkan jika tidak ingin mengganti password.');
}

window.closeAdminModal = function() {
    toggleModal('adminModal', false);
}

// --- 6. DETAIL AKTIVITAS MODAL ---
window.closeDetailModal = function() {
    toggleModal('detailModal', false);
}