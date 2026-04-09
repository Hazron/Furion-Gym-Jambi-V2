document.addEventListener("DOMContentLoaded", function () {
    const dataEl = document.getElementById('chartData');
    
    // Guard Clause: Berhenti jika elemen data tidak ditemukan
    if (!dataEl) {
        console.warn("Elemen #chartData tidak ditemukan di halaman ini.");
        return;
    }
    const parseData = (key) => {
        try {
            const data = dataEl.getAttribute(key);
            return data ? JSON.parse(data) : [];
        } catch (e) {
            console.error(`Gagal mem-parsing data untuk kunci: ${key}`, e);
            return [];
        }
    };

    // Ambil semua data dari Blade
    const chartLabels = parseData('data-labels');
    const chartReg    = parseData('data-reg');
    const chartRenew  = parseData('data-renew');
    const chartReact  = parseData('data-react');
    
    // Data untuk Paket (jika menggunakan donut chart)
    const paketLabels = parseData('data-paket-labels');
    const paketValues = parseData('data-paket-values');

    // --- 1. INISIALISASI AREA CHART (TREN MEMBERSHIP) ---
    const trendChartEl = document.querySelector("#membershipTrendChart");
    if (trendChartEl && chartLabels.length > 0) {
        const trendOptions = {
            series: [
                { name: 'Registrasi', data: chartReg },
                { name: 'Renewal', data: chartRenew },
                { name: 'Reaktifasi', data: chartReact }
            ],
            chart: { 
                type: 'area', 
                height: 320, 
                fontFamily: 'Inter, sans-serif', 
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            stroke: { curve: 'smooth', width: 3 },
            fill: { 
                type: 'gradient', 
                gradient: { 
                    shadeIntensity: 1, 
                    opacityFrom: 0.45, 
                    opacityTo: 0.05,
                    stops: [20, 100] 
                } 
            },
            colors: ['#4f46e5', '#10b981', '#f59e0b'], // Indigo, Emerald, Amber
            xaxis: { 
                categories: chartLabels, 
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { 
                    rotate: -45,
                    rotateAlways: false,
                    style: { colors: '#9ca3af', fontSize: '11px' } 
                } 
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px' },
                    formatter: (val) => val.toFixed(0)
                }
            },
            grid: { 
                borderColor: '#f3f4f6', 
                strokeDashArray: 4,
                padding: { left: 10, right: 10 }
            },
            markers: { size: 4, strokeWidth: 2, hover: { size: 6 } },
            tooltip: { 
                shared: true,
                intersect: false,
                y: { formatter: (val) => val + " Member" } 
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontWeight: 600,
                markers: { radius: 12 }
            }
        };

        const trendChart = new ApexCharts(trendChartEl, trendOptions);
        trendChart.render();
    } else {
        console.log("Trend chart tidak dirender: Label kosong atau elemen tidak ditemukan.");
    }

    // --- 2. INISIALISASI DONUT CHART (JIKA ADA ELEMENNYA) ---
    const donutChartEl = document.querySelector("#paketPropChart");
    if (donutChartEl && paketValues.length > 0) {
        const donutOptions = {
            series: paketValues,
            labels: paketLabels,
            chart: { type: 'donut', height: 300, fontFamily: 'Inter, sans-serif' },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: { 
                                show: true, 
                                label: 'Total', 
                                fontSize: '14px', 
                                fontWeight: 700,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom' }
        };

        const donutChart = new ApexCharts(donutChartEl, donutOptions);
        donutChart.render();
    }
});

$(document).ready(function () {
    const tableEl = $('#membershipTable');
    if (tableEl.length) {
        tableEl.DataTable({
            responsive: true,
            order: [[0, 'desc']], // Urutan berdasarkan kolom pertama (biasanya tanggal/ID)
            language: { 
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' 
            },
            dom: '<"flex flex-col sm:flex-row justify-between items-center gap-4 mb-4"lf>rt<"flex flex-col sm:flex-row justify-between items-center gap-4 mt-4"ip>',
        });
    }
});