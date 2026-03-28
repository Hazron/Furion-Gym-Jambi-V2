document.addEventListener('DOMContentLoaded', function() {
    const dbData = window.chartData;

    if (!dbData) {
        console.error("Chart Error: Data 'window.chartData' tidak ditemukan. Pastikan sudah didefinisikan di Blade.");
        return;
    }

    let chart; 

    function checkFilters() {
        const topic = document.getElementById('chartTopicFilter').value;
        const optToday = document.getElementById('optToday');

        optToday.disabled = false; 
    }

    //ApexCharts
    function generateChartData(topic, period) {
        let categories = [],
            data = [],
            name = '',
            color = '',
            type = 'area',
            unit = '',
            title = '';

        // --- A. DATA KUNJUNGAN (VISITS) ---
        if (topic === 'visits') {
            title = 'Statistik Kunjungan Gym';
            name = 'Total Visit';
            color = '#8B5CF6';
            unit = ' Orang';
            type = 'bar'; 

            if (period === 'today') {
                categories = dbData.today.labels;
                data = dbData.today.visits;
            } else if (period === '7days') {
                categories = dbData.week.labels;
                data = dbData.week.visits;
            } else { // 30days
                categories = dbData.month.labels;
                data = dbData.month.visits;
            }
        }

        // --- B. DATA MEMBERS ---
        else if (topic === 'members') {
            title = 'Statistik Pertumbuhan Member';
            name = 'Member Baru';
            color = '#2563EB'; // Biru
            unit = ' Orang';

            if (period === 'today') {
                categories = dbData.today.labels;
                data = dbData.today.members || [];
            } else if (period === '7days') {
                categories = dbData.week.labels;
                data = dbData.week.members;
            } else {
                categories = dbData.month.labels;
                data = dbData.month.members;
            }
        }

        // --- C. DATA REVENUE ---
        else if (topic === 'revenue') {
            title = 'Total Revenue';
            name = 'Pemasukan';
            color = '#10B981'; // Hijau
            unit = '';

            if (period === 'today') {
                type = 'bar';
                categories = dbData.today.labels;
                data = dbData.today.revenue;
            } else if (period === '7days') {
                type = 'line';
                categories = dbData.week.labels;
                data = dbData.week.revenue;
            } else {
                type = 'area';
                categories = dbData.month.labels;
                data = dbData.month.revenue;
            }
        }

        return { categories, data, name, color, type, unit, title };
    }

    function renderChart() {
        checkFilters();
        
        const topicElement = document.getElementById('chartTopicFilter');
        const periodElement = document.getElementById('chartPeriodFilter');

        if (!topicElement || !periodElement) return;

        const topic = topicElement.value;
        const period = periodElement.value;
        const config = generateChartData(topic, period);

        // Update Judul di HTML
        const titleEl = document.getElementById('chartTitle');
        const subTitleEl = document.getElementById('chartSubtitle');
        
        if(titleEl) titleEl.innerText = config.title;
        
        if(subTitleEl) {
            let subtitleText = '';
            if (period === 'today') subtitleText = 'Data per jam (07:00 - 22:00)';
            else if (period === '7days') subtitleText = '7 Hari Terakhir';
            else subtitleText = '30 Hari Terakhir';
            subTitleEl.innerText = subtitleText;
        }

        const options = {
            series: [{
                name: config.name,
                data: config.data
            }],
            chart: {
                id: 'mainChart',
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
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (val) => {
                        if (topic === 'revenue') {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'jt';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                            return val;
                        }
                        return val;
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

        const chartElement = document.querySelector("#chartMemberGrowth");
        if (chartElement) {
            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(chartElement, options);
                chart.render();
            }
        }
    }

    const topicFilter = document.getElementById('chartTopicFilter');
    const periodFilter = document.getElementById('chartPeriodFilter');

    if (topicFilter) topicFilter.addEventListener('change', renderChart);
    if (periodFilter) periodFilter.addEventListener('change', renderChart);

    renderChart();
});