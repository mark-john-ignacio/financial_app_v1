export class POSCore {
    constructor(options) {
        this.ui = options.ui;
        this.payment = options.payment;
        this.items = options.items;
        this.config = options.config;
        
        this.state = { ...this.config.initialState };
    }

    init() {
        this.ui.init();
        this.setupEventListeners();
        this.loadBaseCustomer();
    }

    setupEventListeners() {
        // Barcode scanner
        $('#barcode').typeahead({
            autoSelect: true,
            source: (request, response) => {
                $.ajax({
                    url: this.config.urls.barcodeList,
                    dataType: "json",
                    data: { query: $("#barcode").val() },
                    success: (res) => {
                        if(res.valid) response(res.data);
                    }
                });
            },
            displayText: (item) => {
                return `<div style="border-top:1px solid gray; width: 300px">
                    <span>${item.partno}</span><br>
                    <small>${item.name}</small>
                </div>`;
            },
            highlighter: Object,
            afterSelect: (items) => {
                this.items.addItem(items);
                this.ui.updateTables(this.state.itemStored);
                $('#barcode').val("").change();
            }
        });

        // Payment buttons
        $('#btnPay').click(() => this.payment.processPayment(this.state));
        
        // Other event listeners...
    }

    loadBaseCustomer() {
        $.ajax({
            url: this.config.urls.baseCustomer,
            dataType: "json",
            success: (res) => {
                $("#myprintframe").attr("src", "");
                $('#customer').val(res.data).change();
                $('#customer').attr("data-val", res.code).change();
                this.state.matrix = res.pm;
            }
        });
    }

    setupCustomerHandling() {
        $('#customer').typeahead({
            autoSelect: true,
            source: (request, response) => {
                $.ajax({
                    url: "Function/th_customer.php",
                    dataType: "json",
                    data: { query: $("#customer").val() },
                    success: (res) => {
                        if(res.valid) response(res.data);
                    }
                });
            },
            displayText: (item) => {
                return `<div style="border-top:1px solid gray; width: 300px">
                    <span>${item.id}</span><br>
                    <small>${item.value}</small>
                </div>`;
            },
            highlighter: Object,
            afterSelect: (item) => this.handleCustomerSelect(item)
        });
    }

    handleCustomerSelect(item) {
        this.state.matrix = item.matrix;
        $('#customer').val(item.value).change();
        $('#customer').attr("data-val", item.id).change();
        this.clearTables();
    }

    createNewCustomer(customerData) {
        return $.ajax({
            url: "Function/add_customer.php",
            type: "post",
            data: customerData,
            dataType: "json"
        });
    }
}