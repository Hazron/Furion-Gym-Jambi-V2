document.addEventListener('DOMContentLoaded', function () {
    
    /* ======================================================================
       1. CONFIG & STATE
       ====================================================================== */
    let currentType = 'member'; // Default: member
    let currentPeriod = 'week'; // Default: week
    let chartInstance = null;

    // Warna Grafik
    const chartColors = {
        member: '#2563EB',  // Biru
        revenue: '#10B981', // Hijau
        visit: '#8B5CF6'    // Ungu
    };

    // Ambil data dari Window (yang dikirim dari Blade)
    const dbData = window.dashboardData || {};

    /* ======================================================================
       2. HELPER FUNCTIONS
       ====================================================================== */
    function getLabelType(type) {
        if (type === 'revenue') return 'Pendapatan';
        if (type === 'visit') return 'Kunjungan';
        return 'Member Baru';
    }

    function updateTitles() {
        const titleEl = document.getElementById('chartTitle');
        const subTitleEl = document.getElementById('chartSubtitle');
        
        const titles = {
            'member': 'Statistik Pertumbuhan Member',
            'revenue': 'Grafik Pendapatan',
            'visit': 'Statistik Kunjungan Gym'
        };
        
        const subtitles = {
            'year': 'Laporan Tahun Ini',
            'month': 'Laporan Bulan Ini',
            'week': '7 Hari Terakhir',
            'day': 'Data Hari Ini (Per Jam)'
        };

        if (titleEl) titleEl.innerText = titles[currentType];
        if (subTitleEl) subTitleEl.innerText = subtitles[currentPeriod];
    }

    /* ======================================================================
       3. CHART LOGIC (APEXCHARTS)
       ====================================================================== */
    function renderChart() {
        const chartElement = document.querySelector("#dynamicChart");
        
        // Safety check
        if (!chartElement) return;

        // Cek ketersediaan data
        if (!dbData[currentType] || !dbData[currentType][currentPeriod]) {
            console.warn('Data chart tidak tersedia untuk tipe/periode ini.');
            chartElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm">Data tidak tersedia</div>';
            return;
        }

        const chartSource = dbData[currentType][currentPeriod];
        
        // Konfigurasi Chart
        const options = {
            series: [{
                name: getLabelType(currentType),
                data: chartSource.data || []
            }],
            chart: {
                type: 'area',
                height: 320,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                animations: { enabled: true }
            },
            colors: [chartColors[currentType]],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: chartSource.labels || [],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#9CA3AF', fontSize: '11px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9CA3AF', fontSize: '11px' },
                    formatter: (val) => {
                        if (currentType === 'revenue') {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'jt';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'rb';
                        }
                        return val;
                    }
                }
            },
            grid: {
                borderColor: '#F3F4F6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: (val) => {
                        if (currentType === 'revenue') {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
                        }
                        return val;
                    }
                }
            }
        };

        // Render atau Update
        if (chartInstance) {
            chartInstance.updateOptions(options);
        } else {
            chartInstance = new ApexCharts(chartElement, options);
            chartInstance.render();
        }

        updateTitles();
    }

    /* ======================================================================
       4. GLOBAL FUNCTIONS (EXPOSED TO WINDOW)
       Penting agar onclick="..." di HTML bisa membacanya
       ====================================================================== */

    // Handler Ganti Tipe Data (Member/Revenue/Visit)
    window.handleTypeChange = function() {
        const select = document.getElementById('dataTypeFilter');
        if(select) {
            currentType = select.value;
            // Opsional: Reset ke 'week' saat ganti tipe agar data konsisten
            document.getElementById('timeFilter').value = 'week';
            currentPeriod = 'week';
            
            // Opsional: Enable/Disable opsi 'day' untuk visit
            const optionDay = document.getElementById('optionDay');
            if(optionDay) {
                optionDay.disabled = (currentType === 'visit'); 
            }
            
            renderChart();
        }
    };

    // Handler Ganti Waktu (Year/Month/Week/Day)
    window.updateChart = function() {
        const select = document.getElementById('timeFilter');
        if(select) {
            currentPeriod = select.value;
            renderChart();
        }
    };

    // Modal Logic (Buka)
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById('modalBackdrop'); // Pastikan ID ini ada di HTML jika pakai backdrop terpisah
        if(modal) {
            modal.classList.remove('hidden');
        }
    };

    // Modal Logic (Tutup)
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) modal.classList.add('hidden');
    };

    // Sidebar Mobile Toggle
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('logo-sidebar'); // Pastikan ID sidebar benar (biasanya di layout navbar)
        const backdrop = document.getElementById('sidebar-backdrop');

        const targetSidebar = sidebar || document.querySelector('aside'); 

        if (targetSidebar) {
            targetSidebar.classList.toggle('-translate-x-full');
            if(backdrop) {
                backdrop.classList.toggle('hidden');
                backdrop.classList.toggle('opacity-0');
            }
        } else {
            console.error('Sidebar element not found. Check ID "logo-sidebar"');
        }
    }

    /* ======================================================================
       5. INIT
       ====================================================================== */
    renderChart();
});