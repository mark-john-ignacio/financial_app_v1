<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    $company = $_SESSION['companyid'];

    include('../Connection/connection_string.php');
    include('../include/denied.php');
    include('../include/access2.php');

    $category = [];
    $items = [];
    $table = [];
    $order = [];
    $discount = [];
    $date = date('Y-m-d');

    $query = mysqli_query($con,"select * from company where compcode='$company'");
    if(mysqli_num_rows($query) !== 0 ){
        while($row = $query -> fetch_assoc()){
            $companyName = $row['compname'];
            $companyAddress  = $row['compadd'];
            $companyTin = $row['comptin'];
        }
    }

    $sql =  "SELECT * FROM groupings WHERE ctype='ITEMCLS' AND ccode in (select cclass From items where compcode='$company' and cstatus = 'ACTIVE' and ctradetype='Trade') order by cdesc";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($category, $row);
    }

    $sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty, a.cuserpic
            from items a 
            left join
                (
                    select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
                    From tblinventory a
                    right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
                    where a.compcode='$company' and  a.dcutdate <= '$date' and d.cstatus = 'ACTIVE'
                    group by a.citemno
                ) c on a.cpartno=c.citemno
            WHERE a.compcode='$company' and a.cstatus = 'ACTIVE' and a.ctradetype='Trade' order by a.cclass, a.citemdesc";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        while($row = $query -> fetch_assoc()){
            array_push($items, $row);
        }
    }

    $sql = "SELECT * FROM pos_grouping where `compcode` = '$company' and `type` = 'TABLE' ";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($table, $row);
    }

    $sql = "SELECT * FROM pos_grouping where `compcode` = '$company' and `type` = 'ORDER' ";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($order, $row);
    }

    $sql = "SELECT * FROM discounts WHERE compcode = '$company' AND lapproved = '1'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($discount, $row);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myx Financials</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap2.css?v=<?php echo time();?>">
	<link href="../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
	<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slicksize.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/keypadz.css?v=<?php echo time();?>">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../include/autoNumeric.js"></script>

    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>


    <style>
        #filter {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
        }
        #filter > div{
            padding: 5px;
        }

        #item-wrapper {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            overflow: auto;
            text-align: center;
        }
        
        #category-wrapper {
            display: grid;
            padding-top: 10px;
            text-align: center;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            grid-template-rows: 1fr;
            max-width: 5fr;
            overflow: hidden; 
        }
        
        #button-wrapper {
            display: grid;
            padding-top: 10px;
            text-align: center;
            grid-gap: 4px;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            grid-template-rows: 1fr;
            max-width: 4fr;
            overflow: hidden;
        }

        #right-side {
            display: absolute;
        }
        #wrapper {
            bottom: 0;
        }
    </style>
</head>
<body>
    <div stlye="display: fixed">
            <div class='row nopadwtop2x' id='header' style="background-color: #2d5f8b; height:65px; margin-bottom: 5px !important">
                <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
                    <img src="../images/LOGOTOP.png" width="150" height="50"/>
                </div>
            </div>

            <div class='container nopadding' id='POSBody' style='display: flex; width: 100%;'>
                <div class="col" style="width: 50%; padding: 5px;">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <div class="digi col-lg-6 nopadding text-left">
                                    <span class="date">
                                        Cashier: <?php echo $_SESSION['employeename']; ?>
                                    </span>    
                                </div>
                            </td>
                            <td align='right'>
                                <div>
                                    <span class="date"><?=date("F d, Y");?></span>
                                    <span class="digital-clock time"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class='input-group margin-bottom-sm'>
                                    <input type="text" name='barcode' id='barcode' class='form-control input-sm' placeholder="|||||||||||||||||||||||||||||||||||||||| Barcode " autocomplete="off">
                                    <span class='input-group-addon'><i class='fa fa-barcode fa-fw'></i></span>
                                </div>
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style='padding-top: 20px'>
                                <div style='height: 3.6in; max-height: 3.6in; overflow: auto;'>
                                    <table class='table' id='listItem' style="width: 100%; ">
                                        <thead style='background-color: #019aca'>
                                            <tr>
                                                <th style="width: 60%;">Item</th>
                                                <th style="text-align: center;">UOM</th>
                                                <th style="text-align: center;">Quantity</th>
                                                <th style="text-align: center;">Price</th>
                                                <th style="text-align: center;">Discount</th>         
                                                <th style="text-align: center;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'>
                                <table   id='amountlist' style='width: 100%'>
                                    <tbody>
                                        <!-- <tr>
                                            <td>Discount</td>
                                            <td align="right">P <span id="discount">0.00</span></td>
                                        </tr> -->
                                        <tr>
                                            <td nowrap align='right' style='font-weight: bold; padding-right: 10px;'>Net of VAT</td>
                                            <td class='form-control input-lg' align="right" style='border: 0px solid; color: #F00; font-weight: bold;'>P <span id="net">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td nowrap align='right' style='font-weight: bold; padding-right: 10px;'>VAT</td>
                                            <td class='form-control input-lg' align="right" style='border: 0px solid; color: #F00; font-weight: bold;'>P <span id="vat">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td nowrap align='right' style='font-weight: bold; padding-right: 10px;'>Gross Amount</td>
                                            <td class='form-control input-lg' align="right" style='border: 0px solid; color: #F00; font-weight: bold;'>P <span id="gross">0.00</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>


                <div class='col' id='right-side' style='width: 50%; padding: 10px;'>
                    <table class='table' style="width: 100%;">
                        <tr>
                            <td>
                                <div id='filter'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><i class='fa fa-user'></i></span><input class='form-control input-sm' type="text" name='customer' id='customer' placeholder="Walkin Customer (Default)" autocomplete="off">
                                    </div>

                                        <div class='input-group'>
                                            <select name="orderType" id="orderType" class='form-control input-sm' style="<?= sizeof($order) != 0 ? null : "display:none" ?>">
                                                <?php foreach($order as $list): ?>
                                                    <option value="<?= $list['code'] ?>"><?= $list['code'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class='input-group'>
                                            <select name="table" id="table"  class='form-control input-sm' style="<?= sizeof($table) != 0 ? null : "display:none" ?>">
                                                <?php foreach($table as $list): ?>
                                                    <option value="<?= $list['code'] ?>"><?= $list['code'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style='height: 350px; overflow: auto;'>
                                    <div id='item-wrapper'>
                                        <?php foreach($items as $list):?>
                                                <div class='itmslist' style="height:100px;                     
                                                    background-color:#019aca; 
                                                    background-image:url('<?=$list["cuserpic"];?>');
                                                    background-repeat:no-repeat;
                                                    background-position: center center;
                                                    background-size: contain;
                                                    border:solid 1px #036;
                                                    text-align:center;
                                                    position: relative" data-itemlist="<?= $list['cclass'] ?>">
                                                    <div id='items' name="<?= $list['cscancode'] ?>" class='items' data-itemlist="<?= $list['cclass'] ?>" style='position: absolute; bottom: 0; width: 100%; background-color: rgba(0,0,0,.5); color: #fff; min-height: 20px;'><font size='-2'><?php echo $list["citemdesc"]; ?></font></div>
                                                </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class='col-lg-12 '>          
                        <section style='width: 90%; padding: 10px' class="regular slider btn">
                            <?php foreach($category as $list):?>
                                <div style="height:100%; 
                                    word-wrap:break-word;
                                    background-color:#019aca; 
                                    border:solid 1px #036;
                                    padding:3px;
                                    text-align:center;" class="itmclass btn btn-info" data-clscode="<?= $list['ccode'] ?>">
                                        <font size="-2"><?= $list['cdesc'] ?></font>
                                </div>

                            <?php endforeach; ?>
                        </section>
                    </div>

                    <div id='wrapper'>
                        <div id='button-wrapper' class='col-lg-12 nopadwtop'>
                            <button class="form-control btn btn-sm btn-success" name="btnPay" id="btnPay" type="button">
                                <i class="fa fa-money fa-fw fa-lg" aria-hidden="true"></i>&nbsp; PAYMENT (F2)
                            </button>
                            <button class="form-control btn btn-sm btn-primary" name="btnHold" id="btnHold" type="button">
                               <i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i>&nbsp; HOLD (INS)
                            </button>
                            <button class="form-control btn btn-sm btn-warning" name="btnRetrieve" id="btnRetrieve" type="button">
                               <i class="fa fa-bar-chart fa-fw fa-lg" aria-hidden="true"></i>&nbsp; RETRIEVE (F4)
                            </button>
                            <button class="form-control btn btn-sm btn-danger" name="btnVoid" id="btnVoid" type="button">
                                <i class="fa fa-plus fa-fw fa-lg" aria-hidden="true"></i>&nbsp;VOID (DEL)
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
    </div>

    <div class='modal fade' id='mymodal' role="dialog">
        <div class="modal-dialog" role="document">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader">Void Item</h3>
                </div>
                <div class='modal-body' id='void' style='height: 4in; overflow: auto;'>
                    <table class='table' id='VoidList' style="width: 100%; ">
                        <thead style='background-color: #019aca'>
                            <tr>
                                <th>&nbsp;</th>
                                <th style="width: 60%;">Item</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: center;">Price</th>
                                <th style="text-align: center;">Discount</th>
						        <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class='modal-body' id='retrieve' style="height: 4in; display: none; overflow: auto;">
                    <table class='table' id='RetrieveList' style='width: 100%'>
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th style="width: 30%;">Transaction</th>
                                <th style="text-align: center;">Table</th>
                                <th style="text-align: center;">Order Type</th>
                                <th style="text-align: center;">Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class='modal-footer' style='display: Relative; width: 100%;'>
                    <div id='footer' style='right: 0px'>
                        <button class='btn btn-danger' id='VoidSubmit' style='padding: 5px; width: 1in;'>Void</button>
                        <button class='btn btn-warning' id='RetrieveSubmit' style='padding: 5px; width: 1in; display:none;'>Retrieve</button>
                    </div>
                </div>
            </div>     
        </div>
    </div>

    <div class='modal fade' id='payModal' role='dialog'>
        <div class='modal-lg modal-dialog' role="document">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader">Payment Terms</h3>
                </div>
                <div class='modal-body' style='height: 100%'>
                    <table class='table' style='width: 100%;'>
                        <tr>
                            <td>
                                <div style='height: 4in;'>
                                    <table class='table' id='paymentList' style='width: 100%'>
                                        <thead style='background-color: #019aca'>
                                            <tr>
                                                <th style='text-align: center'>Item</th>
                                                <th style='text-align: center'>UOM</th>
                                                <th style='text-align: center'>Quantity</th>
                                                <th style="text-align: center;">Price</th>
                                                <th style="text-align: center;">Discount</th>
						                        <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </td>
                            <td style='width: 35%'>
                                <div id='payment-details'>
                                    <div style='width: 100%'>
                                        <label for="totalAmt">Total Amount</label>
                                        <input type='text' id='totalAmt' class='form-control' readonly/>

                                            <label for='discountAmt'>Discount Amount</label>
                                            <select name="discountAmt" id="discountAmt" class='form-control'>
                                                <option value="0">No Discount</option>
                                                <?php foreach($discount as $list): ?>
                                                    <option value="<?= $list["nvalue"] ?>"><?= $list['cdescription'] ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                        <div id='dc' style='display: none'>
                                            <label for='discountAmt'>Discount Code</label>
                                            <input type="text" id="discountcode" name="discountcode" class="form-control">
                                        </div>

                                        <label for="tendered">Amount Tendered</label>
                                        <input type="text" id='tendered' class='form-control' />

                                        <label for="ExchangeAmt">Exchange Amount</label>
                                        <input type="text" id='ExchangeAmt' class='form-control' readonly/>
                                    </div>

                                    <div class='jqbtk-container' style='padding-top: 5px'>
                                        <div class='jqbtk-row'>
                                            <button type='button' class="btnpad btn btn-default" data-val='1'>1</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='2'>2</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='3'>3</button>
                                            <button type='button' class="btnpad btn btn-info jqbtk-shift"  data-val='100'>100</button>
                                        </div>
                                        <div class='jqbtk-row' style='padding-top: 2px;'>
                                            <button type='button' class="btnpad btn btn-default" data-val='4'>4</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='5'>5</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='6'>6</button>
                                            <button type='button' class="btnpad btn btn-info jqbtk-shift"  data-val='200'>200</button>
                                        </div>
                                        <div class='jqbtk-row' style='padding-top: 2px;'>
                                            <button type='button' class="btnpad btn btn-default" data-val='7'>7</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='8'>8</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='9'>9</button>
                                            <button type='button' class="btnpad btn btn-info jqbtk-shift"  data-val='500'>500</button>
                                        </div>
                                        <div class='jqbtk-row' style='padding-top: 2px;'>
                                            <button type='button' class="btnpad btn btn-default" data-val='.'>.</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='0'>0</button>
                                            <button type='button' class="btnpad btn btn-default" data-val='DEL' style="padding-right: 10px !important; padding-left: 10px !important">
                                                <i class='fa fa-arrow-left' aria-hidden="true"></i>
                                            </button>
                                            <button type='button' class="btnpad btn btn-info jqbtk-shift"  data-val='1000'>1000</button>
                                        </div>
                                    </div>

                                    <div style='display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap:4px; padding-top: 10px;'>
                                        <button type='button' class='btnpad btn btn-info' data-val='EXACT'>Exact</button>
                                        <button type='button' class='btnpad btn btn-warning' data-val='CLEAR'>Clear</button>
                                        <button type='button' class='btn btn-danger' data-dismiss="modal" aria-label="Close">Close</button>
                                        <button type='button' id='PaySubmit' class='btn btn-success'>Submit</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- <div class='modal-footer'>
                    <div id='footer' style='right: 0px'>
                        <button class='btn btn-success'  style='padding: 5px; width: 1in; '>Submit</button>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</body>
</html>

<script type='text/javascript'>
    var itemStored = [];
    var matrix = 'PM1';
    var amtTotal = 0;
    var count = 0;
    
    
    $(document).ready(function(){
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
                $('#customer').attr("placeholder",res.data);
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
                console.log(items)
                duplicate(items)
                table_store(itemStored)
                $('#barcode').val("").change()
            }
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
                // $('#ccustname').val(item.value).change(); 
                // $("#ccustid").val(item.id);
                // $("#ccustcredit").val(item.nlimit); 
                // $("#divCreditLim").text(item.nlimit);
                // chkbalance(item.id);
                // $("#citemno").focus();	
                $("#paymentList > tbody").empty()
                $("#VoidList > tbody").empty()
                $("#listItem > tbody").empty()
                $("#gross").text(parseFloat(0).toFixed(2))
                $("#vat").text(parseFloat(0).toFixed(2))
                $("#net").text(parseFloat(0).toFixed(2))
                itemStored = [];
            }
        })


        $('.items, .itmclass').hover(function() {
            $(this).css('cursor','pointer');
        });

        $(".itmclass").on("click", function() {
            const ClassID = $(this).attr("data-clscode");
            
            $('.itmslist').each(function(i, obj) {
                itmcls = $(this).attr("data-itemlist");
                
                if(itmcls==ClassID){
                    $(this).show();
                }else if(itmcls!=ClassID){
                    $(this).hide();
                }
            });		
        });


        $('#item-wrapper').on('click', '#items',function(){
            const name = $(this).attr("name");
            insert_item(name)
        })


        $('#VoidSubmit').click(function(){
            $("input:checkbox[name=itemcheck]:checked").each(function(){
                itemStored.splice($(this).val(), 1);

                table_store(itemStored);
                $('#mymodal').modal('hide')
            });
        })

        $('#btnVoid').click(function(){
            if(itemStored.length === 0) {
                return alert('Transaction is empty!')
            }

            modalshow("Void");
            table_store(itemStored)
        })

        //button for holding items
        $('#btnHold').on('click', function(){
            //storing input values in array
            // if($('#orderType').find(":selected").val() == ""){
            //     return alert("Please Fillup Order Type to procceed!");
            // }

            let tranno, msg;
            var isSuccess = false;
            var isHold = false;

            if(itemStored.length === 0){
                return alert('Transaction is empty! cannot hold transaction');
            }
            const quantity = [];

            $('input[name*="qty"]').each((index, item) => {
                quantity.push($(item).val())
            })
            
            $.ajax({
                url: 'Function/th_hold.php',
                data: {
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
                        },
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.valid){
                                isSuccess = true;
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
            if(isSuccess){
                alert(msg);
                location.reload();
            } else {
                alert(msg);
            }
            
        });

        /**
         * Payment Transaction
         */

        $('#btnPay').click(function(){
            if(itemStored.length === 0){
                return alert('Transaction is empty! cannot proceed transaction');
            }

            $('#tendered').val(0)
            $('#tendered').focus()
            $('#tendered').select()
            $('#discountAmt').val(0)
            $('#ExchangeAmt').val(0)
            
            $('#payModal').modal('show')
            PaymentCompute()
        })

        $('#discountAmt').change(function(){
            let disc = $(this).val();
            computation(itemStored);
            $("#discountcode").val("");
            if(disc != 0) {
                let total = $("#totalAmt").val()
                let dif = total - disc;
                $('#totalAmt').val(dif)
                return $("#dc").show();
            } 
            return $("#dc").hide();
        })


        /**
         * Retrive Hold transaction
         */

        $('#btnRetrieve').click(function(){
            $.ajax({
                url: 'Function/th_gethold.php',
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $('#RetrieveList > tbody').empty();
                        res.data.map((item, index) => {
                            console.log(item)
                            $("<tr>").append( 
                                $("<td>").html("<input type='checkbox' id='chkretrieve' name='chkretrieve' value='"+item.transaction+"' />"),
                                $("<td  >").text(item.transaction),
                                $("<td align='center'>").text(item.table),
                                $("<td align='center'>").text(item.ordertype),
                                $("<td align='center'>").text(item.trandate),
                            ).appendTo('#RetrieveList > tbody')
                        })
                    } else{
                        alert(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })

            modalshow("Retrieve")
        })

        $('#RetrieveSubmit').click(function(){
            const itemRetrieve = [];
            $("input:checkbox[name=chkretrieve]:checked").each(function(){
                itemRetrieve.push($(this).val());
            });

            $.ajax({
                url: 'Function/th_getholdtransaction.php',
                data: {
                    items: itemRetrieve
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
                        console.log(res )
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
        })


        $('#tendered').on('keyup', function(){
            let tender = $(this).val();

            if(tender != ""){
                return PaymentCompute()
            }
            $('#ExchangeAmt').val('0.00')
        })
        
        $('#discountAmt').on('keyup', function(){
            var disc = $(this).val()
            let total = parseFloat(amtTotal)
            let dif = parseFloat(total) - parseFloat(disc);
            if(disc != ""){
                return $('#totalAmt').val(dif.toFixed(2))
            }
            $('#ExchangeAmt').val('0.00')
        })

        $('.btnpad').click(function(){
            let tender = $('#tendered').val();
            let total = $('#totalAmt').val();
            let btn = $(this).attr("data-val");
            let number = 0;
            console.log(total)

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


        $('#PaySubmit').click(function(){
            let exchange = $('#ExchangeAmt').val();
            let total = $('#totalAmt').val().replace(/,/g,'');
            let tender = $('#tendered').val();
            let proceed = false;
            let tranno = '';
            
            if(parseFloat(total) <= parseFloat(tender)){
                $.ajax({
                    url: 'Function/pos_save.php',
                    type: 'post',
                    data: {
                        prepared: '<?= $_SESSION['employeename'];?>',
                        amount: $('#gross').text(),
                        net: $('#net').text(),
                        vat: $('#vat').text(),
                        gross: total,
                        discount: $('#discountAmt').val(),
                        tendered: tender,
                        exchange: exchange,
                        customer: $('#customer').val(),
                        order: $('#orderType').find(":selected").val(),
                        table: $('#table').find(":selected").val(),
                        discountcode: $("#discountcode").val()
                    },
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        if(res.valid){
                            proceed = res.valid;
                            tranno = res.tranno
                            console.log(res.msg)
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
                        },
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.valid){
                                console.log(res.msg)
                                location.reload();
                            } else {
                                console.log(res.msg)
                            }
                        },
                        error: function(res){
                            console.log(res)
                        }
                    })
                })
            }
            
        })
    })

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

    function insert_item(partno){
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
     * for duplication item
     */

     function duplicate(data, qty = 1) {
        if (!Array.isArray(itemStored)) {
            itemStored = [];
        }

        const price = chkprice(data.partno, data.unit, matrix, <?= date('Y-m-d') ?>)
        const disc = discountprice(data.partno, data.unit, matrix, <?= date('Y-m-d') ?>)
        var discvalue = 0;
        let found = false;

        switch(disc.type){
            case "PRICE":
                discvalue = parseFloat(disc.value);
                break;
            case "PERCENT":
                discvalue = parseInt(disc.value) / 100;
                break;
        }
        console.log(parseFloat(discvalue))
        
        for (let i = 0; i < itemStored.length; i++) {
            if (itemStored[i].partno === data.partno) {
                itemStored[i].quantity += qty;
                itemStored[i].price = parseFloat(itemStored[i].price) + parseFloat(price);
                itemStored[i].discount = parseFloat(itemStored[i].price)* parseFloat(discvalue);
                itemStored[i].amount = parseFloat(itemStored[i].price) - parseFloat(itemStored[i].discount);
                found = true;
                break;
            }
        }

        if (!found) {
            itemStored.push({
                partno: data.partno,
                name: (data.name ? data.name : data.item),
                unit: data.unit,
                quantity: qty,
                price: parseFloat(price).toFixed(2),
                discount: parseFloat(price * discvalue).toFixed(2),
                amount: parseFloat(price) - (parseFloat(price * discvalue))
            });
        }

    }


    /**
     * Computation for payments
     */

    function PaymentCompute(){
        
        let amt = $('#totalAmt').val().replace(/,/g,'');
        let tender = $('#tendered').val().replace(/,/g,'');
        let disc = $('#discountAmt').val().replace(/,/g,'');
        let exchange =$('#ExchangeAmt').val().replace(/,/g,'');

        let subtotal = parseFloat(amt) - parseFloat(tender);
        if(subtotal > 0){
            return $('#ExchangeAmt').val("0.00")
        }
        $('#ExchangeAmt').val(Math.abs(subtotal))
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

    function discountprice(item, unit, code, date, price){
        var value;

        $.ajax({
            url: "Function/th_discount.php",
            data: { item: item, unit: unit, code: code, date: date},
            dataType: "json",
            async: false,
            success: function(res){
                let discount = parseFloat(res.data)
                value = discount;
                type = res.type;
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

    function table_store(items){
        $('#listItem > tbody').empty();
        $('#VoidList > tbody').empty();
        $('#paymentList > tbody').empty();

        items.map((item, index) => {
            $("<tr>").append(
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"'/>"),
                $("<td>").text(parseFloat(item.price).toFixed(2)),
                $("<td>").text(parseFloat(item.discount).toFixed(2)),
                $("<td>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#listItem > tbody")


            $("<tr>").append(
                $("<td align='center'>").html("<input type='checkbox' name='itemcheck' value='"+item.name+"'/>"),
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"'/>"),
                $("<td>").text(parseFloat(item.price).toFixed(2)),
                $("<td>").text(parseFloat(item.discount).toFixed(2)),
                $("<td>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#VoidList > tbody")

            $("<tr>").append(
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
    
    function computation(data){
        const itemAmounts = {discount: 0, net: 0, vat: 0, gross: 0}

        data.map((item, index) =>{
            price = parseFloat(item.amount);
            net = price / parseFloat(1 + (12/100));
            itemAmounts['net'] += price / parseFloat(1 + (12/100));
            itemAmounts['vat'] = (itemAmounts.net * (12/100));
            itemAmounts['discount'] += 0;
            itemAmounts['gross'] += price;
        })

        $('#vat').text(parseFloat(itemAmounts.vat).toFixed(2));
        $('#net').text(parseFloat(itemAmounts.net).toFixed(2));
        $('#gross').text(parseFloat(itemAmounts.gross).toFixed(2));
        $('#totalAmt').val(parseFloat(itemAmounts.gross).toFixed(2));
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
</script>