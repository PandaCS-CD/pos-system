<script>
$(function() {
    let currentPeriod = 'today';
    let chatHistory = [];

    // Helper: แปลงข้อความ Markdown เป็น HTML แบบอ่านง่าย
    function renderMarkdown(text) {
        if (!text) return '';
        let html = text
            // Escape HTML tags
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            // Headers
            .replace(/^### (.*$)/gim, '<h6 class="fw-bold mt-3 mb-1 text-primary">$1</h6>')
            .replace(/^## (.*$)/gim, '<h5 class="fw-bold mt-3 mb-2 text-dark">$1</h5>')
            .replace(/^# (.*$)/gim, '<h4 class="fw-bold mt-3 mb-2 text-dark">$1</h4>')
            // Bold
            .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
            // Italic
            .replace(/\*(.*?)\*/gim, '<em>$1</em>')
            // Bullet points
            .replace(/^\s*[\-\*]\s+(.*$)/gim, '<li class="ms-3 mb-1">$1</li>')
            // Numbered lists
            .replace(/^\s*(\d+)\.\s+(.*$)/gim, '<div class="ms-3 mb-1"><span class="badge bg-light text-dark me-1">$1</span> $2</div>')
            // Line breaks
            .replace(/\n/gim, '<br>');

        // Wrap loose <li> in <ul>
        html = html.replace(/(<li.*<\/li>)/gms, '<ul class="list-unstyled mb-2">$1</ul>');
        return html;
    }

    // เลือกระยะเวลา (Period)
    $('#periodSelectors .btn').on('click', function() {
        $('#periodSelectors .btn').removeClass('active');
        $(this).addClass('active');
        currentPeriod = $(this).data('period');
    });

    // ดึงบทวิเคราะห์ภาพรวมธุรกิจ
    $('#btnRunAnalysis').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังวิเคราะห์...');
        $('#insightEmptyState').addClass('d-none');
        $('#insightContent').addClass('d-none');
        $('#insightLoading').removeClass('d-none');

        $.ajax({
            url: '<?= admin_url("aiAssistant/ajax_get_insights") ?>',
            type: 'POST',
            dataType: 'json',
            data: { period: currentPeriod },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-wand-magic-sparkles me-1"></i> วิเคราะห์ข้อมูลเดี๋ยวนี้');
                $('#insightLoading').addClass('d-none');

                if (res.success) {
                    // อัพเดทตัวเลข KPI ด้านบน
                    if (res.context && res.context.summary) {
                        const sum = res.context.summary;
                        $('#kpiRevenue').text(parseFloat(sum.total_revenue).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ฿');
                        $('#kpiProfit').text(parseFloat(sum.total_profit).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ฿');
                        $('#kpiBills').text(parseInt(sum.total_bills).toLocaleString());
                    }

                    // แสดงผลบทวิเคราะห์
                    $('#insightContent').html(renderMarkdown(res.insights)).removeClass('d-none');
                } else {
                    $('#insightEmptyState').removeClass('d-none');
                    if (res.needs_key) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ยังไม่ได้ตั้งค่า API Key',
                            text: res.message,
                            confirmButtonText: 'ตั้งค่า API Key ตอนนี้',
                            showCancelButton: true,
                            cancelButtonText: 'ปิด'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalApiKey').modal('show');
                            }
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', res.message, 'error');
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-wand-magic-sparkles me-1"></i> วิเคราะห์ข้อมูลเดี๋ยวนี้');
                $('#insightLoading').addClass('d-none');
                $('#insightEmptyState').removeClass('d-none');
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ (' + xhr.status + ')', 'error');
            }
        });
    });

    // เลื่อนกล่องแชทลงล่างสุด
    function scrollChatBottom() {
        const chatBox = document.getElementById('chatMessages');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // ส่งข้อความแชท
    function sendMessage(text) {
        if (!text || text.trim() === '') return;

        // แสดงกล่องข้อความของผู้ใช้
        const userHtml = `<div class="chat-bubble user"><strong>คุณ:</strong> ${escapeHtml(text)}</div>`;
        $('#chatMessages').append(userHtml);
        $('#chatInput').val('');
        scrollChatBottom();

        // แสดงสถานะ AI กำลังคิด
        $('#chatTyping').removeClass('d-none');
        $('#btnSendChat').prop('disabled', true);

        // เก็บลงประวัติ
        chatHistory.push({ role: 'user', content: text });

        $.ajax({
            url: '<?= admin_url("aiAssistant/ajax_ask_chat") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                message: text,
                period: currentPeriod,
                history: JSON.stringify(chatHistory.slice(-6)) // ส่ง 6 ข้อความล่าสุด
            },
            success: function(res) {
                $('#chatTyping').addClass('d-none');
                $('#btnSendChat').prop('disabled', false);

                if (res.success) {
                    const aiHtml = `<div class="chat-bubble ai"><strong><i class="fas fa-robot text-primary me-1"></i> AI ที่ปรึกษา:</strong><br>${renderMarkdown(res.reply)}</div>`;
                    $('#chatMessages').append(aiHtml);
                    chatHistory.push({ role: 'model', content: res.reply });
                    scrollChatBottom();
                } else {
                    if (res.needs_key) {
                        $('#modalApiKey').modal('show');
                    }
                    const errHtml = `<div class="chat-bubble ai text-danger"><strong><i class="fas fa-exclamation-circle me-1"></i> เกิดข้อผิดพลาด:</strong> ${res.message}</div>`;
                    $('#chatMessages').append(errHtml);
                    scrollChatBottom();
                }
            },
            error: function(xhr) {
                $('#chatTyping').addClass('d-none');
                $('#btnSendChat').prop('disabled', false);
                const errHtml = `<div class="chat-bubble ai text-danger"><i class="fas fa-times-circle me-1"></i> ไม่สามารถเชื่อมต่อกับระบบได้ (${xhr.status})</div>`;
                $('#chatMessages').append(errHtml);
                scrollChatBottom();
            }
        });
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    // ฟอร์มส่งแชท
    $('#formChat').on('submit', function(e) {
        e.preventDefault();
        const text = $('#chatInput').val();
        sendMessage(text);
    });

    // คลิกปุ่มคำถามด่วน (Quick Chips)
    $('.quick-chip').on('click', function() {
        const query = $(this).data('query');
        $('#chatInput').val(query);
        sendMessage(query);
    });

    // ปุ่มล้างแชท
    $('#btnClearChat').on('click', function() {
        chatHistory = [];
        $('#chatMessages').html(`
            <div class="chat-bubble ai">
                <strong><i class="fas fa-robot me-1 text-primary"></i> AI ที่ปรึกษา:</strong><br>
                ยินดีต้อนรับครับ เริ่มต้นถามคำถามใหม่ได้ทันทีเลยครับ
            </div>
        `);
    });

    // บันทึก API Key
    $('#btnSaveApiKey').on('click', function() {
        const key = $('#inputApiKey').val().trim();
        if (!key) {
            Swal.fire('ข้อความแจ้งเตือน', 'กรุณากรอก API Key', 'warning');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

        $.ajax({
            url: '<?= admin_url("aiAssistant/ajax_save_key") ?>',
            type: 'POST',
            dataType: 'json',
            data: { api_key: key },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> บันทึก API Key');
                if (res.success) {
                    $('#modalApiKey').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message, 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> บันทึก API Key');
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกได้ (' + xhr.status + ')', 'error');
            }
        });
    });
});
</script>
