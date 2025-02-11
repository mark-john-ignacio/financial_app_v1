export class POSPayment {
    constructor(config) {
        this.config = config;
    }

    processPayment(state) {
        const paymentData = this.gatherPaymentData();
        
        if (!this.validatePayment(paymentData)) {
            return false;
        }

        return $.ajax({
            url: this.config.urls.payment,
            type: 'POST',
            data: paymentData,
            dataType: 'json'
        });
    }

    computePayment() {
        const tender = this.parseAmount('#tendered');
        const coupon = this.parseAmount('#couponinput');
        const subtotal = this.parseAmount('#subtotal');
        const serviceFee = subtotal * this.config.constants.SERVICE_FEE;

        const total = subtotal + serviceFee;
        const totalTender = tender + coupon;
        const change = totalTender - total;

        this.updatePaymentFields({
            total,
            totalTender,
            change: Math.max(0, -change),
            serviceFee
        });

        return { total, totalTender, change, serviceFee };
    }

    parseAmount(selector) {
        return parseFloat($(selector).val().replace(/,/g, '') || 0);
    }

    updatePaymentFields(values) {
        $('#ExchangeAmt').val(values.change.toFixed(2));
        $('#ServiceInput').val(values.serviceFee.toFixed(2));
        $('#totalTender').val(values.totalTender.toFixed(2));
        $('#totalAmt').val(values.total.toFixed(2));
    }

    validatePayment(data) {
        if (!data.amount || data.amount <= 0) {
            alert('Invalid payment amount');
            return false;
        }
        if (!data.method) {
            alert('Please select payment method');
            return false;
        }
        return true;
    }

    handleSpecialDiscount(data) {
        const subtotal = this.parseAmount('#subtotal');
        
        if (subtotal <= 0) {
            this.ui.showAlert("No items in cart");
            return false;
        }

        if (!data.person.trim() || !data.id.trim()) {
            this.ui.showAlert("Please input customer name and ID");
            return false;
        }

        return $.ajax({
            url: 'Function/th_specialdiscount.php',
            data: {
                amount: data.amount,
                type: data.type,
                name: data.name,
                person: data.person,
                id: data.id
            },
            dataType: 'json'
        });
    }

    setupPaymentMethod() {
        $("#paymethod").change(function() {
            const method = $(this).val();
            $('#paymethod_txt')
                .val('')
                .prop('required', method !== 'Cash')
                .prop('disabled', method === 'Cash');
        });
    }

    gatherPaymentData() {
        return {
            tranno: $("#tranno").val(),
            method: $("#paymethod").find(":selected").val(),
            reference: $("#paymethod_txt").val(),
            amount: $('#totalAmt').val().replace(/,/g,''),
            exchange: $('#ExchangeAmt').val().replace(/,/g,''),
            tender: $('#tendered').val().replace(/,/g,''),
            coupon: $('#couponinput').val().replace(/,/g,''),
            discount: $('#discountInput').val(),
            service: $('#ServiceInput').val()
        };
    }

    setupPaymentHandlers() {
        $('#tendered').on('keyup', () => this.computePayment());
        
        $('#PaySubmit').click(() => {
            const paymentData = this.gatherPaymentData();
            
            $.ajax({
                url: this.config.urls.payment,
                type: 'post',
                data: paymentData,
                dataType: 'json',
                success: (res) => {
                    if (res.valid) {
                        $("#myprintframe").attr("src", res.data);
                        location.reload();
                    } else {
                        alert(res.msg);
                    }
                }
            });
        });

        $('#CouponSubmit').click(() => {
            const coupons = $("#coupontxt").val();
            this.handleCoupon(coupons);
        });
    }

    handleNumPad(btn) {
        const currentVal = $('#tendered').val();
        if (btn === 'C') {
            $('#tendered').val('');
        } else {
            $('#tendered').val(currentVal + btn);
        }
        $('#tendered').trigger('keyup');
    }

    setupAutoNumeric() {
        $('#ExchangeAmt').autoNumeric('destroy');
        $('#ExchangeAmt').autoNumeric('init', { mDec: 2 });
    }

    updateHiddenFields() {
        $("#discountInput").val(this.getSpecialDisc(this.state.specialDisc)).change();
        $("#h_tranno").val();
    }

    handleCoupon(coupons) {
        const subtotal = this.parseAmount('#subtotal');
        const totalTender = this.parseAmount('#totalTender');

        if(parseFloat(subtotal) < parseFloat(totalTender)) {
            alert("Total tender exceeds subtotal amount");
            return false;
        }

        return $.ajax({
            url: "Function/th_coupon.php",
            data: { coupon: coupons },
            dataType: 'json'
        });
    }

    getSpecialDisc(specialDisc) {
        return specialDisc.reduce((total, item) => {
            return total + parseFloat(item.amount);
        }, 0);
    }

    handleCouponUpdate() {
        const couponValue = $("#couponinput").val();
        return $.ajax({
            url: this.config.dualView.coupon,
            method: 'POST',
            data: { coupon: couponValue }
        });
    }

    handleDiscountUpdate() {
        const discountValue = $("#discountInput").val();
        return $.ajax({
            url: this.config.dualView.discount,
            method: 'POST',
            data: { discount: discountValue }
        });
    }
}