export class POSCore {
    constructor(options) {
        this.ui = options.ui;
        this.payment = options.payment;
        this.items = options.items;
        this.config = options.config;
        
        this.state = {
            ...this.config.initialState,
            retriveStatus: 0,
            count: 0,
            employeeCashierName: null,
            currentTransaction: null
        };
    }

    async init() {
        try {
            await this.ui.init();
            await this.setupEventListeners();
            await this.loadBaseCustomer();
            this.setupHandlers();
        } catch (error) {
            console.error('Initialization failed:', error);
            this.ui.showAlert('System initialization failed');
        }
    }

    // Consolidate setup methods
    setupHandlers() {
        this.setupEmployeeHandlers();
        this.setupItemHandlers();
        this.setupPaymentHandlers();
        this.setupVoidHandlers();
        this.setupHoldHandlers();
        this.setupRetrieveHandlers();
        this.setupQuantityHandlers();
        this.setupDatabaseSync();
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
        this.updateState({
            matrix: item.matrix,
            currentCustomer: {
                value: item.value,
                id: item.id
            }
        });
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

    async handleHoldTransaction(kitchenReceipt, waitingTime) {
        try {
            const response = await $.ajax({
                url: this.config.transactionUrls.hold,
                data: {
                    items: this.state.itemStored,
                    kitchenReceipt,
                    waitingTime
                },
                dataType: 'json'
            });
            
            if (response.valid) {
                this.ui.showAlert("Transaction held successfully");
                return true;
            }
            
            this.ui.showAlert(response.msg || "Error holding transaction");
            return false;
        } catch (error) {
            console.error('Error holding transaction:', error);
            this.ui.showAlert("Error holding transaction");
            return false;
        }
    }

    handleRetrieveTransaction(selectedItems) {
        return $.ajax({
            url: 'Function/th_getholdtransaction.php',
            data: { items: selectedItems },
            dataType: 'json',
            success: (res) => {
                if (res.valid) {
                    res.data.forEach(item => {
                        this.items.addItem(item, parseInt(item.quantity));
                        this.updateOrderTypeAndTable(item);
                    });
                    this.ui.updateTables(this.state.itemStored);
                    this.ui.showAlert("Items Retrieved");
                }
            }
        });
    }

    updateOrderTypeAndTable(item) {
        $("#orderType option").each(function() {
            if(item.ordertype === $(this).val()) {
                $(this).prop('selected', true);
            }
        });
        
        $("#table option").each(function() {
            if(item.table === $(this).val()) {
                $(this).prop('selected', true);
            }
        });
    }

    setupEmployeeHandlers() {
        this.setupEmployeeDelete();
        this.setupEmployeeLogin();
    }

    setupEmployeeDelete() {
        $(window).on('unload', () => {
            $.ajax({
                type: "POST",
                url: this.config.dualView.delete,
                data: { 
                    employeeCashierName: this.state.employeeCashierName 
                },
                async: false
            });
        });
    }

    setupEmployeeLogin() {
        $("#login").click(() => {
            const user = $("#loginid").val();
            const password = $("#loginpass").val();

            return $.ajax({
                url: "Function/th_void.php",
                data: { user, password },
                dataType: 'json'
            });
        });
    }

    setupSpecialDiscountHandlers() {
        $('#SpecialDiscountBtn').click(() => {
            const data = {
                disc: $("#discountAmt").val(),
                type: $("#discountAmt").find(":selected").attr("dataval"),
                name: $("#discountAmt").find(":selected").text(),
                person: $("#discountCust").val(),
                id: $("#discountID").val()
            };

            this.payment.handleSpecialDiscount(data);
        });

        $("#discountAmt").change(function() {
            const disc = $(this).val();
            $("#dc")[disc !== '0' ? 'show' : 'hide']();
        });
    }

    setupQuantityHandlers() {
        $("#listItem tbody").on('change', '#qty', (e) => {
            const $row = $(e.currentTarget).closest('tr');
            const partno = $row.find('td:first').text();
            const quantity = $(e.currentTarget).val();
            
            this.items.updateQuantity(partno, quantity);
            this.ui.updateTables(this.items.getItems());
        });
    }

    updateToDatabase(type, value) {
        return $.ajax({
            url: this.config.dualView[type],
            method: 'POST',
            data: { [type]: value }
        });
    }

    setupDatabaseSync() {
        $('#couponinput').change(() => {
            this.updateToDatabase('coupon', $('#couponinput').val());
        });

        $('#discountInput').change(() => {
            this.updateToDatabase('discount', $('#discountInput').val());
        });

        $('input[name="qty[]"]').change((e) => {
            const $input = $(e.currentTarget);
            this.updateToDatabase('quantity', {
                partNo: $input.data('val'),
                quantity: $input.val()
            });
        });
    }

    clearTables() {
        $("#paymentList > tbody").empty();
        $("#VoidList > tbody").empty();
        $("#listItem > tbody").empty();
        $("#gross").text(parseFloat(0).toFixed(2));
        $("#vat").text(parseFloat(0).toFixed(2));
        $("#net").text(parseFloat(0).toFixed(2));
    }

    setupHoldHandlers() {
        $('#btnHold').on('click', function() {
            this.disabled = true;
            const kitchen_receipt = $("#kitchen_receipt").val();
            const waitingTime = $("#waiting_time").val();
            
            this.handleHoldTransaction(kitchen_receipt, waitingTime);
        });
    }

    setupRetrieveHandlers() {
        $("#RetrieveSubmit").click(() => {
            const itemRetrieve = [];
            $("input:checkbox[name=chkretrieve]:checked").each(function() {
                itemRetrieve.push($(this).val());
            });
            this.handleRetrieveTransaction(itemRetrieve);
        });
    }

    handleCustomerModal() {
        $("#AddCustomerModal").modal("show");
    }

    setupItemHandlers() {
        $('#item-wrapper').on('click', '#itemlist', (e) => {
            const name = $(e.currentTarget).attr("name");
            this.items.insert_item(name);
        });
    }
    
    setupVoidHandlers() {
        $('#VoidSubmit').click(() => {
            $("input:checkbox[name=itemcheck]:checked").each((i, el) => {
                const index = $(el).val();
                this.items.removeItems([index]);
            });
            this.ui.updateTables(this.items.getItems());
        });
    }

    setupPaymentHandlers() {
        $('#PaySubmit').click(() => {
            const proceed = true;
            const total = parseFloat($('#totalAmt').val().replace(/,/g,''));
            const totalTender = parseFloat($('#totalTender').val().replace(/,/g,''));

            if (parseFloat(total) <= parseFloat(totalTender)) {
                this.payment.processPayment();
            } else {
                alert("Insufficient payment amount");
            }
        });
    }

    setupItemQuantityHandlers() {
        $("#listItem tbody").on('change', '#qty', function() {
            const qty = $(this).val();
            const partno = $(this).attr("data-val");
            
            $.ajax({
                url: "Function/ItemList.php",
                data: { code: partno },
                dataType: 'json',
                async: false,
                success: function(res) {
                    if (res.valid) {
                        this.items.updateQuantity(partno, qty);
                    }
                }
            });
        });
    }

    setupItemClassHandlers() {
        $(".itmclass").on("click", function() {
            const ClassID = $(this).attr("data-clscode");
            $('.itmslist').each(function() {
                const id = $(this).attr('data-clscode');
                $(this).toggle(id === ClassID);
            });
        });
    }

    create_new_customer() {
        const customerData = {
            customer: $("#customer_name").val(),
            tin: $("#tin_number").val(),
            houseno: $("#customer_house").val(),
            city: $("#customer_city").val(),
            state: $("#customer_state").val(),
            country: $("#customer_country").val(),
            zip: $("#customer_zip").val()
        };

        $.ajax({
            url: this.config.customerUrls.add,
            type: "post",
            data: customerData,
            dataType: "json",
            async: false,
            success: (res) => {
                if(res.valid) {
                    this.ui.showAlert(res.msg);
                } else {
                    this.ui.showAlert(res.msg);
                }
                location.reload();
            },
            error: (msg) => console.log(msg)
        });
    }

    updateState(changes) {
        this.state = {
            ...this.state,
            ...changes
        };
        this.ui.updateTables(this.state.itemStored);
    }

}