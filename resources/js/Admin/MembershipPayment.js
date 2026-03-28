document.addEventListener('DOMContentLoaded', function() {
    
    // Pastikan jQuery tersedia
    const $ = window.jQuery; 

    // -----------------------------------------------------------
    // 1. KONFIGURASI DATATABLES
    // -----------------------------------------------------------
    var table = $('#paymentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routePaymentData,
            type: "GET",
            data: function(d) {
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            },
            error: function(xhr, error, code) {
                console.error("DataTables Error:", error);
            }
        },
        dom: '<"top">rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip><"clear">',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-6 py-4 font-medium' },
            { data: 'nomor_invoice', name: 'nomor_invoice', className: 'px-6 py-4 font-mono text-xs text-gray-600' },
            { data: 'nama_member', name: 'member.nama_lengkap', className: 'px-6 py-4 font-bold text-gray-800' },
            { data: 'nama_paket', name: 'paket.nama_paket', className: 'px-6 py-4' },
            { data: 'jenis_transaksi', name: 'jenis_transaksi', className: 'px-6 py-4' },
            { data: 'tanggal_transaksi', name: 'tanggal_transaksi', className: 'px-6 py-4' },
            { data: 'nominal', name: 'nominal', className: 'px-6 py-4 text-right font-bold text-green-600' }
        ],
        language: {
            paginate: { previous: "Prev", next: "Next" },
            info: "Hal _PAGE_ dari _PAGES_",
            emptyTable: "Belum ada data transaksi",
            zeroRecords: "Data tidak ditemukan"
        },
        initComplete: function() {
            $('#paymentTable').removeClass('dataTable no-footer');
        }
    });

    // -----------------------------------------------------------
    // 2. FUNGSI LOAD STATS
    // -----------------------------------------------------------
    function loadStats() {
        $.ajax({
            url: window.routePaymentStats, 
            method: "GET",
            data: {
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val()
            },
            success: function(response) {
                $('#statTotalPendapatan').text(response.total_pendapatan);
                $('#statTotalTransaksi').text(response.total_transaksi);
                $('#statTodayIncome').text(response.today_income);
            },
            error: function(xhr) {
                console.error("Gagal memuat stats");
                $('#statTotalPendapatan').text("-");
                $('#statTotalTransaksi').text("-");
            }
        });
    }

    loadStats();

    // -----------------------------------------------------------
    // 3. EVENT LISTENERS
    // -----------------------------------------------------------
    
    // Tombol Filter
    $('#btnFilter').click(function() {
        table.draw();
        loadStats();
    });

    $('#btnReset').click(function() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#customSearch').val('');
        table.search('').draw();
        loadStats();
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

});