<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>แก้ไขสินค้า</h3>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form action="<?= admin_url('product/edit/' . $productID['product_id']); ?>" method="post" enctype="multipart/form-data" class="form-validator">
                    <div class="card">
                        <div class="card-body px-4">
                            <input type="hidden" name="old_image" value="<?= $productID['product_img'] ?? ''; ?>">

                            <div class="form-group mb-3">
                                <label class="form-label">รูปสินค้า <small class="text-muted">แนะนำขนาด 800x800 พิกเซล (เว้นว่างหากไม่ต้องการเปลี่ยน)</small></label>
                                <?= (isset($productID['product_img']) && $productID['product_img']) ?
                                    '<div class="mb-3">
                                        <small class="text-muted">รูปภาพปัจจุบัน:</small>
                                        <div>
                                            <a href="' . base_url('uploads/product/' . $productID['product_img']) . '" class="img-link">
                                                <img src="' . base_url('uploads/product/' . $productID['product_img']) . '" class="img-fluid" style="max-width:150px;">
                                            </a>
                                        </div>
                                    </div>' :
                                    '<div class="mb-3"><span class="text-muted">ไม่มีรูปภาพ</span></div>'; ?>
                                <input type="file" class="image-crop-filepond form-control" id="product_img" name="product_img" accept="image/*">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="category_id" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-select" required>
                                            <option value="">-- เลือกหมวดหมู่ --</option>
                                            <?php if (isset($categories) && !empty($categories)): ?>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['category_id']; ?>" <?= (isset($productID['category_id']) && $productID['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>><?= $cat['category_name']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_barcode" class="form-label">บาร์โค้ด</label>
                                        <input type="text" class="form-control" id="product_barcode" name="product_barcode"
                                            value="<?= $productID['product_barcode'] ?? ''; ?>" placeholder="สแกนหรือพิมพ์บาร์โค้ด">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_code" class="form-label">รหัสสินค้า <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="product_code" name="product_code"
                                            value="<?= $productID['product_code'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="product_name" class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="product_name" name="product_name"
                                            value="<?= $productID['product_name'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_cost" class="form-label">ราคาทุน <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="product_cost" name="product_cost" step="0.01" min="0"
                                            value="<?= $productID['product_cost'] ?? '0'; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_price" class="form-label">ราคาขาย <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" min="0"
                                            value="<?= $productID['product_price'] ?? '0'; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_unit" class="form-label">หน่วยนับ</label>
                                        <input type="text" class="form-control" id="product_unit" name="product_unit"
                                            value="<?= $productID['product_unit'] ?? 'ชิ้น'; ?>" placeholder="เช่น ชิ้น, ขวด, ซอง">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_stock" class="form-label">จำนวนสต๊อก</label>
                                        <input type="number" class="form-control" id="product_stock" name="product_stock" min="0"
                                            value="<?= $productID['product_stock'] ?? '0'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_stock_min" class="form-label">จำนวนขั้นต่ำ (แจ้งเตือน)</label>
                                        <input type="number" class="form-control" id="product_stock_min" name="product_stock_min" min="0"
                                            value="<?= $productID['product_stock_min'] ?? '5'; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกการแก้ไข</button>
                                <a href="<?php echo admin_url('product'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>