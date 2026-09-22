//Validation
// jQuery.extend(jQuery.validator.messages, {
// 	required: "Please input this field",
// 	remote: "Please fix this field.",
// 	email: "Please enter a valid email address.",
// 	url: "Please enter a valid URL.",
// 	date: "Please enter a valid date.",
// 	dateISO: "Please enter a valid date (ISO).",
// 	number: "Please enter a number only.",
// 	digits: "Please enter only digits.",
// 	creditcard: "Please enter a valid credit card number.",
// 	equalTo: "Please enter the same value again.",
// 	accept: "Please enter a value with a valid extension.",
// 	maxlength: jQuery.validator.format(
// 		"Please enter no more than {0} characters."
// 	),
// 	minlength: jQuery.validator.format("Please enter at least {0} characters."),
// 	rangelength: jQuery.validator.format(
// 		"Please enter a value between {0} and {1} characters long."
// 	),
// 	range: jQuery.validator.format("Please enter a value between {0} and {1}."),
// 	max: jQuery.validator.format(
// 		"Please enter a value less than or equal to {0}."
// 	),
// 	min: jQuery.validator.format(
// 		"Please enter a value greater than or equal to {0}."
// 	),
// });
jQuery.extend(jQuery.validator.messages, {
	required: "โปรดกรอกข้อมูลช่องนี้",
	remote: "โปรดแก้ไขข้อมูลในช่องนี้",
	email: "โปรดกรอกอีเมลให้ถูกต้อง",
	url: "โปรดกรอก URL ให้ถูกต้อง",
	date: "โปรดกรอกวันที่ให้ถูกต้อง",
	dateISO: "โปรดกรอกวันที่ในรูปแบบ ISO ให้ถูกต้อง",
	number: "โปรดกรอกตัวเลขเท่านั้น",
	digits: "โปรดกรอกเฉพาะตัวเลขจำนวนเต็ม",
	creditcard: "โปรดกรอกหมายเลขบัตรเครดิตให้ถูกต้อง",
	equalTo: "โปรดกรอกข้อมูลให้ตรงกัน",
	accept: "โปรดเลือกไฟล์ที่มีนามสกุลถูกต้อง",
	maxlength: jQuery.validator.format("โปรดอย่ากรอกเกิน {0} ตัวอักษร"),
	minlength: jQuery.validator.format("โปรดกรอกอย่างน้อย {0} ตัวอักษร"),
	rangelength: jQuery.validator.format(
		"โปรดกรอกข้อมูลความยาวระหว่าง {0} ถึง {1} ตัวอักษร",
	),
	range: jQuery.validator.format("โปรดกรอกค่าระหว่าง {0} ถึง {1}"),
	max: jQuery.validator.format("โปรดกรอกค่าน้อยกว่าหรือเท่ากับ {0}"),
	min: jQuery.validator.format("โปรดกรอกค่ามากกว่าหรือเท่ากับ {0}"),
});

jQuery(document).ready(function () {
	$.validator.setDefaults({
		ignore: [], // อย่าละเว้น input ที่ hidden เช่น select2 หรือ file input
	});

	var form = $(".form-validator");
	form.each(function () {
		var elem = $(this);
		const rules = {};
		elem.validate({
			errorClass: "is-invalid",
			validClass: "is-valid",
			errorElement: "div",
			focusInvalid: true,
			rules: rules,
			errorPlacement: function (error, element) {
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
			highlight: function (element) {
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

			unhighlight: function (element) {
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
			submitHandler: function (form) {
				tinymce.triggerSave();
				$(".form-validator input,textarea,select").attr("readonly", "readonly");
				return true;
			},
		});

		document.querySelectorAll(".choices").forEach(function (selectEl) {
			selectEl.addEventListener("change", function (event) {
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

		$('input[type="radio"]').on("change", function () {
			const name = $(this).attr("name");
			const radios = $(`input[name="${name}"]`);

			radios.removeClass("is-valid is-invalid");
		});

		$("input[type='file']").on("change", function () {
			$(this).valid();
		});
	});
});

function showElement($el, $trigger) {
	const method =
		$trigger.data("slide-show") || $el.data("slide-show") || "show";
	if (typeof $el[method] === "function") {
		if (method === "show") {
			$el.show(500); // เพิ่มความช้า 500ms
		} else {
			$el[method](500);
		}
	} else {
		$el.show(500);
	}

	$el.find("input, select, textarea").each(function () {
		const $field = $(this);
		if ($field.attr("data-required") === "true") {
			$field.attr("required", true);
		}
	});
}

function hideElement($el, $trigger) {
	const method =
		$trigger.data("slide-hide") || $el.data("slide-hide") || "hide";
	if (typeof $el[method] === "function") {
		if (method === "hide") {
			$el.hide(500); // เพิ่มความช้า 500ms
		} else {
			$el[method](500);
		}
	} else {
		$el.hide(500);
	}

	$el.find("input, select, textarea").each(function () {
		const $field = $(this);
		$field.removeAttr("required");
	});
}

function hideTargets(selector) {
	$(selector).each(function () {
		const $el = $(this);
		const target = $el.data("show-element");
		if (target) {
			hideElement($(target), $el);
		}
	});
}

$(document).ready(function () {
	// MagnificPopup
	$(".img-link").magnificPopup({
		removalDelay: 300, // Delay in milliseconds before popup is removed
		mainClass: "mfp-with-zoom mfp-img-mobile", // this class is for CSS animation below
		type: "image",
		gallery: {
			enabled: false,
		},
		zoom: {
			enabled: true,
			duration: 300, // don't foget to change the duration also in CSS
			easing: "ease-in-out", // CSS transition easing function
			opener: function (element) {
				return element.find("img");
			},
		},
	});

	// Datepicker
	$(".datepicker").datepicker({
		format: "dd-mm-yyyy",
		clearBtn: true,
		todayHighlight: true,
		container: "body",
		todayBtn: "linked",
	});

	$(document).on("input", 'input[data-oninput="number"]', function () {
		this.value = this.value.replace(/[^0-9]/g, "");
	});

	// ซ่อน element ของ radio ทั้งหมดตอนโหลด
	$("input[type=radio][data-show-element]").each(function () {
		const target = $(this).data("show-element");
		if (target) {
			$(target).hide(500);
		}
	});

	// ซ่อน element ของ <a> ที่มี data-show-element ตอนโหลด
	$("a[data-show-element]").each(function () {
		const target = $(this).data("show-element");
		if (target) {
			$(target).hide(500);
		}
	});

	// ซ่อน container ที่จะใช้กับ select option ตอนโหลด
	$("select option[data-show-element]").each(function () {
		const target = $(this).data("show-element");
		if (target) {
			$("#" + target).hide(500);
		}
	});

	// event change ของ input radio
	$("input[type=radio]").on("change", function () {
		const $selected = $(this);
		const name = $selected.attr("name");

		// ซ่อนทุก element ที่เกี่ยวกับ radio group นี้
		hideTargets(`input[type=radio][name="${name}"][data-show-element]`);

		// แสดง element ของ radio ที่เลือก
		const target = $selected.data("show-element");
		if (target) {
			showElement($(target), $selected);
		}
	});

	// event click ของ <a>
	$("a[data-show-element]").on("click", function (e) {
		e.preventDefault();

		const $trigger = $(this);
		const target = $trigger.data("show-element");
		if (target) {
			showElement($(target), $trigger);
		}
	});

	$("select").on("change", function () {
		const $select = $(this);
		const lastTarget = $select.data("lastTarget");
		const selectedValue = $select.val();

		// หา option ที่ถูกเลือกด้วย value
		const $selectedOption = $select.find(`option[value="${selectedValue}"]`);
		const target = $selectedOption.data("show-element");

		if (target === lastTarget) return;

		$select.data("lastTarget", target);

		// ซ่อนทั้งหมดที่มี data-show-element
		$select.find("option[data-show-element]").each(function () {
			const t = $(this).data("show-element");
			if (t) {
				hideElement($("#" + t), $select);
			}
		});

		// แสดงตัวที่เลือก
		if (target) {
			showElement($("#" + target), $select);
		}
	});
});

const $editorEl = $("textarea.tiny-editor");
const height = $editorEl.length
	? parseInt($editorEl.data("height")) || 500
	: 500;

// Text Editor
tinymce.init({
	selector: "textarea.tiny-editor",
	// plugins:
	// 	"preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons accordion",
	plugins:
		"preview importcss searchreplace autolink  directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons accordion",
	editimage_cors_hosts: ["picsum.photos"],
	menubar: "file edit view insert format tools table help",
	toolbar:
		"undo redo removeformat | blocks fontfamily fontsizeinput forecolor backcolor  | bold italic underline strikethrough | align numlist bullist | link image imageoptions table tableprops cellprops rowprops media | lineheight outdent indent| charmap emoticons | code fullscreen preview print  | pagebreak anchor codesample | ltr rtl",

	// ✅ เพิ่ม toolbar ของ table โดยเฉพาะ
	table_toolbar:
		"tableprops cellprops rowprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol",

	// ✅ เพิ่ม contextmenu ให้มี cell property ด้วย
	contextmenu: "link image table tablecell",

	autosave_ask_before_unload: false,
	autosave_interval: "30s",
	autosave_prefix: "{path}{query}-{id}-",
	autosave_restore_when_empty: false,
	autosave_retention: "2m",
	image_advtab: true,
	promotion: false,
	branding: false,
	image_title: true,
	automatic_uploads: true,
	image_class_list: [
		{
			title: "img-fluid",
			value: "img-fluid",
		},
	],

	file_picker_types: "image media",
	file_picker_callback: (cb, value, meta) => {
		const input = document.createElement("input");
		input.setAttribute("type", "file");
		if (meta.filetype === "file") {
			input.setAttribute("accept", "image/* audio/* video/*");
		}
		if (meta.filetype === "image") {
			input.setAttribute("accept", "image/*");
		}
		if (meta.filetype === "media") {
			input.setAttribute("accept", "video/*");
		}

		input.addEventListener("change", (e) => {
			const file = e.target.files[0];
			const reader = new FileReader();
			reader.addEventListener("load", () => {
				const id = "blobid" + new Date().getTime();
				const blobCache = tinymce.activeEditor.editorUpload.blobCache;
				const base64 = reader.result.split(",")[1];
				const blobInfo = blobCache.create(id, file, base64);
				blobCache.add(blobInfo);
				cb(blobInfo.blobUri(), {
					title: file.name,
				});
			});
			reader.readAsDataURL(file);
		});

		input.click();
	},
	setup: function (editor) {
		let touched = false;

		function checkContent() {
			if (!touched) return;

			const textarea = $(editor.getElement());
			if (!textarea.prop("required")) return;

			const iframe = $(editor.iframeElement);
			const content = editor.getContent({ format: "html" }).trim();
			const isEmpty = content === "" || content === "<p><br></p>";

			const tinymceWrapper = textarea.siblings(".tox-tinymce");
			let errorDiv = tinymceWrapper.next("div.is-invalid");

			const msgRequired =
				jQuery.validator.messages.required || "This field is required.";

			if (isEmpty) {
				iframe.addClass("is-invalid");
				if (errorDiv.length === 0) {
					errorDiv = $("<div>").addClass("is-invalid").text(msgRequired);
					tinymceWrapper.after(errorDiv);
				}
			} else {
				iframe.removeClass("is-invalid");
				if (errorDiv.length) errorDiv.remove();
				textarea.removeClass("is-invalid");
			}
		}

		const userTriggers = ["keydown", "paste"];
		userTriggers.forEach((e) => {
			editor.on(e, () => {
				touched = true;
				checkContent();
			});
		});

		editor.on("Drop", () => {
			touched = true;
			setTimeout(() => checkContent(), 300);
		});

		const watchEvents = [
			"Change",
			"Input",
			"keyup",
			"SetContent",
			"NodeChange",
			"ExecCommand",
		];

		watchEvents.forEach((e) => {
			editor.on(e, () => {
				checkContent();
			});
		});

		editor.on("change input", function () {
			tinymce.triggerSave(); // ดึงข้อมูลลง textarea ทันทีที่แก้ไข
		});
	},

	height: height,
	image_caption: true,
	quickbars_selection_toolbar:
		"bold italic | quicklink h2 h3 blockquote quickimage quicktable",
	noneditable_class: "mceNonEditable",
	toolbar_mode: "sliding",
	content_style: "body { font-family:Kanit; font-size:16px }",
});

// register desired plugins...
FilePond.registerPlugin(
	// validates the size of the file...
	FilePondPluginFilePoster,
	FilePondPluginImageEditor,
	FilePondPluginFileValidateSize,
	// // validates the file type...
	FilePondPluginFileValidateType,
	// // calculates & dds cropping info based on the input image dimensions and the set crop ratio...
	FilePondPluginImageCrop,
	// // preview the image file type...
	FilePondPluginImagePreview,
	// // filter the image file
	FilePondPluginImageFilter,
	// // corrects mobile image orientation...
	FilePondPluginImageExifOrientation,
	// // calculates & adds resize information...
	FilePondPluginImageResize,
);

FilePond.setOptions({
	onremovefile: (err, fileItem) => {
		const input =
			fileItem?.file?.input || document.querySelector('input[type="file"]');
		$(input).valid();
	},
	onprocessfile: (err, fileItem) => {
		const input =
			fileItem?.file?.input || document.querySelector('input[type="file"]');
		$(input).valid();
	},
});

// Filepond: Basic
const uploadBasics = document.querySelectorAll(".basic-filepond");
Array.from(uploadBasics).forEach((uploadBasic) => {
	FilePond.create(uploadBasic, {
		allowImagePreview: false,
		allowMultiple: false,
		allowFileEncode: false,
		required: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"application/pdf",
			"video/mp4",
			"image/png",
			"image/jpg",
			"image/jpeg",
		],
	});
});

// Filepond: Multiple Files
const uploadMultiples = document.querySelectorAll(".multiple-files-filepond");
Array.from(uploadMultiples).forEach((uploadMultiple) => {
	FilePond.create(uploadMultiple, {
		allowImagePreview: false,
		allowMultiple: true,
		allowFileEncode: false,
		required: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
	});
});

// Filepond: Multiple Files
const uploadPreviewMultiples = document.querySelectorAll(
	".multiple-preview-files-filepond",
);
Array.from(uploadPreviewMultiples).forEach((uploadPreviewMultiple) => {
	FilePond.create(uploadPreviewMultiple, {
		allowImagePreview: true,
		imagePreviewHeight: 100, // optional
		allowMultiple: true,
		allowFileEncode: false,
		required: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
	});
});

// Filepond: With Validation
const uploadValidations = document.querySelectorAll(
	".with-validation-filepond",
);
Array.from(uploadValidations).forEach((uploadValidation) => {
	FilePond.create(uploadValidation, {
		allowImagePreview: false,
		allowMultiple: true,
		allowFileEncode: false,
		required: true,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				// Do custom type detection here and return with promise
				resolve(type);
			}),
	});
});

// Filepond: Image Preview
const uploadPreviews = document.querySelectorAll(".image-preview-filepond");
Array.from(uploadPreviews).forEach((uploadPreview) => {
	const pond = FilePond.create(uploadPreview, {
		allowImagePreview: true,
		allowImageFilter: false,
		allowImageExifOrientation: false,
		allowImageCrop: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				// Do custom type detection here and return with promise
				resolve(type);
			}),
	});
});

let Pintura = pintura;
// Filepond: Image Crop
const uploadImgCrops = document.querySelectorAll(".image-crop-filepond");
Array.from(uploadImgCrops).forEach((uploadImgCrop) => {
	FilePond.create(uploadImgCrop, {
		allowReorder: true,
		filePosterMaxHeight: 256,
		allowImageFilter: false,
		allowImageExifOrientation: false,
		allowImageCrop: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				resolve(type);
			}),
		imageEditor: {
			createEditor: Pintura.openEditor,
			imageReader: [Pintura.createDefaultImageReader],
			imageWriter: [
				Pintura.createDefaultImageWriter,
				{
					targetSize: { width: 128 },
				},
			],
			imageProcessor: Pintura.processImage,
			editorOptions: {
				...Pintura.getEditorDefaults(),
				imageCropAspectRatio: 0,
			},
		},
	});
});

// Filepond: Image Exif Orientation
const uploadImgExifs = document.querySelectorAll(".image-exif-filepond");
Array.from(uploadImgExifs).forEach((uploadImgExif) => {
	FilePond.create(uploadImgExif, {
		allowImagePreview: true,
		allowImageFilter: false,
		allowImageExifOrientation: true,
		allowImageCrop: false,
		storeAsFile: true,
		credits: false,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				// Do custom type detection here and return with promise
				resolve(type);
			}),
	});
});

// Filepond: Image Filter
const uploadImgFilters = document.querySelectorAll(".image-filter-filepond");
Array.from(uploadImgFilters).forEach((uploadImgFilter) => {
	FilePond.create(uploadImgFilter, {
		allowImagePreview: true,
		allowImageFilter: true,
		allowImageExifOrientation: false,
		allowImageCrop: false,
		credits: false,
		storeAsFile: true,
		imageFilterColorMatrix: [
			0.299, 0.587, 0.114, 0, 0, 0.299, 0.587, 0.114, 0, 0, 0.299, 0.587, 0.114,
			0, 0, 0.0, 0.0, 0.0, 1, 0,
		],
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",
			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				// Do custom type detection here and return with promise
				resolve(type);
			}),
	});
});

// Filepond: Image Resize
const uploadImgResizes = document.querySelectorAll(".image-resize-filepond");
Array.from(uploadImgResizes).forEach((uploadImgResize) => {
	FilePond.create(uploadImgResize, {
		allowImagePreview: true,
		allowImageFilter: false,
		allowImageExifOrientation: false,
		allowImageCrop: false,
		allowImageResize: true,
		// imageResizeTargetWidth: 1080,
		// imageResizeTargetHeight: 720,
		imageResizeMode: "cover",
		imageResizeUpscale: true,
		credits: false,
		storeAsFile: true,
		acceptedFileTypes: [
			"image/png",
			"image/jpg",
			"image/jpeg",

			// "image/svg+xml",
		],
		fileValidateTypeDetectType: (source, type) =>
			new Promise((resolve, reject) => {
				// Do custom type detection here and return with promise
				resolve(type);
			}),
	});
});

// DataTable
let labelData = {
	placeholder: "ค้นหา...",
	noRows: '<b style="color: #dc0000;">ไม่พบข้อมูล !</b>',
	info: "แสดง {start} ถึง {end} จาก {rows} แถว (หน้า {page} ถึง {pages} หน้า)",
};

let options = {
	searchable: true,
	responsive: true,
	perPage: 20,
	labels: labelData,
	perPageSelect: [10, 15, 20, 25, 50, 75, 100],
	columns: [{ select: 0, sort: "asc" }],
};

let optionsDesc = {
	searchable: true,
	responsive: true,
	perPage: 20,
	labels: labelData,
	perPageSelect: [10, 15, 20, 25, 50, 75, 100],
	columns: [{ select: 0, sort: "desc" }],
};

const table = document.querySelectorAll(".dataTable");
var label = $("<span>").attr("class", "me-2").html("แสดง");
Array.from(table).forEach((dataTable) => {
	new simpleDatatables.DataTable(dataTable, options);
	$(".dataTable-dropdown").prepend(label);
	$(".dataTable-dropdown label").attr("class", "ms-2").text("รายการ");
	// el.text = "New Value";
});

const tableDesc = document.querySelectorAll(".dataTableDesc");
var label = $("<span>").attr("class", "me-2").html("แสดง");
Array.from(tableDesc).forEach((dataTableDesc) => {
	new simpleDatatables.DataTable(dataTableDesc, optionsDesc);
	$(".dataTable-dropdown").prepend(label);
	$(".dataTable-dropdown label").attr("class", "ms-2").text("รายการ");
	// el.text = "New Value";
});

$("input[data-maxlength]").on("input", function () {
	var $this = $(this);
	var max = parseInt($this.data("maxlength"), 10);

	var val = $this.val().replace(/[^0-9]/g, "");
	if (val.length > max) {
		val = val.substring(0, max);
	}

	$this.val(val);
});

$("input[data-minlength]").on("input", function () {
	var $this = $(this);
	var min = parseInt($this.data("minlength"), 10);

	var val = $this.val().replace(/[^0-9]/g, "");
	if (val.length > min) {
		val = val.substring(0, min);
	}

	$this.val(val);
});

// $(".datePicker").datepicker({
// 	format: "dd-mm-yyyy",
// 	clearBtn: true,
// 	todayHighlight: true,
// 	container: "body",
// 	todayBtn: "linked",
// });

function updatePopupYear(instance) {
	if (!instance.calendarContainer) return;

	const yearInput = instance.calendarContainer.querySelector("input.cur-year");
	if (yearInput) {
		let yearCE = parseInt(yearInput.value, 10);
		if (!isNaN(yearCE)) {
			// ถ้าปี <= 2500 ถือว่าเป็นปี ค.ศ. ให้บวก 543
			if (yearCE <= 2500) {
				yearCE += 543;
			}
			yearInput.value = yearCE;
		}

		yearInput.onchange = function () {
			let val = parseInt(yearInput.value, 10);
			if (!isNaN(val)) {
				// ถ้าปี > 2500 ถือว่าปี พ.ศ. ต้องลบ 543 ก่อนเปลี่ยนปีใน flatpickr
				if (val > 2500) {
					val -= 543;
				}
				yearInput.value = val + 543; // แสดงเป็นปี พ.ศ.
				instance.changeYear(val);
			}
		};
	}
}

flatpickr(".datePickerEN", {
	allowInput: true,
	enableTime: false,
	dateFormat: "d-m-Y",
	enableTime: false,
	locale: "en",
	disableMobile: "true",
	// plugins: [confirmDatePlugin({ confirmText: "เลือก", showAlways: true })],
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "To Day";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
});

flatpickr(".dateTimePickerEN", {
	allowInput: true,
	enableTime: false,
	enableTime: true,
	dateFormat: "d-m-Y H:i",
	locale: "en",
	disableMobile: "true",
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "To Day";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
});

flatpickr(".dateRangePickerEN", {
	allowInput: true,
	enableTime: false,
	dateFormat: "d-m-Y",
	enableTime: false,
	mode: "range",
	locale: "en",
	disableMobile: "true",
	// plugins: [confirmDatePlugin({ confirmText: "เลือก", showAlways: true })],
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "To Day";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
});

flatpickr(".datePickerTH", {
	allowInput: true,
	enableTime: false,
	dateFormat: "d-m-Y",
	locale: "th",
	disableMobile: "true",
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "วันนี้";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
	formatDate: function (date, formatStr) {
		const d = date.getDate().toString().padStart(2, "0");
		const m = (date.getMonth() + 1).toString().padStart(2, "0");
		const y = date.getFullYear() + 543; // แปลงเป็นปี พ.ศ.
		return `${d}-${m}-${y}`;
	},

	parseDate: function (dateStr, format) {
		// รองรับเฉพาะรูปแบบ d-m-Y (ไม่มีเวลา)
		const parts = dateStr.match(/^(\d{2})-(\d{2})-(\d{4})$/);
		if (!parts) return null;
		let day = parseInt(parts[1], 10);
		let month = parseInt(parts[2], 10) - 1;
		let year = parseInt(parts[3], 10) - 543;
		return new Date(year, month, day);
	},

	onOpen: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onMonthChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onYearChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},

	onChange: function (selectedDates, dateStr, instance) {
		if (selectedDates.length) {
			instance.setDate(selectedDates[0], false);
			updatePopupYear(instance);
		}
	},
});

flatpickr(".dateTimePickerTH", {
	allowInput: true, // อนุญาตให้พิมพ์เองใน input ได้
	enableTime: true,
	dateFormat: "d-m-Y",
	enableTime: true, // ❌ ไม่เปิด time picker
	time_24hr: true,
	locale: "th",
	disableMobile: "true",
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "วันนี้";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
	formatDate: function (date, formatStr) {
		const d = date.getDate().toString().padStart(2, "0");
		const m = (date.getMonth() + 1).toString().padStart(2, "0");
		const y = date.getFullYear() + 543;
		const H = date.getHours().toString().padStart(2, "0");
		const i = date.getMinutes().toString().padStart(2, "0");
		return `${d}-${m}-${y} ${H}:${i}`;
	},

	parseDate: function (dateStr, format) {
		const parts = dateStr.match(/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})$/);
		if (!parts) return null;
		let day = parseInt(parts[1], 10);
		let month = parseInt(parts[2], 10) - 1;
		let year = parseInt(parts[3], 10) - 543;
		let hour = parseInt(parts[4], 10);
		let minute = parseInt(parts[5], 10);
		return new Date(year, month, day, hour, minute);
	},

	onOpen: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onMonthChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onYearChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},

	onChange: function (selectedDates, dateStr, instance) {
		if (selectedDates.length) {
			instance.setDate(selectedDates[0], false);
			updatePopupYear(instance);
		}
	},
});

flatpickr(".dateRangePickerTH", {
	allowInput: true,
	enableTime: false,
	dateFormat: "d-m-Y",
	locale: "th",
	disableMobile: "true",
	mode: "range",
	onReady: function (selectedDates, dateStr, instance) {
		if (!instance.todayBtn) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.textContent = "วันนี้";
			btn.className =
				"btn btn-primary btn-sm btn-block flatpickr-today-btn py-1 ";

			btn.addEventListener("click", function () {
				const today = new Date();
				instance.setDate(today, true);

				// ✅ เช็ค input และลบ is-invalid / ใส่ is-valid
				const input = instance.input;
				if (input) {
					input.classList.remove("is-invalid"); // ลบสีแดง
					input.classList.add("is-valid"); // ใส่สีเขียว

					// ✅ ถ้าใช้ jQuery Validate
					if (typeof $ === "function" && $(input).valid) {
						$(input).valid(); // สั่งให้ validate อีกรอบ
					}
				}

				const calendar = instance.calendarContainer;
				calendar.classList.add("fade-out");

				setTimeout(() => {
					instance.close();
					calendar.classList.remove("fade-out");
				}, 300);
			});

			const footer =
				instance.calendarContainer.querySelector(".flatpickr-confirm");
			if (footer) {
				footer.style.display = "flex"; // ให้ footer เป็น flex container
				footer.style.alignItems = "center";
				footer.appendChild(btn);
			} else {
				instance.calendarContainer.appendChild(btn);
			}

			instance.todayBtn = btn;
		}
	},
	formatDate: function (date, formatStr) {
		const d = date.getDate().toString().padStart(2, "0");
		const m = (date.getMonth() + 1).toString().padStart(2, "0");
		const y = date.getFullYear() + 543; // แปลงเป็นปี พ.ศ.
		return `${d}-${m}-${y}`;
	},

	parseDate: function (dateStr, format) {
		// รองรับเฉพาะรูปแบบ d-m-Y (ไม่มีเวลา)
		const parts = dateStr.match(/^(\d{2})-(\d{2})-(\d{4})$/);
		if (!parts) return null;
		let day = parseInt(parts[1], 10);
		let month = parseInt(parts[2], 10) - 1;
		let year = parseInt(parts[3], 10) - 543;
		return new Date(year, month, day);
	},

	onOpen: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onMonthChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},
	onYearChange: function (selectedDates, dateStr, instance) {
		updatePopupYear(instance);
	},

	onChange: function (selectedDates, dateStr, instance) {
		if (selectedDates.length) {
			updatePopupYear(instance);
		}
	},
});

let isClosing = false;

$(".modal").on("hide.bs.modal", function (event) {
	const $modal = $(this);

	if (isClosing) {
		isClosing = false;
		return; // ปล่อยให้ Bootstrap ปิด modal จริง ๆ รอบที่ 2
	}

	event.preventDefault(); // หยุด Bootstrap ปิด modal ทันที

	isClosing = true;

	// เพิ่ม class ปิด animation ของ modal dialog
	$modal.addClass("closing");

	setTimeout(function () {
		$modal.removeClass("closing");

		// ปิด modal จริง ๆ รอบที่ 2
		$modal.modal("hide");
	}, 300); // เวลาต้องตรงกับ CSS animation
});
