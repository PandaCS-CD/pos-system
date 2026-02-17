<style>
    .pos-container {
        height: calc(100vh - 80px);
        overflow: hidden;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
        max-height: calc(100vh - 280px);
        overflow-y: auto;
        padding: 5px;
    }

    .product-card {
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px;
        text-align: center;
        transition: all 0.2s;
        background: #fff;
        min-height: 100px;
    }

    .product-card:hover {
        border-color: #4e73df;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3);
        transform: translateY(-2px);
    }

    .product-card .product-name {
        font-size: 0.78rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        line-height: 1.2;
        max-height: 2.4em;
        overflow: hidden;
    }

    .product-card .product-price-tag {
        font-size: 0.85rem;
        font-weight: 700;
        color: #e74a3b;
    }

    .product-card .product-stock-tag {
        font-size: 0.7rem;
        color: #858796;
    }

    .product-card img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .cart-table {
        font-size: 0.85rem;
    }

    .cart-table th {
        background: #4e73df;
        color: #fff;
        font-weight: 500;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .cart-scroll {
        max-height: calc(100vh - 450px);
        overflow-y: auto;
    }

    .cart-total-section {
        background: #f8f9fc;
        border-radius: 8px;
        padding: 12px;
    }

    .total-amount {
        font-size: 2rem;
        font-weight: 700;
        color: #1cc88a;
    }

    .category-pills .btn {
        font-size: 0.8rem;
        margin: 2px;
    }

    .category-pills .btn.active {
        background: #4e73df !important;
        color: #fff !important;
    }

    .qty-input {
        width: 55px;
        text-align: center;
        font-weight: 600;
    }

    .barcode-input {
        font-size: 1.1rem;
        padding: 10px 15px;
    }

    .btn-checkout {
        font-size: 1.2rem;
        padding: 12px;
        font-weight: 700;
    }

    .cart-item-remove {
        color: #e74a3b;
        cursor: pointer;
        font-size: 1.1rem;
    }

    .cart-item-remove:hover {
        color: #c0392b;
    }

    .low-stock {
        border-color: #f6c23e !important;
    }

    .out-of-stock {
        opacity: 0.5;
        pointer-events: none;
        border-color: #e74a3b !important;
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
            <!-- ============ ฝั่งซ้าย: รายการสินค้าในตะกร้า ============ -->
            <div class="col-md-5 col-lg-5 h-100 d-flex flex-column pe-1">
                <div class="card h-100 mb-0">
                    <div class="card-header bg-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>รายการขาย</h5>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearCart">
                                <i class="fas fa-trash"></i> ล้าง
                            </button>
                        </div>
                        <!-- Barcode Scanner -->
                        <div class="input-group mt-2">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control barcode-input" id="barcodeInput"
                                placeholder="สแกนบาร์โค้ด หรือ พิมพ์รหัสสินค้า..." autofocus autocomplete="off">
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="card-body p-0 ">
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
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-shopping-basket fa-3x mb-3 d-block opacity-25"></i>
                                        ยังไม่มีสินค้าในรายการ
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Summary & Checkout -->

                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-footer bg-white p-3">
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

                        <!-- Payment -->
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
            <!-- ============ ฝั่งขวา: สินค้า ============ -->
            <div class="col-md-3 col-lg-3 h-100 d-flex flex-column ps-1">
                <div class="card h-100 mb-0">
                    <div class="card-header bg-white py-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>สินค้า</h5>
                            <div class="input-group" style="max-width: 280px;">
                                <input type="text" class="form-control" id="searchProduct" placeholder="ค้นหาสินค้า..." autocomplete="off">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
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
                                                style="width:60px;height:60px;background:#f0f0f0;border-radius:6px;margin:0 auto 4px;">
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