/* -------------------------------------------------------------------------- */
/* 1. HELPER & FUNGSI GLOBAL                                                  */
/* -------------------------------------------------------------------------- */
const formatMoney = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

window.openPaymentModal = function(invoiceCode, actionUrl) {
    document.getElementById('paymentInvoiceCode').innerText = invoiceCode;
    document.getElementById('paymentForm').action = actionUrl;
    document.getElementById('modalPayment').classList.remove('hidden');
}

window.closePaymentModal = function() {
    document.getElementById('modalPayment').classList.add('hidden');
}

window.showDetailOrder = function(items, invoiceCode) {
    console.log('Membuka detail order:', invoiceCode); // Debugging log

    const modal = document.getElementById('modalDetailOrder');
    const listContainer = document.getElementById('detailItemsList');
    const invoiceLabel = document.getElementById('detailInvoiceCode');
    
    if(!modal || !listContainer) {
        console.error("Elemen modal tidak ditemukan di HTML");
        return;
    }

    invoiceLabel.innerText = invoiceCode;
    listContainer.innerHTML = ''; 

    if (!items || items.length === 0) {
        listContainer.innerHTML = `<tr><td class="p-6 text-center text-gray-400 text-sm">Tidak ada item</td></tr>`;
    } else {
        items.forEach(item => {
            const dataProduk = item.produk;
            const productName = dataProduk ? dataProduk.nama_produk : '<span class="text-red-400 italic">Produk Dihapus</span>';
            
            // Gunakan path asset global atau placeholder
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
                        <p class="font-bold text-gray-800 text-sm line-clamp-1">${productName}</p>
                        <p class="text-xs text-gray-500 mt-1 font-mono">${item.qty} x ${formatMoney(item.price)}</p>
                    </td>
                    <td class="p-4 text-right font-bold text-gray-700 text-sm align-middle">
                        ${formatMoney(item.total)}
                    </td>
                </tr>`;
            listContainer.insertAdjacentHTML('beforeend', html);
        });
    }

    modal.classList.remove('hidden');
}

/* -------------------------------------------------------------------------- */
/* 2. LOGIC SAAT DOM READY (DataTables & Event Listener)                      */
/* -------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
    
    if(window.jQuery) {
        // Hancurkan datatable lama jika ada untuk mencegah error re-init
        if ($.fn.DataTable.isDataTable('#tableTransaksi')) {
            $('#tableTransaksi').DataTable().destroy();
        }

        var table = $('#tableTransaksi').DataTable({
            "dom": 'rt<"flex flex-col sm:flex-row justify-between items-center mt-6 gap-4"ip><"clear">', 
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                "emptyTable": "Tidak ada data transaksi pada periode ini",
                "zeroRecords": "Tidak ditemukan data yang cocok",
                "paginate": {
                    "previous": "Prev",
                    "next": "Next"
                }
            },
            "order": [
                [0, "desc"] 
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": 6 
            }],
            "initComplete": function() {
                $('#tableTransaksi').removeClass('dataTable no-footer');
            }
        });

        // Search Custom
        const searchInput = document.getElementById('customSearch');
        if(searchInput){
            searchInput.addEventListener('keyup', function() {
                table.search(this.value).draw();
            });
        }
    }
});