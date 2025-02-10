export class POSItems {
    addItem(item, quantity = 1) {
        const price = this.getItemPrice(item);
        const discount = this.getItemDiscount(item);
        
        this.updateStoredItems(item, price, discount, quantity);
    }

    getItemPrice(item) {
        let price = 0;
        $.ajax({
            url: "../Sales/th_checkitmprice.php",
            data: {
                itm: item.partno,
                cust: this.matrix,
                cunit: item.unit,
                dte: this.formatDate(new Date())
            },
            async: false,
            success: (data) => price = parseFloat(data)
        });
        return price;
    }

    // ... more item methods
}