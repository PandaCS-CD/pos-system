<!-- <div class="form-group">
    <select class="choices form-select multiple-remove"
        multiple="multiple">
        <optgroup label="Figures">
            <option value="romboid">Romboid</option>
            <option value="trapeze" selected>Trapeze</option>
            <option value="triangle">Triangle</option>
            <option value="polygon">Polygon</option>
        </optgroup>
        <optgroup label="Colors">
            <option value="red">Red</option>
            <option value="green">Green</option>
            <option value="blue" selected>Blue</option>
            <option value="purple">Purple</option>
        </optgroup>
    </select>
</div> -->
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading">
                <div class="row">
                    <h3>เพิ่มข้อมูลแอดมิน</h3>
                </div>
            </div>
            <div class="page-content">
                <form id="formCreateAdmin" class="form-validator" method="post" enctype="multipart/form-data">
                    <div class="card shadow-sm">
                        <div class="card-body px-4">
                            <div class="form-group">
                                <label for="">ชื่อ <sup class="text-danger">*</sup></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="">ชื่อผู้ใช้งาน <sup class="text-danger">*</sup></label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="">รหัสผ่าน <sup class="text-danger">*</sup></label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="">สิทธิ์ <sup class="text-danger">*</sup></label>
                                <select class="choices form-select" name="permission">
                                    <option value="0">พนักงานขาย</option>
                                    <option value="1">ผู้จัดการ</option>
                                    <option value="2">เจ้าของร้าน</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer border-0">
                            <button class="btn btn-success px-4" type="submit">save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>