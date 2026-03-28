/* -------------------------------------------------------------------------- */
/* 1. HELPER & FUNGSI GLOBAL                                                  */
/* -------------------------------------------------------------------------- */
const formatMoney = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(number);
};

window.openPaymentModal = function (invoiceCode, actionUrl) {
    document.getElementById("paymentInvoiceCode").innerText = invoiceCode;
    document.getElementById("paymentForm").action = actionUrl;
    document.getElementById("modalPayment").classList.remove("hidden");
};

window.closePaymentModal = function () {
    document.getElementById("modalPayment").classList.add("hidden");
};

window.showDetailOrder = function (items, invoiceCode) {
    console.log("MEMBUKA DETAIL ORDER:", invoiceCode); // DEBUGGING LOG

    const modal = document.getElementById("modalDetailOrder");
    const listContainer = document.getElementById("detailItemsList");
    const invoiceLabel = document.getElementById("detailInvoiceCode");

    if (!modal || !listContainer) {
        console.error("ELEMEN MODAL TIDAK DITEMUKAN DI HTML");
        return;
    }

    invoiceLabel.innerText = invoiceCode;
    listContainer.innerHTML = "";

    if (!items || items.length === 0) {
        listContainer.innerHTML = `<tr><td class="p-6 text-center text-gray-400 text-sm uppercase">TIDAK ADA ITEM</td></tr>`;
    } else {
        items.forEach((item) => {
            const dataProduk = item.produk;
            const productName = dataProduk
                ? dataProduk.nama_produk
                : '<span class="text-red-400 italic uppercase">PRODUK DIHAPUS</span>';

            // GUNAKAN PATH ASSET GLOBAL ATAU PLACEHOLDER
            let imgUrl = "https://via.placeholder.com/150?text=No+Img";
            if (dataProduk && dataProduk.gambar_produk && window.assetProduk) {
                imgUrl = window.assetProduk + dataProduk.gambar_produk;
            }

            const html = `
                <tr class="hover:bg-blue-50/30 border-b border-gray-50 last:border-0 transition-colors">
                    <td class="p-4 w-16 align-middle">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                            <img src="${imgUrl}" class="w-full h-full object-cover">
                        </div>
                    </td>
                    <td class="p-4 align-middle">
                        <p class="font-bold text-gray-800 text-sm line-clamp-1 uppercase">${productName}</p>
                        <p class="text-xs text-gray-500 mt-1 font-mono">${item.qty} x ${formatMoney(item.price)}</p>
                    </td>
                    <td class="p-4 text-right font-bold text-gray-700 text-sm align-middle">
                        ${formatMoney(item.total)}
                    </td>
                </tr>`;
            listContainer.insertAdjacentHTML("beforeend", html);
        });
    }

    modal.classList.remove("hidden");
};

/* -------------------------------------------------------------------------- */
/* FUNGSI UNTUK MODAL BUKTI TRANSFER (TAMBAHAN BARU)                          */
/* -------------------------------------------------------------------------- */
window.openBuktiModal = function (imagePath) {
    const modal = document.getElementById("modalBuktiTransfer");
    const modalContent = document.getElementById("buktiTransferContent");
    const imgEl = document.getElementById("buktiImage");
    const loading = document.getElementById("loadingBukti");
    const errorText = document.getElementById("noBuktiText");

    // RESET TAMPILAN
    imgEl.style.display = "none";
    imgEl.src = "";
    errorText.style.display = "none";
    loading.style.display = "block";

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    setTimeout(() => {
        modal.classList.remove("opacity-0");
        modalContent.classList.remove("scale-95");
    }, 10);

    const fullUrl = `${window.storageUrl}/${imagePath}`;

    // PENGECEKAN JIKA FILE ADALAH PDF
    if (imagePath.toLowerCase().endsWith(".pdf")) {
        loading.style.display = "none";
        errorText.innerText =
            "FILE BERUPA PDF. SILAKAN DOWNLOAD UNTUK MELIHAT.";
        errorText.style.display = "block";
        window.open(fullUrl, "_blank");
        return;
    }

    // PROSES LOAD GAMBAR
    imgEl.src = fullUrl;
    imgEl.onload = function () {
        loading.style.display = "none";
        imgEl.style.display = "block";
    };
    imgEl.onerror = function () {
        loading.style.display = "none";
        errorText.innerText = "GAMBAR GAGAL DIMUAT ATAU TIDAK DITEMUKAN.";
        errorText.style.display = "block";
    };
};

window.closeBuktiModal = function () {
    const modal = document.getElementById("modalBuktiTransfer");
    const modalContent = document.getElementById("buktiTransferContent");

    // EFEK FADE OUT
    modal.classList.add("opacity-0");
    modalContent.classList.add("scale-95");

    setTimeout(() => {
        modal.classList.remove("flex");
        modal.classList.add("hidden");
        document.getElementById("buktiImage").src = ""; // BERSIHKAN CACHE MEMORI GAMBAR
    }, 300);
};

// TUTUP MODAL JIKA KLIK AREA LUAR / BACKDROP
document.addEventListener("click", function (event) {
    const modal = document.getElementById("modalBuktiTransfer");
    if (event.target === modal) {
        window.closeBuktiModal();
    }
});

// TUTUP MODAL DENGAN TOMBOL ESCAPE (ESC)
document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
        const modal = document.getElementById("modalBuktiTransfer");
        if (!modal.classList.contains("hidden")) {
            window.closeBuktiModal();
        }
    }
});

/* -------------------------------------------------------------------------- */
/* 2. LOGIC SAAT DOM READY (DATATABLES & EVENT LISTENER)                      */
/* -------------------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery) {
        // HANCURKAN DATATABLE LAMA JIKA ADA UNTUK MENCEGAH ERROR RE-INIT
        if ($.fn.DataTable.isDataTable("#tableTransaksi")) {
            $("#tableTransaksi").DataTable().destroy();
        }

        var table = $("#tableTransaksi").DataTable({
            dom: 'rt<"flex flex-col sm:flex-row justify-between items-center mt-6 gap-4"ip><"clear">',
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                emptyTable: "TIDAK ADA DATA TRANSAKSI PADA PERIODE INI",
                zeroRecords: "TIDAK DITEMUKAN DATA YANG COCOK",
                paginate: {
                    previous: "PREV",
                    next: "NEXT",
                },
            },
            order: [[0, "desc"]],
            // UPDATE TARGET COLUMN UNTUK ORDERABLE FALSE KARENA ADA TAMBAHAN 1 KOLOM (INDEX BERUBAH DARI 6 KE 7)
            columnDefs: [
                {
                    orderable: false,
                    targets: [4, 7],
                },
            ],
            initComplete: function () {
                $("#tableTransaksi").removeClass("dataTable no-footer");
            },
        });

        // SEARCH CUSTOM
        const searchInput = document.getElementById("customSearch");
        if (searchInput) {
            searchInput.addEventListener("keyup", function () {
                table.search(this.value).draw();
            });
        }
    }
});
