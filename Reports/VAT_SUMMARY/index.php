<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/autoNumeric.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        th, td {
            padding-top: 2px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 2px;
        }
    </style>
    <title>MyxFinancials</title>
</head>
<body>
    
    <form action="" method="post" enctype="multipart/form-data">
        <div style="display: flex; padding: 20px">
            <div style="width: 100%;">
                <div class="col-xs-4">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="datefrom" name="datefrom" class="datepicker form-control input-sm" value="<?= date("Y-m-d") ?>">
                    </div>
                </div>
                <div class="col-xs-4" >
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="dateto" name="dateto" class="datepicker form-control input-sm " value="<?= date("Y-m-d") ?>">
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: right; justify-items: right; width: 100%;">
                <button class="btn btn-success btn-sm">Export</button>
            </div>
        </div>
    </form>
        

    <div style="padding-top:10px;">
        <div style="padding: 10px">
            <ul class="nav nav-tabs">
                <li class="active"> 
                    <a href="#Sales" data-toggle="tab">Sales</a>
                </li>
                <li>
                    <a href="#Purchase" data-toggle="tab">Purchase</a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content nopadwtop2x">
            <div id="Sales" class="tab-pane fade in active" style="padding: 10px;">
                <table id="sales_table" class="table">
                    <thead>
                        <tr>
                            <th>Voucher no.</th>
                            <th>Transaction Date</th>
                            <th>Invoice No.</th>
                            <th>Reference</th>
                            <th>Partner</th>
                            <th>TIN</th>
                            <th>Address</th>
                            <th>Gross Amount</th>
                            <th>Net Amount</th>
                            <th>Tax Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div id="Purchase" class="tab-pane fade" style="padding: 10px;">
                <table id="purchase_table" class="table">
                    <thead>
                        <tr>
                            <th align='center'>Voucher no.</th>
                            <th align='center'>Transaction Date</th>
                            <th align='center'>Invoice No.</th>
                            <th align='center'>Reference</th>
                            <th align='center'>Partner</th>
                            <th align='center'>TIN</th>
                            <th align='center'>Address</th>
                            <th align='center'>Gross Amount</th>
                            <th align='center'>Net Amount</th>
                            <th align='center'>Tax Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>
    </div>

    <div class='Sales modal fade' id='ViewModal' role='dialog' data-backdrop="static">
        <div class='modal-sm modal-dialog' style="width: 800px;" role="document">
            <div class='modal-content' >
                <div class='modal-header'>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">View  Accounts Receivable</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'>
                    <div style="display: flex; width: 100%;">
                        <div style="width: 100%; height: 1in; ">
                            <div class="btn btn-success btn" id="AR_STATUS">PAID</div>
                            <div style="display: flex">
                                <h3 >Accounts Receivable</h3> 
                                <div style="color: gray; margin-top: 15px;" id="AR">(AR SAMPLE)</div>
                            </div>
                        </div>
                        <div style="width: 100%;">
                            <table style="width: 50%;">
                                <tr>
                                    <th>DATE: </th>
                                    <td><div id="date"></div></td>
                                </tr>
                                <tr>
                                    <th>DUE DATE: </th>
                                    <td><div id="duedate"></div></td>
                                </tr>
                                <tr>
                                    <th>INVOICE NO: </th>
                                    <td><div id="invoice"></div></td>
                                </tr>
                                <tr>
                                    <th>REFERENCE No: </th>
                                    <td><div id="reference"></div></td>
                                </tr>   
                            </table>
                        </div>
                    </div>
                    <div style="display: relative;">
                        <table class="table" id="GL_AR_TABLE">
                            <thead>
                                <tr>
                                    <th align='center'>Profit Center</th>
                                    <th align='center'>Account</th>
                                    <th align='center'>Desc ription</th>
                                    <th align='center'>Debit</th>
                                    <th align='center'>Credit</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div style="display: relative; margin-top: 1in;">
                        <div style="font-weight: bold;">Receive Payments</div>
                        <table class="table" id="AR_TABLE">
                            <thead>
                                <tr>
                                    <th align="center">DATE</th>
                                    <th align="center">RV No.</th>
                                    <th align="center">MODE</th>
                                    <th align="center">REFERENCE</th>
                                    <th align="center">PAYMENT ACCOUNT</th>
                                    <th align="center">AMOUNT</th>
                                    <th align="center">DISCOUNT</th>
                                    <th align="center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='Sales modal fade' id='ViewModal' role='dialog' data-backdrop="static">
        <div class='modal-sm modal-dialog' style="width: 800px;" role="document">
            <div class='modal-content' >
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">View Sales Invoice</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'> 
                    <div style="display: flex;">
                        <div>

                        </div>
                        <div style="display: flex; justify-content: center; justify-items: center;">
                            <table class="table" id="Invoice_list">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>UOM</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    $(document).ready(function() {
        Fetch_Sales();
        Fetch_Purchase();

        $(".datepicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'YYYY-MM-DD'
        }).on('dp.change', function (e) {
            Fetch_Sales();
            Fetch_Purchase();
        });
    })

    function Invoice_Modal () {
        let invoice = $(this).text();

        $.ajax({
            url: "./INVOICE/",
            data: {
                transaction: invoice
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {

                } else {
                    console.log(res.msg)
                }
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    function AR_MODAL(){
        let header = $(this).text();
        $("#AR").html("<h4>(" + header + ")</h4>");

        $.ajax({
            url: "./RECEIPT",
            data: {
                transaction: header
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) { 
                    $(".Sales").modal("show");
                    res.GLData.map((item, index) => {
                        $("<tr>").append(
                            $("<td>").html("&nbsp;"),
                            $("<td>").text(item.acctno + " " + item.ctitle),
                            $("<td>").text(""),
                            $("<td>").text(item.ndebit),
                            $("<td>").text(item.ncredit),
                        ).appendTo("#GL_AR_TABLE tbody")
                    })

                    res.data.map((item, index) => {
                        $("<tr>").append(
                            $("<td>").text(),
                        ).appendTo("#AR_TABLE tbody");
                    })
                    console.log(res.data)
                } else {
                    console.log(res.msg)
                }
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    function Fetch_Sales() {
        $("#sales_table tbody").empty();

        let from = $("#datefrom").val();
        let to = $("#dateto").val();

        let TOTAL_GROSS = 0;
        let TOTAL_NET = 0;
        let TOTAL_TAX = 0;
        $.ajax({
            url: "./SALES",
            data: {
                from: from,
                to: to
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    let zero = SALES_TABLE_DATA("OUTPUT VAT ZERO RATED SALES", res.zr);
                    let nonvat = SALES_TABLE_DATA("OUTPUT VAT TO GOVERNMENT", res.nv);
                    let exempt = SALES_TABLE_DATA("OUTPUT VAT EXEMPT SALES", res.ve);
                    let vat = SALES_TABLE_DATA("OUTPUT VATABLE SALES", res.vt);
                    TOTAL_GROSS += (zero.gross + exempt.gross) - (vat.gross + nonvat.gross);
                    TOTAL_NET += (zero.net + exempt.net) - (nonvat.net + vat.net);
                    TOTAL_TAX += (zero.tax + exempt.tax) - (nonvat.tax + vat.tax);
                    $("<tr>").append(
                            $("<td colspan='7' align='right'>").text("Grand Total"),
                            $("<td align='center'>").text(parseFloat(TOTAL_GROSS).toFixed(2)),
                            $("<td align='center'>").text(parseFloat(TOTAL_NET).toFixed(2)),
                            $("<td align='center'>").text(parseFloat(TOTAL_TAX).toFixed(2)),
                ).appendTo("#sales_table tbody")
                } else {
                    console.log(msg)
                }

                
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    function Fetch_Purchase() {
        $("#purchase_table tbody").empty();

        let from = $("#datefrom").val();
        let to = $("#dateto").val();

        $.ajax({
            url: "./PURCHASE",
            data: {
                from: from,
                to: to
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    let zero = PURCHASE_TABLE_DATA("INPUT VAT GOODS (OTHER THAN CAPITAL GOODS)  ", res.data);
                } else {
                    console.log(msg)
                }
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    function SALES_TABLE_DATA(label, data) {
        $("<tr>").append(
            $("<td colspan='10'>").text(label)
        ).appendTo("#sales_table tbody")

        $("<tr>").append(
            $("<td colspan='7'>").text("Beginning"),
            $("<td align='center'>").text("(0.00)"),
            $("<td align='center'>").text("(0.00)"),
            $("<td align='center'>").text("(0.00)"),
        ).appendTo("#sales_table tbody")

        let TOTAL_GROSS = 0;
        let TOTAL_NET = 0;
        let TOTAL_TAX = 0;

        data.map((item, index) => {
            TOTAL_GROSS += parseFloat(item.gross);
            TOTAL_NET += parseFloat(item.net);
            TOTAL_TAX += parseFloat(item.tax);

            $("<tr>").append(
                $("<td>").html("<a href='javascript:;' onclick='AR_MODAL.call(this)'>" + item.transaction + "</a"),
                $("<td>").text(item.date),
                $("<td>").html("<a href='javascript:;' onclick='AR_MODAL(\"sample\")'>" + item.sales + "</a>"),
                $("<td>").text(item.reference),
                $("<td>").text(item.partner),
                $("<td>").text(item.tin),
                $("<td>").text(item.address),
                $("<td align='center'>").text(parseFloat(item.gross).toFixed(2)),
                $("<td align='center'>").text(parseFloat(item.net).toFixed(2)),
                $("<td align='center'>").text(parseFloat(item.tax).toFixed(2)),
            ).appendTo("#sales_table tbody");
        });

        $("<tr>").append(
            $("<td colspan='7' align='right'>").text("TOTAL: "),
            $("<td align='center'>").text(parseFloat(TOTAL_GROSS).toFixed(2)),
            $("<td align='center'>").text(parseFloat(TOTAL_NET).toFixed(2)),
            $("<td align='center'>").text(parseFloat(TOTAL_TAX).toFixed(2)),
        ).appendTo("#sales_table tbody")

        return {
            gross: TOTAL_GROSS,
            net: TOTAL_NET,
            tax: TOTAL_TAX
        }
    }

    function PURCHASE_TABLE_DATA(label, data) {
        $("<tr>").append(
            $("<td colspan='10'>").text(label)
        ).appendTo("#purchase_table tbody")

        $("<tr>").append(
            $("<td colspan='7'>").text("Beginning"),
            $("<td align='center'>").text("(0.00)"),
            $("<td align='center'>").text("(0.00)"),
            $("<td align='center'>").text("(0.00)"),
        ).appendTo("#purchase_table tbody")

        let TOTAL_GROSS = 0;
        let TOTAL_NET = 0;
        let TOTAL_TAX = 0;

        data.map((item, index) => {
            TOTAL_GROSS += parseFloat(item.gross);
            TOTAL_NET += parseFloat(item.net);
            TOTAL_TAX += parseFloat(item.tax);

            $("<tr>").append(
                $("<td>").text(item.transaction),
                $("<td>").text(item.date),
                $("<td>").text(item.invoice),
                $("<td>").text(item.reference),
                $("<td>").text(item.partner),
                $("<td>").text(item.tin),
                $("<td>").text(item.address),
                $("<td align='center'>").text(parseFloat(item.gross).toFixed(2)),
                $("<td align='center'>").text(parseFloat(item.net).toFixed(2)),
                $("<td align='center'>").text(parseFloat(item.tax).toFixed(2)),
            ).appendTo("#purchase_table tbody");
        });

        $("<tr>").append(
            $("<td colspan='7' align='right'>").text("TOTAL: "),
            $("<td align='center'>").text(parseFloat(TOTAL_GROSS).toFixed(2)),
            $("<td align='center'>").text(parseFloat(TOTAL_NET).toFixed(2)),
            $("<td align='center'>").text(parseFloat(TOTAL_TAX).toFixed(2)),
        ).appendTo("#purchase_table tbody");

        return {
            gross: TOTAL_GROSS,
            net: TOTAL_NET,
            tax: TOTAL_TAX
        }
    }
</script>