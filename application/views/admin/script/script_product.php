<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    // เก็บ ID ที่เลือกไว้
    let selectedIds = [];

    $(document).ready(function() {

        // โหลดค่าหมวดหมู่ที่เคยเลือกไว้จาก sessionStorage
        const savedCategory = sessionStorage.getItem('product_category_filter');
        if (savedCategory) {
            $('#category_filter').val(savedCategory);
        }

        // Initialize DataTable
        const table = $('#tbProduct').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            pageLength: 20,
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                } // ปิด sort สำหรับคอลัมน์ checkbox, ลำดับ และ แก้ไข/ลบ
            ],
            lengthMenu: [
                [10, 15, 20, 25, 50, 75, 100],
                [10, 15, 20, 25, 50, 75, 100]
            ],
            ajax: {
                url: "<?= admin_url('product/ajax_list') ?>",
                type: "POST",
                data: function(d) {
                    d.category_id = $('#category_filter').val();
                    d.search_product = $('#searchProduct').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTables error:', error, code);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
                }
            },
            drawCallback: function() {
                // หลังจากวาดตารางใหม่ ให้ตรวจสอบ checkbox ที่เคยเลือกไว้
                $('.product-checkbox').each(function() {
                    if (selectedIds.includes($(this).val())) {
                        $(this).prop('checked', true);
                    }
                });
                updateDeleteButtonState();

                // Re-init MagnificPopup for dynamically loaded images
                $(".img-link").magnificPopup({
                    removalDelay: 300,
                    mainClass: "mfp-with-zoom mfp-img-mobile",
                    type: "image",
                    gallery: {
                        enabled: false
                    }
                });
            },
            rowCallback: function(row, data, index) {
                // Apply custom styling from server
                if (data.DT_RowStyle) {
                    $(row).css(data.DT_RowStyle);
                }
            },
            language: {
                lengthMenu: "แสดง _MENU_ รายการ",
                search: "ค้นหา:",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูลที่จะแสดง",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                loadingRecords: "กำลังโหลด...",
                processing: "กำลังประมวลผล...",
                zeroRecords: "ไม่พบข้อมูล",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: ">",
                    previous: "<"
                }
            },
            "initComplete": function() {
                // Remove text node from search label
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();

                // Style search input
                $('.dataTables_filter input').attr("placeholder", "ค้นหา...").css({
                    "width": "250px",
                    "padding": "8px",
                    "border-radius": "6px",
                    "border": "1px solid #ccc",
                    "margin-bottom": "1rem"
                });

                // Style length/entries dropdown
                $('.dataTables_length select').css({
                    "padding": "6px",
                    "border-radius": "6px",
                    "border": "1px solid #ccc"
                });
            }
        });

        // Event listener สำหรับกรองตามหมวดหมู่
        $('#category_filter').on('change', function() {
            // บันทึกค่าหมวดหมู่ที่เลือกลง sessionStorage
            sessionStorage.setItem('product_category_filter', $(this).val());
            table.ajax.reload();
        });

        // Event listener สำหรับค้นหา
        $('#searchProduct').on('keyup', function() {
            table.ajax.reload();
        });

        // Function อัปเดตสถานะปุ่มลบ
        function updateDeleteButtonState() {
            $('#selectedCount').text(selectedIds.length);
            $('#deleteSelectedBtn').prop('disabled', selectedIds.length === 0);
        }

        // เมื่อ checkbox ถูกเปลี่ยน
        $(document).on('change', '.product-checkbox', function() {
            const id = $(this).val();
            if ($(this).is(':checked')) {
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
            updateDeleteButtonState();
        });

        // Select All link click
        $('#selectAll').on('click', function(e) {
            e.preventDefault();
            const checkboxes = $('.product-checkbox');
            const allChecked = checkboxes.filter(':checked').length === checkboxes.length;

            if (allChecked) {
                // ยกเลิกเลือกทั้งหมดในหน้านี้
                checkboxes.prop('checked', false);
                checkboxes.each(function() {
                    const id = $(this).val();
                    selectedIds = selectedIds.filter(item => item !== id);
                });
            } else {
                // เลือกทั้งหมดในหน้านี้
                checkboxes.prop('checked', true);
                checkboxes.each(function() {
                    const id = $(this).val();
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                });
            }
            updateDeleteButtonState();
        });

        // Select All button
        $('#selectAllBtn').on('click', function() {
            $('.product-checkbox').prop('checked', true).each(function() {
                const id = $(this).val();
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            });
            updateDeleteButtonState();
        });

        // Deselect All button
        $('#deselectAllBtn').on('click', function() {
            $('.product-checkbox').prop('checked', false);
            selectedIds = [];
            updateDeleteButtonState();
        });

        // Delete Selected button
        $('#deleteSelectedBtn').on('click', function(e) {
            e.preventDefault();
            if (selectedIds.length > 0) {
                Swal.fire({
                    title: 'ยืนยันการลบสินค้า',
                    text: `คุณแน่ใจหรือไม่ว่าต้องการลบสินค้าทั้งหมด ${selectedIds.length} รายการ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ยืนยันการลบ',
                    cancelButtonText: 'ยกเลิก',
                    footer: '<strong>การกระทำนี้ไม่สามารถย้อนกลับได้</strong>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สร้าง form และส่ง
                        const form = $('#formProduct');
                        form.find('input[name="selected_ids[]"]').remove();

                        selectedIds.forEach(function(id) {
                            form.append('<input type="hidden" name="selected_ids[]" value="' + id + '">');
                        });

                        form.attr('action', '<?= admin_url('product/delete_selected') ?>');
                        form.submit();
                    }
                });
            }
        });
    });

    // Function สำหรับลบสินค้า
    function confirmDelete(id) {
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'ยืนยันการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= admin_url('product/del/') ?>' + id;
            }
        });
    }

    // Function สำหรับลบรูปภาพในแกเลอรี่
    function delete_image(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0d6efd",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                var url = '<?= admin_url('product/del_image/') ?>' + id;
                $.ajax({
                    url: url,
                    type: "post",
                    // data: values,
                    success: function(res) {
                        console.log(res);
                        var json = JSON.parse(res);
                        if (json.success == 0) {
                            Swal.fire({
                                icon: 'success',
                                title: json.msg,
                                showConfirmButton: true,
                                allowOutsideClick: false
                            });
                            $('#gal-' + id).remove();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.msg,
                                showConfirmButton: true,
                                allowOutsideClick: false
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });


            }
        });
    }
</script>