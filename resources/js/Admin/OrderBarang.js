
document.addEventListener('DOMContentLoaded', function() {

    /* -------------------------------------------------------------------------- */
    /* 1. STATE & UTILS                                                           */
    /* -------------------------------------------------------------------------- */
    let cart = [];
    let selectedMemberId = null;

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // --- Expose Utils to Window ---
    window.toggleModal = function(modalID, show) {
        const modal = document.getElementById(modalID);
        if (show) modal.classList.remove('hidden');
        else modal.classList.add('hidden');
    }

    window.previewImage = function(input) {
        const placeholder = document.getElementById('upload-placeholder');
        const preview = document.getElementById('image-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('opacity-0');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /* -------------------------------------------------------------------------- */
    /* 2. MEMBER LOGIC                                                            */
    /* -------------------------------------------------------------------------- */
    window.toggleDropdown = function() {
        document.getElementById('memberDropdownList').classList.toggle('hidden');
    }

    window.selectMember = function(name, id) {
        document.getElementById('selectedMemberDisplay').value = name;
        selectedMemberId = id;
        document.getElementById('memberDropdownList').classList.add('hidden');
    }

    window.filterMembers = function() {
        let input = document.getElementById('memberSearchInput').value.toUpperCase();
        let ul = document.getElementById("memberList");
        let li = ul.getElementsByTagName('li');
        for (let i = 0; i < li.length; i++) {
            let txtValue = li[i].textContent || li[i].innerText;
            if (txtValue.toUpperCase().indexOf(input) > -1) li[i].style.display = "";
            else li[i].style.display = "none";
        }
    }

    /* -------------------------------------------------------------------------- */
    /* 3. CART SYSTEM                                                             */
    /* -------------------------------------------------------------------------- */
    window.tambahKeKeranjang = function(button) {
        const id = button.dataset.id;
        const name = button.dataset.name;
        const price = parseInt(button.dataset.price);
        const image = button.dataset.image;

        if (!id) return;

        const productData = {
            id: String(id),
            name: name,
            price: price,
            image: image,
            qty: 1
        };

        const existingItemIndex = cart.findIndex(item => item.id === productData.id);

        if (existingItemIndex > -1) {
            cart[existingItemIndex].qty += 1;
        } else {
            cart.push(productData);
        }

        renderCart();
    }

    function renderCart() {
        const cartContainer = document.getElementById('cart-items-container');
        const totalElement = document.getElementById('total-payment');
        const subtotalElement = document.getElementById('subtotal-display');

        cartContainer.innerHTML = '';
        let grandTotal = 0;

        if (cart.length === 0) {
            cartContainer.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-3 opacity-60">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="text-sm">Keranjang kosong</p>
                </div>`;
            if (totalElement) totalElement.innerText = formatRupiah(0);
            if (subtotalElement) subtotalElement.innerText = formatRupiah(0);
            return;
        }

        cart.forEach((item) => {
            const itemTotal = item.price * item.qty;
            grandTotal += itemTotal;

            const html = `
                <div class="flex items-center justify-between mb-4 p-2 border-b border-gray-100 bg-white rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                            ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : '<div class="w-full h-full bg-gray-200"></div>'}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-bold text-gray-800 line-clamp-1">${item.name}</h5>
                            <p class="text-xs text-gray-500">${formatRupiah(item.price)}</p>
                            <div class="flex items-center mt-1 gap-2">
                                <button onclick="updateQty('${item.id}', -1)" class="w-6 h-6 bg-gray-100 rounded text-sm hover:bg-gray-200 font-bold">-</button>
                                <span class="text-xs font-bold w-6 text-center">${item.qty}</span>
                                <button onclick="updateQty('${item.id}', 1)" class="w-6 h-6 bg-gray-100 rounded text-sm hover:bg-gray-200 font-bold">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex flex-col justify-between h-full pl-2">
                        <p class="text-sm font-bold text-blue-600">${formatRupiah(itemTotal)}</p>
                        <button onclick="removeItem('${item.id}')" class="text-gray-400 hover:text-red-500 self-end mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            `;
            cartContainer.insertAdjacentHTML('beforeend', html);
        });

        if (totalElement) totalElement.innerText = formatRupiah(grandTotal);
        if (subtotalElement) subtotalElement.innerText = formatRupiah(grandTotal);
    }

    window.updateQty = function(id, change) {
        const index = cart.findIndex(item => item.id === String(id));
        if (index > -1) {
            if (cart[index].qty + change > 0) {
                cart[index].qty += change;
            }
            renderCart();
        }
    };

    window.removeItem = function(id) {
        const index = cart.findIndex(item => item.id === String(id));
        if (index > -1) {
            cart.splice(index, 1);
            renderCart();
        }
    };

    /* -------------------------------------------------------------------------- */
    /* 4. PAYMENT LOGIC                                                           */
    /* -------------------------------------------------------------------------- */
    window.processPayment = function() {
        if (cart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Keranjang Kosong',
                text: 'Silahkan pilih produk.'
            });
            return;
        }

        const memberName = document.getElementById('selectedMemberDisplay').value;
        let grandTotal = 0;
        let cartHtml = '<div class="text-left text-sm space-y-2 mb-4 max-h-60 overflow-y-auto bg-gray-50 p-3 rounded-lg border border-gray-200">';

        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            grandTotal += subtotal;
            cartHtml += `
        <div class="flex justify-between border-b border-gray-200 pb-1 last:border-0">
            <div>
                <div class="font-bold text-gray-700">${item.name}</div>
                <div class="text-xs text-gray-500">${item.qty} x ${formatRupiah(item.price)}</div>
            </div>
            <div class="font-semibold text-gray-800">${formatRupiah(subtotal)}</div>
        </div>`;
        });
        cartHtml += '</div>';

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `
            <div class="text-left mb-3">
                <p class="text-xs font-bold text-gray-400 uppercase">Pembeli</p>
                <p class="text-base font-bold text-blue-600">${memberName}</p>
            </div>
            ${cartHtml}
            <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-3 mt-2 mb-4">
                <span class="font-bold text-gray-700">Total Akhir:</span>
                <span class="font-bold text-xl text-blue-600">${formatRupiah(grandTotal)}</span>
            </div>
            <div class="text-left">
                <label class="text-xs font-bold text-gray-500 uppercase">Metode Pembayaran</label>
                <select id="swal-payment-method" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="qris">QRIS / E-Wallet</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
            <div class="text-left mt-3">
                <label class="text-xs font-bold text-gray-500 uppercase">Status Pembayaran</label>
                <select id="swal-payment-status" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none text-green-600 bg-green-50">
                    <option value="paid" class="text-green-600 font-bold">LUNAS (Paid)</option>
                    <option value="pending" class="text-yellow-600 font-bold">BELUM LUNAS (Pending)</option>
                </select>
            </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Bayar Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563EB',
            allowOutsideClick: false,
            preConfirm: () => {
                return {
                    method: document.getElementById('swal-payment-method').value,
                    status: document.getElementById('swal-payment-status').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitOrderToController(memberName, grandTotal, result.value.method, result.value.status);
            }
        });
    }

    function submitOrderToController(memberName, totalAmount, paymentMethod, paymentStatus) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            Swal.fire('Error', 'CSRF token tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Memproses Transaksi...',
            html: 'Mohon tunggu, sistem sedang menyimpan data.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const payload = {
            member_id: selectedMemberId,
            total_amount: totalAmount,
            payment_method: paymentMethod,
            payment_status: paymentStatus,
            items: cart,
            _token: csrfToken.getAttribute('content')
        };

        fetch(window.routes.simpanTransaksi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) return response.text().then(text => { throw new Error(text) });
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Sukses!',
                        text: `Inv: ${data.invoice} | Status: ${paymentStatus.toUpperCase()}`,
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        cart = [];
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Server Error', 'Cek Console untuk detail.', 'error');
            });
    }

    /* -------------------------------------------------------------------------- */
    /* 5. PRODUCT CRUD LOGIC                                                      */
    /* -------------------------------------------------------------------------- */
    
    // --- TAMBAH PRODUK ---
    const formTambah = document.getElementById('formTambahProduk');
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Menyimpan Produk...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            this.submit();
        });
    }

    // --- EDIT PRODUK ---
    window.openEditModal = function(data) {
        document.getElementById('edit_nama_produk').value = data.nama_produk;
        document.getElementById('edit_harga_produk').value = data.harga_produk;
        document.getElementById('edit_stok_produk').value = data.stok_produk;
        document.getElementById('calc_tambah_stok').value = ''; // Reset barang masuk

        const imgPreview = document.getElementById('edit_preview_img');
        if (data.gambar_produk) {
            imgPreview.src = window.routes.assetProduk + data.gambar_produk;
        } else {
            imgPreview.src = "https://via.placeholder.com/150?text=No+Img";
        }

        const id = data.id_produk || data.id;
        document.getElementById('formEditProduk').action = window.routes.baseProduk + "/" + id;
        window.toggleModal('modalEditProduk', true);
    }

    // Listener untuk Edit Form
    const formEdit = document.getElementById('formEditProduk');
    if(formEdit) {
        formEdit.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Menyimpan Perubahan...',
                text: 'Mohon tunggu, sedang mengupdate data.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            this.submit();
        });
    }

    // Logic Tambah Stok Otomatis di Modal Edit
    const inputTambahStok = document.getElementById('calc_tambah_stok');
    if (inputTambahStok) {
        inputTambahStok.addEventListener('input', function() {
        });
    }

    // --- HAPUS PRODUK ---
    window.hapusProduk = function(id, nama) {
    }

    // --- TOGGLE STATUS ---
    window.toggleStatus = function(id, currentStatus) {
        const actionText = currentStatus === 'active' ? 'menonaktifkan' : 'mengaktifkan';
        const confirmBtnColor = currentStatus === 'active' ? '#d33' : '#10b981';

        Swal.fire({
            title: 'Ubah Status?',
            text: `Anda ingin ${actionText} produk ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            confirmButtonText: 'Ya, Lakukan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    didOpen: () => Swal.showLoading()
                });

                fetch(window.routes.baseProduk + "/" + id + "/status", {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'));
            }
        });
    }
});