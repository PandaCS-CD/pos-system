<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS ร้านขายของเบ็ดเตล็ด - เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/icons/favicon.png'); ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css'); ?>">


    <!-- JQ  Validate-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <style>
        body {
            background: linear-gradient(to right, #bbbbbb, #ffffff);
            font-family: 'Sarabun', sans-serif;
        }

        .login-wrapper {
            max-width: 900px;
            margin: 60px auto;
            display: flex;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(to right bottom, #1a365d, #2b6cb0);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-left h2 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .login-left p {
            font-size: 14px;
            color: #cbd5e1;
        }

        .login-right {
            flex: 1;
            padding: 40px;
        }

        .btn-primary {
            background: linear-gradient(to right, #2b6cb0, #3182ce, #4299e1);
            border: none;
            border-radius: 10px;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .test-account {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .test-account small {
            color: #64748b;
        }

        .form-control-icon {
            z-index: 50;
        }

        .form-group[class*="has-icon-"] .form-control-icon {
            top: 13%;
        }

        .form-group[class*="has-icon-"] .form-control.is-invalid~.form-control-icon {
            top: 10%;
        }

        .form-control {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                flex: unset;
                width: 100%;
            }

            .login-left {
                display: none;
                padding: 30px 20px;
            }

            .login-right {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            .login-left h2 {
                font-size: 20px;
            }

            .login-left p {
                font-size: 12px;
            }

            .btn {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="login-wrapper">
            <!-- LEFT SIDE -->
            <div class="login-left text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" height="100" width="100" viewBox="0 0 576 512">
                    <path d="M547.6 103.8L490.3 13.1C485.2 5 476.1 0 466.4 0L109.6 0C99.9 0 90.8 5 85.7 13.1L28.3 103.8c-29.6 46.8-3.4 111.9 51.9 119.4c4 .5 8.1 .8 12.1 .8c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.2 0 49.3-11.4 65.2-29c16 17.6 39.1 29 65.2 29c4.1 0 8.1-.3 12.1-.8c55.5-7.4 81.8-72.5 52.1-119.4zM499.7 254c-9.9 0-19.7-1.5-29.1-4.4C454.6 256.9 436.9 261 418.6 261c-18.3 0-36-4.1-52-11.4c-16 7.3-33.7 11.4-52 11.4c-18.3 0-36-4.1-52-11.4c-16 7.3-33.7 11.4-52 11.4c-18.3 0-36-4.1-52-11.4c-9.3 2.9-19.2 4.4-29.1 4.4c-9.9 0-19.7-1.5-29.1-4.4C84.4 256.9 66.7 261 48.4 261l0 0L48 480c0 17.7 14.3 32 32 32l128 0 0-128c0-17.7 14.3-32 32-32l96 0c17.7 0 32 14.3 32 32l0 128 128 0c17.7 0 32-14.3 32-32l0-219c-18.3 0-36-4.1-52-11.4c-9.4 2.9-19.2 4.4-29.1 4.4z" />
                </svg>
                <h2 class="mt-4">ระบบ POS</h2>
                <p>ร้านขายของเบ็ดเตล็ด</p>
                <p class="small">ระบบจัดการการขายหน้าร้าน</p>
            </div>

            <!-- RIGHT SIDE -->
            <div class="login-right">
                <div class="text-center  pt-4">
                    <h4 class="mb-3">เข้าสู่ระบบ POS</h4>
                    <p class="text-muted mb-4">กรุณาเข้าสู่ระบบเพื่อเริ่มใช้งาน</p>
                </div>

                <form id="formLogin" method="post">
                    <div class="mb-2 pt-4">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้งาน">
                            <div class="form-control-icon text-center d-flex justify-content-center align-items-center">
                                <i class="fa-regular fa-user" style="font-size: 1rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่าน</label>
                        <div class="form-group position-relative has-icon-left">
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน">
                                <button type="button" class="btn btn-light"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <div class="form-control-icon text-center d-flex justify-content-center align-items-center">
                                <i class="fa-solid fa-lock" style="font-size: 1rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid mt-5 pb-5">
                        <button type="submit" class="btn btn-primary py-3"><i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    jQuery.extend(jQuery.validator.messages, {
        required: "โปรดกรอกข้อมูลช่องนี้.",
        remote: "Please fix this field.",
        email: "Please enter a valid email address.",
        url: "Please enter a valid URL.",
        date: "Please enter a valid date.",
        dateISO: "Please enter a valid date (ISO).",
        number: "Please enter a number only.",
        digits: "Please enter only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        accept: "Please enter a value with a valid extension.",
        maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
        minlength: jQuery.validator.format("Please enter at least {0} characters."),
        rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
        range: jQuery.validator.format("Please enter a value between {0} and {1}."),
        max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
        min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
    });

    //Validation
    jQuery(document).ready(function() {
        $.validator.setDefaults({
            ignore: []
        });
        var form = $('#formLogin');
        form.each(function() {
            var elem = $(this);
            elem.validate({
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: "div",
                focusInvalid: true,
                rules: {
                    username: {
                        required: true,
                    },
                    password: {
                        required: true,
                    }
                },
                validHandler: function(elem, validator) {
                    console.log('1');
                },
                errorPlacement: function(error, element) {
                    if (element.attr("type") === "file") {
                        const formGroup = element.closest(".form-group");
                        if (formGroup.length) {
                            formGroup.find("div.error").remove();
                            error.addClass("error d-block mt-1");
                            formGroup.append(error);
                        } else {
                            element.after(error);
                        }
                    } else if (element.closest('.input-group').length) {
                        // ✅ ถ้าอยู่ใน input-group ให้แสดง error หลัง input-group
                        error.addClass("error d-block mt-1");
                        element.closest('.input-group').after(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    $("#formLogin input,textarea,select").attr('readonly', 'readonly');
                    //form.submit();
                    return true;
                },
            });
        });
    });

    $('.btn-light').on('click', function() {
        const $btn = $(this);
        const $input = $btn.siblings('input');
        const $eyeIcon = $btn.find('i');

        // toggle password visibility
        const isPassword = $input.attr('type') === 'password';
        $input.attr('type', isPassword ? 'text' : 'password');

        // toggle eye icon
        $eyeIcon.toggleClass('fa-eye fa-eye-slash');

        // toggle lock icon (fa-lock <-> fa-unlock)
        const $formGroup = $btn.closest('.form-group');
        const $lockIcon = $formGroup.find('.form-control-icon i');

        $lockIcon.toggleClass('fa-lock fa-unlock-keyhole');
    });
</script>


<?php if ($this->session->flashdata('result') == 'true') {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'ดำเนินการสำเร็จ',
            text: '" . $this->session->flashdata('message') . "', 
            confirmButtonColor: '#198754',
        })

    </script>";
} ?>
<?php if ($this->session->flashdata('result') == 'false') {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: '" . $this->session->flashdata('message') . "',
            confirmButtonColor: '#198754',
        })
            
    </script>";
} ?>
<?php if ($this->session->flashdata('result') == 'duplicate') {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'ดำเนินการไม่สำเร็จ',
            text: '" . $this->session->flashdata('message') . "',
            confirmButtonColor: '#198754',
        })
    </script>";
} ?>


</html>