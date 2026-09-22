<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<style>
    #tbProduct_paginate .paginate_button.current {
        color: white !important;
    }
</style>

<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3>จัดการสินค้า</h3>
                        <div class="row">

                            <div class="col-5">
                                <select name="category_filter" id="category_filter" class="form-select" autocomplete="off">
                                    <option value="" selected>-- ทั้งหมด --</option>
                                    <?php if (isset($categories) && !empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['category_id']; ?>"><?= $cat['category_name']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-9"></div>
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <a href="<?= admin_url('product/create'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มสินค้า</a>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <form id="formProduct" action="" method="post">
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
                                <table id="tbProduct" class="table table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" width="5%"><a href="#" id="selectAll">เลือก</a></th>
                                            <th scope="col" class="text-center" width="5%">ลำดับ</th>
                                            <th scope="col" class="text-center" width="12%">รูปสินค้า</th>
                                            <th scope="col" class="text-center" width="10%">รหัสสินค้า</th>
                                            <th scope="col" class="text-center" width="23%">ชื่อสินค้า</th>
                                            <th scope="col" class="text-center" width="10%">ราคา</th>
                                            <th scope="col" class="text-center" width="10%">ลำดับ / สถานะ</th>
                                            <th scope="col" class="text-center" width="12%">แก้ไข / ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" align="right">
                                                <div class="py-2">
                                                    <button class="btn btn-success btn-sm px-3 py-2" name="order" value="submit-order">เรียงข้อมูล / Sort</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>