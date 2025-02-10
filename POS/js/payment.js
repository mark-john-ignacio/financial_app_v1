export class POSPayment {
    processPayment(state) {
        const paymentData = this.gatherPaymentData();
        
        if (!this.validatePayment(paymentData)) {
            return;
        }

        $.ajax({
            url: 'Function/pos_save.php',
            type: 'post',
            data: paymentData,
            dataType: 'json',
            success: (res) => {
                if (res.valid) {
                    this.handleSuccessfulPayment(res);
                } else {
                    alert(res.msg);
                }
            },
            error: (res) => console.log(res)
        });
    }

    gatherPaymentData() {
        return {
            tranno: $("#tranno").val(),
            method: $("#paymethod").find(":selected").val(),
            reference: $("#paymethod_txt").val(),
            amount: $('#totalAmt').val().replace(/,/g,''),
            // ... other payment data
        };
    }

    // ... more payment methods
}