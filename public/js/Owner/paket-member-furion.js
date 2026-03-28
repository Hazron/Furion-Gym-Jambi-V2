/**
 * Script Logika Manajemen Paket Member (Furion Gym)
 */

document.addEventListener('DOMContentLoaded', function() {
    // --- Batasan Input Durasi (Max 36 Bulan) ---
    const durasiInputs = document.querySelectorAll('input[name="durasi"]');
    durasiInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (parseInt(this.value) > 36) {
                this.value = 36;
            }
        });
    });
});

// --- 1. LOGIKA ANIMASI MODAL ---
window.openModal = function(modalID) {
    const modal = document.getElementById(modalID);
    const panel = modal.querySelector('div[id$="Panel"]'); // Mencari ID yang berakhiran 'Panel'

    if (modal && panel) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }, 10);
    }
};

window.closeModal = function(modalID) {
    const modal = document.getElementById(modalID);
    const panel = modal.querySelector('div[id$="Panel"]');

    if (modal && panel) {
        modal.classList.add('opacity-0');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
};

// --- 2. LOGIKA EDIT FORM ---
window.openEditModal = function(data, baseUrl) {
    // Isi value input
    document.getElementById('edit_nama').value = data.nama_paket;
    document.getElementById('edit_harga').value = parseInt(data.harga);
    document.getElementById('edit_durasi').value = data.durasi;
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';

    // Update Action URL pada Form
    // baseUrl dikirim dari Blade (berisi template route)
    const form = document.getElementById('editForm');
    const finalUrl = baseUrl.replace(':id', data.id_paket);
    form.action = finalUrl;

    openModal('editPaketModal');
};

// --- 3. LOGIKA TOGGLE STATUS (SweetAlert) ---
window.confirmStatusChange = function(id, namaPaket, actionType) {
    const isDeactivate = actionType === 'nonaktifkan';
    const color = isDeactivate ? '#ef4444' : '#10b981';
    const btnText = isDeactivate ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!';
    const title = isDeactivate ? 'Nonaktifkan Paket?' : 'Aktifkan Paket?';
    const text = isDeactivate ?
        `Paket "${namaPaket}" tidak akan bisa dipilih oleh member baru.` :
        `Paket "${namaPaket}" akan kembali tersedia untuk member.`;

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#9ca3af',
        confirmButtonText: btnText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
        background: '#ffffff',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
            cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-form-' + id).submit();
        }
    });
};

// --- 4. UTILITY: TOAST NOTIFIKASI ---
window.showSuccessToast = function(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        position: 'top-end',
        background: '#ffffff',
        iconColor: '#10b981',
        customClass: {
            popup: 'rounded-xl border border-gray-100 shadow-xl'
        }
    });
};