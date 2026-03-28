    let table;

    // --- 1. INITIALIZATION ---
    $(document).ready(function() {
        table = $('#aktivitasAdminTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('owner.aktivitasadmin') }}",
                data: function(d) {
                    d.filter_user_id = $('#currentFilterUser').val();
                }
            },
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                    class: 'px-6 py-4 whitespace-nowrap text-sm text-gray-500'
                },
                {
                    data: 'admin_name',
                    name: 'admin_name',
                    class: 'px-6 py-4 font-medium text-gray-900',
                    render: function(data) {
                        let initial = data ? data.charAt(0).toUpperCase() : '?';
                        return `<div class="flex items-center gap-2"><div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">${initial}</div><span>${data}</span></div>`;
                    }
                },
                {
                    data: 'badge_action',
                    name: 'badge_action',
                    class: 'px-6 py-4 whitespace-nowrap'
                },
                {
                    data: 'description',
                    name: 'description',
                    class: 'px-6 py-4 text-sm text-gray-600 leading-snug'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    class: 'px-6 py-4 text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
            language: {
                search: "Cari:",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
                infoEmpty: "Data Kosong",
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            }
        });
    });

    $('body').append('<input type="hidden" id="currentFilterUser" value="all">');

    // --- 2. FILTER TABLE LOGIC ---
    function filterTable(userId) {
        $('#currentFilterUser').val(userId);
        $('.admin-card').removeClass('bg-blue-50 border-blue-500 border-2').addClass('bg-white border-gray-200 border');
        if (userId == 'all') {
            $('#card-all').removeClass('bg-white border-gray-200 border').addClass('bg-blue-50 border-blue-500 border-2');
            $('#table-title').text('Riwayat Aktivitas: Semua Admin');
        } else {
            $('#card-' + userId).removeClass('bg-white border-gray-200 border').addClass('bg-blue-50 border-blue-500 border-2');
            let name = $('#card-' + userId).find('h4').text();
            $('#table-title').text('Riwayat Aktivitas: ' + name);
        }
        table.ajax.reload();
    }

    // --- 3. MODAL LOGIC (ANIMATED) ---
    function toggleModal(modalId, show) {
        const modal = document.getElementById(modalId);
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

    // --- 4. CRUD ADMIN ---
    function openCreateModal() {
        toggleModal('adminModal', true);
        $('#adminForm').attr('action', "{{ route('owner.admin.store') }}");
        $('#methodField').html('');
        $('#adminModalTitle').text('Tambah Admin Baru');
        $('#inputName').val('');
        $('#inputEmail').val('');
        $('#inputPassword').val('').attr('required', true);
        $('#passHelp').text('Minimal 6 karakter.');
    }

    function openEditModal(id, name, email) {
        event.stopPropagation();
        toggleModal('adminModal', true);
        let url = "{{ route('owner.admin.update', ':id') }}".replace(':id', id);
        $('#adminForm').attr('action', url);
        $('#methodField').html('@method("PUT")');
        $('#adminModalTitle').text('Edit Data Admin');
        $('#inputName').val(name);
        $('#inputEmail').val(email);
        $('#inputPassword').val('').attr('required', false);
        $('#passHelp').text('Kosongkan jika tidak ingin mengganti password.');
    }

    function closeAdminModal() {
        toggleModal('adminModal', false);
    }

    // --- 5. DETAIL AKTIVITAS ---
    function closeDetailModal() {
        toggleModal('detailModal', false);
    }

    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        toggleModal('detailModal', true);
        $('#modalLoading').show();
        $('#modalContent').html('').addClass('hidden');

        $.ajax({
            url: "{{ route('owner.aktivitas-detail') }}",
            type: 'GET',
            data: {
                id: id,
                type: type
            },
            success: function(response) {
                $('#modalLoading').hide();
                $('#modalContent').html(response.html).removeClass('hidden');
            },
            error: function() {
                $('#modalLoading').hide();
                $('#modalContent').html('<p class="text-red-500 font-bold text-center">Gagal memuat data detail.</p>').removeClass('hidden');
            }
        });
    });