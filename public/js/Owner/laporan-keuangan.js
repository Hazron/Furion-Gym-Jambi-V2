$(document).ready(function () {
    const dataContainer = document.getElementById("report-data-container");

    if (dataContainer) {
        const rawDates = JSON.parse(dataContainer.dataset.chartLabels);
        const rawTotals = JSON.parse(dataContainer.dataset.chartValues);
        const totalMember = parseFloat(dataContainer.dataset.totalMember);
        const totalJual = parseFloat(dataContainer.dataset.totalJual);
        const totalAll = totalMember + totalJual;

        // Inisialisasi DataTables
        $("#transactionTable").DataTable({
            // ... (Konfigurasi DataTables) ...
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali",
                },
                emptyTable: "Tidak ada data transaksi yang tersedia",
            },
            order: [[0, "desc"]],
            pageLength: 10,
            columnDefs: [{ orderable: false, targets: [4] }],
        });

        // --- 2. RENDER GRAFIK TREN (LINE/AREA) ---
        var incomeChartEl = document.querySelector("#incomeChart");
        if (incomeChartEl && rawTotals && rawTotals.length > 0) {
            var chartOptions = {
                series: [{ name: "Pendapatan", data: rawTotals }],
                chart: {
                    /* ... (Konfigurasi Chart) ... */ type: "area",
                    height: 350,
                    fontFamily: "Inter, sans-serif",
                    toolbar: { show: false },
                },
                dataLabels: { enabled: false },
                stroke: { curve: "smooth", width: 3, colors: ["#00D2FF"] },
                xaxis: {
                    categories: rawDates,
                    labels: { style: { fontSize: "11px" } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            if (value >= 1000000)
                                return (value / 1000000).toFixed(1) + " Jt";
                            if (value >= 1000)
                                return (value / 1000).toFixed(0) + " Rb";
                            return value;
                        },
                    },
                },
                colors: ["#00D2FF"],
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100],
                        colorStops: [
                            { offset: 0, color: "#00D2FF", opacity: 0.6 },
                            { offset: 100, color: "#000AFF", opacity: 0.1 },
                        ],
                    },
                },
                grid: {
                    borderColor: "#f1f5f9",
                    strokeDashArray: 4,
                    padding: { left: 10 },
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return (
                                "Rp " +
                                new Intl.NumberFormat("id-ID").format(val)
                            );
                        },
                    },
                },
            };
            new ApexCharts(incomeChartEl, chartOptions).render();
        }

        // --- 3. RENDER DONUT CHART ---
        var sourceChartEl = document.querySelector("#sourceChart");
        if (sourceChartEl && totalAll > 0) {
            var donutOptions = {
                series: [totalMember, totalJual],
                labels: ["Membership", "Penjualan"],
                chart: {
                    type: "donut",
                    height: 320,
                    fontFamily: "Inter, sans-serif",
                },
                colors: ["#00D2FF", "#000AFF"],
                plotOptions: {
                    pie: {
                        donut: {
                            size: "75%",
                            labels: { show: true, value: { fontSize: "22px" } },
                        },
                    },
                },
                legend: { show: false },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return (
                                "Rp " +
                                new Intl.NumberFormat("id-ID").format(val)
                            );
                        },
                    },
                },
            };
            new ApexCharts(sourceChartEl, donutOptions).render();
        }
    }
});
