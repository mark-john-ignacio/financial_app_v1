
    /**
     * Initiate a variables
     */
    const itemStored = [];
    const coupon = [];
    const specialDisc = []
    var matrix = 'PM1';
    var amtTotal = 0;
    var count = 0;

    
    
    $(document).ready(function(){
        console.log("isCheckWaitingTime:", isCheckWaitingTime);
        console.log("isCheckManualReceipt:", isCheckManualReceipt);
        console.log("ServiceFee:", serviceFee);
        console.log("Employee Cashier Name:", employeeCashierName);
        clockUpdate();
        setInterval(clockUpdate, 1000);
        $(".regular").slick({
            dots: false,
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4
        });

        $.ajax({
            url: "../System/th_loadbasecustomer.php",
            dataType: "json",
            success: function (res) {
                $("#myprintframe").attr("src", "");
                $('#customer').val(res.data).change();
                $('#customer').attr("data-val", res.code).change();
                matrix = res.pm
            }
        });
        
        $('#barcode').typeahead({
            autoSelect: true,
            source: function(request, response) {
                $.ajax({
                    url: "Function/th_listBarcode.php",
                    dataType: "json",
                    data: {
                        query: $("#barcode").val()
                    },
                    success: function (res) {
                        if(res.valid)
                            response(res.data);
                    }
                });
            },
            displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.partno + '</span><br><small>' + item.name + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(items) { 
                console.log(items);
                duplicate(items); 
                table_store(itemStored); 
                $.ajax({
                    url: "DualView/Function/ibarcode.php", 
                    dataType: "json",
                    data: {
                        selected_item: items.partno 
                    },
                    success: function(response) {
                        
                        console.log(response);
                    }
                });
                $('#barcode').val("").change()
            }
        })

        $("#spcdBtn").click(function(){
            $("#paymentcol").hide();
            $("#specialdiscountcol").show()
        })

        $("#spcBack").click(function(){
            $("#paymentcol").show();
            $("#specialdiscountcol").hide()
        })

        $("#couponBtn").click(function(){
            $("#couponmodal").show()
            $("#paymentcol").hide()
        })
        
        $("#couponback").click(function(){
            $("#couponmodal").hide()
            $("#paymentcol").show()
        })

        $("#CouponSubmit").click(function(){
            let coupons = $("#coupontxt").val()
            var subtotal = $("#subtotal").val()
            var totalTender = $("#totalTender").val();

            if(parseFloat(subtotal) < parseFloat(totalTender)){
                return alert("Coupon reached the total Amount. Cannot enter another Coupon")
            }

            $.ajax({
                url: "Function/th_coupon.php",
                data: { coupon: coupons },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        if(coupon.includes(coupons)){
                            $("#couponmsg").css("color", "RED")
                            return $("#couponmsg").text("Coupon already been entered!")
                        }
                        $("#couponmsg").css("color", "GREEN")
                        $("#couponmsg").text(res.msg)
                        coupon.push(coupons)
                        $("#couponinput").val(getCoupon(coupon))
                        PaymentCompute()
                        updateCouponToDatabase();
                    } else {
                        $("#couponmsg").text(res.msg)
                        $("#couponmsg").css("color", "RED")
                    }
                }
            })
        })


        $('#customer').typeahead({
            autoSelect: true,
            source: function(request, response) {
                let flag = false;
                $.ajax({
                    url: "Function/th_customer.php",
                    dataType: "json",
                    data: {
                        query: $("#customer").val()
                    },
                    success: function (res) {
                        if(res.valid){
                            response(res.data);
                            itemStored.length = 0;
                            coupon.length = 0;
                            specialDisc.length = 0;
                            flag = true;
                        }
                    }
                });
            },
            displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(item) { 				
                console.log(item)  
                matrix = item.matrix;
                $('#customer').val(item.value).change()
                $('#customer').attr("data-val", item.id).change();

                $("#paymentList > tbody").empty()
                $("#VoidList > tbody").empty()
                $("#listItem > tbody").empty()
                $("#gross").text(parseFloat(0).toFixed(2));
                $("#vat").text(parseFloat(0).toFixed(2));
                $("#net").text(parseFloat(0).toFixed(2));
                
            }
        })

        $("#login").click(function(){
            let user = $("#loginid").val();
            let password = $("#loginpass").val();

            $.ajax({
                url: "Function/th_void.php",
                data: { 
                    user: user, 
                    password: password 
                },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid) {
                        alert(res.msg)
                        modalshow("Void");
                    } else {
                        alert(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })
        })


        $('.itmslist, .itmclass').hover(function() {
            $(this).css('cursor','pointer');
        });

        $(".itmclass").on("click", function() {
            const ClassID = $(this).attr("data-clscode");
            
            $('.itmslist').each(function(i, obj) {
                itmcls = $(this).attr("data-itemlist");
                //Show all items
                if(ClassID === "ALL"){
                    return $(this).show();;
                }

                //Show all items per category
                if(itmcls==ClassID){
                    $(this).show();
                }else if(itmcls!=ClassID){
                    $(this).hide();
                }
            });		
        });


        $('#item-wrapper').on('click', '#itemlist', function() {
            const $this = $(this);
            const name = $this.attr("name");
            insert_item(name);
        });


        $('#VoidSubmit').click(function(){
            $("input:checkbox[name=itemcheck]:checked").each(function(){
                var $checkbox = $(this);
                var $row = $checkbox.closest('tr');
                var index = $row.index();

                var value = $checkbox.val();
                var name1 = $checkbox.data('name1');
                
                
                itemStored.splice(index, 1);
                specialDisc.splice(index, 1);

                
                table_store(itemStored);
                $.ajax({
                    url: 'DualView/Function/vdelete.php',
                    method: 'POST',
                    data: { name1: name1 },
                    success: function(response) {
                        console.log('Data sent successfully:', response);
                        $("#loginid").val('');
                        $("#loginpass").val('');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error sending data:', textStatus, errorThrown);
                    }
                });

                $('#mymodal').modal('hide');
            });
        });


        $('#btnVoid').click(function(){
            /*if(!checkAccess("POS_Void.php")){
                return;
            }*/
            if(itemStored.length === 0) {
                return alert('Transaction is empty!')
            }

            $('#voidlogin').modal('show');
            table_store(itemStored);
        })

        $('#SpecialDiscountBtn').click(function(){
            var disc = $("#discountAmt").val();
            var type = $("#discountAmt").find(":selected").attr("dataval");
            var name = $("#discountAmt").find(":selected").text();
            var person = $("#discountCust").val()
            var id = $("#discountID").val()
            var subtotal = $("#subtotal").val()

            if(parseFloat(subtotal) <= 0){
                return alert("Discount has gone to 0! Discount cannot be apply")
            }
            if (person.trim() === "" || id.trim() === "") {
                alert("Please fill out both the Customer Name and Customer Valid ID fields.");
            }else{
                // $("#paymentList tbody").each()
                $("input:checkbox[id='discounted']:checked").each(function(){
                    let amounts = $(this).val();
                    let itemno = $(this).attr("dataval");
                    
                    itemStored.map((item, index) =>{
                        console.log(item)
                        if(item.partno === itemno){
                            switch(type){
                                case "PERCENT":
                                    item['specialDisc'] = item.amount * (disc/100);
                                    item['amount'] -= item.amount * (disc/100);
                                    break;
                                case "PRICE":
                                    item['specialDisc'] = disc;
                                    item['amount'] -= disc;
                            }
                        specialDisc.push({item: item.partno, type: type, name: name, person: person, id: id, amount: item.specialDisc})
                        }
                        console.log(specialDisc)
                    })
                })
                $("#discountInput").val(getSpecialDisc(specialDisc))
                PaymentCompute()

                alert("Special discount has been added!")
                table_store(itemStored);
                $("#paymentcol").show();
                $("#specialdiscountcol").hide()
                updateDiscountToDatabase();
            }
        })

        $("#discountAmt").change(function(){
            var disc = $(this).val();
            if(disc != 0) {
                return $("#dc").show();
            } 
            return $("#dc").hide();
        })

        //button for holding items
        $('#btnHold').on('click', function(){
            this.disabled = true;
            let kitchen_receipt = $("#kitchen_receipt").val();

            let waitingTime = $("#waiting_time").val();
            if (!waitingTime && kitchen_receipt == "Yes" && isCheckManualReceipt == 1) {
                
            }
            else if(!waitingTime && kitchen_receipt == null){
                if(isCheckWaitingTime == 1){
                    this.disabled = false;
                    return alert('Waiting time is required!');
                }
            }
            else if(!waitingTime && kitchen_receipt == "No"){
                if(isCheckWaitingTime == 1){
                    this.disabled = false;
                    return alert('Waiting time is required!');
                }
            }

            if (!kitchen_receipt && isCheckManualReceipt == 1) {
                this.disabled = false;
                if(kitchen_receipt == null){
                    kitchen_receipt = "No";
                }
            }



            let tranno, msg, print, date;
            var isSuccess = false;
            var isHold = false;

            if(itemStored.length === 0){
                this.disabled = false;
                return alert('Transaction is empty! cannot hold transaction');
            }
            const quantity   = [];

            $('input[name*="qty"]').each((index, item) => {
                quantity.push($(item).val())
            })
            
            $.ajax({
                url: 'Function/th_hold.php',
                data: {
                    tranno: $("#tranno").val(),
                    table: $('#table').val(),
                    type:  $('#orderType').val(),
                },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        tranno = res.tranno
                        console.log(res.tranno)
                        isHold = true;
                    } else {
                        alert(res.msg)
                        console.log(res.msg)
                    }
                }
            })
            if(isHold == true){
                itemStored.map((item, index) => {
                    $.ajax({
                        url: 'Function/th_holdtransaction.php',
                        data: {
                            code: tranno,
                            partno: item.partno,
                            name: item.name,
                            unit: item.unit,
                            quantity: item.quantity,
                            cost: item.price,
                            waiting_time: $("#waiting_time").val(),
                            kitchen_receipt: kitchen_receipt,
                            date: date,
                        },
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.valid){
                                isSuccess = true;
                                print = res.receipt
                                date = res.date
                                msg = res.data;
                            } else {
                                msg = res.msg
                            }
                        },
                        error: function(res){
                            console.log(res)
                        }
                    })
                })
            }
            if(isSuccess && print == "No"){
                alert(msg);
                location.reload();
            }
            else if(isSuccess && print == "Yes"){
                $("#kitchenPrintModal").modal('hide');
                alert(msg);
                $("#kitchenPrintModal").modal('show');
                $("#mykprintframe").attr("src", "PendingOrders/kitchen_print.php?tranno=" + tranno + "&transaction_type=Hold&date=" + date);

                console.log();

                // Reload the page when the modal is closed
                $('#kitchenPrintModal').on('hidden.bs.modal', function () {
                    location.reload();
                });
            } else {
                alert(msg);
            }
            
        });

        /**
         * Payment Transaction
         */

        $('#btnPay').click(function(){

            
            let kitchen_receipt = $("#kitchen_receipt").val();

            let waitingTime = $("#waiting_time").val();
            if (!waitingTime && kitchen_receipt == "Yes" && isCheckManualReceipt == 1) {
                
            }
            else if(!waitingTime && kitchen_receipt == null){
                if(isCheckWaitingTime == 1){
                    if(retriveStatus == 1){
                        
                    }
                    else{
                        this.disabled = false;
                        return alert('Waiting time is required!');
                    }
                }
            }
            else if(!waitingTime && kitchen_receipt == "No"){
                if(isCheckWaitingTime == 1){
                    if(retriveStatus == 1){
                        
                    }
                    else{
                        this.disabled = false;
                        return alert('Waiting time is required!');
                    }
                }
            }

            if(itemStored.length === 0){
                return alert('Transaction is empty! cannot proceed transaction');
            }

            let amt = $('#subtotal').val().replace(/,/g,'');
            let ServiceFee = serviceFee;

            let service = parseFloat(amt) * parseFloat(ServiceFee)
            let total = parseFloat(amt) + service



            $('#tendered').val(0)
            $('#tendered').focus()
            $('#tendered').select()
            $("#couponinput").val(getCoupon(coupon))
            $("#h_tranno").val()
            $("#ServiceInput").val(service)
            $("#totalAmt").val(total)
            $("#discountInput").val(0)
            $("#totalTender").val(0)
            $('#discountAmt').val(0)
            $('#ExchangeAmt').val(0)
            
            $('#payModal').modal('show')
            $("#couponmodal").hide();
            $("#specialdiscountcol").hide()
            $('#modal-body').modal('show')
            PaymentCompute()
        })

        /**
         * Retrive Hold transaction
         */

         var retriveStatus = 0;

         $(document).ready(function() {
            $('#btnRetrieve').click(function() {
                

                $.ajax({
                    url: 'Function/th_gethold.php',
                    dataType: 'json',
                    async: false,
                    success: function(res) {
                        if (res.valid) {
                            $('#RetrieveList > tbody').empty();
                            res.data.forEach((item, index) => {
                                console.log(item);
                                // Create a new table row with the required data
                                const $row = $("<tr>").append(
                                    $("<td>").text(item.transaction),
                                    $("<td align='center'>").text(item.table),
                                    $("<td align='center'>").text(item.ordertype),
                                    $("<td align='center'>").text(item.trandate)
                                );

                                // Append the new row to the table body
                                $row.appendTo('#RetrieveList > tbody');

                                // Add a click event listener to the row
                                $row.click(function() {
                                    $("#tranno").val(item.transaction);
                                    // Additional code to handle the display of the selected data can be added here
                                    console.log("Row clicked:", item.transaction);
                                    retriveStatus = 1;
                                });
                            });
                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function(res) {
                        console.log("AJAX error:", res);
                    }
                });

                modalshow("Retrieve");
            });
        });

        /**
         * Retrive Hold transaction Function
         */

         $("#RetrieveList tbody").on("mouseenter", "tr", function() {
            $(this).css("background-color", "#019aca");
            $(this).css("color", "white");
            $(this).css("cursor", "hand");
        }).on("mouseleave", "tr", function() {
            $(this).css("background-color", "");
            $(this).css("color", "");
            $(this).css("cursor", "pointer");
        });

        $("#RetrieveList tbody").on("click", "tr", function() {
            let row = $(this).find('td:eq(0)').text();

            $.ajax({
                url: 'Function/th_getholdtransaction.php',
                data: {
                    items: row
                },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        
                        res.data.map((item, index) => {
                            duplicate(item, parseInt(item.quantity))
                            $("#orderType").each(function(){
                                $(this).children('option').each(function(){
                                    if(item.ordertype == $(this).val()) $(this).prop('selected', true)
                                })
                            })
                            $("#table").each(function(){
                                $(this).children('option').each(function(){
                                    if(item.table == $(this).val()) $(this).prop('selected', true)
                                })
                            })
                            // $('#orderType').find(':selected').val(res.data.orderType)
                            // $('#table').find(':selected').val(res.data.table)
                        })
                        alert("Item Retrieved")
                        console.log(res)
                        table_store(itemStored);
                    } else {
                        console.log(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })

            $('#mymodal').modal('hide')
        });

        /**
         * Retrieve Submit via checkbox
         */
        // $('#RetrieveSubmit').click(function(){
        //     const itemRetrieve = [];
        //     $("input:checkbox[name=chkretrieve]:checked").each(function(){
        //         itemRetrieve.push($(this).val());
        //     });

        //     $.ajax({
        //         url: 'Function/th_getholdtransaction.php',
        //         data: {
        //             items: itemRetrieve
        //         },
        //         dataType: 'json',
        //         async: false,
        //         success: function(res){
        //             if(res.valid){
        //                 res.data.map((item, index) => {
        //                     duplicate(item, parseInt(item.quantity))
        //                     $("#orderType").each(function(){
        //                         $(this).children('option').each(function(){
        //                             if(item.ordertype == $(this).val()) $(this).prop('selected', true)
        //                         })
        //                     })
        //                     $("#table").each(function(){
        //                         $(this).children('option').each(function(){
        //                             if(item.table == $(this).val()) $(this).prop('selected', true)
        //                         })
        //                     })
        //                     // $('#orderType').find(':selected').val(res.data.orderType)
        //                     // $('#table').find(':selected').val(res.data.table)
        //                 })
        //                 alert("Item Retrieved")
        //                 console.log(res )
        //                 table_store(itemStored);
        //             } else {
        //                 console.log(res.msg)
        //             }
        //         },
        //         error: function(res){
        //             console.log(res)
        //         }
        //     })

        //     $('#mymodal').modal('hide')
        // })


        $('#tendered').on('keyup', function(){
            let tender = $(this).val();
            if(tender != ""){
                $("#couponinput").val(getCoupon(coupon))
                return PaymentCompute()
            }
            
            $('#ExchangeAmt').val('0.00')
        })

        /**
         * Number Pad in user perspective
         */

        $('.btnpad').click(function(){
            let tender = $('#tendered').val().replace(/,/g,'');
            let total = $('#totalAmt').val().replace(/,/g,'');
            let btn = $(this).attr("data-val").replace(/,/g,'');
            let number = 0;

            if(tender == "0.00"){
                $('#tendered').val("");
                tender = "";
            }

            switch(btn){
                case ".":
                    if(tender.indexOf(".") != -1) number = ""
                    break;
                case "DEL": 
                    if(tender.length == 1){
                        number = "0.00";
                    } else {
                        btn = tender.slice(0, 1);
                        number = btn;
                    }
                    break;
                case "CLEAR": 
                    number = "0.00"
                    $("#totalTender").val("0.00")
                    break;
                case "EXACT":
                    number = total;
                    break;
                case '1000': 
                    number = parseInt(btn) + parseInt(tender);
                    break;
                case '500':
                    number = parseInt(btn) + parseInt(tender);
                    break;
                case '200':
                    number = parseInt(btn) + parseInt(tender);
                    break;
                case '100': 
                    number = parseInt(btn) + parseInt(tender);
                    break;
                default: 
                    number = parseInt(btn) + parseInt(tender);
              
            }

            $('#tendered').val(number);
            $("#tendered").autoNumeric('destroy');
		    $("#tendered").autoNumeric('init',{mDec:2});
            PaymentCompute();
        })

        $("#paymethod").change(function(){
            if($(this).val() === "CASH") {
                $("#paymethod_txt").prop("disabled", true)
                return $("#paymethod_txt").val("");
            }
            return $("#paymethod_txt").prop("disabled", false)
        })

        /**
         * Pay Submit Function where storing of Payments
         */
        $('#PaySubmit').click(function(){
            let exchange = $('#ExchangeAmt').val().replace(/,/g,'');
            let total = $('#subtotal').val().replace(/,/g,'');
            let totalTender = $('#totalTender').val().replace(/,/g,'');
            let tender = $('#tendered').val();
            let proceed = false, isFinished = false;
            let gross = $('#totalAmt').val().replace(/,/g,'')
            let net = $("#net").text()
            let vat = $("#vat").text()
            let transaction = $("#tranno").val()
            let servicefee = $("#ServiceInput").val().replace(/,/g,'')
            let h_tranno = $("#h_tranno").val()
            // let totalAmt = $("#totalAmt").val().replace(/./g,'');
            let method = $("#paymethod").find(":selected").val();
            let reference = $("#paymethod_txt").val();
            let tranno = '';
            let flag_update = '';
            let cName = $('#cust_name').val();
            let cAddr = $('#cust_address').val();
            let cTin = $('#cust_tin').val();

            let kitchen_receipt = $("#kitchen_receipt").val();
            if (!kitchen_receipt) {
                this.disabled = false;
                if(kitchen_receipt == null){
                    kitchen_receipt = "No";
                }
            }

            let kprint;
            
            if(parseFloat(total) <= parseFloat(totalTender)){
                $.ajax({
                    url: 'Function/pos_save.php',
                    type: 'post',
                    data: {
                        tranno: transaction ,
                        method: method,
                        reference: reference,
                        amount: gross,
                        net: net,
                        vat: vat,
                        gross: parseFloat(gross),
                        subtotal: parseFloat(total),
                        holdtranno: $('#h_tranno').val(),

                        customer: $('#customer').attr('data-val'),
                        order: $('#orderType').val(),
                        table: $('#table').val(),

                        tendered: tender,
                        exchange: parseFloat(exchange),
                        discount: getDiscount(itemStored),
                        coupon: getCoupon(coupon),
                        service: parseFloat(servicefee),

                        customerName: cName,
                        customerAddress: cAddr,
                        customerTin: cTin
                    },
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        if(res.valid){
                            proceed = res.valid;
                            tranno = res.tranno
                            flag_update = res.flag_update
                            alert(res.msg)
                        } else {
                            alert(res.msg)
                        }
                    },
                    error: function(res){
                        console.log(res)
                    }
                })
            } else {
                alert("Amount tender is less than the amount")
            }

            if(proceed){
                itemStored.map((item, index) => {
                    $.ajax({
                        url: 'Function/pos_savedet.php',
                        type: 'post',
                        data: {
                            tranno: tranno,
                            itm: item.partno,
                            unit: item.unit,
                            quantity: item.quantity,
                            amount: item.price,
                            flag_status: flag_update,
                            kitchen_receipt: kitchen_receipt,
                            
                            discount: item.discount,
                            discountID: $("#discountID").val(),
                            discountName: $("#discountCust").val(),
                            waiting_time: $('#waiting_time').val(),

                            coupon: JSON.stringify(coupon),
                            specialdisc: JSON.stringify(specialDisc),
                        },
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.valid){
                                console.log(res.msg)
                                kprint = res.print
                                isFinished = true
                            } else {
                                console.log(res.msg)
                                isFinished = false
                            }
                            
                        },
                        error: function(res){
                            console.log(res)
                        }
                    })
                })
            }

            if (isFinished) {
                $.ajax({
                    url: "../include/th_toInv.php",
                    data: {tran: tranno, type: "POS"},
                    async: false,
                    success: function(res) {
                        console.log(res);
                    },
                    error: function(res) {
                        console.log(res);
                    }
                });

                if (kprint == "Yes") {
                    $("#kitchenPrintModal").modal('show');
                    $("#mykprintframe").attr("src", "PendingOrders/kitchen_print.php?tranno=" + tranno + "&transaction_type=Payment");

                    // When kitchenPrintModal is closed
                    $("#kitchenPrintModal").on('hidden.bs.modal', function () {
                        // Show PrintModal
                        $("#PrintModal").modal('show');
                        $("#myprintframe").attr("src", "pos_print.php?tranno=" + tranno);

                        $("#PrintModal").on('hidden.bs.modal', function () {
                            location.reload();
                            retriveStatus = 0;
                        });
                    });
                } else {
                    $("#PrintModal").modal('show');
                    $("#myprintframe").attr("src", "pos_print.php?tranno=" + tranno);

                    // Reload the page when the modal is closed
                    $('#PrintModal').on('hidden.bs.modal', function () {
                        location.reload();
                        retriveStatus = 0;
                    });
                }
            }

            
        })

        $("#listItem tbody").on('change', '#qty', function(){
            let qty = $(this).val();
            let partno = $(this).attr("data-val");
            $.ajax({
                url: "Function/ItemList.php",
                data: {code : partno},
                dataType: 'json',
                async: false,
                success: function(res){
                    // if()

                    if(res.valid){
                        res.data.map((item, index) => {
                            if (!Array.isArray(itemStored)) {
                                itemStored = [];
                            }
                            

                            const price = chkprice(item.partno, item.unit, matrix, "<?= date('m/d/Y') ?>")
                            const disc = discountprice(item.partno, item.unit, "<?= date('m/d/Y') ?>")
                            var discvalue = 0;
                            let found = false;
                            
                            for (let i = 0; i < itemStored.length; i++) {
                                if (itemStored[i].partno === item.partno) {
                                    itemStored[i].quantity = parseFloat(qty);

                                    switch(disc.type){
                                        case "PRICE":
                                            discvalue = parseFloat(itemStored[i].discount) + parseFloat(disc.value);
                                            break;
                                        case "PERCENT":
                                            discvalue = parseFloat(itemStored[i].price) * (parseInt(disc.value) / 100);
                                            break;
                                    }

                                    itemStored[i].discount = parseFloat(discvalue);
                                    itemStored[i].amount = (parseFloat(itemStored[i].price) * parseFloat(itemStored[i].quantity)) - parseFloat(itemStored[i].discount);
                                    break;
                                }
                            }
                        })  
                        table_store(itemStored);
                    } else {
                        alert(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })
        })

        $('#optInputsCheck').on('change', function() {
            $('#optionalFields').toggle(this.checked);
        });
    })


    /**
     * Modal Show Different Modules
     * @param string {modal} to trigger where modal will show
     */

    function modalshow(modal){
        $('.modal-body').css('display', 'none');
        $('#footer button').css('display', 'none');

        switch(modal){
            case "Retrieve": 
                $('#invheader').text("Retrieve");
                $('#RetrieveSubmit').css('display', 'inline-block')
                $('#retrieve').css('display', 'block');
                break;
            case "Void":
                $('#invheader').text("Void");
                $('#VoidSubmit').css('display', 'inline-block')
                $('#void').css('display', 'block');
                break;
        }
        $('#mymodal').modal("show");
    }

    /**
     * Item List to insert in the table
     */

    function insert_item(partno){
        console.log("Item Inserted: ", partno)
        

        $.ajax({
            url: 'Function/ItemList.php',
            data: {
                code: partno
            },
            dataType: 'json',
            async: false,
            success: function(res) {
                if(res.valid){
                    var quantity = 1;
                    res.data.map((item, index) => {
                        duplicate(item)
                    })
                    // console.log(itemStored)
                    table_store(itemStored);
                } else {
                    alert(res.msg);
                }
                
            },
            error: function(res){
                console.log(res)
            }
        })
    }

    /**
     * @param {data} get all data of items
     * @param decimal {qty} can be manipulated based on the quantity show
     * for duplication item
     */

    function duplicate(data, qty = 1,) {
        if (!Array.isArray(itemStored)) {
            itemStored = [];
        }

        const price = chkprice(data.partno, data.unit, matrix, "<?= date('m/d/Y') ?>")
        const disc = discountprice(data.partno, data.unit, "<?= date('m/d/Y') ?>")
        var discvalue = 0;
        let found = false;
        
        for (let i = 0; i < itemStored.length; i++) {
            let remain = parseFloat(data.quantity)
            let quantity = itemStored[i].quantity; 

             
            
            

            if (itemStored[i].partno === data.partno) {
                itemStored[i].quantity += parseFloat(qty);

                switch(disc.type){
                    case "PRICE":
                        discvalue = parseFloat(itemStored[i].discount) + parseFloat(disc.value);
                        break;
                    case "PERCENT":
                        discvalue = parseFloat(itemStored[i].price) * (parseInt(disc.value) / 100);
                        break;
                }

                itemStored[i].discount = parseFloat(discvalue);
                itemStored[i].amount = (parseFloat(itemStored[i].price) * parseFloat(itemStored[i].quantity)) - parseFloat(itemStored[i].discount);
                found = true;
                break;
            }
        }

        switch(disc.type){
            case "PRICE":
                discvalue = discvalue + parseFloat(disc.value);
                break;
            case "PERCENT":
                discvalue = parseFloat(price) * (parseInt(disc.value) / 100);
                break;
        }

        if (!found) {
            itemStored.push({
                partno: data.partno,
                name: (data.name ? data.name : data.item),
                unit: data.unit,
                quantity: qty,
                price: parseFloat(price).toFixed(2),
                discount: parseFloat(discvalue).toFixed(2),
                specialDisc: 0,
                amount: parseFloat(price) - parseFloat(discvalue)
            });
        }

    }

    /**
     * Computation for payments
     */

    function PaymentCompute(){
        let tender = $('#tendered').val().replace(/,/g,'');
        let coupon = $("#couponinput").val().replace(/,/g,'');
        let exchange =$('#ExchangeAmt').val().replace(/,/g,'');
        let amt = $('#subtotal').val().replace(/,/g,'');
        let ServiceFee = serviceFee;

        let service = parseFloat(amt) * parseFloat(ServiceFee)
        let totaltender = parseFloat(tender) + parseFloat(coupon)

        let total = parseFloat(amt) + service

        let change = parseFloat(total) - totaltender;

        let hold_tranno = $("#h_tranno").val();

        if(change > 0){
            return $('#ExchangeAmt').val("0.00")
        }
        $("#discountInput").val(getSpecialDisc(specialDisc)).change()
        $("#ServiceInput").val(service)
        $("#h_tranno").val()
        $("#totalTender").val(totaltender)
        $("#totalAmt").val(total)
        $('#ExchangeAmt').val(Math.abs(change))
        $('#ExchangeAmt').autoNumeric('destroy');
        $('#ExchangeAmt').autoNumeric('init',{mDec:2});
    }

    //price checking
    function chkprice(partno,unit,code,date){
        var value;
		$.ajax ({ 
			url: "../Sales/th_checkitmprice.php",
			data: { itm: partno, cust: code, cunit: unit, dte: date },
			async: false,
			success: function( data ) {
                value = data;
			}
		});
        return value
	}

    /**
     * Return a discount Price
     */

    function discountprice(item, unit, date){
        var value = 0;
        var type = "";

        $.ajax({
            url: "Function/th_discount.php",
            data: { item: item, unit: unit, date: date},
            dataType: "json",
            async: false,
            success: function(res){
                let discount = parseFloat(res.data)
                value = discount;
                type = res.type;
                console.log(res)
            }, 
            error: function(res){
                console.log(res)
            }
        })
        return {
            value: value,
            type: type
        };
    }


    /**
     * Table tbody Listing an items
     */
    function table_store(items){
        $('#listItem > tbody').empty();
        $('#VoidList > tbody').empty();
        $('#paymentList > tbody').empty();
        console.log(items)

        items.map((item, index) => {
            $("<tr class='font-large'>").append(
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"' data-val='"+ item.partno +"'/>"),
                $("<td style='text-align: right'>").text(parseFloat(item.price).toFixed(2)),
                $("<td style='text-align: right'>").text(parseFloat(item.discount).toFixed(2)),
                $("<td style='text-align: right'>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#listItem > tbody")


            $("<tr>").append(
                $("<td align='center'>").html("<input type='checkbox' name='itemcheck' value='"+item.name+"' data-name1='"+ item.partno +"'/>"),
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").text(item.quantity),
                $("<td>").text(parseFloat(item.price).toFixed(2)),
                $("<td>").text(parseFloat(item.discount).toFixed(2)),
                $("<td>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#VoidList > tbody")

            $("<tr>").append(
                $("<td>").html("<input type='checkbox' name='discounted[]' id='discounted' dataval='"+item.partno+"' value='"+parseFloat(item.amount)+"'/>"),
                $("<td>").text(item.name),
                $("<td align='center'>").text(item.unit),
                $("<td align='center'>").text(item.quantity),
                $("<td align='center'>").text(parseFloat(item.price).toFixed(2)),
                $("<td align='center'>").text(parseFloat(item.discount).toFixed(2)),
                $("<td>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#paymentList > tbody")
        })
        computation(items);
    }

    /**
     * Computation for net, vat, discount and gross
     */
    
    function computation(data){
        const itemAmounts = {discount: 0, net: 0, vat: 0, gross: 0}

        data.map((item, index) =>{
            price = parseFloat(item.amount);
            net = price / parseFloat(1 + (12/100));
            itemAmounts['net'] += price / parseFloat(1 + (12/100));
            itemAmounts['vat'] = (itemAmounts.net * (12/100));
            itemAmounts['discount'] += discountprice(item.partno, item.unit, "<?= date('m/d/Y') ?>");
            itemAmounts['gross'] += price;
        })

        $('#vat').text(parseFloat(itemAmounts.vat).toFixed(2));
        $('#net').text(parseFloat(itemAmounts.net).toFixed(2));
        $('#gross').text(parseFloat(itemAmounts.gross).toFixed(2));
        $('#subtotal').val(parseFloat(itemAmounts.gross).toFixed(2));
        amtTotal = parseFloat(itemAmounts['gross']);
    }

    function clockUpdate() {
        var date = new Date();
        //$('.digital-clock').css({'color': '#fff', 'text-shadow': '0 0 6px #ff0'});
        function addZero(x) {
            if (x < 10) {
            return x = '0' + x;
            } else {
            return x;
            }
        }

        function twelveHour(x) {
            if (x > 12) {
            return x = x - 12;
            } else if (x == 0) {
            return x = 12;
            } else {
            return x;
            }
        }

        var h = addZero(twelveHour(date.getHours()));
        var m = addZero(date.getMinutes());
        var s = addZero(date.getSeconds());

        $('.digital-clock').text(h + ':' + m + ':' + s)
    }

        /**
         * Return Coupons total price
         */
    function getCoupon(coupon){
        if(coupon.length == 0){
            return 0;
        }

        let amount = 0;

        coupon.map((item, index) => {
            $.ajax({
                url: "../MasterFiles/Items/th_couponlist.php",
                data: { coupon: item },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        res.data.map((item, index) => {
                            amount += parseFloat(item.price)
                        })
                    } else {
                        console.log(res.msg)
                    }
                    console.log(amount)
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
        return amount;
    }

    function getSpecialDisc(data){
        let discount = 0;
        data.map((item, index) => {
            discount += parseFloat(item.amount)
        })
        console.log(data)
        return discount;
    }

    function getDiscount(data){
        let discount = 0;
        data.map((item, index)=> {
            discount += parseFloat(item.specialDisc)
        })
        console.log(data)
        return discount;
    }

    function closeModal(modal){
        $("#"+modal).modal("hide");
    }

    function add_customer_modal() {
        $("#AddCustomerModal").modal("show");
    }

    function checkAccess(id){
			var flag;
			$.ajax ({
				url: "Function/th_useraccess.php",
				data: { id: id },
                dataType: 'json',
				async: false,
				success: function(res) {
                    flag = res.valid
                    if(!res.valid){
                        console.log(res.msg)
                        AlertMsg(res.msg, "RED")
                    }
				}
			});
			return flag ;
		}
        

    function AlertMsg(msg, color = "#008000"){
        $("#AlertModal").modal("show")
        // $(".alert-modal-danger").css("background-color", color)
        $("#AlertMsg").html(msg)
        setTimeout(function() {
            location.reload()
        }, 5000)
    }

    function create_new_customer() {
        let customer = $("#customer_name").val();
        let tin = $("#tin_number").val();
        let houseno = $("#customer_house").val();
        let city = $("#customer_city").val();
        let state = $("#customer_state").val();
        let country = $("#customer_country").val();
        let zip = $("#customer_zip").val();

        $.ajax({
            url: "Function/add_customer.php",
            type: "post",
            data: {
                customer: customer,
                tin: tin,
                houseno: houseno,
                city: city,
                state: state,
                country: country,
                zip: zip
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    alert(res.msg)
                } else {
                    alert(res.msg)
                }
                location.reload();
            }, 
            error: function(msg) {
                console.log(msg);
            }
        }) 
    }

    $(document).ready(function(){
        var employeeCashierName = "<?php echo $employee_cashier_name; ?>";
        // AJAX call to delete data when the page is reloaded
        $.ajax({
            type: "POST",
            url: "DualView/Function/rdelete.php",
            data: { employeeCashierName: employeeCashierName },
            success: function(response){
                console.log("Data deleted successfully!");
                retriveStatus = 0;
            },
            error: function(xhr, status, error){
                console.error("Error deleting data:", error);
            }
        });
    });
    $(document).on('change', 'input[name="qty[]"]', function() {
        var partNo = $(this).data('val');
        var quantity = $(this).val();
        updateQuantity(partNo, quantity);
    });

    function updateQuantity(partNo, quantity) {
        $.ajax({
            url: 'DualView/Function/uctable.php',
            method: 'POST',
            data: {
                partNo: partNo,
                quantity: quantity
            },
            success: function(response) {
                console.log('Quantity updated successfully:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error updating quantity:', error);
            }
        });
    }

    function updateCouponToDatabase() {
        let couponValue = $("#couponinput").val();
        
        $.ajax({
            url: 'DualView/Function/dv_coupon.php', 
            method: 'POST',
            data: { coupon: couponValue },
            success: function(response) {
                console.log('Coupon updated successfully.');
            },
            error: function(xhr, status, error) {
                console.error('Error updating coupon:', error);
            }
        });
    }

    function updateDiscountToDatabase() {
        let discountValue = $("#discountInput").val();
        
        $.ajax({
            url: 'DualView/Function/dv_discount.php',
            method: 'POST',
            data: { discount: discountValue },
            success: function(response) {
                console.log('Discount updated successfully.');
            },
            error: function(xhr, status, error) {
                console.error('Error updating discount:', error);
            }
        });
    }