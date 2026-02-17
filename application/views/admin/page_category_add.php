<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>เพิ่มหมวดหมู่</h3>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form action="<?= admin_url('category/create'); ?>" method="post" enctype="multipart/form-data" class="form-validator">
                    <div class="card">
                        <div class="card-body px-4">

                            <div class="form-group mb-3">
                                <label for="category_img" class="form-label">รูปภาพหมวดหมู่ <span class="text-danger">*</span><small class="text-muted">แนะนำขนาด 500x500 พิกเซล</small></label>
                                <input type="file" class="form-control image-crop-filepond" id="category_img" name="category_img" required>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="category_name" class="form-label">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="category_meta" class="form-label">Meta Tags <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="category_meta" name="category_meta"
                                            rows="10" placeholder='<meta name="description" content="รายละเอียดหน้า">
<meta name="keywords" content="คำค้นหา">
<meta property="og:title" content="ชื่อหน้า">
<meta property="og:description" content="คำอธิบาย">' style="font-family: monospace; font-size: 14px;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
                                <a href="<?php echo admin_url('category'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> ยกเลิก</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>