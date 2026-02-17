<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .receipt-print,
        .receipt-print * {
            visibility: visible;
        }

        .receipt-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
        }

        .no-print {
            display: none !important;
        }
    }

    .receipt-print {
        max-width: 350px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.4;
        border: 1px dashed #ccc;
    }

    .receipt-print .receipt-header {
        text-align: center;
        border-bottom: 1px dashed #333;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .receipt-print .receipt-header h4 {
        margin: 0;
        font-size: 16px;
    }

    .receipt-print .receipt-items {
        border-bottom: 1px dashed #333;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .receipt-print .receipt-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .receipt-print .receipt-total {
        border-bottom: 1px dashed #333;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .receipt-print .receipt-footer {
        text-align: center;
        font-size: 12px;
    }
</style>

<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-3 no-print">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> พิมพ์ใบเสร็จ
                    </button>
                    <a href="<?= admin_url('pos') ?>" class="btn btn-success">
                        <i class="fas fa-cash-register me-1"></i> กลับหน้าขาย
                    </a>
                    <a href="<?= admin_url('sales') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> ประวัติการขาย
                    </a>
                </div>

                <div class="receipt-print">
                    <div class="receipt-header">
                        <h4><?= $storeInfo['info_name'] ?? 'ร้านขายของเบ็ดเตล็ด' ?></h4>
                        <div><?= $storeInfo['info_address'] ?? '' ?></div>
                        <div>โทร: <?= $storeInfo['info_phone'] ?? '' ?></div>
                        <?php if (!empty($storeInfo['info_tax_id'])): ?>
                            <div>เลขผู้เสียภาษี: <?= $storeInfo['info_tax_id'] ?></div>
                        <?php endif; ?>
                        <div style="margin-top:5px;">
                            <strong>ใบเสร็จรับเงิน</strong>
                        </div>
                    </div>

                    <div style="margin-bottom: 8px;">
                        <div class="receipt-item">
                            <span>เลขที่:</span>
                            <span><?= $sale['sale_code'] ?></span>
                        </div>
                        <div class="receipt-item">
                            <span>วันที่:</span>
                            <span><?= date('d/m/Y', strtotime($sale['sale_date'])) ?> <?= date('H:i', strtotime($sale['sale_time'])) ?></span>
                        </div>
                        <div class="receipt-item">
                            <span>พนักงาน:</span>
                            <span><?= $sale['admin_name'] ?? '-' ?></span>
                        </div>
                    </div>

                    <div style="border-top: 1px dashed #333; margin-bottom: 5px;"></div>

                    <div class="receipt-items">
                        <?php $no = 0;
                        foreach ($saleDetails as $item): $no++; ?>
                            <div>
                                <div><strong><?= $item['product_name'] ?></strong></div>
                                <div class="receipt-item" style="padding-left: 15px;">
                                    <span><?= $item['qty'] ?> x <?= number_format($item['product_price'], 2) ?></span>
                                    <span><?= number_format($item['total'], 2) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="receipt-total">
                        <div class="receipt-item">
                            <span>รวม</span>
                            <span><?= number_format($sale['sale_subtotal'], 2) ?></span>
                        </div>
                        <?php if ($sale['sale_discount'] > 0): ?>
                            <div class="receipt-item">
                                <span>ส่วนลด</span>
                                <span>-<?= number_format($sale['sale_discount'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="receipt-item" style="font-size: 16px; font-weight: bold;">
                            <span>ยอดสุทธิ</span>
                            <span><?= number_format($sale['sale_total'], 2) ?></span>
                        </div>
                        <div style="border-top: 1px dashed #333; margin: 5px 0;"></div>
                        <div class="receipt-item">
                            <span><?php
                                    $payLabels = ['cash' => 'เงินสด', 'transfer' => 'โอนเงิน', 'credit' => 'บัตรเครดิต'];
                                    echo $payLabels[$sale['sale_payment_method']] ?? 'เงินสด';
                                    ?></span>
                            <span><?= number_format($sale['sale_received'], 2) ?></span>
                        </div>
                        <div class="receipt-item">
                            <span>เงินทอน</span>
                            <span><?= number_format($sale['sale_change'], 2) ?></span>
                        </div>
                    </div>

                    <div class="receipt-footer">
                        <div><?= $storeInfo['info_receipt_footer'] ?? 'ขอบคุณที่ใช้บริการ' ?></div>
                        <div style="font-size:11px; margin-top: 5px;">*** <?= $sale['sale_code'] ?> ***</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>