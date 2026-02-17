<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>จัดการข้อมูลหมวดหมู่</h3>
                    </div>
                    <div class="col-6 text-end">
                        <a href="<?= admin_url('category/create'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มหมวดหมู่</a>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form id="formcategory" action="" method="post" autocomplete="off">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" id="selectAllBtn" class="btn btn-sm btn-secondary">เลือกทั้งหมด</button>
                                <button type="button" id="deselectAllBtn" class="btn btn-sm btn-outline-secondary">ยกเลิกการเลือก</button>
                            </div>
                            <button type="button" id="deleteSelectedBtn" class="btn btn-sm btn-danger" disabled>
                                <i class="far fa-trash-alt"></i> ลบรายการที่เลือก (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                        <div class="card-body px-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped dataTable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" width="15%"><a href="#" id="selectAll" for="">เลือก</a></th>
                                            <th scope="col" class="text-center" width="8%">ลำดับ</th>
                                            <th scope="col" class="text-center" width="25%">รูปภาพหมวดหมู่</th>
                                            <th scope="col" class="text-center">ชื่อหมวดหมู่</th>
                                            <th scope="col" class="text-center" width="15%">ลำดับ / สถานะ</th>
                                            <th scope="col" class="text-center" width="15%">แก้ไข / ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $num = 0;
                                        $homepage_count = 0;

                                        foreach ($category as $row) {
                                            if (isset($row['show_home']) && $row['show_home'] == 1) {
                                                $homepage_count++;
                                            }
                                        }

                                        foreach ($category as $row):
                                            $num++;
                                        ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="selected_ids[]" value="<?= $row['category_id'] ?>" class="form-check-input message-checkbox" autocomplete="off">
                                                </td>
                                                <td class="text-center"><?= $num; ?></td>

                                                <td class="text-center"><?php if (!empty($row['category_img'])): ?>
                                                        <a href="<?= base_url('uploads/category/' . $row['category_img']); ?>" class="img-link">
                                                            <img src="<?= base_url('uploads/category/' . $row['category_img']); ?>" class="img-fluid" style="max-width: 80px;">
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('assets/images/imgs/No_Image.jpg'); ?>" class="img-link">
                                                            <img src="<?= base_url('assets/images/imgs/No_Image.jpg'); ?>" class="img-fluid" style="max-width: 80px;">
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $row['category_name']; ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center input-group px-2">
                                                        <input type="hidden" name="id[]" value="<?= $row['category_id']; ?>">
                                                        <input type="text" class="form-control form-control-sm input-order" name="order_data[]" value="<?= $row['category_sort']; ?>" inputmode="numeric">
                                                        <?= ($row['category_status'] == 1) ?
                                                            '<a href="' . admin_url('category/status/' . $row['category_id'] . '/0') . '" class="form-control btn btn-info btn-sm pt-2" title="Active"><i class="fa fa-desktop"></i></a>' :
                                                            '<a href="' . admin_url('category/status/' . $row['category_id'] . '/1') . '" class="form-control btn btn-danger btn-sm pt-2" title="Inactive"><i class="fa fa-eye-slash"></i></a>'; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center input-group input-group-edit px-2">
                                                        <a href="category/edit/<?= $row['category_id']; ?>" class="btn btn-warning btn-sm px-3"><i class="fas fa-edit"></i></a>
                                                        <a type="button" class="btn btn-danger btn-sm px-3"
                                                            data-bs-toggle="modal" data-bs-target="#modalDel"
                                                            data-id="<?= ($row['category_id']); ?>"
                                                            data-url="<?= admin_url('category/del/' . $row['category_id']); ?>">
                                                            <i class="far fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfooter>
                                        <tr>
                                            <td colspan="10" align="right">
                                                <div class="py-2">
                                                    <button class="btn btn-success btn-sm px-3 py-2" name="order" value="submit-order">เรียงข้อมูล / Sort</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfooter>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>