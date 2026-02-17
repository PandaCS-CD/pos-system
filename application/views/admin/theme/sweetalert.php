<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($this->session->flashdata('result') == 'true') {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: '" . $this->session->flashdata('message') . "', 
            confirmButtonColor: '#198754',
        })
    </script>";
} ?>
<?php if ($this->session->flashdata('result') == 'false') {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ไม่สำเร็จ',
            text: '" . $this->session->flashdata('message') . "',
            confirmButtonColor: '#198754',
        })
    </script>";
} ?>
<?php if ($this->session->flashdata('result') == 'duplicate') {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'ไม่สำเร็จ',
            text: '" . $this->session->flashdata('message') . "',
            confirmButtonColor: '#198754',
        })
    </script>";
} ?>

<script>
    function confirm(url) {
        Swal.fire({
            html: "<br> <label class='fw-bold' style='font-size: 26px;'>คุณต้องการออกจากระบบ ?</label> <br><br>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3950a2',
            cancelButtonColor: "#84868c",
            confirmButtonText: "ยืนยัน!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>