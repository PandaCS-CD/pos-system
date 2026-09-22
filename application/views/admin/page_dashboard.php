<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h3>
                        <p class="text-muted mb-0" style="font-size:0.85rem;">ภาพรวมร้านค้าของคุณวันนี้</p>
                    </div>
                    <a href="<?= admin_url('pos') ?>" class="btn btn-success btn-lg" style="border-radius:14px;">
                        <i class="fas fa-cash-register me-2"></i>เปิดหน้าขาย POS
                    </a>
                </div>
            </div>

            <div class="page-content">
                <!-- สรุปยอดวันนี้ -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small opacity-75">ยอดขายวันนี้</div>
                                        <div class="fs-2 fw-bold"><?= number_format($today['total_amount'] ?? 0, 2) ?></div>
                                        <div class="small">บาท</div>
                                    </div>
                                    <i class="fas fa-coins fa-3x opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small opacity-75">จำนวนบิลวันนี้</div>
                                        <div class="fs-2 fw-bold"><?= number_format($today['total_bills'] ?? 0) ?></div>
                                        <div class="small">ใบ</div>
                                    </div>
                                    <i class="fas fa-receipt fa-3x opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small opacity-75">ยอดขายเดือนนี้</div>
                                        <div class="fs-2 fw-bold"><?= number_format($monthly['total_amount'] ?? 0, 2) ?></div>
                                        <div class="small">บาท (<?= number_format($monthly['total_bills'] ?? 0) ?> บิล)</div>
                                    </div>
                                    <i class="fas fa-chart-line fa-3x opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                $admin_perm = isset($this->session->userdata('_auth')['admin_permission']) ? (int)$this->session->userdata('_auth')['admin_permission'] : 0;
                if ($admin_perm >= 1): 
                ?>
                <!-- AI Smart Insights Card -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%); border-left: 5px solid #6366f1 !important;">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fas fa-brain"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">✨ AI สรุปภาพรวม & กลยุทธ์ประจำวัน</h6>
                                            <small class="text-muted">ขับเคลื่อนด้วย Google Gemini Retail Intelligence</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnDashboardAiRefresh">
                                            <i class="fas fa-wand-magic-sparkles me-1"></i> ให้ AI วิเคราะห์วันนี้
                                        </button>
                                        <a href="<?= admin_url('aiAssistant') ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                            <i class="fas fa-comments me-1"></i> ถาม-ตอบกับ AI <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="dashboardAiBox" class="mt-2 p-3 bg-white rounded-3 border">
                                    <div id="dashboardAiEmpty" class="text-muted small">
                                        <i class="fas fa-info-circle text-primary me-1"></i> 
                                        กดปุ่ม <strong>"ให้ AI วิเคราะห์วันนี้"</strong> เพื่อรับข้อสรุปยอดขาย การแจ้งเตือนสต๊อกวิกฤต และกลยุทธ์เพิ่มยอดขายสำหรับวันนี้ทันที
                                    </div>
                                    <div id="dashboardAiLoading" class="text-center py-2 d-none">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        <span class="small text-muted">AI กำลังสังเคราะห์ข้อมูลยอดขายและสต๊อก...</span>
                                    </div>
                                    <div id="dashboardAiContent" class="small d-none" style="line-height: 1.6;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- กราฟยอดขาย 7 วัน -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>ยอดขาย 7 วันล่าสุด</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartDashboard" height="180"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- สินค้าขายดี -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-fire text-danger me-2"></i>สินค้าขายดี</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <?php if (!empty($top_products)):
                                            foreach ($top_products as $tp): ?>
                                                <tr>
                                                    <td class="ps-3"><?= $tp['product_name'] ?></td>
                                                    <td class="text-end pe-3">
                                                        <span class="badge bg-primary"><?= number_format($tp['total_qty']) ?> ชิ้น</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td class="text-center text-muted py-3">ยังไม่มีข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- สินค้าใกล้หมด -->
                        <div class="card mb-3">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>สินค้าใกล้หมด</h5>
                                <a href="<?= admin_url('stockManage') ?>" class="btn btn-sm btn-outline-warning">เพิ่มสต๊อก</a>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <?php if (!empty($low_stock)):
                                            foreach ($low_stock as $ls): ?>
                                                <tr class="<?= $ls['product_stock'] <= 0 ? 'table-danger' : '' ?>">
                                                    <td class="ps-3"><?= $ls['product_name'] ?></td>
                                                    <td class="text-end pe-3">
                                                        <span class="badge <?= $ls['product_stock'] <= 0 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                                            เหลือ <?= $ls['product_stock'] ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td class="text-center text-muted py-3">ไม่มีสินค้าใกล้หมด</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>