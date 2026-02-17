<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>แอดมิน</h3>
                    </div>
                    <div class="col-6 text-end">
                        <a href="admin/create" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่ม</a>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form action="" method="post">
                    <div class="card shadow-sm">
                        <div class="card-body px-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped dataTable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" width="10%">ลำดับ</th>
                                            <th scope="col" class="text-center">ชื่อ</th>
                                            <th scope="col" class="text-center">ชื่อผู้ใช้งาน</th>
                                            <th scope="col" class="text-center">สิทธิ์</th>
                                            <th scope="col" class="text-center" width="15%">ลำดับ / สถานะ</th>
                                            <th scope="col" class="text-center" width="15%">แก้ไข / ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $num = 0;
                                        foreach ($admins as $admin):
                                            $num++;
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $num; ?></td>
                                                <td><?= $admin['admin_name']; ?></td>
                                                <td><?= $admin['admin_user']; ?></td>
                                                <td class="text-center"><?= ($admin['admin_permission'] == 0) ? 'พนักงานขาย' : ''; ?><?= ($admin['admin_permission'] == 1) ? 'ผู้จัดการ' : ''; ?><?= ($admin['admin_permission'] == 2) ? 'เจ้าของร้าน' : ''; ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center input-group px-2">
                                                        <input type="hidden" name="id[]" value="<?= $admin['admin_id']; ?>">
                                                        <input type="text" class="form-control form-control-sm  input-order" name="order_data[]" value="<?= $admin['admin_sort']; ?>" inputmode="numeric">
                                                        <?php if ($admin['admin_status'] == 1) { ?>
                                                            <a href="<?= admin_url('admin/status/') . $admin['admin_id'] ?>/0" class="form-control btn btn-info btn-sm pt-2" title="Show"><i class="fa fa-desktop"></i></a>
                                                        <?php  } else { ?>
                                                            <a href="<?= admin_url('admin/status/') . $admin['admin_id'] ?>/1" class="form-control btn btn-danger btn-sm pt-2" title="Not Show"><i class="fa fa-eye-slash"></i></a>
                                                        <?php  } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center input-group input-group-edit px-2">
                                                        <a href="admin/edit/<?= $admin['admin_id']; ?>" class="btn btn-warning btn-sm px-3"><i class="fas fa-edit"></i></a>
                                                        <a type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDel" data-id="<?= $admin['admin_id']; ?>"
                                                            data-url="<?= admin_url('admin/del/') . $admin['admin_id']; ?>">
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