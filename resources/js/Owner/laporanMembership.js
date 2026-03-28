document.addEventListener("DOMContentLoaded", function () {
    const dataEl = document.getElementById('chartData');
    if (!dataEl) return;

    // Pastikan data ada sebelum dirender
    let chartLabels = [];
    let chartReg = [];
    let chartRenew = [];
    let chartReact = [];
    
    try {
        chartLabels = JSON.parse(dataEl.dataset.labels || "[]");
        chartReg = JSON.parse(dataEl.dataset.reg || "[]");
        chartRenew = JSON.parse(dataEl.dataset.renew || "[]");
        chartReact = JSON.parse(dataEl.dataset.react || "[]");
    } catch (e) {
        console.error("Gagal mem-parsing data chart", e);
    }

    // 1. CHART TREND (Area Chart)
    const trendChartEl = document.querySelector("#membershipTrendChart");
    if (trendChartEl && chartLabels.length > 0) {
        new ApexCharts(trendChartEl, {
            series: [
                { name: 'Registrasi', data: chartReg },
                { name: 'Renewal', data: chartRenew },
                { name: 'Reaktifasi', data: chartReact }
            ],
            chart: { 
                type: 'area', 
                height: 300, 
                fontFamily: 'Inter, sans-serif', 
                toolbar: { show: false } 
            },
            stroke: { curve: 'smooth', width: 3 },
            fill: { 
                type: 'gradient', 
                gradient: { opacityFrom: 0.4, opacityTo: 0.05 } 
            },
            colors: ['#2563eb', '#10b981', '#f59e0b'],
            xaxis: { 
                categories: chartLabels, 
                labels: { style: { colors: '#9ca3af', fontSize: '11px' } } 
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            tooltip: { y: { formatter: (val) => val + " Member" } }
        }).render();
    }

    // 2. CHART PAKET TERLARIS (Donut Chart - Jika Anda MENGGUNAKAN ELEMEN CHART INI NANTINYA)
    // Berhubung di Blade HTML sebelumnya tidak ada elemen <div id="paketPropChart"></div>, 
    // chart ini saya bungkus dengan 'if' agar tidak error saat element tidak ditemukan.
    const donutChartEl = document.querySelector("#paketPropChart");
    if (donutChartEl) {
        let paketLabels = [];
        let paketValues = [];
        
        try {
            paketLabels = JSON.parse(dataEl.dataset.paketLabels || "[]");
            paketValues = JSON.parse(dataEl.dataset.paketValues || "[]");
        } catch (e) {}

        if (paketLabels.length > 0 && paketValues.length > 0) {
            new ApexCharts(donutChartEl, {
                series: paketValues,
                labels: paketLabels,
                chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: { show: true, label: 'Total', fontSize: '12px', fontWeight: 600 }
                            }
                        }
                    }
                },
                legend: { position: 'bottom', fontSize: '11px' }
            }).render();
        }
    }
});

// INISIALISASI DATATABLES (Jika Anda meng-uncomment tag table di Blade HTML)
$(document).ready(function () {
    const tableEl = $('#membershipTable');
    if (tableEl.length) {
        tableEl.DataTable({
            responsive: true,
            order: [[0, 'desc']],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4"ip>',
        });
    }
});