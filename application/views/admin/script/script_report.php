<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(function() {
        // ✅ Sync flatpickr date range → hidden inputs
        var dateRangeEl = document.getElementById('dateRange');
        if (dateRangeEl && dateRangeEl._flatpickr) {
            dateRangeEl._flatpickr.destroy();
        }
        if (dateRangeEl) {
            flatpickr(dateRangeEl, {
                allowInput: true,
                enableTime: false,
                dateFormat: "d-m-Y",
                mode: "range",
                locale: "en",
                disableMobile: true,
                defaultDate: ["<?= date('d-m-Y', strtotime($date_from)) ?>", "<?= date('d-m-Y', strtotime($date_to)) ?>"],
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        var fmt = function(d) {
                            return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
                        };
                        document.getElementById('dateFrom').value = fmt(selectedDates[0]);
                        document.getElementById('dateTo').value = fmt(selectedDates[1]);
                    }
                }
            });
        }
        // Chart.js - ยอดขาย 7 วันล่าสุด
        const salesData = <?= json_encode($last7days) ?>;
        const labels = salesData.map(d => {
            const date = new Date(d.sale_date);
            return date.toLocaleDateString('th-TH', {
                day: 'numeric',
                month: 'short'
            });
        });
        const amounts = salesData.map(d => parseFloat(d.total_amount));
        const bills = salesData.map(d => parseInt(d.total_bills));

        new Chart(document.getElementById('chartSales7Days'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: amounts,
                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'จำนวนบิล',
                    data: bills,
                    type: 'line',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    backgroundColor: 'rgba(28, 200, 138, 0.2)',
                    tension: 0.3,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' ฿';
                            }
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>