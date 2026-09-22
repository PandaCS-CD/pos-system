<script>
    $(function() {
        // Search
        $('#searchStock').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            $('#tbStock tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(keyword));
            });
        });

        // Modal: Stock In
        $('#modalStockIn').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            $('#stockInProductId').val(btn.data('id'));
            $('#stockInProductName').val(btn.data('name'));
            $('#stockInCurrentStock').val(btn.data('stock'));
        });

        // Modal: Adjust
        $('#modalAdjust').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            $('#adjustProductId').val(btn.data('id'));
            $('#adjustProductName').val(btn.data('name'));
            $('#adjustCurrentStock').val(btn.data('stock'));
        });
    });
</script>