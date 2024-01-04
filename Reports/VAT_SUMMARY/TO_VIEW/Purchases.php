<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "Journal.php";

    include('../../../Connection/connection_string.php');
    include('../../../include/denied.php');
    include('../../../include/access2.php');

    $company = $_SESSION['companyid'];
    $sql =  "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../../include/autoNumeric.js"></script>

    <script src="../../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../../Bootstrap/js/moment.js"></script>
    <script src="../../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

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
    <div class='container-fluid'>
        <h5>PURCHASES TRANSACTION</h5>
        <h5>VAT SUMMARY</h5>
        <br><br>
        <h5>TIN: <?= substr($company['comptin'],0,11) ?></h5>
        <h5>OWNER'S Name: <?= $company['compname'] ?></h5>
        <h5>OWNER'S TRADE NAME: <?= $company['compdesc'] ?></h5>
        <h5>OWNER'S ADDRESS: <?= $company['compadd'] ?></h5>
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
            

    <div class='AR modal fade' id='ViewModal' role='dialog'>
        <div class='modal-sm modal-dialog' style="width: 800px;" role="document">
            <div class='modal-content' >
                <div class='modal-header'>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">View Accounts Receivable</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'>
                    <div style="display: flex; width: 100%;">
                        <div style="width: 100%; height: 1in; ">
                            <div class="btn btn-success btn" id="AR_STATUS">PAID</div>
                            <div style="display: flex">
                                <h3 id="AR_TITLE">Accounts Receivable</h3> 
                                <div style="color: gray; margin-top: 15px;" id="AR">(AR SAMPLE)</div>
                            </div>
                        </div>
                        <div style="width: 100%;">
                            <table style="width: 80%;">
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
                    <div style="display: relative; width: 100%">
                        <table>
                            <tr>
                                <th>Name: </th>
                                <td id="AR_customer"></td>
                            </tr>
                            <tr>
                                <th>Email: </th>
                                <td id="AR_email"></td>
                            </tr>
                            <tr>
                                <th>TIN</th>
                                <td id="AR_tin"></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td id="AR_address"></td>
                            </tr>
                        </table>
                    </div>
                    <div style="display: relative;">
                        <table class="table" id="GL_AR_TABLE">
                            <thead>
                                <tr>
                                    <th align='center'>Profit Center</th>
                                    <th align='center'>Account</th>
                                    <th align='center'>Description</th>
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

    <div class='Sales modal fade' id='ViewModal' role='dialog' >
        <div class='modal-sm modal-dialog' style="width: 800px;" role="document"  style="width:80%" >
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">View Sales Invoice</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'> 
                    <div style="display: flex;">
                        <div style="display: flex; justify-content: center; justify-items: center; width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <th style="text-align: end;">Invoice No.</th>
                                    <td id="sales_invoice"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end;">Customer</th>
                                    <td id="sales_customer"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end;">Delivery Receipt</th>
                                    <td id="sales_dr"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end; font-style: italic">Tin.</th>
                                    <td id="sales_tin"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end; font-style: italic">Terms</th>
                                    <td id="sales_term"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end; font-style: italic">Address</th>
                                    <td id="sales_address"></td>
                                </tr>
                            </table>
                        </div>
                        <div style="display: flex; justify-content: center; justify-items: center; width: 100%;">
                            <table>
                                <tr>
                                    <th style="text-align: end;">Invoice Date</th>
                                    <td id="sales_date"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end;">Reference</th>
                                    <td id="sales_reference"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end;">Due Date</th>
                                    <td id="sales_due"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: end;">Notes</th>
                                    <td id="sales_note"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: center; justify-items: center; width: 100%; padding-top: 30px;">
                        <table class="table" id="Invoice_list" style="width: 100%;">
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
                    <div style="display: flex; justify-content: right; justify-items: right; width: 100%; padding-top: 30px;">
                        <div style="width: 35%">
                            <table>
                                <tr>
                                    <th style="text-align: right;">VATable Sales</th>
                                    <td id="vatable_sales"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">VAT-Exempt Sales</th>
                                    <td id="vatable_exempt"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">VAT Zero Rated Sales</th>
                                    <td id="vatable_zero"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">Total Sales</th>
                                    <td id="total_sales"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">Add VAT</th>
                                    <td id="add_vat"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">Total Amount Due</th>
                                    <td id="amount_due"></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;">Discount</th>
                                    <td id="vat_discount"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='Paybills modal fade' id='ViewModal' role='dialog' >
        <div class='modal-sm modal-dialog' style="width: 800px;" role="document"  style="width:80%" >
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">View Bills Payment</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'> 
                    
                    <div style="display: flex; width: 100%;">
                        <div style="width: 100%; height: 1in; ">
                            <div style="display: flex">
                                <h3 id="AR_TITLE">Account Payments</h3> 
                                <div style="color: gray; margin-top: 15px;" id="AP">(AR SAMPLE)</div>
                            </div>
                        </div>
                        <div style="width: 100%;">
                            <table style="width: 80%;">
                                <tr>
                                    <th>DATE: </th>
                                    <td><div id="AP_DATE"></div></td>
                                </tr>
                                <tr>
                                    <th>DUE DATE: </th>
                                    <td><div id="AP_DUE"></div></td>
                                </tr>
                                <tr>
                                    <th>INVOICE NO: </th>
                                    <td><div id="AP_INVOICE"></div></td>
                                </tr>
                                <tr>
                                    <th>REFERENCE No: </th>
                                    <td><div id="AP_REFERENCE"></div></td>
                                </tr>   
                            </table>
                        </div>
                    </div>

                    <div style="display: relative;">
                        <table class="table" id="GL_AP_TABLE">
                            <thead>
                                <tr>
                                    <th align='center'>Profit Center</th>
                                    <th align='center'>Account</th>
                                    <th align='center'>Description</th>
                                    <th align='center'>Debit</th>
                                    <th align='center'>Credit</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    $(document).ready(function() {
        //Fetch_Sales();
        Fetch_Purchase();

        $(".datepicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'YYYY-MM-DD'
        }).on('dp.change', function (e) {
            //Fetch_Sales();
            Fetch_Purchase();
        });
    })

    /*function AR_MODAL(){
        let header = $(this).text();
        $("#AR").html("<h4>(" + header + ")</h4>");
        $("#GL_AR_TABLE tbody").empty();

        $.ajax({
            url: "../RECEIPT",
            data: {
                transaction: header
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) { 
                    $(".AR").modal("show");
                    res.GLData.map((item, index) => {
                        $("<tr>").append(
                            $("<td>").html("&nbsp;"),
                            $("<td>").text(item.acctno + " - " + item.ctitle),
                            $("<td>").text(""),
                            $("<td>").text(item.ndebit),
                            $("<td>").text(item.ncredit),
                        ).appendTo("#GL_AR_TABLE tbody")
                    })
                    res.data.map((item, index) => {
                        $("#date").text(item.date);
                        $("#duedate").text(item.due);
                        $("#invoice").text(item.invoice);
                        $("#reference").text(item.reference)

                        $("#AR_customer").text(item.customer);
                        $("#AR_tin").text(item.tin);
                        $("#AR_address").text(item.address)
                    })

                    if(res.approved === 1) {
                        $("#AR_STATUS").prop("class", "btn btn-success btn-sm");
                        $("#AR_STATUS").text("Paid");
                    } else {
                        $("#AR_STATUS").prop("class", "btn btn-primary btn-sm");
                        $("#AR_STATUS").text("Pending");
                    }

                } else {
                    console.log(res.msg)
                }
                
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }*/

    function AP_MODAL() {
        let header = $(this).text();
        $("#AP").html("<h4>(" + header + ")</h4>");
        $("#GL_AP_TABLE tbody").empty();

        $.ajax({
            url: "../AP_LIST",
            data: {
                transaction: header
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) { 
                    $(".Paybills").modal("show");
                    res.GLData.map((item, index) => {
                        $("<tr>").append(
                            $("<td>").html("&nbsp;"),
                            $("<td>").text(item.acctno + " - " + item.ctitle),
                            $("<td>").text(""),
                            $("<td>").text(ToMoney(item.ndebit)),
                            $("<td>").text(ToMoney(item.ncredit)),
                        ).appendTo("#GL_AP_TABLE tbody")
                    })
                    res.data.map((item, index) => {
                        $("#AP_DATE").text(item.date);
                        $("#AP_DUE").text(item.due);
                        $("#AP_INVOICE").text(item.invoice);
                        $("#AP_REFERENCE").text(item.reference)

                        $("#AR_customer").text(item.customer);
                        $("#AR_tin").text(item.tin);
                        $("#AR_address").text(item.address)
                    })

                    if(res.approved === 1) {
                        $("#AR_STATUS").prop("class", "btn btn-success btn-sm");
                        $("#AR_STATUS").text("Paid");
                    } else {
                        $("#AR_STATUS").prop("class", "btn btn-primary btn-sm");
                        $("#AR_STATUS").text("Pending");
                    }

                } else {
                    console.log(res.msg)
                }
                
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    /*function Sales_Modal() {
        let header = $(this).text().trim();
        let TOTAL_VAT = 0;
        let TOTAL_VATABLE = 0;
        let TOTAL_EXEMPT = 0;
        let TOTAL_ZERO = 0;
        let TOTAL_SALES = 0;
        let TOTAL_DISCOUNT = 0;
        let TOTAL_AMOUNT_DUE = 0;
        $("#Invoice_list tbody").empty();

        $.ajax({
            url: "../INVOICE",
            data: {
                transaction: header
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    $(".Sales").modal("show");
                    $("#sales_invoice").text(res.transaction)
                    $("#sales_customer").text(res.customer)
                    $("#sales_dr").text(res.reference)
                    $("#sales_tin").text(res.tin)
                    $("#sales_term").text(res.term)
                    $("#sales_address").text(res.address)
                    $("#sales_date").text(res.date)
                    $("#sales_reference").text(res.reference)
                    $("#sales_due").text(res.due)
                    $("#sales_note").text(res.notes)

                    res.data.map((item, index) => {
                        TOTAL_SALES += parseFloat(item.amount);
                        $("<tr>").append(
                            $("<td>").text(item.items),
                            $("<td>").text(item.description),
                            $("<td>").text(item.quantity),
                            $("<td>").text(item.UOM),
                            $("<td>").text(ToMoney(item.price)),
                            $("<td>").text(ToMoney(item.discount)),
                            $("<td>").text(item.tax),
                            $("<td>").text(ToMoney(item.amount)),
                        ).appendTo("#Invoice_list tbody")
                    })
                } else {
                    console.log(res.msg)
                }
            },
            error: function(res) {
                console.log(res)
            }
        })
        TOTAL_AMOUNT_DUE = TOTAL_SALES + TOTAL_VAT;
        $("#vatable_sales").text(ToMoney(TOTAL_VATABLE))
        $("#vatable_exempt").text(ToMoney(TOTAL_EXEMPT))
        $("#vatable_zero").text(ToMoney(TOTAL_ZERO))
        $("#total_sales").text(ToMoney(TOTAL_SALES))
        $("#add_vat").text(ToMoney(TOTAL_VAT))
        $("#amount_due").text(ToMoney(TOTAL_AMOUNT_DUE))
        $("#vat_discount").text(ToMoney(TOTAL_DISCOUNT))
    }*/

    /*function Fetch_Sales() {
        $("#sales_table tbody").empty();

        let from = '<?//= $_POST['from'] ?>';
        let to = '<?//= $_POST['to'] ?>';
       /* let vatable = '<?//= $_POST['vatable'] ?>';
        let zero = '<?//= $_POST['zero'] ?>';
        let gov =  '<?//= $_POST['vatgov'] ?>';  
        let exempt = '<?//= $_POST['vatexempt'] ?>';
        
        
        ,
                vatable: vatable,
                gov: gov,
                zero: zero,
                exempt: exempt
        
                    
        let TOTAL_GROSS = 0;
        let TOTAL_NET = 0;
        let TOTAL_TAX = 0;
        $.ajax({
            url: "../SALES",
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

                    alert(vat);
                    TOTAL_GROSS += (zero.gross + exempt.gross) - (vat.gross + nonvat.gross);
                    TOTAL_NET += (zero.net + exempt.net) - (nonvat.net + vat.net);
                    TOTAL_TAX += (zero.tax + exempt.tax) - (nonvat.tax + vat.tax);
                    $("<tr>").append(
                            $("<td colspan='7' align='right'>").text("Grand Total"),
                            $("<td align='center'>").text(ToMoney(TOTAL_GROSS)),
                            $("<td align='center'>").text(ToMoney(TOTAL_NET)),
                            $("<td align='center'>").text(ToMoney(TOTAL_TAX)),
                    ).appendTo("#sales_table tbody")
                } else {
                    console.log(msg)
                }

                
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }*/

    function Fetch_Purchase() {
        $("#purchase_table tbody").empty();

        let from = '<?= $_POST['from'] ?>';
        let to = '<?= $_POST['to'] ?>';

        //let other = "<?//= $_POST['other_goods'] ?>";
       // let service = "<?////= $_POST['services'] ?>";
       // let capital = "<?//= $_POST['capital'] ?>";

        let TOTAL_GROSS = 0;
        let TOTAL_NET = 0;
        let TOTAL_TAX = 0;

        $.ajax({
            url: "../PURCHASE",
            data: {
                from: from,
                to: to,
                //other: other,
                //service: service,
                //capital: capital
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    let other = PURCHASE_TABLE_DATA("INPUT VAT GOODS (OTHER THAN CAPITAL GOODS)  ", res.other);
                    
                    let service = PURCHASE_TABLE_DATA("INPUT VAT SERVICES  ", res.service);
                    
                    let capital = PURCHASE_TABLE_DATA("INPUT VAT CAPITAL GOODS  ", res.capital);

                    
                    TOTAL_GROSS += other.gross + service.gross + capital.gross;
                    TOTAL_NET += other.net + service.net + capital.net;
                    TOTAL_TAX += other.tax + service.tax + capital.tax;
                    $("<tr>").append(
                            $("<td colspan='7' align='right'>").text("Grand Total"),
                            $("<td align='center'>").text(ToMoney(TOTAL_GROSS)),
                            $("<td align='center'>").text(ToMoney(TOTAL_NET)),
                            $("<td align='center'>").text(ToMoney(TOTAL_TAX)),
                    ).appendTo("#purchase_table tbody")
                } else {
                    console.log(res.msg)
                }
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    /*function SALES_TABLE_DATA(label, data) {
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
                $("<td>").html("<a href='javascript:;' onclick='Sales_Modal.call(this)'>" + item.sales + "</a>"),
                $("<td>").text(item.reference),
                $("<td>").text(item.partner),
                $("<td>").text(item.tin),
                $("<td>").text(item.address),
                $("<td align='center'>").text(ToMoney(item.gross)),
                $("<td align='center'>").text(ToMoney(item.net)),
                $("<td align='center'>").text(ToMoney(item.tax)),
            ).appendTo("#sales_table tbody");
        });

        $("<tr>").append(
            $("<td colspan='7' align='right'>").text("TOTAL: "),
            $("<td align='center'>").text(ToMoney(TOTAL_GROSS)),
            $("<td align='center'>").text(ToMoney(TOTAL_NET)),
            $("<td align='center'>").text(ToMoney(TOTAL_TAX)),
        ).appendTo("#sales_table tbody")

        return {
            gross: TOTAL_GROSS,
            net: TOTAL_NET,
            tax: TOTAL_TAX
        }
    }*/

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
            const tax = item.credit != 0 ? item.credit : item.debit;
            const gross = item.gross;
            const net = item.gross - tax;
            const isCredit = item.credit != 0 ? true : false; 

            TOTAL_GROSS += parseFloat(gross);
            TOTAL_NET += parseFloat(net);
            TOTAL_TAX += parseFloat(tax);
            $("<tr>").append(
                $("<td>").html("<a href='javascript:;' onclick='AP_MODAL.call(this)'>" +item.transaction + "</a>"),
                $("<td>").text(item.date),
                // $("<td>").html("<a href='javascript:;' onclick=''>" + item.reference + "</a>"),
                $("<td>").html("<a href='javascript:;' onclick=''>" + "</a>"),
                $("<td>").text(""),
                $("<td>").text(item.partner),
                $("<td>").text(item.tin),
                $("<td>").text(item.address),
                $("<td align='center'>").text(ToMoney(gross)),
                $("<td align='center'>").text(ToMoney(net)),
                $("<td align='center'>").text(ToMoney(tax)),
            ).appendTo("#purchase_table tbody");
        });

        $("<tr>").append(
            $("<td colspan='7' align='right'>").text("TOTAL: "),
            $("<td align='center'>").text(ToMoney(TOTAL_GROSS)),
            $("<td align='center'>").text(ToMoney(TOTAL_NET)),
            $("<td align='center'>").text(ToMoney(TOTAL_TAX)),
        ).appendTo("#purchase_table tbody");

        return {
            gross: TOTAL_GROSS,
            net: TOTAL_NET,
            tax: TOTAL_TAX
        }
    }

    function ToMoney(amount) {
        return parseFloat(amount).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
    }
</script>