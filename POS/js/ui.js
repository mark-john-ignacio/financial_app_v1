export class POSUI {
    constructor() {
        this.clockTimer = null;
    }

    init() {
        this.initClock();
        this.initSlick();
        this.setupModalHandlers();
        this.setupUIElements();
    }

    setupUIElements() {
        $('#optInputsCheck').on('change', function() {
            $('#optionalFields').toggle(this.checked);
        });

        $('.itmslist, .itmclass').hover(function() {
            $(this).css('cursor', 'pointer');
        });
    }

    setupModalHandlers() {
        $('#btnVoid').click(() => this.showVoidModal());
        $('#btnHold').click(() => this.showHoldModal());
        $('#btnRetrieve').click(() => this.showRetrieveModal());
    }

    updateTables(items) {
        $('#listItem > tbody').empty();
        $('#VoidList > tbody').empty();
        $('#paymentList > tbody').empty();

        items.forEach(item => {
            this.addItemToMainList(item);
            this.addItemToVoidList(item);
            this.addItemToPaymentList(item);
        });

        this.updateTotals(items);
    }

    updateTotals(items) {
        const totals = this.calculateTotals(items);
        
        $('#vat').text(totals.vat.toFixed(2));
        $('#net').text(totals.net.toFixed(2));
        $('#gross').text(totals.gross.toFixed(2));
        $('#subtotal').val(totals.gross.toFixed(2));
    }

    calculateTotals(items) {
        return items.reduce((acc, item) => {
            const price = parseFloat(item.price);
            const quantity = parseFloat(item.quantity);
            const net = price / 1.12; // VAT rate 12%

            acc.net += net * quantity;
            acc.vat += net * 0.12 * quantity;
            acc.gross += price * quantity;
            acc.discount += parseFloat(item.discount);

            return acc;
        }, { net: 0, vat: 0, gross: 0, discount: 0 });
    }

    showAlert(message, color = "#008000") {
        $("#AlertModal").modal("show");
        $("#AlertMsg").html(message);
        setTimeout(() => location.reload(), 5000);
    }

    initClock() {
        const updateClock = () => {
            const date = new Date();
            const h = this.formatTwelveHour(date.getHours());
            const m = this.padZero(date.getMinutes());
            const s = this.padZero(date.getSeconds());
            $('.digital-clock').text(`${h}:${m}:${s}`);
        };

        updateClock();
        this.clockTimer = setInterval(updateClock, 1000);
    }

    formatTwelveHour(hours) {
        return hours > 12 ? this.padZero(hours - 12) : this.padZero(hours);
    }

    padZero(num) {
        return num < 10 ? `0${num}` : num;
    }

    showVoidModal() {
        if (this.items.length === 0) {
            alert('No items to void');
            return;
        }
        $('#voidlogin').modal('show');
    }

    showHoldModal() {
        $("#HoldModal").modal("show");
    }

    showRetrieveModal() {
        $("#RetrieveModal").modal("show");
    }
}