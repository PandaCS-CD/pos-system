<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-history me-2"></i>ประวัติสต๊อก
                        <?php if ($product_id): ?>
                            <small class="text-muted fs-6">(รหัสสินค้า: <?= $product_id ?>)</small>
                        <?php endif; ?>
                    </h3>
                    <a href="<?= admin_url('stockManage') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> กลับ
                    </a>
                </div>
            </div>

            <div class="page-content">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>วันที่/เวลา</th>
                                        <th>สินค้า</th>
                                        <th class="text-center">ประเภท</th>
                                        <th class="text-center">จำนวน</th>
                                        <th class="text-center">ก่อน</th>
                                        <th class="text-center">หลัง</th>
                                        <th>หมายเหตุ</th>
                                        <th>ผู้ทำรายการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($history)):
                                        $no = 0;
                                        foreach ($history as $h): $no++; ?>
                                            <tr>
                                                <td><?= $no ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                                <td>
                                                    <strong><?= $h['product_name'] ?? '-' ?></strong>
                                                    <small class="text-muted d-block"><?= $h['product_code'] ?? '' ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $typeLabels = [
                                                        'in' => '<span class="badge bg-success">รับเข้า</span>',
                                                        'out' => '<span class="badge bg-danger">ขายออก</span>',
                                                        'adjust' => '<span class="badge bg-info">ปรับปรุง</span>'
                                                    ];
                                                    echo $typeLabels[$h['stock_type']] ?? $h['stock_type'];
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($h['stock_qty'] > 0): ?>
                                                        <span class="text-success fw-bold">+<?= $h['stock_qty'] ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger fw-bold"><?= $h['stock_qty'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $h['stock_before'] ?></td>
                                                <td class="text-center"><?= $h['stock_after'] ?></td>
                                                <td><?= $h['stock_note'] ?? '-' ?></td>
                                                <td><?= $h['admin_name'] ?? '-' ?></td>
                                            </tr>
                                        <?php endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">ไม่มีประวัติ</td>
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
</section>