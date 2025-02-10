export class POSItems {
    constructor(config) {
        this.config = config;
        this.items = [];
    }

    addItem(item, quantity = 1) {
        const price = this.getItemPrice(item);
        const discount = this.getItemDiscount(item);
        
        const existingItem = this.findExistingItem(item.partno);
        
        if (existingItem) {
            this.updateExistingItem(existingItem, quantity);
        } else {
            this.addNewItem(item, price, discount, quantity);
        }

        return this.items;
    }

    findExistingItem(partno) {
        return this.items.find(item => item.partno === partno);
    }

    updateExistingItem(item, additionalQuantity) {
        item.quantity += additionalQuantity;
        item.amount = this.calculateAmount(item);
        return item;
    }

    addNewItem(item, price, discount, quantity) {
        this.items.push({
            partno: item.partno,
            name: item.name || item.item,
            unit: item.unit,
            quantity: quantity,
            price: parseFloat(price).toFixed(2),
            discount: parseFloat(discount).toFixed(2),
            specialDisc: 0,
            amount: this.calculateAmount({
                price: price,
                quantity: quantity,
                discount: discount
            })
        });
    }

    calculateAmount(item) {
        return (parseFloat(item.price) * parseFloat(item.quantity) - 
                parseFloat(item.discount)).toFixed(2);
    }

    removeItem(partno) {
        this.items = this.items.filter(item => item.partno !== partno);
        return this.items;
    }

    updateQuantity(partno, quantity) {
        const item = this.findExistingItem(partno);
        if (item) {
            item.quantity = parseFloat(quantity);
            item.amount = this.calculateAmount(item);
        }
        return this.items;
    }
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

    // ... existing methods ...
}