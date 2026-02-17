<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(function() {
        const salesData = <?= json_encode($last7days ?? []) ?>;

        if (salesData.length > 0) {
            const labels = salesData.map(d => {
                const date = new Date(d.sale_date);
                return date.toLocaleDateString('th-TH', {
                    day: 'numeric',
                    month: 'short'
                });
            });
            const amounts = salesData.map(d => parseFloat(d.total_amount));

            new Chart(document.getElementById('chartDashboard'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'ยอดขาย (บาท)',
                        data: amounts,
                        backgroundColor: 'rgba(78, 115, 223, 0.6)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ฿';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>