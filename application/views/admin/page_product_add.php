<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>เพิ่มสินค้า</h3>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form action="<?= admin_url('product/create'); ?>" method="post" enctype="multipart/form-data" class="form-validator">
                    <div class="card">
                        <div class="card-body px-4">

                            <div class="form-group mb-3">
                                <label class="form-label">รูปสินค้า <small class="text-muted">แนะนำขนาด 800x800 พิกเซล</small></label>
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
                                                    <option value="<?= $cat['category_id']; ?>"><?= $cat['category_name']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_barcode" class="form-label">บาร์โค้ด</label>
                                        <input type="text" class="form-control" id="product_barcode" name="product_barcode" placeholder="สแกนหรือพิมพ์บาร์โค้ด">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_code" class="form-label">รหัสสินค้า <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="product_code" name="product_code" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="product_name" class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_cost" class="form-label">ราคาทุน <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="product_cost" name="product_cost" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_price" class="form-label">ราคาขาย <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="product_price" name="product_price" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_unit" class="form-label">หน่วยนับ</label>
                                        <input type="text" class="form-control" id="product_unit" name="product_unit" value="ชิ้น" placeholder="เช่น ชิ้น, ขวด, ซอง">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_stock" class="form-label">จำนวนสต๊อก</label>
                                        <input type="number" class="form-control" id="product_stock" name="product_stock" value="0" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="product_stock_min" class="form-label">จำนวนขั้นต่ำ (แจ้งเตือน)</label>
                                        <input type="number" class="form-control" id="product_stock_min" name="product_stock_min" value="5" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
                                <a href="<?php echo admin_url('product'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>