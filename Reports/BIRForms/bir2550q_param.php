<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRForms";

    include("../../Connection/connection_string.php");
    include('../../include/denied.php');
    include('../../include/access.php');

    $company = $_SESSION['companyid'];

    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);
    
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $comprdo = $row;
    }

   //get default EWT acct code
	@$ewtpaydef = "";
	@$ewtpaydefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno'];
			@$ewtpaydefdsc = $row['cacctdesc']; 
		}
	}

    @$inputtaxdef = "";
	@$inputtaxdefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='PURCH_VAT'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$inputtaxdef = $row['cacctno'];
			@$inputtaxdefdsc = $row['cacctdesc']; 
		}
	}

    $year = date("Y", strtotime($_POST['years']));

    $apv = array();
    $xendingmonth = "";
    switch($_POST['selqrtr']){
        case 1:
            $months = "1,2,3";
            $xendingmonth = 3;
            break;
        case 2:
            $months = "4,5,6";
            $xendingmonth = 6;
            break;
        case 3:
            $months = "7,8,9";
            $xendingmonth = 9;
            break;
        case 4:
            $months = "10,11,12";
            $xendingmonth = 12;
            break;
        default: 
            $months = "";
            break; 
    }
    $sql = "SELECT SUM(a.ncredit-a.ndebit) as ncredit, a.cewtcode, a.newtrate
        FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON b.compcode = c.compcode AND b.ccode = c.ccode 
        LEFT JOIN groupings d ON c.compcode = d.compcode AND c.csuppliertype = d.ccode AND d.ctype = 'SUPTYP'				
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) in ($months) AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and a.cacctno='$ewtpaydef' and IFNULL(a.cewtcode,'') <> '' Group By a.cewtcode, a.newtrate Order By a.cewtcode";
    
    //echo $sql."<br>";
    $query = mysqli_query($con, $sql);               
    while($row = $query -> fetch_assoc()){
        $apv[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../../include/select2/select2.min.css">

    <link rel="stylesheet" type="text/css" href="../../global/plugins/icheck/skins/all.css?x=<?php echo time();?>">

    <link href="../../global/css/components.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/select2/select2.full.min.js"></script>

    <script src="../../global/plugins/icheck/icheck.min.js"></script>

    <script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>

    <form action="bir1601eq.php" name="frmpos" id="frmpos" method="post" target="_blank">
        <div class="container">
            <br>
            <div class="row">
                <div class="col-sm-10">
                &nbsp;
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-print"></i>&nbsp;PRINT PDF</button>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12"><img src="../../bir_forms/birheader.jpg" width="100%"></div>

                <div class="col-12" style="padding-top: 5px; padding-bottom: 0px">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" width="150px"> BIR FORM No.<h3 class="nopadding">2550Q</h3>April 2024 (ENCS)<br>Page 1</td>
                            <td align="center" style="vertical-align: middle !important;"><h3 class="nopadding">Quarterly Value-Added Tax</h3><h3 class="nopadding">(VAT) Return</h3></td>
                            <td align="center" width="200px" style="vertical-align: middle !important;"><img src="../../bir_forms/hdr2550q.jpg" width="100%"> </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 0px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="left" width="70px" style="border-right: 1px solid #fff !important; vertical-align: middle !important">
                                <b>1.</b> For the
                            </td>
                            <td align="left" width="300px">
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;Calendar</li>

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;Fiscal</li>
                                        
                                        </ul>

                                    </div>
                                </div>
                            </td>
                            <td align="center" width="200px" style="border-right: 1px solid #fff !important; vertical-align: middle !important">
                                <b>2.</b> Year Ended <i>(MM/YYYY)</i>
                            </td>
                            <td align="center" width="60px" style="border-right: 1px solid #fff !important; ">
                                <input type="number" class="form-control input-sm" name="txt1601eq_nosheets" id="txt1601eq_nosheets">
                            </td>
                            <td align="center" width="100px">
                                <input type="number" class="form-control input-sm" name="txt1601eq_nosheets" id="txt1601eq_nosheets">
                            </td>
                            <td align="center" width="100px" style="border-right: 1px solid #fff !important; vertical-align: middle !important">
                                <b>3.</b> Quarter
                            </td>
                            <td align="left">
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndY" name="txt1601eq_amnd" value="Y"><label for="txt1601eq_amndY">&nbsp;1st</li>
                                            
                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndN" name="txt1601eq_amnd" value="N" checked><label for="txt1601eq_amndN">&nbsp;2nd</li>

                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndY" name="txt1601eq_amnd" value="Y"><label for="txt1601eq_amndY">&nbsp;3rd</li>
                                            
                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndN" name="txt1601eq_amnd" value="N" checked><label for="txt1601eq_amndN">&nbsp;4th</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                           
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <b>4. </b>Return Period <i>(MM/DD/YYYY)</i> 
                                        <div class="row">
                                            <div class="col-md-1">
                                                &nbsp;
                                            </div>
                                            <div class="col-md-1" style="padding-top: 5px">
                                                FROM
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control input-sm" name="txt1601eq_nosheets" id="txt1601eq_nosheets">
                                            </div>
                                            <div class="col-md-1" style="padding-top: 5px">
                                                TO
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control input-sm" name="txt1601eq_nosheets" id="txt1601eq_nosheets">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <b>5. </b>Amended Return?
                                        <div class="input-group"  style="padding-left: 20px">
                                            <div style="margin-top: 5px">
                                                <ul class="ichecks list-inline" style="margin: 0px !important">

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;Yes</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;No</li>
                                                
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <b>6. </b>Short Period Return?
                                        <div class="input-group"  style="padding-left: 20px">
                                            <div style="margin-top: 5px">
                                                <ul class="ichecks list-inline" style="margin: 0px !important">

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;Yes</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;No</li>
                                                
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="4"> <b> Part I - Background Information</b></td>
                        </tr>
                        <tr>
                            <td width="20%"> <b> 7 </b> Taxpayer Identification Number (TIN) </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_tin" id="txt1601eq_tin" value="<?=$comprdo['comptin']?>" readonly></td>
                            <td align="right" nowrap width="100"> <b> 8 </b> RDO Code </td>
                            <td width="100"><input type="text" class="form-control input-sm" name="txt1601eq_rdo" id="txt1601eq_rdo" value="<?=$comprdo['comprdo']?>" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="4"> <b> 9 </b> Withholding Agent's Name (Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual) <input type="text" class="form-control input-sm" name="txt1601eq_nme" id="txt1601eq_nme" value="<?=$comprdo['compname']?>" readonly>
                        </tr>
                        <tr>
                            <td colspan="4"> <b> 10 </b> Registered Address <small>(Indicate complete address. If branch, indicate the branch address. If registered address is different from the current address, go to the RDO to update
                            registered address by using BIR Form No. 1905)</small> <input type="text" class="form-control input-sm" name="txt1601eq_add" id="txt1601eq_add" value="<?=substr($comprdo['compadd'],0,40)?>" readonly>
                            
                        </tr>
                        <tr>
                            <td colspan="2"> <input type="text" class="form-control input-sm" name="txt1601eq_add2" id="txt1601eq_add2" value="<?=(strlen($comprdo['compadd']) > 40) ? substr($comprdo['compadd'],40,71) : ""?>" readonly> </td>
                            <td align="right" style="vertical-align: middle"> <b> 10A </b> ZIP Code</td>
                            <td> <input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly> </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <table class="table table-sm table-borderedless" style="margin: 0px !important">
                                    <tr>
                                        <td width="40%" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                            <b> 11 </b> Contact Number
                                            <input type="text" class="form-control input-sm" name="txt1601eq_signum" id="txt1601eq_signum" value="<?=$comprdo['bir_sig_phone']?>">
                                        </td>
                                        <td style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                            <b> 12 </b> Email Address 
                                            <input type="text" class="form-control input-sm" name="txt1601eq_email" id="txt1601eq_email" value="<?=$comprdo['bir_sig_email']?>">
                                        </td>                                       
                                    </tr>
                                </table>
                            </td>                                            
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <div class="row">
                                    <div class="col-md-2" style="padding-top: 5px;">
                                        &nbsp;<b> 13 </b> Taxpayer Classification
                                    </div>
                                    <div class="col-md-10">
                                        <div class="input-group"  style="padding-left: 20px">
                                            <div style="margin-top: 5px">
                                                <ul class="ichecks list-inline" style="margin: 0px !important">

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;Micro&nbsp;&nbsp;</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;Small&nbsp;&nbsp;</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;Medium&nbsp;&nbsp;</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;Large&nbsp;&nbsp;</li>
                                                
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>                                            
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <div class="col-md-12" style="padding-left: 2px !important">
                                    <div class="col-md-3">
                                        <b> 14 </b> Are you availing of tax relief under<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Special Law or International Tax Treaty? 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group"  style="padding-left: 20px">
                                            <div style="margin-top: 5px">
                                                <ul class="ichecks list-inline" style="margin: 0px !important">

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;YES&nbsp;&nbsp;</li>

                                                    <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;NO&nbsp;&nbsp;</li>
                                                
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="padding-top: 9px; text-align: right">
                                       <b> 14A </b> If yes, specify
                                    </div>
                                    <div class="col-md-5" style="padding-top: 5px;">
                                        <input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly>
                                    </div> 
                                </div>
                            </td>                                            
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="4"> <b> Part II - Total TAX Payable</b></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="4" style="padding: 0px !important;"> 
                                <?php
                                
                                ?>
                                <table class="table table-sm table-bordered" style="margin: 0px !important">                               

                                        <tr>
                                            <td style="vertical-align: middle;"><b> 15 </b> Net VAT Payable/(Excess Input Tax) <i>(From Part IV, Item 61)</i></td>                                       
                                            <td>  <input type="text" class="form-control input-sm text-right" name="txt1601eq_totewt" id="txt1601eq_totewt" value="<?=number_format($totEWT,2)?>" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="vertical-align: middle;"><b> <font color="white">15 </font></b> Less: Tax Credits/Payments</td>                                       
                                           
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 16 </b> Creditable VAT Withheld <i>(From Part V - Schedule 3, Column D) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 17 </b> Advance VAT Payments <i>(From Part V - Schedule 4)</i></td>
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less2" id="txt1601eq_less2" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 18 </b> VAT paid in return previously filed, if this is an amended return</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_prev" id="txt1601eq_prev" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 19 </b> Other Credits/Payment <i>(Specify)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_overr" id="txt1601eq_overr" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 20 </b> Total Tax Credits/Payment <i>(Sum of Items 16 to 19) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_otrpay" id="txt1601eq_otrpay" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> 21 </b> Tax Still Payable/(Excess Credits) <i>(Item 15 Less Item 20) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_totrem" id="txt1601eq_totrem" value="0.00" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> <font color="white"> 21 </font></b> Add: Penalties <b>22 </b> Surcharge</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pensur" id="txt1601eq_pensur" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><font color="white"><b> 21 </b> Add: Penalties </font><b>23 </b> Interest</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_penint" id="txt1601eq_penint" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><font color="white"><b> 21 </b> Add: Penalties </font> <b>24 </b> Compromise</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pencom" id="txt1601eq_pencom" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;"><font color="white"><b> 21 </b> Add: Penalties </font> <b>25 </b> Total Penalties <i>(Sum of Items 22 to 24) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pentot" id="txt1601eq_pentot" value="0.00" readonly> </td>
                                        </tr> 
                                        <tr> 
                                            <td style="vertical-align: middle;"><b> 26 TOTAL AMOUNT PAYABLE/(Excess Credits)</b> <i>(Sum of Items 21 and 25)  </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_gtot" id="txt1601eq_gtot" value="<?=number_format($totEWT,2)?>" readonly> </td>
                                        </tr>
                                    
                                </table>
                            </td>
                        </tr>
                        
                        <!--<tr>
                            <td align="center" colspan="4"  style="padding: 0px !important;">
                                <table class="ichecks table table-sm table-bordered" style="margin: 0px !important">
                                    <tr>
                                        <td height="60px" width="50%"> For Individual:</td>
                                        <td> For Non-Individual: </td>
                                    </tr>
                                    <tr>
                                        <td align="center"> Signature over Printed Name of Taxpayer/Authorized Representative/Tax Agent <br> (Indicate Title/Designation and TIN)</td>
                                        <td align="center"> Signature over Printed Name of President/Vice President/ Authorized Officer or
                                        Representative/Tax Agent (Indicate Title/Designation and TIN) </td>
                                    </tr>
                                </table>
                            </td>
                        </tr> -->   
                    </table>
                </div>  

                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="3"> <b> Part IV - Details of VAT Computation</b></td>
                        </tr>
                        <tr>
                            <td align="center" width="25%"> <b> Total Sales and Output Tax </b></td>
                            <td align="center" width="35%"> <b> A. Sales for the Quarter (Exclusive of VAT) </b></td>
                            <td align="center"> <b> B. Output Tax for the Quarter </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 31 </b> VATable Sales </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 32 </b> Zero-Rated Sales </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 33 </b> Exempt Sales </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px"><b> 34 Total Sales & Output Tax Due </b><br><small><i>(Sum of Items 31A to 33A) / (Item 31B)</i></small></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 35 </b> Less: Output VAT on Uncollected Receivable</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 36 </b> Add: Output VAT on Recovered Uncollected Receivables Previously Deducted</td>
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less2" id="txt1601eq_less2" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 37 Total Adjusted Output Tax Due </b> <i>(Item 34B Less Item 35B Add Item 36B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_prev" id="txt1601eq_prev" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Less: Allowable Input Tax </b></td>
                            <td align="center"> <b> B. Input Tax </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 38 </b> Input Tax Carried Over from Previous Quarter </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 39 </b> Input Tax Deferred on Capital Goods Exceeding P1 Million from Previous Quarter <i>(From Part V - Schedule 1 Col E)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 40 </b> Transitional Input Tax</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 41 </b> Presumptive Input Tax</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 42 </b> Others <i>(Specify)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 43 Total</b> <i>(Sum of Items 38B to 42B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr style="margin-top: 10px">
                            <td align="center" width="25%"> <b> Current Transactions </b></td>
                            <td align="center" width="35%"> <b> A. Purchases </b></td>
                            <td align="center"> <b> B. Input Tax </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 44 </b> Domestic Purchases </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 45 </b> Services Rendered by Non-Residents </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 46 </b> Importations </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 47 </b> Others <i>(Specify)</i></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 48 </b> Domestic Purchases with No Input Tax </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 49 </b> VAT- Exempt Importations </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px"><b> 50 </b> Total Current Purchases/Input Tax<br><small><i>(Sum of Items 44A to 49A)/(Sum of Items 44B to 47B)</i></small></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px" colspan="2"><b> 51 Total Available Input Tax </b><i>(Sum of Items 43B and 50B)</i></td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly></td>
                           
                        </tr>
                        <tr>
                            <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Less: Adjustment/Deductions from Input Tax </b></td>
                            <td align="center"> <b> B. Input Tax </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px" colspan="2"><b> 52 </b> Input Tax on Purchases/Importation of Capital Goods exceeding P1 Million deferred for the succeeding period<br><small><i>(From Part V Schedule 1, Column I)</i></small> </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 53 </b> Input Tax Attributable to VAT Exempt Sales <i>(From Part V - Schedule 2)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 54 </b> VAT Refund/TCC Claimed </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 55 </b> Input VAT on Unpaid Payables </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 56 </b> Others <i>(Specify)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 57 </b> Total Deductions from Input Tax <i>(Sum of Items 52B to 56B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 58 </b> Add: Input VAT on Settled Unpaid Payables Previously Deducted</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 59 </b> Adjusted Deductions from Input Tax <i>(Sum of Items 57B and 58B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 60 </b> Total Allowable Input Tax <i>(Item 51B Less Item 59B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 61 Net VAT Payable/(Excess Input Tax)</b> <i>(Item 37B Less Item 60B) (To Part II, Item 15)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                        </tr>
                    </table>
                </div>
                
            </div>
        </div>
               
        <br><br><br><br>
    </form>



</body>
</html>

<script type="text/javascript">
    var sawt = [];
    $(document).ready(function(){

        $(".xcompute").autoNumeric('init',{mDec:2});
        $(".xcompute").on("click", function () {
            $(this).select();
        });

        $(".ichecks input").iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
       // $(".birforms").hide();

        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        })

        $("#selfrmname").select2({
            placeholder: "Please select a form"
        }); 

        $("#selfil").on("change", function(){
            $x = $(this).val();
            if($x=="Monthly"){
                $("#divqr").hide();
                $("#divmn").show();
            }else if($x=="Quarterly"){
                $("#divqr").show();
                $("#divmn").hide();
            }else if($x=="Annually"){
                $("#divqr").hide();
                $("#divmn").hide();
            }
        });

        $("#selfrmname").on("change", function(){
            $xc = $(this).find(':selected').attr('data-param')

            $('.birforms').each(function(i, obj) {
                if($(this).attr("id")==$xc){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            });
            
        });
        

        $("#btnView").on("click", function(){
            $("#frmBIRForm").attr("action", $("#selfrmname").val());
            $("#frmBIRForm").submit();
        });

        $(".xcompute").on("keyup", function(){   
            $TotalTaxesWithheld = $("#txt1601eq_totewt").val().replace(/,/g,'');

            $less1 = ($("#txt1601eq_less1").val()=="") ? 0 : $("#txt1601eq_less1").val().replace(/,/g,'');
            $less2 = ($("#txt1601eq_less2").val()=="") ? 0 : $("#txt1601eq_less2").val().replace(/,/g,'');
            $taxrmmited = ($("#txt1601eq_prev").val()=="") ? 0 : $("#txt1601eq_prev").val().replace(/,/g,'');
            $overremit = ($("#txt1601eq_overr").val()=="") ? 0 : $("#txt1601eq_overr").val().replace(/,/g,''); 
            $othrpay = ($("#txt1601eq_otrpay").val()=="") ? 0 : $("#txt1601eq_otrpay").val().replace(/,/g,'');

            $totrem = parseFloat($less1) + parseFloat($less2) + parseFloat($taxrmmited) + parseFloat($overremit) + parseFloat($othrpay);
            $("#txt1601eq_totrem").val($totrem);
            $("#txt1601eq_totrem").autoNumeric('destroy');
			$("#txt1601eq_totrem").autoNumeric('init',{mDec:2});


            $totsdue = parseFloat($TotalTaxesWithheld) - parseFloat($totrem);
            $("#txt1601eq_taxdue").val($totsdue);
            $("#txt1601eq_taxdue").autoNumeric('destroy');
			$("#txt1601eq_taxdue").autoNumeric('init',{mDec:2});


            $penaltysur = ($("#txt1601eq_pensur").val()=="") ? 0 : $("#txt1601eq_pensur").val().replace(/,/g,'');
            $penaltyint = ($("#txt1601eq_penint").val()=="") ? 0 : $("#txt1601eq_penint").val().replace(/,/g,'');
            $penaltycom = ($("#txt1601eq_pencom").val()=="") ? 0 : $("#txt1601eq_pencom").val().replace(/,/g,'');

            $totpenalty = parseFloat($penaltysur) + parseFloat($penaltyint) + parseFloat($penaltycom);
            $("#txt1601eq_pentot").val($totpenalty);
            $("#txt1601eq_pentot").autoNumeric('destroy');
			$("#txt1601eq_pentot").autoNumeric('init',{mDec:2});

            txt1601eq_gtot = $totsdue + $totpenalty;
            $("#txt1601eq_gtot").val(txt1601eq_gtot);
            $("#txt1601eq_gtot").autoNumeric('destroy');
			$("#txt1601eq_gtot").autoNumeric('init',{mDec:2});
        });
        
    })

</script>