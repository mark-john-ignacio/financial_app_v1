<?php 
if (!isset($_SESSION)) {
    session_start();
}

$company = $_SESSION['companyid'];
$employee_cashier_name = $_SESSION['employeeid'];

include('../Connection/connection_string.php');
include('functions.php');

$companyDetails = getCompanyDetails($con, $company);
$categories = getCategories($con, $company);
$items = getItems($con, $company, date('Y-m-d'));
$tables = getTables($con, $company);
$orders = getOrders($con, $company);
$discounts = getDiscounts($con, $company);
list($serviceFee, $isCheck) = getServiceFee($con, $company);
$isCheckWaitingTime = getWaitingTime($con, $company);
$isCheckManualReceipt = getManualReceipt($con, $company);


//for old code compatibility
$companyName = $companyDetails['compname'];
$companyAddress = $companyDetails['compadd'];
$companyTin = $companyDetails['comptin'];
$category = $categories;
$table = $tables;
$order = $orders;
$discount = $discounts;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap2.css?v=<?php echo time();?>">
    <link href="../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slicksize.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/keypadz.css?v=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../include/autoNumeric.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="scripts.js"></script>
    <title>MyxFinancials</title>
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
                                <div style='height: 52vh; max-height: 60vh; overflow: auto;'>
                                    <table class='table' id='listItem' style="width: 100%; ">
                                        <thead style='background-color: #019aca'>
                                            <tr>
                                                <th style="width: 60%;">Item</th>
                                                <th style="text-align: center;">UOM</th>
                                                <th style="text-align: center;">Quantity</th>
                                                <th style="text-align: right;">Price</th>
                                                <th style="text-align: right;">Discount</th>         
                                                <th style="text-align: right;">Amount</th>
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
                                <span class='input-group-addon' id="addcustomer" onclick="add_customer_modal()"><i class='fa fa-user'></i></span><input class='form-control input-sm' type="text" name='customer' id='customer' placeholder="Walkin Customer (Default)" autocomplete="off">
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
                                <div class='input-group' id = "WaitingTimeContainer" style="display: none;">
                                    <input class='form-control input-sm' type="number" name='waiting_time' id='waiting_time' min="0" placeholder="Waiting Time (Mins)">
                                </div>
                                <div class="input-group" id = "KitchenContainer"  style="display: none;">
                                    <select name="kitchen_receipt" id="kitchen_receipt" class="form-control input-sm" style="display: none;">
                                        <option value="" disabled selected>Kitchen Receipt ?</option>
                                        <option value="Yes">YES</option>
                                        <option value="No">NO</option>
                                    </select>
                                    <button class=" btn btn-sm btn-primary" id="kitchenReceiptButton" type="button">
                                        <i class="material-icons fa-fw" aria-hidden="true" style="font-size:15px; vertical-align:middle;">&#xe8b0;</i> Kitchen Receipt
                                    </button>   
                                </div>
                                <script>
                                    document.getElementById('kitchenReceiptButton').addEventListener('click', function() {
                                        var selectElement = document.getElementById('kitchen_receipt');
                                        selectElement.value = "Yes";
                                    });

                                    document.addEventListener('DOMContentLoaded', function() {
                                        var isCheckWaitingTime = <?php echo $isCheckWaitingTime; ?>;
                                        var isCheckManualReceipt = <?php echo $isCheckManualReceipt; ?>;

                                        if (isCheckWaitingTime == 1) {
                                            document.getElementById('WaitingTimeContainer').style.display = 'block';
                                        } else {
                                            document.getElementById('WaitingTimeContainer').style.display = 'none';
                                        }

                                        if (isCheckManualReceipt == 1) {
                                            document.getElementById('KitchenContainer').style.display = 'block';
                                        } else {
                                            document.getElementById('KitchenContainer').style.display = 'none';
                                        }
                                    });
                                </script>
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
                        

                        <div style='height: 69vh; overflow: auto;'>
                            <div id='item-wrapper'>
                                <?php foreach($items as $list):
                                    if($list['isInvetory'] != 1) {?>
                                    
                                        <div class='itmslist' id="itemlist" style="margin: 2px; height:130px;                     
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
                                            <div id='items' name="<?= $list['cscancode'] ?>" class='items' data-itemlist="<?= $list['cclass'] ?>" style='position: absolute; bottom: 0; width: 100%; background-color: rgba(0,0,0,.5); color: #fff; min-height: 20px; text-align:center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'><font size='-2'><?php echo $list["citemdesc"]; ?></font></div>
                                        </div>
                                <?php } else { ?>
                                    <div class='itmslist' id="itemlist" style="height:130px;                     
                                            background-color:#019aca; 
                                            background-image:url('<?=$list["cuserpic"];?>');
                                            background-repeat:no-repeat;
                                            background-position: center center;
                                            background-size: contain;
                                            border:solid 1px #036;
                                            position: relative" data-itemlist="<?= $list['cclass'] ?>" name="<?= $list['cscancode'] ?>">   
                                            <div id='items' name="<?= $list['cscancode'] ?>" class='items' data-itemlist="<?= $list['cclass'] ?>" style='position: absolute; bottom: 0; width: 100%; background-color: rgba(0,0,0,.5); color: #fff; min-height: 20px; text-align:center;'><font size='-2'><?php echo $list["citemdesc"]; ?></font></div>
                                        </div>
                                <?php } endforeach ?>
                            </div>
                        </div>
    
                        
                    </div>
                </div>

        </div>
    </div>
    <footer style="position: fixed; bottom: 0px; padding: 10px; min-width: 100%;">
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
                                    <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(0, 1fr)); grid-gap: 10px;'>
                                            <button type="button" class="btn btn-sm btn-warning form-control " id='spcdBtn' name='spcdBtn'>Discount</button>
                                            <button type="button" class="btn btn-sm btn-info form-control " id='couponBtn' name='couponBtn'>Coupon</button>
                                            <select name="paymethod" id="paymethod">
                                                <option value="CASH">CASH</option>
                                                <option value="MOBILE">MOBILE PAYMENT</option>
                                                <option value="DEBIT">DEBIT</option>
                                                <option value="CREDIT">CREDIT</option>
                                            </select>
                                    </div>
                                    <div style='width: 100%'>
                                        <label for="paymethod_txt">Reference no.</label>
                                        <input type="text" id="paymethod_txt" class="form-control" disabled>

                                        <label for="tendered">Tendered</label>
                                        <input type="number" id='tendered' class='form-control' />

                                        <label for="couponinput">Coupon Amount</label>
                                        <input type="text" name="couponinput" id="couponinput" class='form-control' readonly>

                                        <label for="totalTender">Total Tendered Amount</label>
                                        <input type="text" name="totalTender" id="totalTender" class='form-control' readonly>
                                        <input type="hidden" id="h_tranno">

                                        <label for="discountInput">Special Discount</label>
                                        <input type="text" name="discountInput" id="discountInput" class='form-control' readonly>

                                        <label for="discountInput">Service Fee</label>
                                        <input type="text" name="ServiceInput" id="ServiceInput" class='form-control' readonly>

                                        <label for="subtotal">Total Amount</label>
                                        <input type='text' id='subtotal' class='form-control' readonly/>
                                        
                                        <label for="totalAmt">Total to Pay</label>
                                        <input type='text' id='totalAmt' class='form-control' readonly/>

                                        <label for="ExchangeAmt">Change Amount</label>
                                        <input type="text" id='ExchangeAmt' class='form-control' readonly/><br>

                                        <!-- Optional inputs toggle -->
                                        <label>
                                            <input type="checkbox" id="optInputsCheck" />
                                            Enter Name, Address, TIN
                                        </label>

                                        <!-- Optional inputs, hidden by default -->
                                        <div id="optionalFields" style="display:none; margin-top:10px;">
                                            <label>Name: </label>
                                            <input type="text" id="cust_name" class='form-control'/><br/>

                                            <label>Address: </label>
                                            <input type="text" id="cust_address" class='form-control'/><br/>

                                            <label>TIN: </label>
                                            <input type="text" id="cust_tin" class='form-control'/>
                                        </div>
                                        
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

                <div class='modal-bodylong' id='modal-bodylong' style='height: 100%'>
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

    <div class="modal fade" id="kitchenPrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-contnorad">   
                <div class="modal-bodylong">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <iframe id="mykprintframe" name="mykprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="AddCustomerModal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-contnorad">
                <div class="modal-header">
                    <h3 class="modal-title" id="invheader">New Customer</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(200px, 1fr)); grid-gap: 5px">
                        <div style="font-size: 14px; font-weight: bold;">Enter Customer Name: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="customer_name" placeholder="Enter Customer's Name...." class="form-control input-sm">
                        </div>
                        <div style="font-size: 14px; font-weight: bold;">Enter Tin Number: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="tin_number" placeholder="Tin Number... (xxx-xxx-xxx-xxxxx)" class="form-control input-sm">
                        </div>
                        <div style="font-size: 14px; font-weight: bold;">Enter House Number: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="customer_house" placeholder="Enter House no. ... (blk xx lot xx)" class="form-control input-sm" />
                        </div>
                        <div style="font-size: 14px; font-weight: bold">Enter City: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="customer_city" placeholder="Enter City..." class="form-control input-sm">
                        </div>
                        <div style="font-size: 14px; font-weight: bold">Enter State: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="customer_state" placeholder="Enter State..." class="form-control input-sm">
                        </div>
                        <div style="font-size: 14px; font-weight: bold">Enter Country: </div>
                        <div class="col-xs-10 nopadding">
                            <input type="text" id="customer_country" placeholder="Enter Country..." class="form-control input-sm">
                        </div>
                        <div style="font-size: 14px; font-weight: bold">Enter Zip Code: </div>
                        <div class="col-xs-6 nopadding">
                            <input type="text" id="customer_zip" placeholder="Zip Code..." class="form-control input-sm"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm" onclick="create_new_customer()">Submit</button>
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
<script>
    var isCheckWaitingTime = <?php echo $isCheckWaitingTime; ?>;
    var isCheckManualReceipt = <?php echo $isCheckManualReceipt; ?>;
    var serviceFee = <?= $isCheck != 0 ? $serviceFee / 100 : 0 ?>;
    var employeeCashierName = "<?php echo $employee_cashier_name; ?>";
</script>
