let chart;
let progressInterval;
let blockIdCounter = 0;

// --- 1. LOGIC CHART ---
function initChart(data) {
    const options = {
        series: [{ name: 'Penggunaan Promo', data: data }],
        chart: { type: 'area', height: 250, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#3b82f6'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1, stops: [0, 90, 100] } },
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#9ca3af', fontSize: '11px' } } },
        yaxis: { show: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
        tooltip: { y: { formatter: function (val) { return val + " Member" } } }
    };

    if (chart) chart.destroy();
    chart = new ApexCharts(document.querySelector("#promoChart"), options);
    chart.render();
}

window.changeYear = function(offset) {
    const holder = document.getElementById('promoDataHolder');
    let currentYear = parseInt(holder.dataset.year) + offset;
    holder.dataset.year = currentYear;
    document.getElementById('displayYear').innerText = currentYear;

    fetch(`${holder.dataset.mainUrl}?year=${currentYear}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(response => response.json())
    .then(data => {
        chart.updateSeries([{ data: data.promoStats }]);
    })
    .catch(error => console.error('Error fetching data:', error));
}

// --- 2. LOGIC MODAL & UI ---
window.openModal = function(modalID) {
    const modal = document.getElementById(modalID);
    if (!modal) return;
    const panel = modal.querySelector('.transform');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        if (panel) { panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }
    }, 10);
}

window.closeModal = function(modalID) {
    const modal = document.getElementById(modalID);
    if (!modal) return;
    const panel = modal.querySelector('.transform');
    modal.classList.add('opacity-0');
    if (panel) { panel.classList.remove('scale-100'); panel.classList.add('scale-95'); }
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}

window.previewImage = function(input, previewId, placeholderId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

window.openEditModal = function(data) {
    const holder = document.getElementById('promoDataHolder');
    document.getElementById('edit_nama_campaign').value = data.nama_campaign;
    document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
    document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;

    let url = holder.dataset.updateUrl.replace(':id', data.id_campaign);
    document.getElementById('editForm').action = url;
    window.openModal('editPromoModal');
}

window.confirmStatusChange = function(idCampaign, namaCampaign, action) {
    Swal.fire({
        title: 'Konfirmasi',
        text: `Anda akan ${action} campaign ${namaCampaign}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'Ya, Lanjutkan'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-toggle-' + idCampaign).submit();
        }
    })
}

// --- 3. DYNAMIC FORM PROMO ---
window.addDurationBlock = function() {
    blockIdCounter++;
    const container = document.getElementById('duration-container');
    const existingInputs = document.querySelectorAll('.month-input-visible');
    let nextMonthValue = (existingInputs.length > 0) ? (parseInt(existingInputs[existingInputs.length - 1].value) || 0) + 1 : 1;

    const html = `
        <div class="duration-block border-2 border-gray-100 rounded-xl p-4 transition-colors bg-gray-50/50 relative group animate-fade-in-down" id="duration_block_${blockIdCounter}">
            <button type="button" onclick="removeDurationBlock(${blockIdCounter})" class="btn-remove-block absolute top-3 right-3 text-gray-300 hover:text-red-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            <div class="flex items-center gap-2 mb-4">
                <input type="number" min="1" class="month-input-visible w-16 h-10 rounded-lg border-2 border-gray-200 text-center font-black" value="${nextMonthValue}" onchange="updateHiddenDuration(this, ${blockIdCounter})">
                <span class="text-sm font-bold">Bulan</span>
            </div>
            <div class="space-y-3">
                <div class="bg-white border rounded-lg p-3">
                    <label class="flex justify-between cursor-pointer mb-2"><span class="text-xs font-bold">👤 Reguler</span><input type="checkbox" name="promos[${blockIdCounter}_reguler][is_selected]" value="1" onchange="document.getElementById('w_${blockIdCounter}_reg').classList.toggle('hidden', !this.checked); document.getElementById('h_${blockIdCounter}_reg').required = this.checked;"></label>
                    <div id="w_${blockIdCounter}_reg" class="hidden relative mt-2"><span class="absolute left-3 top-2 text-gray-400 text-sm font-bold">Rp</span><input type="number" name="promos[${blockIdCounter}_reguler][harga]" id="h_${blockIdCounter}_reg" class="w-full pl-9 pr-3 py-2 text-sm border-2 rounded-lg"><input type="hidden" name="promos[${blockIdCounter}_reguler][durasi]" id="d_${blockIdCounter}_reg" value="${nextMonthValue} Bulan"><input type="hidden" name="promos[${blockIdCounter}_reguler][jenis]" value="promo"></div>
                </div>
                <div class="bg-white border rounded-lg p-3">
                    <label class="flex justify-between cursor-pointer mb-2"><span class="text-xs font-bold">👥 Couple</span><input type="checkbox" name="promos[${blockIdCounter}_couple][is_selected]" value="1" onchange="document.getElementById('w_${blockIdCounter}_cpl').classList.toggle('hidden', !this.checked); document.getElementById('h_${blockIdCounter}_cpl').required = this.checked;"></label>
                    <div id="w_${blockIdCounter}_cpl" class="hidden relative mt-2"><span class="absolute left-3 top-2 text-gray-400 text-sm font-bold">Rp</span><input type="number" name="promos[${blockIdCounter}_couple][harga]" id="h_${blockIdCounter}_cpl" class="w-full pl-9 pr-3 py-2 text-sm border-2 rounded-lg"><input type="hidden" name="promos[${blockIdCounter}_couple][durasi]" id="d_${blockIdCounter}_cpl" value="${nextMonthValue} Bulan"><input type="hidden" name="promos[${blockIdCounter}_couple][jenis]" value="promo couple"></div>
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    checkDeleteButtons();
}

window.updateHiddenDuration = function(input, id) {
    const val = input.value + ' Bulan';
    document.getElementById(`d_${id}_reg`).value = val;
    document.getElementById(`d_${id}_cpl`).value = val;
}

window.removeDurationBlock = function(id) {
    document.getElementById(`duration_block_${id}`).remove();
    checkDeleteButtons();
}

function checkDeleteButtons() {
    const blocks = document.querySelectorAll('.duration-block');
    document.querySelectorAll('.btn-remove-block').forEach(btn => btn.classList.toggle('hidden', blocks.length <= 1));
}

// --- 4. BROADCAST NOTIFIKASI ---
window.toggleManualInput = function(value) {
    const container = document.getElementById('manualInputContainer');
    container.classList.toggle('hidden', value !== 'manual');
    container.querySelector('input').required = (value === 'manual');
}

window.generatePromoMessage = function(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;
    const pkts = JSON.parse(opt.dataset.paket);
    document.getElementById('hiddenSubject').value = opt.dataset.nama;
    let msg = `Halo Member Furion! 🔥\nFurion Gym sedang ada promo spesial nih:\n*${opt.dataset.nama}*\n_(Periode: ${opt.dataset.mulai} s.d ${opt.dataset.selesai})_\n\nBerikut daftar paketnya:\n`;
    pkts.forEach(p => msg += `${p}\n`);
    msg += `\nYuk daftar sebelum berakhir! 💪`;
    document.getElementById('promoMessage').value = msg;
}

function fetchBroadcastProgress() {
    const holder = document.getElementById('promoDataHolder');
    fetch(holder.dataset.progressUrl)
    .then(r => r.json())
    .then(data => {
        const container = document.getElementById('broadcast-progress-container');
        if (data.aktif) {
            container.style.display = 'block';
            document.getElementById('bp-nama').innerText = data.nama_campaign;
            document.getElementById('bp-total').innerText = data.total;
            document.getElementById('bp-terproses').innerText = data.terproses;
            document.getElementById('bp-persen-text').innerText = data.persentase + '%';
            document.getElementById('bp-progress-bar').style.width = data.persentase + '%';
            document.getElementById('bp-pending').innerText = data.detail.pending;
            document.getElementById('bp-sent').innerText = parseInt(data.detail.sent) + parseInt(data.detail.delivered);
            document.getElementById('bp-read').innerText = data.detail.read;
            document.getElementById('bp-failed').innerText = data.detail.failed;
            if (data.status_global === 'selesai' || data.persentase >= 100) clearInterval(progressInterval);
        } else {
            container.style.display = 'none';
            clearInterval(progressInterval);
        }
    });
}

// --- INIT ---
document.addEventListener("DOMContentLoaded", function () {
    const holder = document.getElementById('promoDataHolder');
    initChart(JSON.parse(holder.dataset.stats));
    window.addDurationBlock();
    fetchBroadcastProgress();
    progressInterval = setInterval(fetchBroadcastProgress, 3000);
});