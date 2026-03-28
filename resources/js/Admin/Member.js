document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // --- HELPER FUNCTIONS ---
    // ==========================================
    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    };

    const formatDateIndo = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(date);
    };

    const formatTanggalIndo = (dateObj) => {
        return dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    function hitungTanggalSelesai(tanggalMulaiStr, durasiStr) {
        let date = new Date(tanggalMulaiStr);
        if (isNaN(date.getTime())) date = new Date();

        const match = String(durasiStr).match(/\d+/);
        const angka = match ? parseInt(match[0]) : 0;
        const textLower = String(durasiStr).toLowerCase();
        let satuan = 'Bulan';

        if (angka > 0) {
            if (textLower.includes('tahun')) {
                satuan = 'Tahun';
                date.setFullYear(date.getFullYear() + angka);
            } else if (textLower.includes('hari')) {
                satuan = 'Hari';
                date.setDate(date.getDate() + angka);
            } else {
                satuan = 'Bulan';
                date.setMonth(date.getMonth() + angka);
            }
        }

        return {
            tanggalBaru: date,
            textTanggal: formatTanggalIndo(date),
            textDurasi: angka + ' ' + satuan
        };
    }

    // --- FUNGSI AMAN INIT SELECT2 ---
    function initSafeSelect2(inputId, modalId) {
        setTimeout(() => {
            if ($(inputId).hasClass("select2-hidden-accessible")) {
                $(inputId).select2('destroy');
            }
            $(inputId).select2({
                dropdownParent: $(modalId),
                width: '100%',
                language: {
                    noResults: function() {
                        return "Member tidak ditemukan";
                    }
                }
            });
        }, 150); // Jeda diperpanjang sedikit agar animasi Tailwind selesai
    }


    // ==========================================
    // --- DATATABLES SETUP ---
    // ==========================================
    if (typeof $ !== 'undefined') {
        var table = $('#memberTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: window.routeDataMember,
                data: function (d) {
                    d.status_filter = $('#filterStatus').val();
                }
            },
            order: [[7, "desc"]], // Sort by updated_at terbaru
            dom: '<"top">rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip><"clear">',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-6 py-4 font-medium' },
                { data: 'nama_lengkap', name: 'nama_lengkap', className: 'px-6 py-4 font-bold text-gray-800' },
                {
                    data: 'status',
                    name: 'status',
                    className: 'px-6 py-4',
                    render: function (data) {
                        if (data == 'active') {
                            return '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>';
                        } else {
                            return '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Nonaktif</span>';
                        }
                    }
                },
                { data: 'paket_members', name: 'paket_members', className: 'px-6 py-4 text-gray-600' },
                { data: 'tanggal_daftar', name: 'tanggal_daftar', className: 'px-6 py-4 text-gray-600' },
                { data: 'sisa_waktu', name: 'sisa_waktu', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'px-6 py-4 text-right' },
                { data: 'updated_at', name: 'updated_at', visible: false, searchable: false }
            ],
            language: { paginate: { previous: "Prev", next: "Next" }, info: "Hal _PAGE_ dari _PAGES_" },
            initComplete: function () {
                $('#memberTable').removeClass('dataTable no-footer');
            }
        });

        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#filterStatus').on('change', function () {
            table.column(2).search(this.value).draw();
        });
    }

    // ==========================================
    // --- GLOBAL MODAL TOGGLE ---
    // ==========================================
    window.toggleModal = function(modalId, show) {
        const modal = document.getElementById(modalId);
        const box = modal.querySelector('div[id^="modalBox"]');

        if (show) {
            modal.classList.remove("hidden");
            setTimeout(() => {
                if (box) {
                    box.classList.remove("opacity-0", "scale-95", "translate-y-4");
                    box.classList.add("opacity-100", "scale-100", "translate-y-0");
                }
            }, 10);
        } else {
            if (box) {
                box.classList.remove("opacity-100", "scale-100", "translate-y-0");
                box.classList.add("opacity-0", "scale-95", "translate-y-4");
            }
            setTimeout(() => { modal.classList.add("hidden"); }, 300);
        }
    };


    // ==========================================
    // 1. TAMBAH MEMBER (REGISTER)
    // ==========================================
    window.openModal = function() { window.toggleModal("modalTambahMember", true); };
    
    window.closeModal = function() { 
        window.toggleModal("modalTambahMember", false);
        if ($('#inputPartnerTambah').hasClass("select2-hidden-accessible")) {
            $('#inputPartnerTambah').val('').trigger('change').select2('destroy');
        }
    };

    window.updateKalkulasiTambah = function() {
        const select = document.getElementById('selectPaketTambah');
        const inputMulai = document.getElementById('inputTanggalMulai');
        if (!select || !inputMulai) return;

        const selectedOption = select.options[select.selectedIndex];
        const durasiStr = selectedOption.getAttribute('data-durasi') || '0';
        const hargaRaw = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        const tipe = selectedOption.getAttribute('data-tipe');
        const tanggalMulaiVal = inputMulai.value;

        document.getElementById('inputTipePaketTambah').value = tipe;
        const hasil = hitungTanggalSelesai(tanggalMulaiVal, durasiStr);

        document.getElementById('display_durasi_tambah_member').innerText = hasil.textDurasi;
        document.getElementById('display_tanggal_selesai_tambah').innerText = hasil.textTanggal;
        document.getElementById('display_total_tambah').innerText = formatRupiah(hargaRaw);
    };

    window.handlePaketChange = function(select) {
        const option = select.options[select.selectedIndex];
        const tipe = option.getAttribute('data-tipe'); // Ini akan mengambil nilai: reguler, couple, promo, atau promo couple
        document.getElementById('inputTipePaketTambah').value = tipe;

        // LOGIKA CERDAS: Cek apakah tipe adalah 'couple' / 'promo couple', atau teksnya mengandung kata couple
        const isCouple = (tipe === 'couple' || tipe === 'promo couple' || option.text.toLowerCase().includes('couple'));

        const coupleForm = document.getElementById('coupleMemberForm');
        const partnerInput = $('#inputPartnerTambah');
        const inputs2 = [document.getElementById('inputNama2'), document.getElementById('inputTelp2'), document.getElementById('inputEmail2')];
        const radios2 = document.querySelectorAll('.group-radio-2');

        if (isCouple) {
            coupleForm.classList.remove('hidden');
            initSafeSelect2('#inputPartnerTambah', '#modalTambahMember');

            partnerInput.on('change', function() {
                if ($(this).val() !== '') {
                    inputs2.forEach(el => { el.removeAttribute('required'); });
                    radios2.forEach(el => { el.removeAttribute('required'); });
                } else {
                    inputs2.forEach(el => el.setAttribute('required', true));
                    radios2.forEach(el => el.setAttribute('required', true));
                }
            });
            partnerInput.trigger('change');
        } else {
            coupleForm.classList.add('hidden');
            if (partnerInput.hasClass("select2-hidden-accessible")) {
                partnerInput.val('').trigger('change').select2('destroy');
            } else {
                partnerInput.val('').trigger('change');
            }
            inputs2.forEach(el => { el.removeAttribute('required'); el.value = ''; });
            radios2.forEach(el => { el.removeAttribute('required'); el.checked = false; });
        }
    };


    // ==========================================
    // 2. EDIT MEMBER
    // ==========================================
    window.openEditModal = function(data) {
        document.getElementById('edit_nama').value = data.nama_lengkap;
        document.getElementById('edit_telepon').value = data.no_telepon;
        document.getElementById('edit_email').value = data.email ?? '';
        document.getElementById('edit_id_member').value = data.id_members;

        if (data.jenis_kelamin === 'Perempuan') document.getElementById('edit_gender_female').checked = true;
        else document.getElementById('edit_gender_male').checked = true;

        let url = window.routeMemberUpdate.replace(':id', data.id_members);
        document.getElementById('formEditMember').action = url;
        window.toggleModal("modalEditMember", true);
    };
    window.closeEditModal = function() { window.toggleModal("modalEditMember", false); };


    // ==========================================
    // 3. PERPANJANG MEMBER (RENEWAL)
    // ==========================================
    let rawTanggalSelesaiAwal = null;
    const selectPaketPerpanjang = document.getElementById('selectPaketPerpanjang');

    window.updatePerpanjangSummary = function() {
        if (selectPaketPerpanjang.selectedIndex < 0) return;
        const selectedOption = selectPaketPerpanjang.options[selectPaketPerpanjang.selectedIndex];
        const durasiStr = selectedOption.getAttribute('data-durasi') || '0';
        const hargaRaw = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        const tipe = selectedOption.getAttribute('data-tipe');

        document.getElementById('inputTipePaketPerpanjang').value = tipe;

        // LOGIKA CERDAS COUPLE
        const isCouple = (tipe === 'couple' || tipe === 'promo couple' || selectedOption.text.toLowerCase().includes('couple'));

        const coupleSection = document.getElementById('renewCoupleSection');
        const partnerInput = $('#inputPartnerRenew');
        const currentMemberId = document.getElementById('perpanjang_id_member').value;

        if (isCouple) {
            coupleSection.classList.remove('hidden');
            partnerInput.prop('required', true);
            partnerInput.find('option').prop('disabled', false);
            partnerInput.find('option[value="'+currentMemberId+'"]').prop('disabled', true);
            initSafeSelect2('#inputPartnerRenew', '#modalPerpanjangMember');
        } else {
            coupleSection.classList.add('hidden');
            partnerInput.prop('required', false);
            if (partnerInput.hasClass("select2-hidden-accessible")) {
                partnerInput.val('').trigger('change').select2('destroy');
            } else {
                partnerInput.val('').trigger('change');
            }
        }

        if (!rawTanggalSelesaiAwal) return;
        let startDateObj = new Date(rawTanggalSelesaiAwal);
        let todayObj = new Date();
        startDateObj.setHours(0, 0, 0, 0); todayObj.setHours(0, 0, 0, 0);

        let basisTanggalMulai = (startDateObj < todayObj) ? todayObj : startDateObj;
        const basisString = basisTanggalMulai.getFullYear() + "-" + String(basisTanggalMulai.getMonth() + 1).padStart(2, '0') + "-" + String(basisTanggalMulai.getDate()).padStart(2, '0');
        document.getElementById('inputTanggalMulaiPerpanjang').value = basisString;

        const hasil = hitungTanggalSelesai(basisString, durasiStr);
        document.getElementById('display_durasi_tambah').innerText = hasil.textDurasi;
        document.getElementById('display_tanggal_selesai_baru').innerText = hasil.textTanggal;
        document.getElementById('display_total_perpanjang').innerText = formatRupiah(hargaRaw);
    };

    window.openPerpanjangModal = function(data) {
        rawTanggalSelesaiAwal = data.tanggal_selesai ? data.tanggal_selesai.split(' ')[0] : new Date().toISOString().split('T')[0];
        document.getElementById('formPerpanjangMember').reset();
        document.getElementById('perpanjang_id_member').value = data.id_members;
        document.getElementById('display_perpanjang_nama').textContent = data.nama_lengkap;

        const tglSelesaiFormatted = data.tanggal_selesai ? formatTanggalIndo(new Date(data.tanggal_selesai)) : 'Sudah Expired / Baru';
        document.getElementById('display_tanggal_selesai_saat_ini').textContent = tglSelesaiFormatted;

        let realUrl = window.routeMemberPerpanjang.replace('ID_MEMBER_PLACEHOLDER', data.id_members);
        document.getElementById('formPerpanjangMember').action = realUrl;

        document.getElementById('display_durasi_tambah').innerText = '-';
        document.getElementById('display_tanggal_selesai_baru').innerText = '-';
        document.getElementById('display_total_perpanjang').innerText = 'Rp 0';
        document.getElementById('renewCoupleSection').classList.add('hidden');
        selectPaketPerpanjang.selectedIndex = 0;

        window.toggleModal("modalPerpanjangMember", true);
        selectPaketPerpanjang.addEventListener('change', window.updatePerpanjangSummary);
    };

    window.closePerpanjangModal = function() {
        window.toggleModal("modalPerpanjangMember", false);
        selectPaketPerpanjang.removeEventListener('change', window.updatePerpanjangSummary);
        if ($('#inputPartnerRenew').hasClass("select2-hidden-accessible")) {
            $('#inputPartnerRenew').val('').trigger('change').select2('destroy');
        }
    };


    // ==========================================
    // 4. REAKTIFASI
    // ==========================================
    const selectPaketReaktifasi = document.getElementById('selectPaketReaktifasi');

    window.updateReaktifasiSummary = function() {
        const selectedOption = selectPaketReaktifasi.options[selectPaketReaktifasi.selectedIndex];
        const durasiStr = selectedOption.getAttribute('data-durasi') || '0';
        const hargaRaw = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        const tipe = selectedOption.getAttribute('data-tipe');

        document.getElementById('inputTipePaketReaktifasi').value = tipe;

        // LOGIKA CERDAS COUPLE
        const isCouple = (tipe === 'couple' || tipe === 'promo couple' || selectedOption.text.toLowerCase().includes('couple'));

        const coupleSection = document.getElementById('reactivateCoupleSection');
        const partnerInput = $('#inputPartnerReactivate');
        const currentMemberId = document.getElementById('reaktifasi_id_member').value;

        if (isCouple) {
            coupleSection.classList.remove('hidden');
            partnerInput.prop('required', true);
            partnerInput.find('option').prop('disabled', false);
            partnerInput.find('option[value="'+currentMemberId+'"]').prop('disabled', true);
            initSafeSelect2('#inputPartnerReactivate', '#modalReaktifasiMember');
        } else {
            coupleSection.classList.add('hidden');
            partnerInput.prop('required', false);
            if (partnerInput.hasClass("select2-hidden-accessible")) {
                partnerInput.val('').trigger('change').select2('destroy');
            } else {
                partnerInput.val('').trigger('change');
            }
        }

        const todayStr = new Date().toISOString().split('T')[0];
        const hasil = hitungTanggalSelesai(todayStr, durasiStr);
        document.getElementById('display_tanggal_selesai_reaktifasi').textContent = hasil.textTanggal;
        document.getElementById('display_total_reaktifasi').textContent = formatRupiah(hargaRaw);
    };

    window.openReaktifasiModal = function(data) {
        document.getElementById('formReaktifasiMember').reset();
        document.getElementById('reaktifasi_id_member').value = data.id_members;
        document.getElementById('display_reaktifasi_nama').textContent = data.nama_lengkap;

        let url = window.routeMembershipReactivate.replace(':id', data.id_members);
        document.getElementById('formReaktifasiMember').action = url;

        document.getElementById('display_tanggal_selesai_reaktifasi').innerText = '-';
        document.getElementById('display_total_reaktifasi').innerText = 'Rp 0';
        document.getElementById('reactivateCoupleSection').classList.add('hidden');
        selectPaketReaktifasi.selectedIndex = 0;

        window.toggleModal("modalReaktifasiMember", true);
        selectPaketReaktifasi.addEventListener('change', window.updateReaktifasiSummary);
    };

    window.closeReaktifasiModal = function() {
        window.toggleModal("modalReaktifasiMember", false);
        selectPaketReaktifasi.removeEventListener('change', window.updateReaktifasiSummary);
        if ($('#inputPartnerReactivate').hasClass("select2-hidden-accessible")) {
            $('#inputPartnerReactivate').val('').trigger('change').select2('destroy');
        }
    };


    // ==========================================
    // 5. DETAIL MEMBER (PAGINATION)
    // ==========================================
    let currentDetailPayments = [];
    let currentModalPage = 1;
    const itemsPerModalPage = 5;

    window.openDetailModal = function(data) {
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

        window.toggleModal("detailMemberModal", true);
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
            let jenisTxt = pay.jenis_transaksi || '-';
            let jenisLower = jenisTxt.toLowerCase();

            if (jenisLower.includes('membership')) badgeClass = 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20';
            else if (jenisLower.includes('renewal')) badgeClass = 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20';
            else if (jenisLower.includes('reactivation')) badgeClass = 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20';

            let namaPaketHistory = '-';
            if (pay.nama_paket_snapshot) namaPaketHistory = pay.nama_paket_snapshot;
            else if (pay.paket) namaPaketHistory = pay.paket.nama_paket;
            else if (pay.promo) namaPaketHistory = pay.promo.nama_paket + ' (Promo)';
            else namaPaketHistory = pay.paket_id ? `Paket ID: ${pay.paket_id}` : '-';

            let keteranganTxt = pay.keterangan || '<span class="text-gray-300 italic">Tidak ada keterangan</span>';

            const row = `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 align-top">
                        ${formatDateIndo(pay.tanggal_transaksi)}
                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">${pay.nomor_invoice || ''}</div>
                    </td>
                    <td class="px-3 py-4 text-sm align-top">
                        <div class="flex flex-col items-start gap-1.5">
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider ${badgeClass}">
                                ${jenisTxt}
                            </span>
                            <span class="text-gray-700 font-semibold whitespace-nowrap">${namaPaketHistory}</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-mono font-bold text-right align-top">
                        ${formatRupiah(pay.nominal)}
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500 leading-relaxed max-w-[200px] break-words whitespace-normal align-top">
                        ${keteranganTxt}
                    </td>
                </tr>
            `;
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
                    <button onclick="changeModalPage(${currentModalPage - 1})" 
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors border 
                        ${isFirstPage ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 active:scale-95'}"
                        ${isFirstPage ? 'disabled' : ''}>
                        <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Prev
                    </button>
                    <button onclick="changeModalPage(${currentModalPage + 1})" 
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors border 
                        ${isLastPage ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 active:scale-95'}"
                        ${isLastPage ? 'disabled' : ''}>
                        Next
                        <svg class="w-4 h-4 inline-block -mt-0.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        `;
    }

    window.changeModalPage = function(newPage) {
        const totalPages = Math.ceil(currentDetailPayments.length / itemsPerModalPage);
        if (newPage >= 1 && newPage <= totalPages) {
            currentModalPage = newPage;
            renderModalTable();
            renderModalPagination();
        }
    };

    window.closeDetailModal = function() { window.toggleModal("detailMemberModal", false); };
});