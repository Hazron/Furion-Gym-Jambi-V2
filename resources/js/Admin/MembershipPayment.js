document.addEventListener("DOMContentLoaded", function () {
    // Pastikan jQuery tersedia
    const $ = window.jQuery;

    // -----------------------------------------------------------
    // 1. KONFIGURASI DATATABLES
    // -----------------------------------------------------------
    var table = $("#paymentTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routePaymentData,
            type: "GET",
            data: function (d) {
                d.start_date = $("#startDate").val();
                d.end_date = $("#endDate").val();
            },
            error: function (xhr, error, code) {
                console.error("DataTables Error:", error);
            },
        },
        dom: '<"top">rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip><"clear">',
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "px-6 py-4 font-medium",
            },
            {
                data: "nomor_invoice",
                name: "nomor_invoice",
                className: "px-6 py-4 font-mono text-xs text-gray-600",
            },
            {
                data: "nama_member",
                name: "member.nama_lengkap",
                className: "px-6 py-4 font-bold text-gray-800",
            },
            {
                data: "nama_paket",
                name: "paket.nama_paket",
                className: "px-6 py-4",
            },
            {
                data: "jenis_transaksi",
                name: "jenis_transaksi",
                className: "px-6 py-4",
            },
            {
                data: "tanggal_transaksi",
                name: "tanggal_transaksi",
                className: "px-6 py-4",
            },
            {
                data: "nominal",
                name: "nominal",
                className: "px-6 py-4 text-right font-bold text-green-600",
            },
            {
                data: "bukti_transfer",
                name: "bukti_transfer",
                className: "px-6 py-4 text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (data) {
                        return `<button onclick="openBuktiModal('${data}')" class="bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-sm flex items-center gap-1 mx-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat
                                </button>`;
                    } else {
                        if (
                            row.metode_pembayaran &&
                            row.metode_pembayaran.toLowerCase() === "bypass"
                        ) {
                            return '<span class="inline-flex items-center px-2 py-1 rounded bg-red-50 text-red-500 text-[10px] font-bold border border-red-100">BYPASS</span>';
                        }
                        // Jika null/kosong (pembayaran cash biasanya)
                        return '<span class="text-gray-400 italic text-xs">Cash/Tidak Ada</span>';
                    }
                },
            },
        ],
        language: {
            paginate: { previous: "Prev", next: "Next" },
            info: "Hal _PAGE_ dari _PAGES_",
            emptyTable: "Belum ada data transaksi",
            zeroRecords: "Data tidak ditemukan",
        },
        initComplete: function () {
            $("#paymentTable").removeClass("dataTable no-footer");
        },
    });

    // -----------------------------------------------------------
    // 2. FUNGSI LOAD STATS
    // -----------------------------------------------------------
    function loadStats() {
        $.ajax({
            url: window.routePaymentStats,
            method: "GET",
            data: {
                start_date: $("#startDate").val(),
                end_date: $("#endDate").val(),
            },
            success: function (response) {
                $("#statTotalPendapatan").text(response.total_pendapatan);
                $("#statTotalTransaksi").text(response.total_transaksi);
                $("#statTodayIncome").text(response.today_income);
            },
            error: function (xhr) {
                console.error("Gagal memuat stats");
                $("#statTotalPendapatan").text("-");
                $("#statTotalTransaksi").text("-");
            },
        });
    }

    loadStats();

    // -----------------------------------------------------------
    // 3. EVENT LISTENERS
    // -----------------------------------------------------------

    // Tombol Filter
    $("#btnFilter").click(function () {
        table.draw();
        loadStats();
    });

    $("#btnReset").click(function () {
        $("#startDate").val("");
        $("#endDate").val("");
        $("#customSearch").val("");
        table.search("").draw();
        loadStats();
    });

    $("#customSearch").on("keyup", function () {
        table.search(this.value).draw();
    });
    // -----------------------------------------------------------
    // 4. MODAL BUKTI TRANSFER HANDLER
    // -----------------------------------------------------------

    // Daftarkan fungsi ke window agar bisa dipanggil dari HTML atribut onclick
    window.openBuktiModal = function (imagePath) {
        const modal = $("#buktiModal");
        const modalContent = $("#modalContent");
        const imgEl = $("#buktiImage");
        const loading = $("#loadingBukti");
        const errorText = $("#noBuktiText");

        // Reset state
        imgEl.hide().attr("src", "");
        errorText.hide();
        loading.show();

        // Tampilkan modal container
        modal.removeClass("hidden").addClass("flex");

        // Animasi masuk (setTimeout kecil untuk mentrigger CSS transition)
        setTimeout(() => {
            modal.removeClass("opacity-0").addClass("opacity-100");
            modalContent.removeClass("scale-95").addClass("scale-100");
        }, 10);

        // Set Image Source
        const fullUrl = `${window.storageUrl}/${imagePath}`;

        // Cek jika file adalah PDF (Opsional, jika kamu memperbolehkan upload PDF)
        if (imagePath.toLowerCase().endsWith(".pdf")) {
            loading.hide();
            errorText
                .text("File berupa PDF. Silakan download untuk melihat.")
                .show();
            window.open(fullUrl, "_blank"); // Otomatis buka tab baru untuk PDF
            return;
        }

        // Load image and show it when ready
        imgEl
            .attr("src", fullUrl)
            .on("load", function () {
                loading.hide();
                imgEl.show();
            })
            .on("error", function () {
                loading.hide();
                errorText
                    .text("Gambar gagal dimuat atau tidak ditemukan.")
                    .show();
            });
    };

    window.closeBuktiModal = function () {
        const modal = $("#buktiModal");
        const modalContent = $("#modalContent");

        modal.removeClass("opacity-100").addClass("opacity-0");
        modalContent.removeClass("scale-100").addClass("scale-95");

        setTimeout(() => {
            modal.removeClass("flex").addClass("hidden");
            $("#buktiImage").attr("src", ""); 
        }, 300);
    };

    $("#buktiModal").on("click", function (e) {
        if (e.target === this) {
            closeBuktiModal();
        }
    });

    // Tutup modal jika user tekan tombol ESC
    $(document).on("keydown", function (e) {
        if (e.key === "Escape" && !$("#buktiModal").hasClass("hidden")) {
            closeBuktiModal();
        }
    });
});
