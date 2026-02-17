<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-file-invoice me-2"></i>รายละเอียดบิล <?= $sale['sale_code'] ?></h3>
                    <div>
                        <a href="<?= admin_url('pos/receipt/' . $sale['sale_id']) ?>" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-print me-1"></i> พิมพ์ใบเสร็จ
                        </a>
                        <a href="<?= admin_url('sales') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> กลับ
                        </a>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">รายการสินค้า</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>สินค้า</th>
                                            <th class="text-center">ราคา</th>
                                            <th class="text-center">จำนวน</th>
                                            <th class="text-center">ส่วนลด</th>
                                            <th class="text-end pe-3">รวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 0;
                                        foreach ($saleDetails as $item): $no++; ?>
                                            <tr>
                                                <td class="ps-3"><?= $no ?></td>
                                                <td><?= $item['product_name'] ?></td>
                                                <td class="text-center"><?= number_format($item['product_price'], 2) ?></td>
                                                <td class="text-center"><?= $item['qty'] ?></td>
                                                <td class="text-center"><?= number_format($item['discount'], 2) ?></td>
                                                <td class="text-end pe-3"><strong><?= number_format($item['total'], 2) ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end pe-3"><strong>รวม</strong></td>
                                            <td class="text-end pe-3"><strong><?= number_format($sale['sale_subtotal'], 2) ?> ฿</strong></td>
                                        </tr>
                                        <?php if ($sale['sale_discount'] > 0): ?>
                                            <tr>
                                                <td colspan="5" class="text-end pe-3 text-danger">ส่วนลด</td>
                                                <td class="text-end pe-3 text-danger">-<?= number_format($sale['sale_discount'], 2) ?> ฿</td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="table-success">
                                            <td colspan="5" class="text-end pe-3"><strong class="fs-5">ยอดสุทธิ</strong></td>
                                            <td class="text-end pe-3"><strong class="fs-5"><?= number_format($sale['sale_total'], 2) ?> ฿</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">ข้อมูลบิล</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted">เลขที่บิล</td>
                                        <td class="fw-bold"><?= $sale['sale_code'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">วันที่</td>
                                        <td><?= date('d/m/Y', strtotime($sale['sale_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">เวลา</td>
                                        <td><?= date('H:i:s', strtotime($sale['sale_time'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">พนักงาน</td>
                                        <td><?= $sale['admin_name'] ?? '-' ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">ชำระโดย</td>
                                        <td>
                                            <?php
                                            $payLabels = ['cash' => 'เงินสด', 'transfer' => 'โอนเงิน', 'credit' => 'บัตรเครดิต'];
                                            echo $payLabels[$sale['sale_payment_method']] ?? $sale['sale_payment_method'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">รับเงิน</td>
                                        <td><?= number_format($sale['sale_received'], 2) ?> ฿</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">เงินทอน</td>
                                        <td><?= number_format($sale['sale_change'], 2) ?> ฿</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">สถานะ</td>
                                        <td>
                                            <?php if ($sale['sale_status'] == 1): ?>
                                                <span class="badge bg-success">สำเร็จ</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">ยกเลิก</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if (!empty($sale['sale_note'])): ?>
                                        <tr>
                                            <td class="text-muted">หมายเหตุ</td>
                                            <td><?= $sale['sale_note'] ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>