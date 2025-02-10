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
}