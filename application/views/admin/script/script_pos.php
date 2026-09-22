<script>
    // ==========================================
    // POS Terminal JavaScript
    // ==========================================

    let cart = [];
    let cartIdCounter = 0;

    // ==========================================
    // Cart Functions
    // ==========================================

    function addToCart(el) {
        const productId = $(el).data('id');
        const productName = $(el).data('name');
        const productPrice = parseFloat($(el).data('price'));
        const productCost = parseFloat($(el).data('cost'));
        const productStock = parseInt($(el).data('stock'));

        if (productStock <= 0) {
            Swal.fire('สินค้าหมด', 'สินค้า "' + productName + '" หมดสต๊อก', 'warning');
            return;
        }

        // ถ้ามีอยู่แล้ว เพิ่มจำนวน
        const existing = cart.find(item => item.product_id == productId);
        if (existing) {
            if (existing.qty >= productStock) {
                Swal.fire('สต๊อกไม่พอ', 'สินค้าคงเหลือเพียง ' + productStock + ' ชิ้น', 'warning');
                return;
            }
            existing.qty++;
        } else {
            cart.push({
                cartId: ++cartIdCounter,
                product_id: productId,
                name: productName,
                price: productPrice,
                cost: productCost,
                stock: productStock,
                qty: 1,
                discount: 0
            });
        }

        renderCart();
        playBeep();
    }

    function addToCartByBarcode(barcode) {
        // ค้นหาจากตัว product card ในหน้า
        const card = $(`.product-card[data-barcode="${barcode}"], .product-card[data-code="${barcode}"]`);
        if (card.length > 0) {
            addToCart(card[0]);
            return;
        }

        // ค้นหาจาก server
        $.post('<?= admin_url("pos/searchBarcode") ?>', {
            barcode: barcode
        }, function(resp) {
            const res = JSON.parse(resp);
            if (res.status === 'success') {
                const p = res.data;
                const fakeEl = $('<div>')
                    .data('id', p.product_id)
                    .data('name', p.product_name)
                    .data('price', p.product_price)
                    .data('cost', p.product_cost)
                    .data('stock', p.product_stock);
                addToCart(fakeEl[0]);
            } else {
                Swal.fire('ไม่พบสินค้า', 'ไม่พบสินค้าที่มีบาร์โค้ด/รหัส: ' + barcode, 'error');
            }
        });
    }

    function removeFromCart(cartId) {
        cart = cart.filter(item => item.cartId !== cartId);
        renderCart();
    }

    function updateQty(cartId, newQty) {
        const item = cart.find(i => i.cartId === cartId);
        if (!item) return;

        newQty = parseInt(newQty);
        if (newQty <= 0) {
            removeFromCart(cartId);
            return;
        }
        if (newQty > item.stock) {
            Swal.fire('สต๊อกไม่พอ', 'คงเหลือเพียง ' + item.stock + ' ชิ้น', 'warning');
            newQty = item.stock;
        }
        item.qty = newQty;
        renderCart();
    }

    function clearCart() {
        Swal.fire({
            title: 'ล้างรายการ?',
            text: 'ต้องการล้างรายการสินค้าทั้งหมดหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ล้าง',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
                $('#barcodeInput').focus();
            }
        });
    }

    // ==========================================
    // Render Cart
    // ==========================================

    function renderCart() {
        const tbody = $('#cartBody');
        tbody.empty();

        if (cart.length === 0) {
            tbody.html(`<tr id="emptyCart">
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-shopping-basket fa-3x mb-3 d-block opacity-25"></i>
                    ยังไม่มีสินค้าในรายการ
                </td>
            </tr>`);
            updateTotals();
            return;
        }

        cart.forEach(item => {
            const itemTotal = (item.price * item.qty) - item.discount;
            tbody.append(`
                <tr data-cart-id="${item.cartId}">
                    <td class="ps-3">
                        <div class="fw-semibold" style="font-size:0.85rem">${item.name}</div>
                    </td>
                    <td class="text-center">${item.price.toFixed(0)}</td>
                    <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${item.cartId}, ${item.qty - 1})">-</button>
                            <input type="number" class="form-control qty-input" value="${item.qty}" min="1" max="${item.stock}"
                                onchange="updateQty(${item.cartId}, this.value)">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${item.cartId}, ${item.qty + 1})">+</button>
                        </div>
                    </td>
                    <td class="text-center fw-semibold">${itemTotal.toFixed(0)}</td>
                    <td class="text-center">
                        <i class="fas fa-times-circle cart-item-remove" onclick="removeFromCart(${item.cartId})"></i>
                    </td>
                </tr>
            `);
        });

        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        let totalItems = 0;

        cart.forEach(item => {
            subtotal += (item.price * item.qty) - item.discount;
            totalItems += item.qty;
        });

        const discount = parseFloat($('#discountInput').val()) || 0;
        const total = Math.max(0, subtotal - discount);

        $('#totalItems').text(totalItems);
        $('#totalItemsBadge').text(totalItems + ' รายการ');
        $('#subtotalDisplay').text(subtotal.toFixed(2) + ' ฿');
        $('#discountDisplay').text('-' + discount.toFixed(2) + ' ฿');
        $('#totalDisplay').text(total.toFixed(2));

        // Change calculation
        const received = parseFloat($('#receivedInput').val()) || 0;
        const change = received - total;

        if (received > 0) {
            $('#changeSection').show();
            $('#changeDisplay').text(change.toFixed(2));
            if (change < 0) {
                $('#changeDisplay').removeClass('text-primary').addClass('text-danger');
            } else {
                $('#changeDisplay').removeClass('text-danger').addClass('text-primary');
            }
        } else {
            $('#changeSection').hide();
        }

        // Enable/disable checkout
        const payMethod = $('#paymentMethod').val();
        const canCheckout = cart.length > 0 && (payMethod !== 'cash' || received >= total);
        $('#btnCheckout').prop('disabled', !canCheckout);
    }

    // ==========================================
    // Checkout
    // ==========================================

    function doCheckout() {
        if (cart.length === 0) return;

        const discount = parseFloat($('#discountInput').val()) || 0;
        const received = parseFloat($('#receivedInput').val()) || 0;
        const paymentMethod = $('#paymentMethod').val();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty - item.discount), 0);
        const total = subtotal - discount;

        if (paymentMethod === 'cash' && received < total) {
            Swal.fire('เงินไม่พอ', 'จำนวนเงินที่รับน้อยกว่ายอดที่ต้องชำระ', 'warning');
            return;
        }

        // ส่งข้อมูลไป server
        $('#btnCheckout').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');

        const items = cart.map(item => ({
            product_id: item.product_id,
            qty: item.qty,
            discount: item.discount
        }));

        $.post('<?= admin_url("pos/checkout") ?>', {
            items: JSON.stringify(items),
            discount: discount,
            received: received,
            payment_method: paymentMethod,
            note: ''
        }, function(resp) {
            try {
                const res = JSON.parse(resp);
                if (res.status === 'success') {
                    // แสดง modal สำเร็จ
                    $('#successSaleCode').text(res.sale_code);
                    $('#successTotal').text(parseFloat(res.total).toFixed(2));
                    $('#successReceived').text(parseFloat(res.received).toFixed(2));
                    $('#successChange').text(parseFloat(res.change).toFixed(2));
                    $('#btnPrintReceipt').attr('href', '<?= admin_url("pos/receipt/") ?>' + res.sale_id);
                    $('#modalSuccess').modal('show');

                    // Reset
                    cart = [];
                    renderCart();
                    $('#discountInput').val(0);
                    $('#receivedInput').val(0);
                } else {
                    Swal.fire('ผิดพลาด', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการประมวลผล', 'error');
            }

            $('#btnCheckout').prop('disabled', false).html('<i class="fas fa-cash-register me-2"></i>ชำระเงิน (F12)');
        }).fail(function() {
            Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            $('#btnCheckout').prop('disabled', false).html('<i class="fas fa-cash-register me-2"></i>ชำระเงิน (F12)');
        });
    }

    // ==========================================
    // Sound Effect
    // ==========================================

    function playBeep() {
        try {
            const ctx = new(window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 1200;
            osc.type = 'sine';
            gain.gain.value = 0.1;
            osc.start();
            osc.stop(ctx.currentTime + 0.08);
        } catch (e) {}
    }

    // ==========================================
    // Event Handlers
    // ==========================================

    $(function() {
        // Barcode input - Enter
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                const val = $(this).val().trim();
                if (val) {
                    addToCartByBarcode(val);
                    $(this).val('');
                }
            }
        });

        // Search product
        $('#searchProduct').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            $('.product-card').each(function() {
                const name = $(this).data('name').toString().toLowerCase();
                const code = ($(this).data('code') || '').toString().toLowerCase();
                const barcode = ($(this).data('barcode') || '').toString().toLowerCase();
                const match = name.includes(keyword) || code.includes(keyword) || barcode.includes(keyword);
                $(this).toggle(match);
            });
        });

        // Category filter
        $('.category-pills .btn').on('click', function() {
            $('.category-pills .btn').removeClass('active');
            $(this).addClass('active');
            const catId = $(this).data('category');

            $('.product-card').each(function() {
                if (catId === 'all') {
                    $(this).show();
                } else {
                    $(this).toggle($(this).data('category') == catId);
                }
            });
        });

        // Discount/received change
        $('#discountInput, #receivedInput').on('input change', updateTotals);

        // Payment method change
        $('#paymentMethod').on('change', function() {
            if ($(this).val() !== 'cash') {
                // สำหรับโอนเงิน/บัตร ไม่ต้องกรอกเงินที่รับ
                $('#receivedInput').val(0);
                $('#quickCashBtns').hide();
            } else {
                $('#quickCashBtns').show();
            }
            updateTotals();
        });

        // Quick cash buttons
        $('.quick-cash').on('click', function() {
            $('#receivedInput').val($(this).data('amount'));
            updateTotals();
        });

        // Exact amount button
        $('#btnExact').on('click', function() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty - item.discount), 0);
            const discount = parseFloat($('#discountInput').val()) || 0;
            const total = subtotal - discount;
            $('#receivedInput').val(total);
            updateTotals();
        });

        // Clear cart
        $('#btnClearCart').on('click', clearCart);

        // Checkout button
        $('#btnCheckout').on('click', doCheckout);

        // New sale button
        $('#btnNewSale').on('click', function() {
            $('#modalSuccess').modal('hide');
            location.reload();
        });

        // Keyboard shortcut F12 = checkout
        $(document).on('keydown', function(e) {
            if (e.key === 'F12') {
                e.preventDefault();
                if (!$('#btnCheckout').prop('disabled')) {
                    doCheckout();
                }
            }
            // F5 = focus barcode
            if (e.key === 'F5') {
                e.preventDefault();
                $('#barcodeInput').focus().select();
            }
        });

        // SweetAlert2 (load if not exists)
        if (typeof Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
        }
    });
</script>