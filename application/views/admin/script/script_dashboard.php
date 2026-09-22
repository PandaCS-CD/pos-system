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

        // AI Dashboard Widget Refresh
        $('#btnDashboardAiRefresh').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังวิเคราะห์...');
            $('#dashboardAiEmpty').addClass('d-none');
            $('#dashboardAiContent').addClass('d-none');
            $('#dashboardAiLoading').removeClass('d-none');

            $.ajax({
                url: '<?= admin_url("aiAssistant/ajax_get_dashboard_card") ?>',
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-wand-magic-sparkles me-1"></i> วิเคราะห์ใหม่อีกครั้ง');
                    $('#dashboardAiLoading').addClass('d-none');

                    if (res.success) {
                        let html = res.summary
                            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                            .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
                            .replace(/^\s*[\-\*]\s+(.*$)/gim, '<div class="mb-1"><i class="fas fa-check-circle text-primary me-2"></i>$1</div>')
                            .replace(/\n/gim, '<br>');
                        $('#dashboardAiContent').html(html).removeClass('d-none');
                    } else {
                        $('#dashboardAiEmpty').removeClass('d-none');
                        if (res.needs_key) {
                            Swal.fire({
                                icon: 'info',
                                title: 'ต้องตั้งค่า API Key',
                                text: 'กรุณาตั้งค่า Google Gemini API Key ที่หน้า AI Assistant ก่อนเริ่มใช้งาน',
                                confirmButtonText: 'ไปหน้าตั้งค่า AI',
                                showCancelButton: true,
                                cancelButtonText: 'ปิด'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '<?= admin_url("aiAssistant") ?>';
                                }
                            });
                        } else {
                            Swal.fire('ข้อผิดพลาด', res.message, 'error');
                        }
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-wand-magic-sparkles me-1"></i> ให้ AI วิเคราะห์วันนี้');
                    $('#dashboardAiLoading').addClass('d-none');
                    $('#dashboardAiEmpty').removeClass('d-none');
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อระบบได้ (' + xhr.status + ')', 'error');
                }
            });
        });
    });
</script>