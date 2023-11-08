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

    $sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty, a.cuserpic, c.nqty as quantity
            from items a 
            left join
                (
                    select a.citemno, COALESCE((SUM(nqtyin) - SUM(nqtyout)), 0) as nqty
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

    $sql = "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = 'SERVICE_FEE'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $serviceFee = $row['cvalue'];
            $isCheck = $row['nallow'];
        }
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
    <title>MyxFinancials</title>

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
            display: absolute;
            bottom: 0px;
        }
    </style>
</head>
<body style='margin: 0; padding: 0;'> 
    <div stlye="min-height: 100vh; position: relative; ">
            <div class='row nopadwtop' id='header' style="background-color: #2d5f8b; width:100%; height:55px; margin-bottom: 5px !important">
                <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
                <img src="../images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="48" />
                </div>
                <div style='position: fixed; top: 10px; right: 30px; font-size: 20px; '>
                    <a href="../logout.php" id="logout" style="color: white;">
                        <i class='fa fa-sign-out fa-fw fa-lg'></i>logout
                    </a>
                </div>
                
            </div>
    

        <div style="display: flex; min-width: 100%; ">
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
                                    <span class="digital-clock"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class='input-group margin-bottom-sm'>
                                    <input type="text" name='barcode' id='barcode' class='form-control input-sm' placeholder="|||||||||||||||||||||||||||||||||||||||| Barcode " autocomplete="off">
                                    <input type="text" name="tranno" id="tranno" class='form-control input-sm' style='display: none;'/>
                                    <span class='input-group-addon'><i class='fa fa-barcode fa-fw'></i></span>
                                </div>
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style='padding-top: 20px'>
                                <div style='height: 3.3in; max-height: 3.6in; overflow: auto;'>
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
                            <td colspan='2' class="nopadding">
                                <table id='amountlist' class="nopadding" style='width: 100%'>
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

                <div class='col' id='right-side' style='width: 50%; height: 100%; padding: 10px;'>
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

                        <div>
                            <section style='width: 90%; padding: 10px' class="regular slider btn">
                                <div style="height:100%; 
                                        word-wrap:break-word;
                                        background-color:#019aca; 
                                        border:solid 1px #036;
                                        padding:3px;
                                        text-align:center;" class="itmclass btn btn-info" data-clscode="ALL">
                                            <font size="-2">ALL</font>
                                    </div>
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
                        

                        <div style='height: 350px; overflow: auto;'>
                            <div id='item-wrapper'>
                                <?php foreach($items as $list):?>
                                        <div class='itmslist' id="itemlist" style="height:100px;                     
                                            background-color:#019aca; 
                                            background-image:url('<?=$list["cuserpic"];?>');
                                            background-repeat:no-repeat;
                                            background-position: center center;
                                            background-size: contain;
                                            border:solid 1px #036;
                                            position: relative" data-itemlist="<?= $list['cclass'] ?>" name="<?= $list['cscancode'] ?>">
                                            <div style='position: absolute; text-align: right; width: 100%; color: #fff; min-height: 20px;'>
                                                <?= !empty($list['quantity']) && $list['quantity'] >= 0? "Remaining: <span id='remain'>" . number_format($list['quantity']) ."</span>" : "Sold Out" ?>
                                            </div>
                                            <div id='items' name="<?= $list['cscancode'] ?>" class='items' data-itemlist="<?= $list['cclass'] ?>" style='position: absolute; bottom: 0; width: 100%; background-color: rgba(0,0,0,.5); color: #fff; min-height: 20px; text-align:center;'><font size='-2'><?php echo $list["citemdesc"]; ?></font></div>
                                        </div>
                                <?php endforeach ?>
                            </div>
                        </div>
    
                        
                    </div>
                </div>

        </div>
    </div>
    <footer style="position: absolute; bottom: 0px; padding: 10px; min-width: 100%;">
                <div id='wrapper' >
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
    </footer>
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
                                <!-- <th>&nbsp;</th> -->
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
                        <!-- <button class='btn btn-warning' id='RetrieveSubmit' style='padding: 5px; width: 1in; display:none;'>Retrieve</button> -->
                    </div>
                </div>
            </div>     
        </div>
    </div>

    <!-- Retrieve and Hold Modal -->
    <div class='modal fade' id='payModal' role='dialog'>
        <div class='modal-lg modal-dialog' role="document">
            <div class='modal-content'>
                <div class='modal-header'>
                    
                    <h3 class="modal-title" id="invheader">Payment Terms</h3>
                </div>
                <div class='modal-body' id='modal-body' style='height: 100%; overflow: auto;'>
                    <table class='table' style='width: 100%;'>
                        <tr>
                            <td>
                                <div style='height: 4in;'>
                                    <table class='table' id='paymentList' style='width: 100%;'>
                                        <thead style='background-color: #019aca'>
                                            <tr>
                                                <th>&nbsp;</th>
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
                            <td style='width: 35%' id='paymentcol'>
                                <div id='payment-details'>
                                    <div style='display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); grid-gap: 10px;'>
                                            <button type="button" class="btn btn-sm btn-warning form-control " id='spcdBtn' name='spcdBtn'>Discount</button>
                                            <button type="button" class="btn btn-sm btn-info form-control " id='couponBtn' name='couponBtn'>Coupon</button>
                                            <button type="button" class="btn btn-sm btn-success form-control " id='serviceBtn' name='serviceBtn'>Service Fee</button>
                                    </div>
                                    <div style='width: 100%'>
                                        <label for="tendered">Tendered</label>
                                        <input type="number" id='tendered' class='form-control' />

                                        <label for="couponinput">Coupon Amount</label>
                                        <input type="text" name="couponinput" id="couponinput" class='form-control' readonly>

                                        <label for="totalTender">Total Tendered Amount</label>
                                        <input type="text" name="totalTender" id="totalTender" class='form-control' readonly>

                                        <label for="discountInput">Special Discount</label>
                                        <input type="text" name="discountInput" id="discountInput" class='form-control' readonly>

                                        <label for="discountInput">Service Fee</label>
                                        <input type="text" name="ServiceInput" id="ServiceInput" class='form-control' readonly>

                                        <label for="subtotal">Sub-Total</label>
                                        <input type='text' id='subtotal' class='form-control' readonly/>
                                        
                                        <label for="totalAmt">Total Amount</label>
                                        <input type='text' id='totalAmt' class='form-control' readonly/>

                                        <label for="ExchangeAmt">Change Amount</label>
                                        <input type="text" id='ExchangeAmt' class='form-control' readonly/><br>
                                        
                                    </div>

                                    <div class='jqbtk-container' style='padding-top: 5px; display:none;'>
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

                            <td id='specialdiscountcol' style='width: 35%; height: 100%; overflow: auto;' >
                                <div><a href="javascript:;" id='spcBack'><i class='fa fa-arrow-left'></i></a></div>
                                <div>
                                    <div style='width: 100%'>
                                            <label for='discountAmt'>Discount Type</label>
                                            <select name="discountAmt" id="discountAmt" class='form-control'>
                                                <option value="0">No Discount</option>
                                                <?php foreach($discount as $list): ?>
                                                    <option value='<?= $list["nvalue"] ?>' dataval='<?= $list["type"] ?>'><?= $list['cdescription'] ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                        <div id='dc' style='display: none'>
                                            <label for='discountCust'>Customer Name</label>
                                            <input type="text" id="discountCust" name="discountCust" placeholder="Customer Name..." class="form-control">

                                            <label for='discountID'>Customer Valid ID</label>
                                            <input type="text" id="discountID" name="discountID" placeholder="Customer Valid ID..." class="form-control">
                                        </div>

                                    </div>
                                    <br>
                                    <center>
                                        <button type='button' id='SpecialDiscountBtn' class='btn btn-success'>Submit</button>
                                    </center>
                                </div>
                            </td>

                            <td id='couponmodal'>
                                    <div><a href="javascript:;" id='couponback'><i class='fa fa-arrow-left'></i></a></div>
                                    <div>
                                        <label for="coupontxt">Enter your coupon</label>
                                        <input type="text" class="form-control input-sm" id='coupontxt' name='coupontxt' placeholder="Enter Coupon..." autocomplete="false">
                                        <div class='input-sm' id='couponmsg'></div>
                                        <center>
                                            <button class='btn btn-success' id='CouponSubmit' style='padding: 5px; width: 1in;'>Submit</button>
                                        </center>
                                    </div>
                            </td>
                            <td id='servicefee'>
                                <div><a href="javascript:;" id='serviceBack'><i class='fa fa-arrow-left'></i></a></div>
                                <div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="ServiceSwitch" <?= $isCheck != 0 ? "Checked": null ?>>
                                        <label class="form-check-label" for="ServiceSwitch">Check if you need to enable Service Fee</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- <div class="modal-body" >
                    
                </div> -->
            </div>
        </div>
    </div>
    <!-- Void Login User Modal -->
    <div class='modal fade' id='voidlogin' role='dialog' data-backdrop="static">
        <div class='modal-sm modal-dialog' role="document">
            <div class='modal-content'>
                <div class='modal-header'>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>  
                    <h3 class="modal-title" id="invheader">Void Authentication</h3>
                </div>

                <div class='modal-body' id='modal-body' style='height: 100%'>
                    <div>
                        <label for="loginid" class='nopadwtop'>Username:</label>
                        <input type="text" class='form-control input-sm' id='loginid' name='loginid' placeholder="Enter Username..." autocomplete='false' />
                        
                        <label for="loginpass" class='nopadwtop'>Password:</label>
                        <input type="password" class='form-control input-sm' id='loginpass' name='loginpass' placeholder="Enter Password..." autocomplete='false' />
                        
                        <button type='button' class='btn btn-success form-control input-sm' id='login' name='login' data-dismiss="modal" aria-label="Close" style='margin-top: 30px;'>Login </button>
                        <button type='button' class='btn btn-danger form-control input-sm' data-dismiss="modal" aria-label="Close" style='margin-top: 10px;'> Cancel </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Print Modal -->
    <div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-contnorad">   
                <div class="modal-bodylong">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Message Modal -->
    <div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog vertical-align-top">
                <div class="modal-content">
                <div class="alert-modal-danger">
                    <p id="AlertMsg"></p>
                    <p>
                        <center>
                            <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                        </center>
                    </p>
                </div>
                </div>
            </div>
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

        $("#serviceBtn").click(function(){
            $("#paymentcol").hide();
            $("#servicefee").show()
        })

        $("#serviceBack").click(function(){
            $("#paymentcol").show();
            $("#servicefee").hide()
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


        $('#item-wrapper').on('click', '#itemlist',function(){
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
            var subtotal = $("#subtotal").val()

            if(parseFloat(subtotal) <= 0){
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
                    console.log(specialDisc)
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
         * Service Fee Switch Check if enable
         */

        $("#ServiceSwitch").change(function(){
            let isCheck = 0;
            if($(this).prop("checked")){
                isCheck = 1;
            }

            $.ajax({
                url: "Function/th_updateService.php",
                data: { isCheck: isCheck },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        console.log(res.msg)
                    } else {
                        console.log(res.msg)
                    }
                    location.reload()
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
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

            let amt = $('#subtotal').val().replace(/,/g,'');
            let ServiceFee = <?= $isCheck != 0 ? $serviceFee / 100 : 0 ?>

            let service = parseFloat(amt) * parseFloat(ServiceFee)
            let total = parseFloat(amt) + service



            $('#tendered').val(0)
            $('#tendered').focus()
            $('#tendered').select()
            $("#couponinput").val(getCoupon(coupon))
            $("#ServiceInput").val(service)
            $("#totalAmt").val(total)
            $("#discountInput").val(0)
            $("#totalTender").val(0)
            $('#discountAmt').val(0)
            $('#ExchangeAmt').val(0)
            
            $('#payModal').modal('show')
            $("#servicefee").hide()
            $("#couponmodal").hide();
            $("#specialdiscountcol").hide()
            $('#modal-body').modal('show')
            PaymentCompute()
        })

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
            // let totalAmt = $("#totalAmt").val().replace(/./g,'');
            let tranno = '';
            
            if(parseFloat(total) <= parseFloat(totalTender)){
                $.ajax({
                    url: 'Function/pos_save.php',
                    type: 'post',
                    data: {
                        tranno: transaction ,
                        amount: gross,
                        net: net,
                        vat: vat,
                        gross: parseFloat(gross),
                        subtotal: parseFloat(total),

                        customer: $('#customer').val(),
                        order: $('#orderType').val(),
                        table: $('#table').val(),

                        tendered: tender,
                        exchange: parseFloat(exchange),
                        discount: getDiscount(itemStored),
                        coupon: getCoupon(coupon),
                        service: parseFloat(servicefee)
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
                                    itemStored[i].price = parseFloat(itemStored[i].quantity) * parseFloat(price);

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
            let remain = parseFloat(data.quantity)
            let quantity = itemStored[i].quantity; 

            if(quantity >= remain){
                return alert("No more stock available")
            }

            if (itemStored[i].partno === data.partno) {
                itemStored[i].quantity += parseFloat(qty);
                itemStored[i].price = parseFloat(itemStored[i].quantity) * parseFloat(price);

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
        let tender = $('#tendered').val().replace(/,/g,'');
        let coupon = $("#couponinput").val().replace(/,/g,'');
        let exchange =$('#ExchangeAmt').val().replace(/,/g,'');
        let amt = $('#subtotal').val().replace(/,/g,'');
        let ServiceFee = <?= $isCheck != 0 ? $serviceFee / 100 : 0 ?>

        let service = parseFloat(amt) * parseFloat(ServiceFee)
        let totaltender = parseFloat(tender) + parseFloat(coupon)

        let total = parseFloat(amt) + service

        let change = parseFloat(total) - totaltender;

        if(change > 0){
            return $('#ExchangeAmt').val("0.00")
        }
        $("#discountInput").val(getSpecialDisc(specialDisc)).change()
        $("#ServiceInput").val(service)
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
            $("<tr>").append(
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"' data-val='"+ item.partno +"'/>"),
                $("<td>").text(parseFloat(item.price).toFixed(2)),
                $("<td>").text(parseFloat(item.discount).toFixed(2)),
                $("<td>").text(parseFloat(item.amount).toFixed(2)),
            ).appendTo("#listItem > tbody")


            $("<tr>").append(
                $("<td align='center'>").html("<input type='checkbox' name='itemcheck' value='"+item.name+"'/>"),
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