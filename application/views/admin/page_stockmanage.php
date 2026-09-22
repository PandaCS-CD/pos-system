<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-warehouse me-2"></i>จัดการสต๊อกสินค้า</h3>
                    <a href="<?= admin_url('stockManage/history') ?>" class="btn btn-outline-info">
                        <i class="fas fa-history me-1"></i> ดูประวัติสต๊อก
                    </a>
                </div>
            </div>

            <div class="page-content">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchStock" placeholder="ค้นหาสินค้า..." autocomplete="off">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tbStock">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>รหัส</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>หมวดหมู่</th>
                                        <th class="text-center">ราคาทุน</th>
                                        <th class="text-center">ราคาขาย</th>
                                        <th class="text-center">คงเหลือ</th>
                                        <th class="text-center">ขั้นต่ำ</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0;
                                    foreach ($products as $p): $no++;
                                        $rowClass = '';
                                        if ($p['product_stock'] <= 0) $rowClass = 'table-danger';
                                        elseif ($p['product_stock'] <= $p['product_stock_min']) $rowClass = 'table-warning';
                                    ?>
                                        <tr class="<?= $rowClass ?>">
                                            <td><?= $no ?></td>
                                            <td><?= $p['product_code'] ?></td>
                                            <td><strong><?= $p['product_name'] ?></strong></td>
                                            <td><?= $p['category_name'] ?? '-' ?></td>
                                            <td class="text-center"><?= number_format($p['product_cost'], 2) ?></td>
                                            <td class="text-center"><?= number_format($p['product_price'], 2) ?></td>
                                            <td class="text-center">
                                                <strong class="<?= $p['product_stock'] <= 0 ? 'text-danger' : ($p['product_stock'] <= $p['product_stock_min'] ? 'text-warning' : 'text-success') ?>">
                                                    <?= $p['product_stock'] ?>
                                                </strong>
                                                <small class="text-muted"><?= $p['product_unit'] ?? 'ชิ้น' ?></small>
                                            </td>
                                            <td class="text-center"><?= $p['product_stock_min'] ?></td>
                                            <td class="text-center">
                                                <?php if ($p['product_stock'] <= 0): ?>
                                                    <span class="badge bg-danger">หมด</span>
                                                <?php elseif ($p['product_stock'] <= $p['product_stock_min']): ?>
                                                    <span class="badge bg-warning text-dark">ใกล้หมด</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">ปกติ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#modalStockIn"
                                                        data-id="<?= $p['product_id'] ?>"
                                                        data-name="<?= htmlspecialchars($p['product_name']) ?>"
                                                        data-stock="<?= $p['product_stock'] ?>">
                                                        <i class="fas fa-plus"></i> รับเข้า
                                                    </button>
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#modalAdjust"
                                                        data-id="<?= $p['product_id'] ?>"
                                                        data-name="<?= htmlspecialchars($p['product_name']) ?>"
                                                        data-stock="<?= $p['product_stock'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= admin_url('stockManage/history/' . $p['product_id']) ?>" class="btn btn-outline-secondary btn-sm" title="ประวัติ">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: รับสินค้าเข้า -->
<div class="modal fade" id="modalStockIn" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= admin_url('stockManage/stockIn') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle text-success me-2"></i>รับสินค้าเข้าสต๊อก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="stockInProductId">
                    <div class="mb-3">
                        <label class="form-label">สินค้า</label>
                        <input type="text" class="form-control" id="stockInProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนปัจจุบัน</label>
                        <input type="text" class="form-control" id="stockInCurrentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนที่รับเข้า <span class="text-danger">*</span></label>
                        <input type="number" name="qty" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <input type="text" name="note" class="form-control" placeholder="เช่น ซื้อเพิ่ม, รับจากตัวแทน">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> รับเข้า</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: ปรับปรุงสต๊อก -->
<div class="modal fade" id="modalAdjust" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= admin_url('stockManage/adjust') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit text-info me-2"></i>ปรับปรุงสต๊อก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="adjustProductId">
                    <div class="mb-3">
                        <label class="form-label">สินค้า</label>
                        <input type="text" class="form-control" id="adjustProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนปัจจุบัน</label>
                        <input type="text" class="form-control" id="adjustCurrentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนใหม่ <span class="text-danger">*</span></label>
                        <input type="number" name="new_stock" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <input type="text" name="note" class="form-control" placeholder="เช่น นับสต๊อก, สินค้าเสียหาย">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><i class="fas fa-check me-1"></i> ปรับปรุง</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>
</div>