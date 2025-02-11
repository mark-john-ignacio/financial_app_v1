export class POSItems {
    constructor(config) {
        this.config = config;
        this.items = [];
    }

    getItems() {
        return this.items;
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
        try {
            const item = this.findExistingItem(partno);
            if (item) {
                item.quantity = parseFloat(quantity);
                item.amount = this.calculateAmount(item);
                
                return $.ajax({
                    url: this.config.dualView.quantity,
                    method: 'POST',
                    data: { partNo: partno, quantity: quantity }
                });
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
        }
        return Promise.reject('Item not found');
    }

    getItemPrice(item) {
        let price = 0;
        $.ajax({
            url: this.config.urls.priceCheck,
            data: {
                itm: item.partno,
                cust: this.matrix,
                cunit: item.unit,
                dte: this.utils.formatDate(new Date())
            },
            async: false,
            success: (data) => price = parseFloat(data)
        });
        return price;
    }

    getItemDiscount(item) {
        let discount = {
            value: 0,
            type: ""
        };
        
        $.ajax({
            url: this.config.urls.discount,
            data: { 
                item: item.partno, 
                unit: item.unit, 
                date: this.utils.formatDate(new Date()) 
            },
            dataType: "json",
            async: false,
            success: (res) => {
                if(res.valid) {
                    discount.value = res.data.amount;
                    discount.type = res.data.type;
                }
            }
        });
        return discount;
    }

    getCouponTotal(coupons) {
        if(!coupons.length) return 0;
        let amount = 0;

        coupons.forEach(coupon => {
            $.ajax({
                url: "../MasterFiles/Items/th_couponlist.php",
                data: { coupon },
                dataType: 'json',
                async: false,
                success: (res) => {
                    if(res.valid) {
                        amount += parseFloat(res.data.amount);
                    }
                }
            });
        });
        return amount;
    }

    handleCoupon(couponCode) {
        const subtotal = this.parseAmount('#subtotal');
        const totalTender = this.parseAmount('#totalTender');

        if (parseFloat(subtotal) < parseFloat(totalTender)) {
            this.ui.showAlert("Total tender exceeds subtotal amount");
            return false;
        }

        return $.ajax({
            url: "Function/th_coupon.php",
            data: { coupon: couponCode },
            dataType: 'json'
        });
    }

    updateCouponToDatabase(couponValue) {
        return $.ajax({
            url: this.config.dualView.coupon,
            method: 'POST',
            data: { coupon: couponValue }
        });
    }

    duplicate(data, qty = 1) {
        const price = this.getItemPrice(data.partno, data.unit, this.matrix, new Date());
        const disc = this.getItemDiscount(data.partno, data.unit, new Date());
        let discvalue = 0;
        
        switch (disc.type) {
            case "PRICE":
                discvalue = parseFloat(disc.value);
                break;
            case "PERCENT":
                discvalue = parseFloat(price) * (parseInt(disc.value) / 100);
                break;
        }

        const existingItem = this.findExistingItem(data.partno);
        if (existingItem) {
            existingItem.quantity += parseFloat(qty);
            existingItem.amount = this.calculateAmount(existingItem);
            return this.items;
        }

        this.items.push({
            partno: data.partno,
            name: data.name || data.item,
            unit: data.unit,
            quantity: qty,
            price: parseFloat(price).toFixed(2),
            discount: parseFloat(discvalue).toFixed(2),
            specialDisc: 0,
            amount: this.calculateAmount({
                price: price,
                quantity: qty,
                discount: discvalue
            })
        });

        return this.items;
    }

    insert_item(partno) {
        console.log("Item Inserted: ", partno);
        return $.ajax({
            url: this.config.urls.itemList,
            data: { code: partno },
            dataType: 'json',
            async: false,
            success: (res) => {
                if (res.valid) {
                    this.duplicate(res.data);
                    this.table_store(this.items);
                }
            },
            error: (res) => console.log(res)
        });
    }

    setupItemListHandlers() {
        $('.itmclass').on('click', (e) => {
            const ClassID = $(e.currentTarget).attr('data-clscode');
            
            $('.itmslist').each(function() {
                const id = $(this).attr('data-clscode');
                $(this).toggle(id === ClassID);
            });
        });

        $('#item-wrapper').on('click', '#itemlist', (e) => {
            const name = $(e.currentTarget).attr('name');
            this.insert_item(name);
        });
    }

    handleVoid() {
        $("input:checkbox[name=itemcheck]:checked").each((i, el) => {
            const index = $(el).val();
            if (index > -1) {
                this.items.splice(index, 1);
            }
        });
        this.ui.updateTables(this.items);
    }

    handleBarcodeScanned(items) {
        $.ajax({
            url: "DualView/Function/ibarcode.php", 
            dataType: "json",
            data: { selected_item: items.partno },
            success: (response) => {
                this.duplicate(items);
                this.table_store(this.items);
            }
        });
    }

    handleVoidSubmit() {
        $("input:checkbox[name=itemcheck]:checked").each((i, el) => {
            const index = $(el).val();
            if (index > -1) {
                this.items.splice(index, 1);
            }
        });
        this.updateTables();
    }

    table_store(items) {
        if (!items || !Array.isArray(items)) {
            console.error('Invalid items array');
            return;
        }
        
        console.log('Storing items:', items);
        this.ui.updateTables(items);
        this.computation(items);
    }

    computation(data) {
        const itemAmounts = { 
            discount: 0, 
            net: 0, 
            vat: 0, 
            gross: 0 
        };

        data.forEach(item => {
            const price = parseFloat(item.price);
            const qty = parseFloat(item.quantity);
            const net = price / 1.12;
            
            itemAmounts.net += net * qty;
            itemAmounts.vat += (net * 0.12) * qty;
            itemAmounts.gross += price * qty;
            itemAmounts.discount += parseFloat(item.discount);
        });

        this.ui.updateAmounts(itemAmounts);
        this.state.amtTotal = itemAmounts.gross;
    }

    addItemToPaymentList(item) {
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

    getCouponTotal(coupons) {
        if(!coupons.length) return 0;
        let amount = 0;

        coupons.forEach(coupon => {
            $.ajax({
                url: "../MasterFiles/Items/th_couponlist.php",
                dataType: 'json',
                async: false,
                success: (res) => {
                    if(res.valid) {
                        amount += parseFloat(res.data.amount);
                    }
                }
            });
        });
        return amount;
    }

    getSpecialDisc(data) {
        let discount = 0;
        data.forEach(item => {
            discount += parseFloat(item.amount);
        });
        return discount;
    }

    setupItemListHandlers() {
        $(".itmclass").on("click", (e) => {
            const ClassID = $(e.currentTarget).attr("data-clscode");
            $('.itmslist').each(function() {
                const id = $(this).attr('data-clscode');
                $(this).toggle(id === ClassID);
            });
        });
    }

    getDiscount(data) {
        let discount = 0;
        data.forEach(item => {
            discount += parseFloat(item.amount);
        });
        return discount;
    }

    computeAmounts(data) {
        const amounts = {
            discount: 0,
            net: 0,
            vat: 0,
            gross: 0
        };

        data.forEach(item => {
            const price = parseFloat(item.price);
            const qty = parseFloat(item.quantity);
            const net = price / (1 + this.config.constants.VAT_RATE);
            
            amounts.net += net * qty;
            amounts.vat += net * this.config.constants.VAT_RATE * qty;
            amounts.discount += parseFloat(item.discount);
            amounts.gross += price * qty;
        });

        return amounts;
    }

    chkprice(partno, unit, code, date) {
        try {
            let value = 0;
            $.ajax({
                url: this.config.urls.priceCheck,
                data: { 
                    itm: partno, 
                    cust: code, 
                    cunit: unit, 
                    dte: POSUtils.formatDate(date) 
                },
                async: false,
                success: data => value = parseFloat(data),
                error: xhr => console.error('Price check failed:', xhr)
            });
            return value;
        } catch (error) {
            console.error('Price check error:', error);
            return 0;
        }
    }

    discountprice(item, unit, date) {
        let value = 0;
        let type = "";
        
        $.ajax({
            url: this.config.urls.discount,
            data: { item, unit, date: POSUtils.formatDate(date) },
            dataType: "json",
            async: false,
            success: res => {
                if(res.valid) {
                    value = res.data.amount;
                    type = res.data.type;
                }
            }
        });
        return { value, type };
    }

    PaymentCompute() {
        const tender = this.utils.parseAmount($('#tendered').val());
        const coupon = this.utils.parseAmount($("#couponinput").val());
        const exchange = this.utils.parseAmount($('#ExchangeAmt').val());
        const amt = this.utils.parseAmount($('#subtotal').val());
    
        const service = amt * this.config.constants.SERVICE_FEE;
        const totaltender = tender + coupon;
        const total = amt + service;
        const change = total - totaltender;
    
        if (change > 0) {
            $('#ExchangeAmt').val("0.00");
        } else {
            $('#ExchangeAmt').val(Math.abs(change).toFixed(2));
        }
    
        this.updateAutoNumeric();
        this.updateHiddenInputs(service, totaltender, total);
    }

    handleDelete() {
        const employeeCashierName = this.state.employeeCashierName;
        return $.ajax({
            type: "POST",
            url: this.config.dualView.delete,
            data: { employeeCashierName },
            success: () => {
                console.log("Data deleted successfully!");
                this.state.retriveStatus = 0;
            }
        });
    }
}