<script>
    jQuery.extend(jQuery.validator.messages, {
        required: "โปรดกรอกข้อมูลช่องนี้",
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
        var form = $('#formCreateAdmin');
        form.each(function() {
            var elem = $(this);
            elem.validate({
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: "div",
                focusInvalid: true,
                messages: {
                    password: {
                        minlength: jQuery.validator.format("รหัสผ่านต้องไม่น้อยกว่า {0} ตัวอักษร."),
                    },
                    passwordCf: {
                        equalTo: "รหัสผ่านไม่ตรงกัน.",
                    },
                    email: {
                        remote: "อีเมลนี้มีในระบบแล้ว.",
                    },
                    username: {
                        remote: "ผู้ใช้นี้มีในระบบแล้ว.",
                    }
                },
                rules: {
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: "<?= base_url('api/api/checkRegisterEmail') ?>",
                            type: "POST",
                            dataType: "json",
                            data: "email=" + $("#admin_email").val(),
                            dataFilter: function(data) {
                                // console.log(data);
                                var json = JSON.parse(data);
                                if (json.success == 0) {
                                    //console.log('1')
                                    return true;
                                } else {
                                    //console.log('0')
                                    return false;
                                }
                            },
                            beforeSend: function(xhr, opts) {
                                //console.log( $('#payerEmail').val());
                                // console.log(opts);
                                opts.data = "email=" + $("#admin_email").val();
                            },
                        },
                    },
                    username: {
                        required: true,
                        remote: {
                            url: "<?= base_url('api/api/checkRegisterUsername') ?>",
                            type: "POST",
                            dataType: "json",
                            data: "username=" + $("#username").val(),
                            dataFilter: function(data) {
                                // console.log(data);
                                var json = JSON.parse(data);
                                if (json.success == 0) {
                                    //console.log('1')
                                    return true;
                                } else {
                                    //console.log('0')
                                    return false;
                                }
                            },
                            beforeSend: function(xhr, opts) {
                                //console.log( $('#payerEmail').val());
                                // console.log(opts);
                                opts.data = "username=" + $("#username").val();
                            },
                        },
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    passwordCf: {
                        required: true,
                        equalTo: "#password",
                    },
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass("tiny-editor")) {
                        const tinymceWrapper = element.siblings(".tox-tinymce");
                        if (tinymceWrapper.length) {
                            tinymceWrapper.after(error);
                        } else {
                            // ถ้ายังไม่มี wrapper แสดงว่ากำลัง init → เก็บไปวางไว้ที่ textarea ก่อนก็ได้
                            element.after(error);
                        }
                        return;
                    }

                    if (element.hasClass("choices__input--cloned")) {
                        // หา Choices container ของ select ตัวจริง
                        let choicesContainer = element.closest(".choices");
                        if (choicesContainer.length) {
                            error.insertAfter(choicesContainer);
                        } else {
                            error.insertAfter(element);
                        }
                    } else if (
                        element.is("select") &&
                        element.closest(".form-group").length
                    ) {
                        const choicesEl = element
                            .closest(".form-group")
                            .find(".choices")
                            .first();
                        if (choicesEl.length) {
                            choicesEl.after(error);
                            return;
                        }
                        error.insertAfter(element);
                    } else {
                        // กรณีอื่นๆ ใช้ของเดิม
                        error.insertAfter(element);
                    }

                    if (element.is("select") && element.closest(".form-group").length) {
                        const choicesEl = element
                            .closest(".form-group")
                            .find(".choices")
                            .first();
                        if (choicesEl.length) {
                            choicesEl.after(error);
                            return;
                        }
                    }
                    // กรณีอื่น ๆ
                    const isFileInput = element.attr("type") === "file";
                    const isTinyEditor = element.hasClass("tiny-editor");
                    const hasIconRight = element.closest(".has-icon-right").length > 0;
                    const inputGroup = element.closest(".input-group");
                    const formGroup = element.closest(".form-group");
                    const pondRoot = element.closest(".filepond--root");

                    if (isFileInput && pondRoot.length) {
                        pondRoot.after(error);
                    } else if (isTinyEditor) {
                        const tinymceWrapper = element.siblings(".tox-tinymce");
                        if (tinymceWrapper.length) {
                            tinymceWrapper.after(error);
                        } else {
                            element.after(error);
                        }
                    } else if (inputGroup.length) {
                        inputGroup.after(error);
                        return;
                    } else if (hasIconRight && formGroup.length) {
                        formGroup.after(error);
                    } else {
                        element.after(error);
                    }

                    if (element.attr("type") === "radio") {
                        const radioGroup = element.closest(".radio-wrapper");
                        if (radioGroup.length) {
                            radioGroup.after(error);
                        } else {
                            element.closest(".form-group").append(error);
                        }
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element) {
                    const $el = $(element);
                    const isFileInput = $el.attr("type") === "file";
                    const parent = $el.closest(".has-icon-right");

                    if ($el.attr("type") === "radio") {
                        const name = $el.attr("name");
                        $(`input[name="${name}"]`).removeClass("is-valid is-invalid");
                        return;
                    }
                    $el.addClass("is-invalid").removeClass("is-valid");

                    if (isFileInput) {
                        $el.closest(".filepond--root").addClass("invalid-filepond");
                    }

                    if (parent.length) {
                        parent.addClass("has-error-icon").removeClass("has-valid-icon");
                    }

                    if ($el.hasClass("tiny-editor")) {
                        const iframe = $el.siblings(".tox-tinymce").find("iframe");
                        iframe.addClass("is-invalid");
                    } else {
                        $el.addClass("is-invalid").removeClass("is-valid");
                    }
                },

                unhighlight: function(element) {
                    const $el = $(element);
                    const isFileInput = $el.attr("type") === "file";
                    const parent = $el.closest(".has-icon-right");

                    if ($el.attr("type") === "radio") {
                        // ❌ ลบทุก class ออกจาก radio
                        const name = $el.attr("name");
                        const radios = $(`input[name="${name}"]`);
                        radios.removeClass("is-valid is-invalid");
                        return;
                    }

                    $el.removeClass("is-invalid").addClass("is-valid");

                    if (isFileInput) {
                        $el.removeClass("is-invalid").addClass("is-valid");
                        $el.closest(".filepond--root").removeClass("invalid-filepond");

                        const errorId = $el.attr("aria-describedby");
                        if (errorId) {
                            $("#" + errorId)
                                .removeClass("is-invalid d-block")
                                .text("");
                        }
                    } else {
                        if ($el.is("select")) {
                            // หา .choices แบบยืดหยุ่น
                            let choicesEl = $el.next(".choices");
                            if (choicesEl.length === 0) {
                                choicesEl = $el.siblings(".choices");
                            }
                            if (choicesEl.length === 0) {
                                choicesEl = $el.closest(".form-group").find(".choices").first();
                            }
                            if (choicesEl.length) {
                                choicesEl.find(".choices__inner").removeClass("is-invalid");
                            } else {
                                $el.removeClass("is-invalid").addClass("is-valid");
                            }
                        } else {
                            $el.removeClass("is-invalid").addClass("is-valid");
                        }
                    }

                    if (parent.length) {
                        parent.addClass("has-valid-icon").removeClass("has-error-icon");
                    }

                    if ($el.hasClass("tiny-editor")) {
                        const iframe = $el.siblings(".tox-tinymce").find("iframe");
                        iframe.removeClass("is-invalid");
                    } else {
                        $el.removeClass("is-invalid").addClass("is-valid");
                    }
                },
                validHandler: function(elem, validator) {
                    console.log('1');
                },
                submitHandler: function(form) {
                    $("#formCreateAdmin input,textarea,select").attr('readonly', 'readonly');
                    //form.submit();
                    return true;
                },
            });
            document.querySelectorAll(".choices").forEach(function(selectEl) {
                selectEl.addEventListener("change", function(event) {
                    const el = $(event.target);

                    // ตรวจสอบว่ามี rule หรือ required จริงก่อนถึงจะ validate
                    const validator = el.closest("form").data("validator");
                    if (!validator) return;

                    const name = el.attr("name");
                    const rules = validator.settings.rules || {};

                    // เฉพาะกรณี field นี้มี rule หรือ required
                    if (
                        el.attr("required") ||
                        (rules[name] && Object.keys(rules[name]).length > 0)
                    ) {
                        el.valid();
                    }
                });
            });
        });
    });

    //Validation
    jQuery(document).ready(function() {
        $.validator.setDefaults({
            ignore: []
        });
        var form = $('#formUpdateAdmin');
        form.each(function() {
            var elem = $(this);
            elem.validate({
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: "div",
                focusInvalid: true,
                messages: {
                    password: {
                        minlength: jQuery.validator.format("รหัสผ่านต้องไม่น้อยกว่า {0} ตัวอักษร."),
                    },
                    passwordCf: {
                        equalTo: "รหัสผ่านไม่ตรงกัน.",
                    },
                    email: {
                        remote: "อีเมลนี้มีในระบบแล้ว.",
                    },
                    username: {
                        remote: "ผู้ใช้นี้มีในระบบแล้ว.",
                    }
                },
                rules: {
                    email: {
                        email: true,
                        remote: {
                            url: "/api/checkRegisterEmail",
                            type: "POST",
                            dataType: "json",
                            data: "email=" + $("#admin_email").val(),
                            dataFilter: function(data) {
                                var json = JSON.parse(data);
                                var after_mail = $("#admin_email").val()
                                var befor_mail = $("#admin_update_email").val()
                                if (json.success == 0) {
                                    return true;
                                } else {
                                    if (befor_mail == after_mail) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }
                            },
                            beforeSend: function(xhr, opts) {
                                //console.log( $('#payerEmail').val());
                                // console.log(opts);
                                opts.data = "email=" + $("#admin_email").val();
                            },
                        },
                    },
                    username: {
                        required: true,
                        remote: {
                            url: "<?= base_url('api/api/checkRegisterUsername') ?>",
                            type: "POST",
                            dataType: "json",
                            data: "username=" + $("#username").val(),
                            dataFilter: function(data) {
                                // console.log(data);
                                var json = JSON.parse(data);
                                var after_user = $("#username").val()
                                var befor_user = $("#admin_update_username").val()
                                if (json.success == 0) {
                                    return true;
                                } else {
                                    if (after_user == befor_user) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }
                            },
                            beforeSend: function(xhr, opts) {
                                //console.log( $('#payerEmail').val());
                                // console.log(opts);
                                opts.data = "username=" + $("#username").val();
                            },
                        },
                    },
                    password: {
                        minlength: 6
                    },
                    passwordCf: {
                        equalTo: "#password",
                    },
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass("tiny-editor")) {
                        const tinymceWrapper = element.siblings(".tox-tinymce");
                        if (tinymceWrapper.length) {
                            tinymceWrapper.after(error);
                        } else {
                            // ถ้ายังไม่มี wrapper แสดงว่ากำลัง init → เก็บไปวางไว้ที่ textarea ก่อนก็ได้
                            element.after(error);
                        }
                        return;
                    }

                    if (element.hasClass("choices__input--cloned")) {
                        // หา Choices container ของ select ตัวจริง
                        let choicesContainer = element.closest(".choices");
                        if (choicesContainer.length) {
                            error.insertAfter(choicesContainer);
                        } else {
                            error.insertAfter(element);
                        }
                    } else if (
                        element.is("select") &&
                        element.closest(".form-group").length
                    ) {
                        const choicesEl = element
                            .closest(".form-group")
                            .find(".choices")
                            .first();
                        if (choicesEl.length) {
                            choicesEl.after(error);
                            return;
                        }
                        error.insertAfter(element);
                    } else {
                        // กรณีอื่นๆ ใช้ของเดิม
                        error.insertAfter(element);
                    }

                    if (element.is("select") && element.closest(".form-group").length) {
                        const choicesEl = element
                            .closest(".form-group")
                            .find(".choices")
                            .first();
                        if (choicesEl.length) {
                            choicesEl.after(error);
                            return;
                        }
                    }
                    // กรณีอื่น ๆ
                    const isFileInput = element.attr("type") === "file";
                    const isTinyEditor = element.hasClass("tiny-editor");
                    const hasIconRight = element.closest(".has-icon-right").length > 0;
                    const inputGroup = element.closest(".input-group");
                    const formGroup = element.closest(".form-group");
                    const pondRoot = element.closest(".filepond--root");

                    if (isFileInput && pondRoot.length) {
                        pondRoot.after(error);
                    } else if (isTinyEditor) {
                        const tinymceWrapper = element.siblings(".tox-tinymce");
                        if (tinymceWrapper.length) {
                            tinymceWrapper.after(error);
                        } else {
                            element.after(error);
                        }
                    } else if (inputGroup.length) {
                        inputGroup.after(error);
                        return;
                    } else if (hasIconRight && formGroup.length) {
                        formGroup.after(error);
                    } else {
                        element.after(error);
                    }

                    if (element.attr("type") === "radio") {
                        const radioGroup = element.closest(".radio-wrapper");
                        if (radioGroup.length) {
                            radioGroup.after(error);
                        } else {
                            element.closest(".form-group").append(error);
                        }
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element) {
                    const $el = $(element);
                    const isFileInput = $el.attr("type") === "file";
                    const parent = $el.closest(".has-icon-right");

                    if ($el.attr("type") === "radio") {
                        const name = $el.attr("name");
                        $(`input[name="${name}"]`).removeClass("is-valid is-invalid");
                        return;
                    }
                    $el.addClass("is-invalid").removeClass("is-valid");

                    if (isFileInput) {
                        $el.closest(".filepond--root").addClass("invalid-filepond");
                    }

                    if (parent.length) {
                        parent.addClass("has-error-icon").removeClass("has-valid-icon");
                    }

                    if ($el.hasClass("tiny-editor")) {
                        const iframe = $el.siblings(".tox-tinymce").find("iframe");
                        iframe.addClass("is-invalid");
                    } else {
                        $el.addClass("is-invalid").removeClass("is-valid");
                    }
                },

                unhighlight: function(element) {
                    const $el = $(element);
                    const isFileInput = $el.attr("type") === "file";
                    const parent = $el.closest(".has-icon-right");

                    if ($el.attr("type") === "radio") {
                        // ❌ ลบทุก class ออกจาก radio
                        const name = $el.attr("name");
                        const radios = $(`input[name="${name}"]`);
                        radios.removeClass("is-valid is-invalid");
                        return;
                    }

                    $el.removeClass("is-invalid").addClass("is-valid");

                    if (isFileInput) {
                        $el.removeClass("is-invalid").addClass("is-valid");
                        $el.closest(".filepond--root").removeClass("invalid-filepond");

                        const errorId = $el.attr("aria-describedby");
                        if (errorId) {
                            $("#" + errorId)
                                .removeClass("is-invalid d-block")
                                .text("");
                        }
                    } else {
                        if ($el.is("select")) {
                            // หา .choices แบบยืดหยุ่น
                            let choicesEl = $el.next(".choices");
                            if (choicesEl.length === 0) {
                                choicesEl = $el.siblings(".choices");
                            }
                            if (choicesEl.length === 0) {
                                choicesEl = $el.closest(".form-group").find(".choices").first();
                            }
                            if (choicesEl.length) {
                                choicesEl.find(".choices__inner").removeClass("is-invalid");
                            } else {
                                $el.removeClass("is-invalid").addClass("is-valid");
                            }
                        } else {
                            $el.removeClass("is-invalid").addClass("is-valid");
                        }
                    }

                    if (parent.length) {
                        parent.addClass("has-valid-icon").removeClass("has-error-icon");
                    }

                    if ($el.hasClass("tiny-editor")) {
                        const iframe = $el.siblings(".tox-tinymce").find("iframe");
                        iframe.removeClass("is-invalid");
                    } else {
                        $el.removeClass("is-invalid").addClass("is-valid");
                    }
                },
                validHandler: function(elem, validator) {
                    console.log('1');
                },
                submitHandler: function(form) {
                    $("#formUpdateAdmin input,textarea,select").attr('readonly', 'readonly');
                    //form.submit();
                    return true;
                },
            });
            document.querySelectorAll(".choices").forEach(function(selectEl) {
                selectEl.addEventListener("change", function(event) {
                    const el = $(event.target);

                    // ตรวจสอบว่ามี rule หรือ required จริงก่อนถึงจะ validate
                    const validator = el.closest("form").data("validator");
                    if (!validator) return;

                    const name = el.attr("name");
                    const rules = validator.settings.rules || {};

                    // เฉพาะกรณี field นี้มี rule หรือ required
                    if (
                        el.attr("required") ||
                        (rules[name] && Object.keys(rules[name]).length > 0)
                    ) {
                        el.valid();
                    }
                });
            });
        });
    });
</script>