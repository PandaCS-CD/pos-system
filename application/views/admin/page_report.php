<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <h3><i class="fas fa-chart-bar me-2"></i>รายงานสรุป</h3>
            </div>

            <div class="page-content">
                <!-- Date Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="get" action="<?= admin_url('report') ?>" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">ช่วงวันที่</label>
                                <input type="text" class="form-control dateRangePickerEN" id="dateRange" placeholder="เลือกช่วงวันที่" value="<?= date('d-m-Y') ?> to <?= date('d-m-Y') ?>" autocomplete="off">
                                <input type="hidden" id="dateFrom" value="<?= date('Y-m-d') ?>">
                                <input type="hidden" id="dateTo" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>ดูรายงาน</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-4">
                            <div class="card-body py-3">
                                <div class="text-muted small">จำนวนบิล</div>
                                <div class="fs-3 fw-bold text-primary"><?= number_format($profit_report['total_bills'] ?? 0) ?></div>
                                <div class="text-muted small">บิลทั้งหมด</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-success border-4">
                            <div class="card-body py-3">
                                <div class="text-muted small">ยอดขายรวม</div>
                                <div class="fs-3 fw-bold text-success"><?= number_format($profit_report['total_revenue'] ?? 0, 2) ?></div>
                                <div class="text-muted small">บาท</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-danger border-4">
                            <div class="card-body py-3">
                                <div class="text-muted small">ต้นทุน</div>
                                <div class="fs-3 fw-bold text-danger"><?= number_format($profit_report['total_cost'] ?? 0, 2) ?></div>
                                <div class="text-muted small">บาท</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-4">
                            <div class="card-body py-3">
                                <div class="text-muted small">กำไร</div>
                                <div class="fs-3 fw-bold text-info"><?= number_format($profit_report['total_profit'] ?? 0, 2) ?></div>
                                <div class="text-muted small">บาท</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- กราฟยอดขาย 7 วัน -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>ยอดขาย 7 วันล่าสุด</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartSales7Days" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- สินค้าขายดี -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>สินค้าขายดี Top 10</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>สินค้า</th>
                                            <th class="text-center">จำนวน</th>
                                            <th class="text-end pe-3">ยอด</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($top_products)):
                                            $i = 0;
                                            foreach ($top_products as $tp): $i++; ?>
                                                <tr>
                                                    <td class="ps-3"><?= $i ?></td>
                                                    <td><?= $tp['product_name'] ?></td>
                                                    <td class="text-center"><?= number_format($tp['total_qty']) ?></td>
                                                    <td class="text-end pe-3"><?= number_format($tp['total_amount'], 0) ?> ฿</td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">ยังไม่มีข้อมูล</td>
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