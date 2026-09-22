<style>
    .ai-gradient-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
    }
    .ai-pill-btn {
        border-radius: 20px;
        font-size: 0.85rem;
        padding: 6px 14px;
        transition: all 0.2s ease;
    }
    .ai-insight-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        min-height: 250px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 520px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8fafc;
    }
    .chat-bubble {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 14px;
        margin-bottom: 14px;
        font-size: 0.92rem;
        line-height: 1.55;
        word-wrap: break-word;
    }
    .chat-bubble.user {
        background: #4f46e5;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 3px;
    }
    .chat-bubble.ai {
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        margin-right: auto;
        border-bottom-left-radius: 3px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .chat-bubble.ai strong {
        color: #4338ca;
    }
    .quick-chip {
        display: inline-block;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.8rem;
        cursor: pointer;
        margin: 3px 4px 3px 0;
        transition: all 0.2s;
    }
    .quick-chip:hover {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    .typing-indicator span {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #94a3b8;
        border-radius: 50%;
        margin-right: 3px;
        animation: typing 1.4s infinite both;
    }
    .typing-indicator span:nth-child(2) { animation-delay: .2s; }
    .typing-indicator span:nth-child(3) { animation-delay: .4s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    .kpi-mini-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
    }
    .kpi-mini-card .val {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }
    .kpi-mini-card .lbl {
        font-size: 0.75rem;
        color: #64748b;
    }
</style>

<section>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="ai-gradient-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="badge bg-white text-dark px-3 py-2 mb-2" style="border-radius: 20px;">
                            <i class="fas fa-sparkles text-warning me-1"></i> Generative AI Retail Analytics
                        </span>
                        <h2 class="mb-1 fw-bold text-white"><i class="fas fa-brain me-2"></i>AI วิเคราะห์ธุรกิจอัจฉริยะ</h2>
                        <p class="mb-0 text-white-50" style="font-size:0.95rem;">
                            ประมวลผลข้อมูลการขาย กำไร สต๊อก และแปลงเป็นคำแนะนำเชิงกลยุทธ์สำหรับร้านของคุณด้วย Google Gemini
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($has_api_key): ?>
                            <span class="badge bg-success bg-opacity-75 text-white px-3 py-2" style="border-radius: 20px;">
                                <i class="fas fa-check-circle me-1"></i> เชื่อมต่อ Gemini แล้ว (<?= $masked_api_key ?>)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-3 py-2" style="border-radius: 20px;">
                                <i class="fas fa-exclamation-triangle me-1"></i> ยังไม่ได้ตั้งค่า API Key
                            </span>
                        <?php endif; ?>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalApiKey" style="border-radius: 14px; font-weight: 600;">
                            <i class="fas fa-key me-1"></i> ตั้งค่า API Key
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- คอลัมน์ซ้าย: รายงานวิเคราะห์ภาพรวมและกลยุทธ์เชิงลึก -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-chart-pie text-primary me-2"></i>รายงานวิเคราะห์ภาพรวมธุรกิจ
                            </h5>
                            <button class="btn btn-primary btn-sm ai-pill-btn shadow-sm" id="btnRunAnalysis">
                                <i class="fas fa-wand-magic-sparkles me-1"></i> วิเคราะห์ข้อมูลเดี๋ยวนี้
                            </button>
                        </div>
                        <div class="mt-3 d-flex flex-wrap gap-1" id="periodSelectors">
                            <button class="btn btn-outline-primary btn-sm ai-pill-btn active" data-period="today">วันนี้</button>
                            <button class="btn btn-outline-primary btn-sm ai-pill-btn" data-period="yesterday">เมื่อวาน</button>
                            <button class="btn btn-outline-primary btn-sm ai-pill-btn" data-period="last7days">7 วันล่าสุด</button>
                            <button class="btn btn-outline-primary btn-sm ai-pill-btn" data-period="last30days">30 วันล่าสุด</button>
                            <button class="btn btn-outline-primary btn-sm ai-pill-btn" data-period="this_month">เดือนนี้</button>
                        </div>
                    </div>

                    <div class="card-body px-4 pt-2">
                        <!-- KPI Strip -->
                        <div class="row g-2 mb-3 mt-1" id="kpiStrip">
                            <div class="col-4">
                                <div class="kpi-mini-card">
                                    <div class="val text-primary" id="kpiRevenue"><?= number_format($today_context['summary']['total_revenue'], 2) ?> ฿</div>
                                    <div class="lbl">ยอดขาย</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="kpi-mini-card">
                                    <div class="val text-success" id="kpiProfit"><?= number_format($today_context['summary']['total_profit'], 2) ?> ฿</div>
                                    <div class="lbl">กำไรขั้นต้น (<?= $today_context['summary']['profit_margin'] ?>%)</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="kpi-mini-card">
                                    <div class="val text-info" id="kpiBills"><?= number_format($today_context['summary']['total_bills']) ?></div>
                                    <div class="lbl">จำนวนบิล</div>
                                </div>
                            </div>
                        </div>

                        <!-- ผลการวิเคราะห์จาก AI -->
                        <div class="ai-insight-box" id="insightBox">
                            <div class="text-center text-muted py-5" id="insightEmptyState">
                                <i class="fas fa-robot fa-3x mb-3 text-indigo opacity-50" style="color:#6366f1;"></i>
                                <h6 class="fw-bold">พร้อมให้ AI ช่วยประเมินร้านของคุณ</h6>
                                <p class="small text-muted mb-3">กดปุ่ม "วิเคราะห์ข้อมูลเดี๋ยวนี้" เพื่อให้ Gemini สรุปยอดขาย จุดเสี่ยงสต๊อก และกลยุทธ์เพิ่มกำไร</p>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="$('#btnRunAnalysis').click()">
                                    <i class="fas fa-play me-1"></i> เริ่มการวิเคราะห์
                                </button>
                            </div>
                            <div id="insightLoading" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <h6 class="fw-bold text-dark">AI กำลังสังเคราะห์ข้อมูลร้านค้าของคุณ...</h6>
                                <p class="small text-muted mb-0">กำลังคำนวณอัตรากำไร ตรวจสอบความถี่ของสินค้า และจับคู่กลยุทธ์</p>
                            </div>
                            <div id="insightContent" class="d-none" style="line-height: 1.7; font-size: 0.95rem;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- คอลัมน์ขวา: ห้องแชทถาม-ตอบกับ AI เกี่ยวกับข้อมูลร้านค้า -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-comments text-success me-2"></i>ถาม-ตอบอัจฉริยะกับ AI (Business Chat)
                            </h5>
                            <button class="btn btn-outline-secondary btn-sm" id="btnClearChat" style="border-radius: 12px;" title="ล้างประวัติการสนทนา">
                                <i class="fas fa-rotate-right me-1"></i> ล้างแชท
                            </button>
                        </div>
                        <p class="text-muted small mb-0 mt-1">สอบถามเกี่ยวกับยอดขาย สินค้า และคำแนะนำเฉพาะเจาะจงได้ทันที</p>
                    </div>

                    <div class="card-body px-4 pt-1">
                        <!-- Quick Prompts -->
                        <div class="mb-2">
                            <span class="small text-muted me-1"><i class="fas fa-lightbulb text-warning"></i> คำถามด่วน:</span>
                            <span class="quick-chip" data-query="สินค้าตัวไหนควรจัดโปรโมชั่นคู่กันเพื่อเพิ่มยอดขาย?">🎯 สินค้าที่ควรจัดเซ็ตคู่</span>
                            <span class="quick-chip" data-query="ช่วยวิเคราะห์ว่าช่วงเวลาไหนของวันมีลูกค้าเยอะสุด และควรเตรียมการอย่างไร?">⚡ ช่วงเวลาขายดีที่สุด</span>
                            <span class="quick-chip" data-query="มีสินค้าสต๊อกต่ำหรือสินค้าขายดีตัวไหนที่สุ่มเสี่ยงของจะขาดบ้าง?">📦 สินค้าเสี่ยงของขาด</span>
                            <span class="quick-chip" data-query="แนะนำ 3 วิธีง่ายๆ ที่จะเพิ่มกำไรให้ร้านในสัปดาห์นี้">💡 แนะนำวิธีเพิ่มกำไร</span>
                        </div>

                        <!-- Chat Box Container -->
                        <div class="chat-container">
                            <div class="chat-messages" id="chatMessages">
                                <div class="chat-bubble ai">
                                    <strong><i class="fas fa-robot me-1 text-primary"></i> AI ที่ปรึกษา:</strong><br>
                                    สวัสดีครับ! ผมคือผู้ช่วยวิเคราะห์ธุรกิจของคุณ มีข้อมูลยอดขายและสต๊อกของร้านพร้อมแล้ว สามารถพิมพ์ถามคำถาม เช่น โปรโมชั่น สินค้าที่ขายดี หรือกลยุทธ์การขายได้เลยครับ
                                </div>
                            </div>

                            <!-- Typing Indicator (Hidden) -->
                            <div class="px-3 py-1 bg-white border-top d-none" id="chatTyping">
                                <small class="text-muted me-2"><i class="fas fa-robot text-primary"></i> AI กำลังคิดวิเคราะห์</small>
                                <div class="typing-indicator d-inline-block"><span></span><span></span><span></span></div>
                            </div>

                            <!-- Input Bar -->
                            <div class="p-3 bg-white border-top">
                                <form id="formChat" onsubmit="return false;">
                                    <div class="input-group">
                                        <input type="text" id="chatInput" class="form-control" placeholder="พิมพ์คำถามภาษาไทย เช่น สินค้าไหนขายดีสุด..." style="border-radius: 12px 0 0 12px;" autocomplete="off">
                                        <button class="btn btn-primary px-4" id="btnSendChat" type="submit" style="border-radius: 0 12px 12px 0;">
                                            <i class="fas fa-paper-plane me-1"></i> ส่ง
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: ตั้งค่า Gemini API Key -->
<div class="modal fade" id="modalApiKey" tabindex="-1" aria-labelledby="modalApiKeyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border:none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalApiKeyLabel">
                    <i class="fas fa-key text-warning me-2"></i>ตั้งค่า Google Gemini API Key
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    ระบบใช้ Google Gemini เพื่อประมวลผลบทวิเคราะห์ คุณสามารถขอ API Key ได้ฟรีจาก Google AI Studio
                </p>
                <div class="mb-3">
                    <label for="inputApiKey" class="form-label fw-bold small">Gemini API Key</label>
                    <input type="password" class="form-control" id="inputApiKey" placeholder="AIzaSy..." value="">
                    <div class="form-text">
                        ยังไม่มี API Key? <a href="https://aistudio.google.com/app/apikey" target="_blank" class="fw-bold text-primary">คลิกที่นี่เพื่อรับฟรีจาก Google AI Studio <i class="fas fa-external-link-alt fa-xs"></i></a>
                    </div>
                </div>
                <div class="alert alert-light border small text-muted">
                    <i class="fas fa-shield-alt text-success me-1"></i> คีย์จะถูกจัดเก็บในฐานข้อมูลของร้านค้าของคุณอย่างปลอดภัย
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="btnSaveApiKey" style="border-radius: 10px;">
                    <i class="fas fa-save me-1"></i> บันทึก API Key
                </button>
            </div>
        </div>
    </div>
</div>
