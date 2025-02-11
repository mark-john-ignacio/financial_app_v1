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

    setupSpecialDiscountHandlers() {
        $("#spcdBtn").click(() => {
            $("#paymentcol").hide();
            $("#specialdiscountcol").show();
        });

        $("#spcBack").click(() => {
            $("#paymentcol").show();
            $("#specialdiscountcol").hide();
        });
    }

    setupCouponHandlers() {
        $("#couponBtn").click(() => {
            $("#couponmodal").show();
            $("#paymentcol").hide();
        });

        $("#couponback").click(() => {
            $("#couponmodal").hide();
            $("#paymentcol").show();
        });
    }

    setupNumPad() {
        $('.btnpad').click(function() {
            const currentVal = $('#tendered').val();
            const btnVal = $(this).val();
            
            if (btnVal === 'C') {
                $('#tendered').val('');
            } else {
                $('#tendered').val(currentVal + btnVal);
            }
            $('#tendered').trigger('keyup');
        });
    }

    modalshow(modal) {
        $('.modal-body').css('display', 'none');
        $('#footer button').css('display', 'none');

        switch(modal) {
            case 'void':
                $('#VoidList').css('display', 'block');
                $('#VoidSubmit').css('display', 'inline-block');
                break;
            case 'hold':
                $('#HoldList').css('display', 'block');
                $('#HoldSubmit').css('display', 'inline-block');
                break;
            case 'retrieve':
                $('#RetrieveList').css('display', 'block');
                $('#RetrieveSubmit').css('display', 'inline-block');
                break;
        }
        
        $('#mymodal').modal("show");
    }

    setupRetrieveHandlers() {
        $("#RetrieveList tbody")
            .on("mouseenter", "tr", function() {
                $(this).css("background-color", "#ccc");
            })
            .on("mouseleave", "tr", function() {
                $(this).css("background-color", "transparent");
            })
            .on("click", "tr", function() {
                $(this).find('input[type="checkbox"]').prop('checked', 
                    function(i, val) {
                        return !val;
                    });
            });
    }

    updateAmounts(amounts) {
        $('#vat').text(amounts.vat.toFixed(2));
        $('#net').text(amounts.net.toFixed(2));
        $('#gross').text(amounts.gross.toFixed(2));
        $('#subtotal').val(amounts.gross.toFixed(2));
    }

    addItemToMainList(item, index) {
        const row = `<tr>
            <td>${item.partno}</td>
            <td>${item.name}</td>
            <td>${item.unit}</td>
            <td><input type="number" id="qty" value="${item.quantity}"></td>
            <td>${parseFloat(item.price).toFixed(2)}</td>
            <td>${parseFloat(item.discount).toFixed(2)}</td>
            <td>${parseFloat(item.amount).toFixed(2)}</td>
        </tr>`;
        $('#listItem > tbody').append(row);
    }

    addItemToVoidList(item, index) {
        const row = `<tr>
            <td><input type="checkbox" name="itemcheck" value="${index}"></td>
            <td>${item.partno}</td>
            <td>${item.name}</td>
            <td>${item.unit}</td>
            <td>${item.quantity}</td>
            <td>${parseFloat(item.price).toFixed(2)}</td>
            <td>${parseFloat(item.amount).toFixed(2)}</td>
        </tr>`;
        $('#VoidList > tbody').append(row);
    }

    addItemToPaymentList(item, index) {
        const row = `<tr>
            <td>${item.partno}</td>
            <td>${item.name}</td>
            <td>${item.unit}</td>
            <td>${item.quantity}</td>
            <td>${parseFloat(item.price).toFixed(2)}</td>
            <td>${parseFloat(item.discount).toFixed(2)}</td>
            <td>${parseFloat(item.amount).toFixed(2)}</td>
        </tr>`;
        $('#paymentList > tbody').append(row);
    }

    initSlick() {
        $(".regular").slick({
            dots: false,
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4
        });
    }
}