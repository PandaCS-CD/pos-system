<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(function() {
        var table = $('#tbSales').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '<?= admin_url("sales/ajax_list") ?>',
                type: 'POST',
                data: function(d) {
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                }
            },
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i>',
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีรายการ",
                emptyTable: "ไม่พบข้อมูล",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            },
            pageLength: 25,
            order: [],
            columnDefs: [{
                targets: [0, 7],
                orderable: false
            }]
        });

        // Filter button
        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });
    });
</script>