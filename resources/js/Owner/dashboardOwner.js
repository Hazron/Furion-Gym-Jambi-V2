document.addEventListener('DOMContentLoaded', function() {
    const dbData = window.dashboardData;

    if (!dbData) {
        console.error("Chart Error: Data 'window.dashboardData' tidak ditemukan. Pastikan sudah didefinisikan di Blade.");
        return;
    }

    let chart; 

    function checkFilters() {
        const optionDay = document.getElementById('optionDay');
        if (optionDay) {
            optionDay.disabled = false; // Aktifkan opsi jam
        }
    }

    function generateChartData(topic, period) {
        let categories = [],
            data = [],
            name = '',
            color = '',
            type = 'area',
            unit = '',
            title = '';

        // Ambil data dari object dbData Owner
        const source = dbData[period];
        if (source) {
            categories = source.labels || [];
            data = source[topic] || [];
        }

        // --- A. DATA KUNJUNGAN (VISIT) ---
        if (topic === 'visit') {
            title = 'Statistik Kunjungan Gym';
            name = 'Total Visit';
            color = '#8B5CF6'; // Ungu
            unit = ' Orang';
            type = 'bar'; // Visit selalu Bar
        }

        // --- B. DATA MEMBER ---
        else if (topic === 'member') {
            title = 'Statistik Pertumbuhan Member';
            name = 'Member Baru';
            color = '#2563EB'; // Biru
            unit = ' Orang';
            type = 'area'; // Member selalu Area
        }

        // --- C. DATA REVENUE ---
        else if (topic === 'revenue') {
            title = 'Total Revenue';
            name = 'Pemasukan';
            color = '#10B981'; // Hijau
            unit = '';

            // Revenue berubah-ubah bentuk sesuai waktu seperti Admin
            if (period === 'day') type = 'bar';
            else if (period === 'week') type = 'line';
            else type = 'area';
        }

        return { categories, data, name, color, type, unit, title };
    }

    function renderChart() {
        checkFilters();
        
        const topicElement = document.getElementById('dataTypeFilter');
        const periodElement = document.getElementById('timeFilter');

        if (!topicElement || !periodElement) return;

        const topic = topicElement.value;
        const period = periodElement.value;
        const config = generateChartData(topic, period);

        // Update Judul & Subjudul di HTML
        const titleEl = document.getElementById('chartTitle');
        const subTitleEl = document.getElementById('chartSubtitle');
        
        if(titleEl) titleEl.innerText = config.title;
        
        if(subTitleEl) {
            let subtitleText = '';
            if (period === 'day') subtitleText = 'Data per jam (06:00 - 23:00)';
            else if (period === 'week') subtitleText = '7 Hari Terakhir';
            else if (period === 'month') subtitleText = 'Laporan Bulan Ini';
            else subtitleText = 'Laporan Tahun Ini';
            subTitleEl.innerText = subtitleText;
        }

        // Cek jika data kosong untuk hindari bug Y-axis "nyangkut" di angka jutaan
        const isAllZero = config.data.length > 0 && config.data.every(val => val === 0);

        const options = {
            series: [{
                name: config.name,
                data: config.data
            }],
            chart: {
                id: 'mainChart', // ID yang sama dengan Admin agar animasinya identik
                type: config.type,
                height: 320,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                animations: { enabled: true }
            },
            colors: [config.color],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: config.categories,
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                max: isAllZero ? 10 : undefined, // Memperbaiki bug sumbu Y kosong
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (val) => {
                        if (topic === 'revenue') {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'jt';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                            return val;
                        }
                        return Math.round(val);
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: (val) => {
                        if (topic === 'revenue') {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(val);
                        }
                        return val + config.unit;
                    }
                }
            }
        };

        const chartElement = document.querySelector("#dynamicChart");
        if (chartElement) {
            if (chart) {
                // Update pakai cara Admin agar transisi & animasinya 100% sama
                chart.updateOptions(options, false, true, true);
            } else {
                chart = new ApexCharts(chartElement, options);
                chart.render();
            }
        }
    }

    // Listener persis seperti Admin
    const topicFilter = document.getElementById('dataTypeFilter');
    const periodFilter = document.getElementById('timeFilter');

    if (topicFilter) topicFilter.addEventListener('change', renderChart);
    if (periodFilter) periodFilter.addEventListener('change', renderChart);

    // Render pertama kali
    renderChart();

    // =========================================================
    // Fungsi Global (Karena Blade Owner masih pakai onclick="...")
    // =========================================================
    window.handleTypeChange = function() {
        renderChart();
    };
    window.updateChart = function() {
        renderChart();
    };
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) modal.classList.remove('hidden');
    };
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) modal.classList.add('hidden');
    };
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('logo-sidebar') || document.querySelector('aside');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar) sidebar.classList.toggle('-translate-x-full');
        if (backdrop) {
            backdrop.classList.toggle('hidden');
            backdrop.classList.toggle('opacity-0');
        }
    };
});