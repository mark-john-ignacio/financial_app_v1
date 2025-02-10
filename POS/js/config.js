export const POSConfig = {
    constants: {
        VAT_RATE: 0.12,
        SERVICE_FEE: 0.10,
        REFRESH_RATE: 1000
    },
    
    urls: {
        baseCustomer: "../System/th_loadbasecustomer.php",
        itemList: "Function/ItemList.php",
        barcodeList: "Function/th_listBarcode.php",
        priceCheck: "../Sales/th_checkitmprice.php",
        discount: "Function/th_discount.php",
        payment: "Function/pos_save.php"
    },
    
    initialState: {
        itemStored: [],
        coupon: [],
        specialDisc: [],
        matrix: 'PM1',
        amtTotal: 0
    },

    customerUrls: {
        add: "Function/add_customer.php",
        search: "Function/th_customer.php",
        access: "Function/th_useraccess.php"
    },
    
    dualView: {
        coupon: "DualView/Function/dv_coupon.php",
        discount: "DualView/Function/dv_discount.php",
        quantity: "DualView/Function/uctable.php",
        delete: "DualView/Function/rdelete.php"
    }
};