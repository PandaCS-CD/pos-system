<!-- POS styles managed by pos-modern.css -->
<style>
    .pos-container {
        height: calc(100vh - 80px);
        overflow: hidden;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        max-height: calc(100vh - 260px);
        overflow-y: auto;
        padding: 5px;
    }
    .cart-scroll {
        max-height: calc(100vh - 520px);
        overflow-y: auto;
    }
    .qty-input {
        width: 55px;
        text-align: center;
        font-weight: 600;
    }
    .low-stock {
        border-color: #f59e0b !important;
    }
    .out-of-stock {
        opacity: 0.5;
        pointer-events: none;
        border-color: #ef4444 !important;
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<section class="pos-container">
    <div class="container-fluid h-100">
        <div class="row h-100">

            <!-- ============ ฝั่งซ้าย: เลือกสินค้า ============ -->
            <div class="col-md-7 col-lg-7 h-100 d-flex flex-column pe-1">
                <div class="card h-100 mb-0">
                    <div class="card-header bg-white py-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>สินค้า</h5>
                            <!-- Barcode + Search -->
                            <div class="d-flex gap-2" style="max-width: 480px; flex:1;">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" class="form-control" id="barcodeInput"
                                        placeholder="สแกนบาร์โค้ด..." autofocus autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchProduct" placeholder="ค้นหาสินค้า..." autocomplete="off">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="category-pills">
                            <button type="button" class="btn btn-outline-primary btn-sm active" data-category="all">ทั้งหมด</button>
                            <?php if (isset($categories) && !empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if ($cat['category_status'] == 1): ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            data-category="<?= $cat['category_id'] ?>"><?= $cat['category_name'] ?></button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-2">
                        <div class="product-grid" id="productGrid">
                            <?php if (isset($products) && !empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                    <?php
                                    $stockClass = '';
                                    if ($p['product_stock'] <= 0) $stockClass = 'out-of-stock';
                                    elseif ($p['product_stock'] <= ($p['product_stock_min'] ?? 5)) $stockClass = 'low-stock';
                                    ?>
                                    <div class="product-card <?= $stockClass ?>"
                                        data-id="<?= $p['product_id'] ?>"
                                        data-name="<?= htmlspecialchars($p['product_name']) ?>"
                                        data-price="<?= $p['product_price'] ?>"
                                        data-cost="<?= $p['product_cost'] ?>"
                                        data-stock="<?= $p['product_stock'] ?>"
                                        data-barcode="<?= $p['product_barcode'] ?>"
                                        data-code="<?= $p['product_code'] ?>"
                                        data-category="<?= $p['category_id'] ?>"
                                        onclick="addToCart(this)">
                                        <?php if (!empty($p['product_img'])): ?>
                                            <img src="<?= base_url('uploads/product/' . $p['product_img']) ?>" alt="">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width:50px;height:50px;background:#f0f0f0;border-radius:10px;margin:0 auto 4px;flex-shrink:0;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="product-name"><?= $p['product_name'] ?></div>
                                        <div class="product-price-tag">฿<?= number_format($p['product_price'], 0) ?></div>
                                        <div class="product-stock-tag">
                                            คงเหลือ: <?= $p['product_stock'] ?> <?= $p['product_unit'] ?? 'ชิ้น' ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ ฝั่งขวา: ตะกร้า + ชำระเงิน ============ -->
            <div class="col-md-5 col-lg-5 h-100 d-flex flex-column ps-1">
                <div class="card h-100 mb-0 d-flex flex-column">
                    <!-- Cart Header -->
                    <div class="card-header bg-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>รายการขาย</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary" id="totalItemsBadge">0 รายการ</span>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearCart">
                                    <i class="fas fa-trash"></i> ล้าง
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="card-body p-0 flex-grow-1" style="overflow-y:auto;">
                        <table class="table table-hover cart-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width:40%">สินค้า</th>
                                    <th class="text-center" style="width:15%">ราคา</th>
                                    <th class="text-center" style="width:20%">จำนวน</th>
                                    <th class="text-center" style="width:15%">รวม</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="emptyCart">
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-25"></i>
                                        <span style="font-size:0.85rem;">คลิกสินค้าด้านซ้ายเพื่อเพิ่มรายการ</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Section -->
                    <div class="card-footer bg-white p-3" style="border-top: 2px solid var(--pm-border, #e2e8f0);">
                        <!-- Totals -->
                        <div class="cart-total-section mb-2">
                            <div class="row mb-1">
                                <div class="col-6">รวม (<span id="totalItems">0</span> รายการ)</div>
                                <div class="col-6 text-end" id="subtotalDisplay">0.00 ฿</div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">ส่วนลด</span>
                                        <input type="number" class="form-control" id="discountInput" value="0" min="0" step="1">
                                        <span class="input-group-text">฿</span>
                                    </div>
                                </div>
                                <div class="col-6 text-end text-danger" id="discountDisplay">-0.00 ฿</div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-6"><strong>ยอดสุทธิ</strong></div>
                                <div class="col-6 text-end total-amount" id="totalDisplay">0.00</div>
                            </div>
                        </div>

                        <!-- Payment Method + Received -->
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select class="form-select" id="paymentMethod">
                                    <option value="cash">💵 เงินสด</option>
                                    <option value="transfer">📱 โอนเงิน</option>
                                    <option value="credit">💳 บัตรเครดิต</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">รับ</span>
                                    <input type="number" class="form-control" id="receivedInput" value="0" min="0" step="1">
                                    <span class="input-group-text">฿</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2" id="changeSection" style="display:none;">
                            <div class="col-12 text-center">
                                <span class="fs-5">เงินทอน: <strong class="text-primary fs-4" id="changeDisplay">0.00</strong> ฿</span>
                            </div>
                        </div>

                        <!-- Quick Cash Buttons -->
                        <div class="d-flex gap-1 mb-2 flex-wrap" id="quickCashBtns">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash" data-amount="20">20</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash" data-amount="50">50</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash" data-amount="100">100</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash" data-amount="500">500</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash" data-amount="1000">1000</button>
                            <button type="button" class="btn btn-outline-info btn-sm" id="btnExact">พอดี</button>
                        </div>

                        <button type="button" class="btn btn-success btn-checkout w-100" id="btnCheckout" disabled>
                            <i class="fas fa-cash-register me-2"></i>ชำระเงิน (F12)
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Modal: Checkout Success -->
<div class="modal fade" id="modalSuccess" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4>ชำระเงินสำเร็จ!</h4>
                <p class="mb-1">เลขที่ใบเสร็จ: <strong id="successSaleCode"></strong></p>
                <p class="mb-1">ยอดรวม: <strong id="successTotal"></strong> ฿</p>
                <p class="mb-1">รับเงิน: <strong id="successReceived"></strong> ฿</p>
                <p class="mb-0 fs-3 text-primary">เงินทอน: <strong id="successChange"></strong> ฿</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#" class="btn btn-secondary" id="btnPrintReceipt" target="_blank">
                    <i class="fas fa-print me-1"></i> พิมพ์ใบเสร็จ
                </a>
                <button type="button" class="btn btn-primary" id="btnNewSale">
                    <i class="fas fa-plus me-1"></i> ขายรายการใหม่
                </button>
            </div>
        </div>
    </div>
</div>