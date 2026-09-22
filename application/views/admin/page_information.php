<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-12">
                        <h3>จัดการข้อมูลร้าน</h3>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <div class="card shadow-sm">
                    <div class="card-body px-4">
                        <form action="<?= admin_url('information'); ?>" method="post" id="information-form" class="form-validator">
                            <input type="hidden" name="info_id" value="<?= $information ? $information['info_id'] : ''; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_name" class="form-label">ชื่อร้าน <span class="text-danger">*</span></label>
                                        <input type="text" id="info_name" name="info_name" class="form-control"
                                            value="<?= $information ? $information['info_name'] : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_phone" class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
                                        <input type="text" id="info_phone" name="info_phone" data-oninput="number" class="form-control"
                                            value="<?= $information ? $information['info_phone'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="info_address" class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                                        <textarea id="info_address" name="info_address" class="form-control" rows="2" required><?= $information ? $information['info_address'] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_email" class="form-label">อีเมล</label>
                                        <input type="email" id="info_email" name="info_email" class="form-control"
                                            value="<?= $information ? $information['info_email'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_line" class="form-label">LINE ID</label>
                                        <input type="text" id="info_line" name="info_line" class="form-control"
                                            value="<?= $information ? $information['info_line'] : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_tax_id" class="form-label">เลขประจำตัวผู้เสียภาษี</label>
                                        <input type="text" id="info_tax_id" name="info_tax_id" class="form-control" maxlength="13"
                                            value="<?= $information ? $information['info_tax_id'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="info_receipt_footer" class="form-label">ข้อความท้ายใบเสร็จ</label>
                                        <input type="text" id="info_receipt_footer" name="info_receipt_footer" class="form-control"
                                            value="<?= $information ? $information['info_receipt_footer'] : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>