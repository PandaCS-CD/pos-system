<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <div class="col-6">
                        <h3><i class="fas fa-receipt me-2"></i>ประวัติการขาย</h3>
                    </div>
                    <div class="col-6 text-end">
                        <a href="<?= admin_url('pos') ?>" class="btn btn-success">
                            <i class="fas fa-cash-register me-1"></i> เปิดหน้าขาย
                        </a>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">วันที่เริ่ม</label>
                                <input type="date" class="form-control" id="dateFrom" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" class="form-control" id="dateTo" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary" id="btnFilter">
                                    <i class="fas fa-search me-1"></i> ค้นหา
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tbSales" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">เลขที่บิล</th>
                                        <th class="text-center">วันที่/เวลา</th>
                                        <th class="text-center">ยอดรวม</th>
                                        <th class="text-center">ชำระ</th>
                                        <th class="text-center">พนักงาน</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
