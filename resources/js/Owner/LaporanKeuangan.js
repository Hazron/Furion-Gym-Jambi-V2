// === 1. FUNGSI MODAL & DETAIL ===
window.openModal = function(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => { 
            modal.classList.remove('opacity-0'); 
            const panel = modal.querySelector('.transform'); 
            if(panel) panel.classList.remove('scale-95'); 
        }, 10);
    }
}

window.closeModal = function(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        modal.classList.add('opacity-0');
        const panel = modal.querySelector('.transform');
        if(panel) panel.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
}

window.showDetailBelanja = function(details) {
    const tbody = document.getElementById('detailContent');
    tbody.innerHTML = '';
    
    if (!details || details.length === 0) { 
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-gray-400 italic">Tidak ada detail item.</td></tr>'; 
    } else {
        details.forEach(item => {
            const hargaFormatter = new Intl.NumberFormat('id-ID').format(item.harga);
            const totalFormatter = new Intl.NumberFormat('id-ID').format(item.total);
            
            const row = `<tr class="border-b border-gray-50 last:border-0">
                            <td class="px-3 py-3 font-medium text-gray-800">
                                ${item.nama_produk}
                                <div class="text-[10px] text-gray-400">@ Rp ${hargaFormatter}</div>
                            </td>
                            <td class="px-3 py-3 text-center font-mono text-xs">${item.qty}</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900">Rp ${totalFormatter}</td>
                        </tr>`;
            tbody.innerHTML += row;
        });
    }
    window.openModal('detailModal');
}

// === 2. CHART (APEXCHARTS) ===
document.addEventListener("DOMContentLoaded", function() {
    const dataEl = document.getElementById('chartData');
    if (!dataEl) return;
    
    // Ambil data dari atribut HTML 'data-*'
    const labels = JSON.parse(dataEl.dataset.labels);
    const values = JSON.parse(dataEl.dataset.values);
    const totalMember = parseInt(dataEl.dataset.member);
    const totalSales = parseInt(dataEl.dataset.sales);

    // Render Area Chart (Tren Pendapatan)
    if (document.querySelector("#incomeChart")) {
        new ApexCharts(document.querySelector("#incomeChart"), {
            series: [{ name: 'Pendapatan', data: values }],
            chart: { 
                type: 'area', 
                height: window.innerWidth < 640 ? 250 : 300, 
                fontFamily: 'Inter, sans-serif', 
                toolbar: { 
                    show: true, 
                    tools: { download: false, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } 
                }, 
                zoom: { enabled: true, type: 'x', autoScaleYaxis: true } 
            },
            dataLabels: { enabled: false }, 
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
            colors: ['#2563eb'],
            xaxis: { 
                categories: labels, 
                axisBorder: { show: false }, 
                axisTicks: { show: false }, 
                labels: { style: { colors: '#9ca3af', fontSize: '10px' } }, 
                tickAmount: window.innerWidth < 640 ? 5 : undefined 
            },
            yaxis: { 
                labels: { 
                    formatter: (val) => { 
                        if(val >= 1000000) return (val/1000000).toFixed(1) + 'jt'; 
                        if(val >= 1000) return (val/1000).toFixed(0) + 'rb'; 
                        return val; 
                    }, 
                    style: { colors: '#9ca3af', fontSize: '10px' } 
                } 
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 }, 
            tooltip: { y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) } }
        }).render();
    }

    // Render Donut Chart (Sumber Dana)
    const sourceChartEl = document.querySelector("#sourceChart");
    if (sourceChartEl) {
        const safeMember = totalMember || 0; 
        const safeSales = totalSales || 0;
        const isZeroData = (safeMember === 0 && safeSales === 0);
        
        new ApexCharts(sourceChartEl, {
            series: isZeroData ? [1] : [safeMember, safeSales],
            labels: isZeroData ? ['Belum ada data'] : ['Membership', 'Penjualan'],
            chart: { type: 'donut', height: 250, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: isZeroData ? ['#e5e7eb'] : ['#3b82f6', '#10b981'],
            plotOptions: { 
                pie: { 
                    donut: { 
                        size: '70%', 
                        labels: { 
                            show: true, 
                            total: { 
                                show: true, 
                                label: isZeroData ? 'Kosong' : 'Total', 
                                formatter: (w) => isZeroData ? 'Rp 0' : "Rp " + new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 3 }).format(safeMember + safeSales) 
                            } 
                        } 
                    } 
                } 
            },
            dataLabels: { enabled: false }, 
            legend: { show: false }, 
            stroke: { width: 0 }, 
            tooltip: { 
                enabled: !isZeroData, 
                y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) } 
            }
        }).render();
    }
});

// === 3. DATATABLES INITIALIZATION ===
$(document).ready(function() {
    // Desktop Table
    if (window.innerWidth >= 640 && $('#transactionTable').length) {
        $('#transactionTable').DataTable({ 
            responsive: false, 
            pageLength: 10, 
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }, 
            order: [[0, 'desc']], 
            columnDefs: [{ targets: 5, className: 'text-right' }], 
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip>' 
        });
    }
    
    // Mobile Table
    if (window.innerWidth < 640 && $('#mobileTable').length) {
        $('#mobileTable').DataTable({ 
            pageLength: 5, 
            ordering: false, 
            info: false, 
            lengthChange: false, 
            language: { 
                search: "", 
                searchPlaceholder: "Cari transaksi...", 
                paginate: { next: "Next", previous: "Prev" } 
            }, 
            dom: '<"mb-4"f>rt<"flex justify-center mt-4"p>' 
        });
    }
});