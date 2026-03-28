// --- HELPER FUNCTIONS ---
const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
const formatDateIndo = (dateStr) => {
    if (!dateStr) return '-';
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr));
};

let currentDetailPayments = [];
let currentModalPage = 1;
const itemsPerModalPage = 5;
let table;

// --- DATA TABLES INITIALIZATION ---
$(document).ready(function () {
    // Pastikan ID tabel ada di DOM
    if ($('#membersTable').length) {
        table = $('#membersTable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: window.routeMemberFurion, 
                data: function (d) {
                    d.status_filter = $('#filterStatus').val();
                }
            },
            order: [[7, "desc"]], // Mengurutkan by updated_at (hidden)
            dom: '<"top">rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip><"clear">',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-4 py-3 font-medium text-xs sm:text-sm text-center align-middle' },
                { data: 'nama_lengkap', name: 'nama_lengkap', className: 'px-4 py-3 font-bold text-gray-800 text-xs sm:text-sm align-middle' },
                {
                    data: 'status', name: 'status', className: 'px-4 py-3 text-center align-middle',
                    render: function (data) {
                        if (data === 'active') {
                            return '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-green-100 text-green-700 border border-green-200">AKTIF</span>';
                        } else {
                            return '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-red-100 text-red-700 border border-red-200">NONAKTIF</span>';
                        }
                    }
                },
                // Tambahkan class hidden yang sama persis dengan yang ada di Blade HTML
                { data: 'paket_members', name: 'paket_members', className: 'px-4 py-3 text-gray-600 text-xs sm:text-sm hidden md:table-cell align-middle' },
                { data: 'tanggal_daftar', name: 'tanggal_daftar', className: 'px-4 py-3 text-gray-600 text-xs sm:text-sm hidden lg:table-cell align-middle' },
                { data: 'sisa_waktu', name: 'sisa_waktu', orderable: false, searchable: false, className: 'px-4 py-3 text-xs sm:text-sm hidden sm:table-cell align-middle' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'px-4 py-3 text-right align-middle' },
                { data: 'updated_at', name: 'updated_at', visible: false, searchable: false }
            ],
            language: { 
                paginate: { previous: "Prev", next: "Next" }, 
                info: "Hal _PAGE_ dari _PAGES_",
                emptyTable: "Belum ada data member."
            },
            initComplete: function () {
                $('#membersTable').removeClass('dataTable no-footer');
            }
        });

        // Trigger pencarian custom
        $('#customSearch').on('keyup', function () { table.search(this.value).draw(); });
        
        // Trigger filter status
        $('#filterStatus').on('change', function () { table.column(2).search(this.value).draw(); });
    }
});

// --- MODAL & PAGINATION LOGIC ---
function toggleModal(modalId, show) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const box = modal.querySelector('div[id^="modalBox"]');
    if (show) {
        modal.classList.remove("hidden");
        setTimeout(() => {
            box.classList.remove("opacity-0", "scale-95", "translate-y-4");
            box.classList.add("opacity-100", "scale-100", "translate-y-0");
        }, 10);
    } else {
        box.classList.remove("opacity-100", "scale-100", "translate-y-0");
        box.classList.add("opacity-0", "scale-95", "translate-y-4");
        setTimeout(() => { modal.classList.add("hidden"); }, 300);
    }
}

// Fungsi ini diekspos ke window agar bisa dipanggil dari HTML (onclick)
window.openDetailModal = function (data) {
    document.getElementById('detail-initials').innerText = data.nama_lengkap.substring(0, 2).toUpperCase();
    document.getElementById('detail-nama').innerText = data.nama_lengkap;

    let paketAktif = '-';
    if (data.paket) paketAktif = data.paket.nama_paket;
    else if (data.promo) paketAktif = data.promo.nama_paket + ' (PROMO)';
    document.getElementById('detail-paket').innerText = paketAktif;

    document.getElementById('detail-telp').innerText = data.no_telepon || '-';
    document.getElementById('detail-email').innerText = data.email || '-';

    const joinDate = data.tanggal_daftar ? new Date(data.tanggal_daftar).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
    const expDate = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

    document.getElementById('detail-join').innerText = `Join: ${joinDate}`;
    document.getElementById('detail-masa-aktif').innerText = `Exp: ${expDate}`;

    const statusBadge = document.getElementById('detail-status');
    if (data.status === 'active') {
        statusBadge.className = "inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-600/20";
        statusBadge.innerText = "AKTIF";
    } else {
        statusBadge.className = "inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20";
        statusBadge.innerText = "NONAKTIF";
    }

    currentDetailPayments = data.membership_payments || [];
    document.getElementById('total-transaksi-badge').innerText = `${currentDetailPayments.length} Transaksi`;

    currentDetailPayments.sort((a, b) => {
        const dateA = new Date(a.tanggal_transaksi);
        const dateB = new Date(b.tanggal_transaksi);
        if (dateB - dateA !== 0) return dateB - dateA;
        return b.id - a.id;
    });

    currentModalPage = 1;
    renderModalTable();
    renderModalPagination();

    toggleModal("detailMemberModal", true);
};

function renderModalTable() {
    const tableBody = document.getElementById('transaction-table-body');
    const emptyState = document.getElementById('empty-transaction');
    tableBody.innerHTML = '';

    if (currentDetailPayments.length === 0) {
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');

    const startIndex = (currentModalPage - 1) * itemsPerModalPage;
    const endIndex = startIndex + itemsPerModalPage;
    const paginatedData = currentDetailPayments.slice(startIndex, endIndex);

    paginatedData.forEach(pay => {
        let badgeClass = 'bg-gray-100 text-gray-600';
        let rawJenis = pay.jenis_transaksi || '-';
        let jenisLower = rawJenis.toLowerCase();

        let jenisTxt = rawJenis;

        if (jenisLower.includes('membership')) {
            badgeClass = 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20';
            jenisTxt = 'Register';
        }
        else if (jenisLower.includes('renewal')) {
            badgeClass = 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20';
            jenisTxt = 'Perpanjang';
        }
        else if (jenisLower.includes('reactivation')) {
            badgeClass = 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20';
            jenisTxt = 'Reaktivasi';
        }

        let namaPaketHistory = '-';
        let jenisPaketDb = ''; 

        if (pay.nama_paket_snapshot) {
            namaPaketHistory = pay.nama_paket_snapshot;
        } else if (pay.paket) {
            namaPaketHistory = pay.paket.nama_paket;
            jenisPaketDb = pay.paket.jenis || ''; 
        } else if (pay.promo) {
            namaPaketHistory = pay.promo.nama_paket + ' (Promo)';
            jenisPaketDb = pay.promo.jenis || '';
        } else {
            namaPaketHistory = pay.paket_id ? `Paket ID: ${pay.paket_id}` : '-';
        }

        let keteranganTxt = pay.keterangan || '<span class="text-gray-300 italic">Tidak ada keterangan</span>';

        let displayJenis = jenisPaketDb
            ? `<span class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">${jenisPaketDb}</span>`
            : '';

        const row = `
            <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 align-top">
                    ${formatDateIndo(pay.tanggal_transaksi)}
                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">${pay.nomor_invoice || ''}</div>
                </td>
                <td class="px-3 py-4 text-sm align-top">
                    <div class="flex flex-col items-start gap-1.5">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider ${badgeClass}">${jenisTxt}</span>
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-semibold whitespace-nowrap leading-tight">${namaPaketHistory}</span>
                            ${displayJenis}
                        </div>
                    </div>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-mono font-bold text-right align-top">${formatRupiah(pay.nominal)}</td>
                <td class="px-3 py-4 text-sm text-gray-500 leading-relaxed max-w-[200px] break-words whitespace-normal align-top">${keteranganTxt}</td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });
}

function renderModalPagination() {
    const paginationContainer = document.getElementById('detail-pagination-container');
    if (!paginationContainer) return;

    const totalPages = Math.ceil(currentDetailPayments.length / itemsPerModalPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    const isFirstPage = currentModalPage === 1;
    const isLastPage = currentModalPage === totalPages;

    paginationContainer.innerHTML = `
        <div class="flex items-center justify-between border-t border-gray-200 pt-3 mt-2">
            <span class="text-xs text-gray-500 font-medium">Halaman <span class="font-bold text-gray-800">${currentModalPage}</span> dari ${totalPages}</span>
            <div class="flex gap-2">
                <button onclick="window.changeModalPage(${currentModalPage - 1})" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors border ${isFirstPage ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 active:scale-95'}" ${isFirstPage ? 'disabled' : ''}>Prev</button>
                <button onclick="window.changeModalPage(${currentModalPage + 1})" 
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors border ${isLastPage ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 active:scale-95'}" ${isLastPage ? 'disabled' : ''}>Next</button>
            </div>
        </div>`;
}

window.changeModalPage = function (newPage) {
    const totalPages = Math.ceil(currentDetailPayments.length / itemsPerModalPage);
    if (newPage >= 1 && newPage <= totalPages) {
        currentModalPage = newPage;
        renderModalTable();
        renderModalPagination();
    }
};

window.closeDetailModal = function () { toggleModal("detailMemberModal", false); };