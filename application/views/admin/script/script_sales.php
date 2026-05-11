<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(function() {
        // Flatpickr date range → hidden inputs
        flatpickr("#dateRange", {
            allowInput: true,
            enableTime: false,
            dateFormat: "d-m-Y",
            mode: "range",
            locale: "en",
            disableMobile: true,
            defaultDate: [new Date(), new Date()],
            onChange: function(selectedDates) {
                var fmt = function(d) {
                    var yyyy = d.getFullYear();
                    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
                    var dd = ('0' + d.getDate()).slice(-2);
                    return yyyy + '-' + mm + '-' + dd;
                };
                if (selectedDates.length === 2) {
                    $('#dateFrom').val(fmt(selectedDates[0]));
                    $('#dateTo').val(fmt(selectedDates[1]));
                } else if (selectedDates.length === 1) {
                    $('#dateFrom').val(fmt(selectedDates[0]));
                    $('#dateTo').val(fmt(selectedDates[0]));
                }
            }
        });

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