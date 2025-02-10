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
    }
};