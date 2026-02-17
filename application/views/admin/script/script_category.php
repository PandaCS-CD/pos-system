<script>
    // เคลียร์ checkbox เมื่อกลับจากหน้าอื่น (รองรับ back-forward cache)
    window.addEventListener('pageshow', function(event) {
        $('.message-checkbox').prop('checked', false);
        $('#selectedCount').text(0);
        $('#deleteSelectedBtn').prop('disabled', true);
        $('#selectAll').text('เลือก');
    });

    // Initialize the message modal with data
    $(document).ready(function() {

        // เคลียร์ checkbox ที่เลือกไว้เมื่อโหลดหน้าใหม่ (กลับจากหน้าเพิ่ม/แก้ไข)
        $('.message-checkbox').prop('checked', false).each(function() {
            this.checked = false;
        });
        // อัพเดทสถานะปุ่มลบ
        $('#selectedCount').text(0);
        $('#deleteSelectedBtn').prop('disabled', true);

        // Handle checkbox selection
        const $selectAllLink = $('#selectAll');
        const $messageCheckboxes = $('.message-checkbox');
        const $deleteSelectedBtn = $('#deleteSelectedBtn');
        const $categoryForm = $('#formcategory');
        const $tableRows = $('table.dataTable tbody tr');

        // Function to update delete button state and link text
        function updateDeleteButtonState() {
            const visibleCheckboxes = $messageCheckboxes.filter(':visible');
            const checkedBoxes = visibleCheckboxes.filter(':checked').length;
            const totalBoxes = visibleCheckboxes.length;

            $('#selectedCount').text(checkedBoxes);
            $deleteSelectedBtn.prop('disabled', checkedBoxes === 0);

            // เปลี่ยนข้อความ link
            if (checkedBoxes === totalBoxes && totalBoxes > 0) {
                $selectAllLink.text('ไม่เลือก');
            } else {
                $selectAllLink.text('เลือก');
            }
        }

        //  ฟังก์ชันค้นหา
        // $('#searchCategory').on('keyup', function() {
        //     const searchText = $(this).val().toLowerCase();

        //     $tableRows.each(function() {
        //         const rowText = $(this).text().toLowerCase();
        //         if (rowText.indexOf(searchText) > -1) {
        //             $(this).show();
        //         } else {
        //             $(this).hide();
        //         }
        //     });

        //     updateDeleteButtonState();
        // });

        // Select/Deselect all link click
        $selectAllLink.on('click', function(e) {
            e.preventDefault();
            const visibleCheckboxes = $messageCheckboxes.filter(':visible');
            const isAllChecked = visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;
            visibleCheckboxes.prop('checked', !isAllChecked);
            updateDeleteButtonState();
        });

        // Individual checkbox change
        $messageCheckboxes.on('change', function() {
            updateDeleteButtonState();
        });

        // Select All button
        $('#selectAllBtn').on('click', function() {
            $messageCheckboxes.filter(':visible').prop('checked', true);
            updateDeleteButtonState();
        });

        // Deselect All button
        $('#deselectAllBtn').on('click', function() {
            $messageCheckboxes.prop('checked', false);
            updateDeleteButtonState();
        });

        // Delete Selected button handling with SweetAlert
        $deleteSelectedBtn.on('click', function(e) {
            e.preventDefault();
            const checkedBoxes = $messageCheckboxes.filter(':checked');
            if (checkedBoxes.length > 0) {
                Swal.fire({
                    title: 'ยืนยันการลบข้อความ',
                    text: `คุณแน่ใจหรือไม่ว่าต้องการลบข้อความทั้งหมด ${checkedBoxes.length} รายการ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ยืนยันการลบ',
                    cancelButtonText: 'ยกเลิก',
                    footer: '<strong>การกระทำนี้ไม่สามารถย้อนกลับได้</strong>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $categoryForm.attr('action', '<?= admin_url('category/delete_selected') ?>');
                        $categoryForm.submit();
                    }
                });
            }
        });

        // Single delete with SweetAlert
        $(document).on('click', '.delete-single', function() {
            const messageId = $(this).data('id');

            console.log('Delete message ID:', messageId);
            Swal.fire({
                title: 'ยืนยันการลบข้อความ',
                text: 'คุณแน่ใจหรือไม่ว่าต้องการลบข้อความนี้?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก',
                footer: '<strong>การกระทำนี้ไม่สามารถย้อนกลับได้</strong>'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to delete URL
                    window.location.href = '<?= admin_url('category/del/') ?>' + messageId;
                }
            });
        });

        // เริ่มต้น
        updateDeleteButtonState();
    });
</script>