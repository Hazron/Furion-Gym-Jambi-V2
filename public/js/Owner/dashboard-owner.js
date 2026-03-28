let chartInstance = null;
const allChartData = window.dashboardData || {}; 

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const backdrop = modal.querySelector('#modalBackdrop');

    modal.classList.remove('hidden');
    setTimeout(() => {
        if (backdrop) backdrop.classList.remove('opacity-0');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const backdrop = modal.querySelector('#modalBackdrop');

    if (backdrop) backdrop.classList.add('opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

document.addEventListener('keydown', function (event) {
    if (event.key === "Escape") {
        const openModals = document.querySelectorAll('[role="dialog"]:not(.hidden)');
        openModals.forEach(modal => closeModal(modal.id));
    }
});

document.addEventListener("DOMContentLoaded", function () {
    if (!window.dashboardData) return;
    
    checkDayOptionAvailability();
    initChart();
});

function initChart() {
    const chartContainer = document.querySelector("#dynamicChart");
    if (!chartContainer) return;

    const options = getChartOptions('member', 'week');
    chartInstance = new ApexCharts(chartContainer, options);
    
    chartInstance.render().then(() => {
        updateChart();
    });
}

function updateChart() {
    if (!chartInstance) return;

    const type = document.getElementById('dataTypeFilter').value;
    const time = document.getElementById('timeFilter').value;

    updateLabels(type, time);

    if (!allChartData[time] || !allChartData[time][type]) {
        chartInstance.updateSeries([{ name: 'No Data', data: [] }]);
        return;
    }

    const newData = allChartData[time][type];
    const newLabels = allChartData[time]['labels'];

    chartInstance.updateOptions({
        xaxis: { categories: newLabels },
        colors: [getColor(type)],
        ...getYAxisOptions(type)
    }, false, true);

    chartInstance.updateSeries([{
        name: type === 'revenue' ? 'Pendapatan' : (type === 'visit' ? 'Kunjungan' : 'Member'),
        data: newData
    }]);
}

function getColor(type) {
    if (type === 'revenue') return '#10b981';
    if (type === 'visit') return '#8b5cf6';
    return '#3b82f6';
}

function getYAxisOptions(type) {
    return {
        yaxis: {
            labels: {
                style: { colors: '#6b7280', fontSize: '12px' },
                formatter: function (val) {
                    if (type === 'revenue') {
                        if (val >= 1000000) return (val / 1000000).toFixed(1) + " Jt";
                        if (val >= 1000) return (val / 1000).toFixed(0) + " Rb";
                    }
                    return val.toFixed(0);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    if (type === 'revenue') return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    return val + " Orang";
                }
            }
        }
    };
}

function getChartOptions(type, time) {
    return {
        series: [{ name: 'Total', data: [] }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: [],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        },
        ...getYAxisOptions(type)
    };
}

function updateLabels(type, time) {
    const titles = { 'member': 'Pertumbuhan Member', 'revenue': 'Total Pendapatan', 'visit': 'Kunjungan Gym' };
    const subtitles = { 'year': 'Data Tahunan', 'month': 'Data Bulanan', 'week': '7 Hari Terakhir', 'day': 'Data Per Jam' };

    document.getElementById('chartTitle').innerText = "Statistik " + titles[type];
    document.getElementById('chartSubtitle').innerText = subtitles[time];
}

function handleTypeChange() {
    checkDayOptionAvailability();
    updateChart();
}

function checkDayOptionAvailability() {
    const typeFilter = document.getElementById('dataTypeFilter');
    const timeFilter = document.getElementById('timeFilter');
    const dayOption = document.getElementById('optionDay');

    if (!typeFilter || !dayOption) return;

    if (typeFilter.value === 'visit') {
        dayOption.disabled = false;
        dayOption.innerText = "Hari Ini (Jam)";
    } else {
        dayOption.disabled = true;
        dayOption.innerText = "Hari Ini (Khusus Kunjungan)";
        if (timeFilter.value === 'day') timeFilter.value = 'week';
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('logo-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar || !backdrop) return;

    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('opacity-0');
        setTimeout(() => backdrop.classList.add('hidden'), 300);
    }
}