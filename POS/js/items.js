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
        const price = this.getItemPrice(data.partno, data.unit, this.state.matrix, new Date());
        const disc = this.getItemDiscount(data.partno, data.unit, new Date());
        let discvalue = 0;
        
        const existingItem = this.findExistingItem(data.partno);
        if (existingItem) {
            existingItem.quantity += qty;
            return this.items;
        }

        switch (disc.type) {
            case 'Percentage':
                discvalue = price * (disc.value / 100);
                break;
            case 'Amount':
                discvalue = disc.value;
                break;
        }

        this.items.push({
            partno: data.partno,
            name: data.name || data.item,
            unit: data.unit,
            quantity: qty,
            price: parseFloat(price).toFixed(2),
            discount: parseFloat(discvalue).toFixed(2),
            specialDisc: 0
        });

        return this.items;
    }

    insert_item(partno) {
        return $.ajax({
            url: this.config.urls.itemList,
            data: { code: partno },
            dataType: 'json'
        }).then(res => {
            if (res.valid) {
                this.duplicate(res.data);
                return true;
            }
            return false;
        });
    }
}