<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "POS_View.php";
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

    <title>POS Template</title>
    
    <style>
            .regular {
                width: 90%; 
                padding: 10px;
            }

            .itmclass {
                height:100%; 
                word-wrap:break-word;
                background-color:#019aca; 
                border:solid 1px #036;
                padding:3px;
                text-align:center;
            }
            #item-wrapper {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                overflow: auto;
                text-align: center;
            }
            #itemGrid {
                height: 70vh
            }
            #button-wrapper {
                display: grid;
                padding-top: 10px;
                height: 100%;
                text-align: center;
                grid-gap: 4px;
                grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
                grid-template-rows: 1fr;
                max-width: 4fr;
                overflow: hidden;
            }
            #filter {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            }
            #filter > div{
                padding: 5px;
            }


    </style>
</head>
<body>
    <div class='row nopadwtop2x' id='header' style="background-color: #2d5f8b; height:65px; margin-bottom: 5px !important">
        <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
            <img src="../images/LOGOTOP.png" width="150" height="50"/>
        </div>
    </div>
        

    <div class="col-sm-12 nopadwtop" style="display: flex; ">
        <div class="col-sm-7 nopadwtop">
            <div class='col-sm-12' id='categorySlick'>
                <!-- Slick Category Item for Item List -->
                        <section class="regular slider btn">
                            <?php foreach($category as $list):?>
                                <div class="itmclass btn btn-info" data-clscode="<?= $list['ccode'] ?>">
                                        <font size="-2"><?= $list['cdesc'] ?></font>
                                </div>
                            <?php endforeach; ?>
                        </section>
            </div>
            <div class='col-sm-12' id='itemGrid '>
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

        </div>
        <div class="container col-sm-12 nopadwtop" >
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
                <div>
                    <table id='amountlist' style='width: 100%'>
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
                </div>
        </div>
    </div>
    <div class='col-sm-12 nopadwtop' id='buttons'>
            <div id='button-wrapper' class='col-lg-12 nopadwtop'>
                <button class="form-control btn btn-sm btn-danger" name="btnVoid" id="btnVoid" type="button">
                    <i class="fa fa-plus fa-fw fa-lg" aria-hidden="true"></i>&nbsp;VOID (DEL)
                </button>
                <button class="form-control btn btn-sm btn-warning" name="btnRetrieve" id="btnRetrieve" type="button">
                    <i class="fa fa-bar-chart fa-fw fa-lg" aria-hidden="true"></i>&nbsp; RETRIEVE (F4)
                </button>
                <button class="form-control btn btn-sm btn-primary" name="btnHold" id="btnHold" type="button">
                    <i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i>&nbsp; HOLD (INS)
                </button>
                <button class="form-control btn btn-sm btn-success" name="btnPay" id="btnPay" type="button">
                    <i class="fa fa-money fa-fw fa-lg" aria-hidden="true"></i>&nbsp; PAYMENT (F2)
                </button>
            </div>
    </div>

    

    
</body>
</html>

<script type='text/javascript'>
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
                $('#customer').val(res.data).change();
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
            $("#modal-body").hide()
        })
        
        $("#couponback").click(function(){
            $("#couponmodal").hide()
            $("#modal-body").show()
        })

        $("#CouponSubmit").click(function(){
            let coupons = $("#coupontxt").val()
            var totalAmt = $("#totalAmt").val()
            var subtotal = $("#subtotal").val();

            if(parseFloat(totalAmt) < parseFloat(subtotal)){
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
                coupon = [];
                specialDisc = []
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
            if(!checkAccess("POS_Void.php")){
                return;
            }
            if(itemStored.length === 0) {
                return alert('Transaction is empty!')
            }

            $('#voidlogin').modal('show')
            table_store(itemStored)
        })

        $('#SpecialDiscountBtn').click(function(){
            var disc = $("#discountAmt").val();
            var type = $("#discountAmt").find(":selected").attr("dataval");
            var name = $("#discountAmt").find(":selected").text();
            var person = $("#discountCust").val()
            var id = $("#discountID").val()
            var totalAmt = $("#totalAmt").val()

            if(parseFloat(totalAmt) <= 0){
                return alert("Discount has gone to 0! Discount cannot be apply")
            }
            

            // $("#paymentList tbody").each()
            $("input:checkbox[id='discounted']:checked").each(function(){
                let amounts = $(this).val();
                let itemno = $(this).attr("dataval");
                
                itemStored.map((item, index) =>{
                    console.log(item)
                    if(item.partno === itemno){
                        switch(type){
                            case "PERCENT":
                                item['specialDisc'] = (item.amount * (disc/100))
                                item['amount'] -= (item.amount * (disc/100));
                                break;
                            case "PRICE":
                                item['specialDisc'] = disc;
                                item['amount'] -= disc;
                        }
                       specialDisc.push({item: item.partno, type: type, name: name, person: person, id: id, amount: item.amount * (disc/100)})
                    }
                })
            })
            $("#discountInput").val(getSpecialDisc(specialDisc))
            PaymentCompute()

            alert("Special discount has been added!")
            table_store(itemStored);
            $("#paymentcol").show();
            $("#specialdiscountcol").hide()
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

            if(!checkAccess("POS_Hold.php")){
                return;
            }

            let tranno, msg;
            var isSuccess = false;
            var isHold = false;

            if(itemStored.length === 0){
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

            if(!checkAccess("POS_Payment.php")){
                return;
            }
            if(itemStored.length === 0){
                return alert('Transaction is empty! cannot proceed transaction');
            }

            $('#tendered').val(0)
            $('#tendered').focus()
            $('#tendered').select()
            $("#couponinput").val(getCoupon(coupon))
            $("#discountInput").val(0)
            $("#subtotal").val(0)
            $('#discountAmt').val(0)
            $('#ExchangeAmt').val(0)
            
            $('#payModal').modal('show')
            $("#couponmodal").hide();
            $("#specialdiscountcol").hide()
            $('#modal-body').modal('show')
            PaymentCompute()
        })


        // $('#discountAmt').change(function(){
        //     var disc = $(this).val();
        //     var type = $(this).find(":selected").attr("dataval");
        //     var name = $(this).find(":selected").text();

        //     $("#discountcode").val("");

        //     // $("#paymentList tbody").each()
        //     $("input:checkbox[id='discounted']:checked").each(function(){
        //         let amounts = $(this).val();
        //         let itemno = $(this).attr("dataval");
                
        //         itemStored.map((item, index) =>{
        //             console.log(item)
        //             if(item.partno === itemno){
        //                 switch(type){
        //                     case "PERCENT":
        //                         item['specialDisc'] = (item.amount * (disc/100))
        //                         item['amount'] -= (item.amount * (disc/100));
        //                         break;
        //                     case "PRICE":
        //                         item['specialDisc'] = disc;
        //                         item['amount'] -= disc;
        //                 }
        //                specialDisc.push({item: item.partno, type: type, name: name, person: null, id: null, amount: item.amount * (disc/100)})
        //             }
        //         })
        //         console.log(specialDisc)
        //         table_store(itemStored);
        //     })
        //     if(disc != 0) {
        //         // let total = $("#totalAmt").val()
        //         // let dif = total - disc;
        //         // $('#totalAmt').val(dif)
        //         return $("#dc").show();
        //     } 
        //     return $("#dc").hide();
        // })


        /**
         * Retrive Hold transaction
         */

        $('#btnRetrieve').click(function(){
            if(!checkAccess("POS_Retrieve.php")){
                return;
            }

            $.ajax({
                url: 'Function/th_gethold.php',
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $('#RetrieveList > tbody').empty();
                        res.data.map((item, index) => {
                            console.log(item)
                            $("#tranno").val(item.transaction)
                            $("<tr>").append( 
                                // $("<td>").html("<input type='checkbox' id='chkretrieve' name='chkretrieve' value='"+item.transaction+"' />"),
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
            let row = $(this).find('td:eq(0)').text()

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

        /**
         * Pay Submit Function where storing of Payments
         */
        $('#PaySubmit').click(function(){
            let exchange = $('#ExchangeAmt').val().replace(/,/g,'');
            let total = $('#totalAmt').val().replace(/,/g,'');
            let subtotal = $('#subtotal').val().replace(/,/g,'');
            let tender = $('#tendered').val();
            let proceed = false, isFinished = false;
            let gross = $('#gross').text()
            let net = $("#net").text()
            let vat = $("#vat").text()
            let transaction = $("#tranno").val()
            let tranno = '';
            
            if(parseFloat(total) <= parseFloat(subtotal)){
                $.ajax({
                    url: 'Function/pos_save.php',
                    type: 'post',
                    data: {
                        tranno: transaction ,
                        amount: gross,
                        net: net,
                        vat: vat,
                        gross: parseFloat(total),

                        customer: $('#customer').val(),
                        order: $('#orderType').val(),
                        table: $('#table').val(),

                        tendered: tender,
                        exchange: parseFloat(exchange),
                        discount: getDiscount(itemStored),
                        coupon: getCoupon(coupon),
                    },
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        if(res.valid){
                            proceed = res.valid;
                            tranno = res.tranno
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
                            
                            discount: item.discount,
                            discountID: $("#discountID").val(),
                            discountName: $("#discountCust").val(),

                            coupon: JSON.stringify(coupon),
                            specialdisc: JSON.stringify(specialDisc),
                        },
                        dataType: 'json',
                        async: false,
                        success: function(res){
                            if(res.valid){
                                console.log(res.msg)
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

            if(isFinished){
                $.ajax({
                    url: "../include/th_toInv.php",
                    data:{ tran: tranno, type: "POS"},
                    async: false,
                    success: function(res){
                        console.log(res)
                    },
                    error: function(res){
                        console.log(res)
                    }
                })

                $("#myprintframe").attr("src", "pos_print.php?tranno="+ tranno)
                $("#PrintModal").modal('show');
                // setInterval(() => {
                //     location.reload()
                // }, 10000);

            }
            
        })
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

    function duplicate(data, qty = 1) {
        if (!Array.isArray(itemStored)) {
            itemStored = [];
        }
        

        const price = chkprice(data.partno, data.unit, matrix, "<?= date('m/d/Y') ?>")
        const disc = discountprice(data.partno, data.unit, "<?= date('m/d/Y') ?>")
        var discvalue = 0;
        let found = false;
        
        for (let i = 0; i < itemStored.length; i++) {
            if (itemStored[i].partno === data.partno) {
                itemStored[i].quantity += qty;
                itemStored[i].price = parseFloat(itemStored[i].price) + parseFloat(price);

                switch(disc.type){
                    case "PRICE":
                        discvalue = parseFloat(itemStored[i].discount) + parseFloat(disc.value);
                        break;
                    case "PERCENT":
                        discvalue = parseFloat(itemStored[i].price) * (parseInt(disc.value) / 100);
                        break;
                }

                itemStored[i].discount = parseFloat(discvalue);
                itemStored[i].amount = parseFloat(itemStored[i].price) - parseFloat(itemStored[i].discount);
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
        
        let amt = $('#totalAmt').val().replace(/,/g,'');
        let tender = $('#tendered').val().replace(/,/g,'');
        let coupon = $("#couponinput").val().replace(/,/g,'');
        let exchange =$('#ExchangeAmt').val().replace(/,/g,'');

        let totaltender = parseFloat(tender) + parseFloat(coupon)
        let subtotal = parseFloat(amt) - totaltender;
        if(subtotal > 0){
            return $('#ExchangeAmt').val("0.00")
        }
        $("#discountInput").val(getSpecialDisc(specialDisc)).change()
        $("#subtotal").val(totaltender)
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

</script>