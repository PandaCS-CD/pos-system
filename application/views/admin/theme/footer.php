<!-- Modal Del-->
<div class="modal modal-animated fadeIn " id="modalDel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="del_url" action="" method="post">
                <div class="modal-body pb-0">
                    <div class="text-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center" style="padding-top: 10px; padding-bottom: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#eb182d" wdith="100" height="100">
                            <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z" />
                        </svg>
                        <h4 class="fw-normal text-dark pt-4">คุณแน่ใจหรือไม่ ?</h4>
                        <p class="text-secondary"><b>คุณต้องการลบข้อมูลรายการนี้ใช่หรือไม่?</b></p>
                        <input hidden type="text" id="del_id" name="id">
                    </div>
                </div>
                <div class="modal-footer  justify-content-center" style="border-top: 0px;padding: 0px 10px 30px 0px;">
                    <button type="submit" class="btn btn-danger px-4">ลบ</button>
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/main.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js'); ?>"></script>
<!-- Tinymce -->
<script src="<?= base_url('assets/vendors/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/tinymce/plugins/code/plugin.min.js'); ?>"></script>
<!-- Filepond validation -->
<!-- <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script> -->
<!-- <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script> -->
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-file-validate-size.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-file-validate-type.js'); ?>"></script>
<!-- Filepond -->
<!-- <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-filter/dist/filepond-plugin-image-filter.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script> -->
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-transform.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-exif-orientation.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-crop.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-filter.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-preview.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-resize.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-image-edit.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-file-encode.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/filepond-plugin-file-poster.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/FilePondPluginImageEditor.js'); ?>"></script>
<script src="<?= base_url('assets/vendors/filepond/js/pintura-umd.js'); ?>"></script>
<!-- Filepond -->
<!-- <script src="https://unpkg.com/filepond/dist/filepond.js"></script> -->
<script src="<?= base_url('assets/vendors/filepond/js/filepond.js'); ?>"></script>
<!-- Magnific -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.js"></script>
<!-- DataTables -->
<script src="<?= base_url('assets/vendors/simple-datatables/simple-datatables.js'); ?>"></script>
<!-- Include Choices JavaScript -->
<script src="<?= base_url('assets/vendors/choices.js/choices.min.js'); ?>"></script>

<script src="<?= base_url('assets/js/app.js'); ?>"></script>
<script src="<?= base_url('assets/js/modal.js'); ?>"></script>