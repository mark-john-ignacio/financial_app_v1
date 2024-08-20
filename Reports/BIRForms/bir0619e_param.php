<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    // print_r($_POST);
    // print_r($_SESSION);

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
                            <td align="center" width="150px"> BIR FORM No.<h3 class="nopadding">0619-E</h3>January 2018 (ENCS)<br>Page 1</td>
                            <td align="center" style="vertical-align: middle !important;"><h3 class="nopadding">Monthy Remittance Form</h3><h4 class="nopadding">of Creditable Income Taxes Withheld (Expanded)</h4></td>
                            <td align="center" width="200px" style="vertical-align: middle !important;"><img src="../../bir_forms/hdr1601eq.jpg" width="100%"> </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 0px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" width="200px">
                                <b>1.</b> For the Month of
                                <input type="text" class="form-control input-sm" name="txt1601eq_yr" id="txt1601eq_yr" value="<?=$year?>" readonly>
                                <input type="text" class="form-control input-sm" name="txt1601eq_yr" id="txt1601eq_yr" value="<?=$year?>" readonly>
                            </td>
                            <td align="center">
                                <b>2.</b> Quarter
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr1" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt1601eq_qrtr1">&nbsp;1st</li>

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr2" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt1601eq_qrtr2">&nbsp;2nd</li>

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr3" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==3) ? "checked" : "disabled"?> value="3"><label for="txt1601eq_qrtr3">&nbsp;3rd</li>

                                            <li><input tabindex="3" type="radio" id="txt1601eq_qrtr4" name="txt1601eq_qrtr" <?=($_POST['selqrtr']==4) ? "checked" : "disabled"?> value="4"><label for="txt1601eq_qrtr4">&nbsp;4th</li>
                                        
                                        </ul>

                                    </div>
                                </div>
                            </td>
                            <td align="center" width="150px"><b>3.</b> Amended Return?
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndY" name="txt1601eq_amnd" value="Y"><label for="txt1601eq_amndY">&nbsp;YES</li>
                                            
                                            <li><input tabindex="3" type="radio" id="txt1601eq_amndN" name="txt1601eq_amnd" value="N" checked><label for="txt1601eq_amndN">&nbsp;NO</li>

                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td align="center" width="150px"><b>4.</b> Any Taxes Withheld?
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt1601eq_anyY" name="txt1601eq_anytx" value="Y"><label for="txt1601eq_anyY">&nbsp;YES</li>
                                            
                                            <li><input tabindex="3" type="radio" id="txt1601eq_anyN" name="txt1601eq_anytx" value="N" checked><label for="txt1601eq_anyN">&nbsp;NO</li>

                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td align="center" width="150px">
                                <b>4.</b> No. of Sheets/Attached
                                <input type="number" class="form-control input-sm" name="txt1601eq_nosheets" id="txt1601eq_nosheets">
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
                            <td width="20%"> <b> 6 </b> Taxpayer Identification Number (TIN) </td>
                            <td><input type="text" class="form-control input-sm" name="txt1601eq_tin" id="txt1601eq_tin" value="<?=$comprdo['comptin']?>" readonly></td>
                            <td align="right" nowrap width="100"> <b> 7 </b> RDO Code </td>
                            <td width="100"><input type="text" class="form-control input-sm" name="txt1601eq_rdo" id="txt1601eq_rdo" value="<?=$comprdo['comprdo']?>" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="4"> <b> 8 </b> Withholding Agent's Name (Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual) <input type="text" class="form-control input-sm" name="txt1601eq_nme" id="txt1601eq_nme" value="<?=$comprdo['compname']?>" readonly>
                        </tr>
                        <tr>
                            <td colspan="4"> <b> 9 </b> Registered Address <small>(Indicate complete address. If branch, indicate the branch address. If registered address is different from the current address, go to the RDO to update
                            registered address by using BIR Form No. 1905)</small> <input type="text" class="form-control input-sm" name="txt1601eq_add" id="txt1601eq_add" value="<?=substr($comprdo['compadd'],0,40)?>" readonly>
                            
                        </tr>
                        <tr>
                            <td colspan="2"> <input type="text" class="form-control input-sm" name="txt1601eq_add2" id="txt1601eq_add2" value="<?=(strlen($comprdo['compadd']) > 40) ? substr($comprdo['compadd'],40,71) : ""?>" readonly> </td>
                            <td align="right" style="vertical-align: middle"> <b> 9A </b> ZIP Code</td>
                            <td> <input type="text" class="form-control input-sm" name="txt1601eq_zip" id="txt1601eq_zip" value="<?=$comprdo['compzip']?>" readonly> </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <table class="table table-sm table-borderedless" style="margin: 0px !important">
                                    <tr>
                                        <td width="200px" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                            <b> 10 </b> Contact Number
                                        </td>
                                        <td width="250px" style="border-right: 1px solid #dddddd !important"> 
                                            <input type="text" class="form-control input-sm" name="txt1601eq_signum" id="txt1601eq_signum" value="<?=$comprdo['bir_sig_phone']?>"> 
                                        </td>
                                        <td width="250px" style="vertical-align: middle;">
                                            <b> 11 </b> Category of Withholding Agent
                                        </td>
                                        <td style="vertical-align: middle;"> 
                                            <div class="input-group">
                                                <ul class="ichecks list-inline" style="margin: 0px !important">
                                                    <li><input tabindex="3" type="radio" id="txt1601eq_catP" name="txt1601eq_cat" value="P" checked><label for="txt1601eq_catP">&nbsp;PRIVATE</li>
                                                    
                                                    <li><input tabindex="3" type="radio" id="txt1601eq_catG" name="txt1601eq_cat" value="G"><label for="txt1601eq_catG">&nbsp;GOVERNMENT</li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td> 
                            <tr>
                                <td style="vertical-align: middle;"> <b> 12 </b> Email Address </td>
                                <td colspan="3"><input type="text" class="form-control input-sm" name="txt1601eq_email" id="txt1601eq_email" value="<?=$comprdo['bir_sig_email']?>"></td>
                            
                            </tr>                                             
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="4"> <b> Part II - Computation of Tax</b></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="4" style="padding: 0px !important;"> 
                                <?php
                                
                                ?>
                                <table class="table table-sm table-bordered" style="margin: 0px !important">
                                    <tr>
                                        <td align="center" width="20px">&nbsp;</td>
                                        <td align="center" width="150px"> <b> ATC </b> </td>
                                        <td align="center" nowrap> <b> Tax Base (Consolidated for the Quarter) </b> </td>
                                        <td align="center" width="150px"> <b> Tax Rate </b> </td>
                                        <td align="center" nowrap> <b> Tax Withheld (Consolidated for the Quarter) </b> </td>
                                    </tr>
                                    <?php
                                        $rowcnt = 0;

                                        $cnt = 12;
                                        $totEWT = 0;
                                        foreach($apv as $row){
                                            $cnt++;   
                                            $rowcnt++; 

                                            $xngross = floatval($row['ncredit']) / (floatval($row['newtrate'])/100);
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><b> <?=$cnt?> </b></td>
                                            <td> <input type="text" class="form-control input-sm" name="txt1601eq_atc<?=$rowcnt?>" id="txt1601eq_atc<?=$rowcnt?>" value="<?=$row['cewtcode']?>" readonly>  </td>
                                            <td>  <input type="text" class="form-control input-sm text-right" name="txt1601eq_gross<?=$rowcnt?>" id="txt1601eq_gross<?=$rowcnt?>" value="<?=number_format($xngross,2)?>" readonly> </td>
                                            <td>  <input type="text" class="form-control input-sm text-right" name="txt1601eq_rate<?=$rowcnt?>" id="txt1601eq_rate<?=$rowcnt?>" value="<?=number_format($row['newtrate'],2)?>" readonly> </td>
                                            <td>  <input type="text" class="form-control input-sm text-right" name="txt1601eq_tax<?=$rowcnt?>" id="txt1601eq_tax<?=$rowcnt?>" value="<?=number_format($row['ncredit'],2)?>" readonly> </td>
                                        </tr>
                                    <?php

                                            $totEWT += floatval($row['ncredit']);
                                        }
                                    ?>

                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 19 </b> Total Taxes Withheld for the Quarter <i>(Sum of Items 13 to 18) </i></td>                                       
                                            <td>  <input type="text" class="form-control input-sm text-right" name="txt1601eq_totewt" id="txt1601eq_totewt" value="<?=number_format($totEWT,2)?>" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 20 </b> Less: Remittances Made: 1st Month of the Quarter </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less1" id="txt1601eq_less1" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 21 </b> <font color="white">Less: Remittances Made: </font>2nd Month of the Quarter</td>
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_less2" id="txt1601eq_less2" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 22 </b> Tax Remitted in Return Previously Filed, if this is an amended return</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_prev" id="txt1601eq_prev" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 23 </b> Over-remmitance from Previous Quarter of the same taxable year</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_overr" id="txt1601eq_overr" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 24 </b> Other Payments Made <i>(please attach proof of payments - BIR Form No. 0605)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_otrpay" id="txt1601eq_otrpay" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 25 </b> Total Remittances Made (Sum of Items 20 to 24)</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_totrem" id="txt1601eq_totrem" value="0.00" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 26 </b> Tax Still Due/(Over-remittance) (Item 19 Less Item 25)</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_taxdue" id="txt1601eq_taxdue" value="<?=number_format($totEWT,2)?>" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;">Add: Penalties <b> 27 </b> Surcharge</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pensur" id="txt1601eq_pensur" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 28 </b> Interest</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_penint" id="txt1601eq_penint" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 29 </b> Compromise</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pencom" id="txt1601eq_pencom" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 30 </b> Total Penalties <i>(Sum of Items 27 to 29) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_pentot" id="txt1601eq_pentot" value="0.00" readonly> </td>
                                        </tr> 
                                        <tr> 
                                            <td colspan="4" style="vertical-align: middle;"><b> 31 TOTAL AMOUNT STILL DUE</b> /(Over-remittance) <i>((Sum of Items 26 and 30)) </i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt1601eq_gtot" id="txt1601eq_gtot" value="<?=number_format($totEWT,2)?>" readonly> </td>
                                        </tr>
                                    
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="4"  style="padding: 0px !important;">
                                <table class="ichecks table table-sm table-borderless" style="margin: 0px !important">
                                    <tr>
                                        <td align="center" nowrap style="vertical-align: middle;"> If over-remittance, mark one (1) box only</td>
                                        <td align="center" width="10px" style="vertical-align: middle"> <input tabindex="3" type="checkbox" id="txt1601eq_ifovr1" name="txt1601eq_ifovr1" value="1"> </td>
                                        <td style="vertical-align: middle">&nbsp;To be refunded </td>
                                        <td align="center" width="10px" style="vertical-align: middle"> <input tabindex="3" type="checkbox" id="txt1601eq_ifovr2" name="txt1601eq_ifovr2" value="1"> </td> 
                                        <td style="vertical-align: middle">&nbsp;To be issued Tax Credit Certificate </td>
                                        <td align="center" width="10px" style="vertical-align: middle"> <input tabindex="3" type="checkbox" id="txt1601eq_ifovr3" name="txt1601eq_ifovr3" value="1"> </td>
                                        <td style="vertical-align: middle">&nbsp;To be carried over to the next quarter within the same calendar year (not applicable for succeeding year </td>
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
                <!-- <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center"> <b> Part III - Details of Payment</b></td>
                        </tr>
                        <tr>
                            <td align="center" style="padding: 0px !important;">
                                <table class="table table-sm table-bordered" style="margin: 0px !important">
                                    <tr>
                                        <td align="center">Particulars</td>
                                        <td align="center">Drawee Bank/Agency</td>
                                        <td align="center">Number</td>
                                        <td align="center">Date(MM/DD/YYYY)</td>
                                        <td align="center">Amount</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle"><b>31 </b>Cash/Bank Debit Memo</td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_casdm1" id="txt1601eq_casdm1" value=""></td>
                                        <th><input type="text" class="form-control input-sm" name="txt1601eq_casdm2" id="txt1601eq_casdm2" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_casdm3" id="txt1601eq_casdm3" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_casdm4" id="txt1601eq_casdm4" value=""></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle"><b>32 </b>Check</td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_chk1" id="txt1601eq_chk1" value=""></td>
                                        <th><input type="text" class="form-control input-sm" name="txt1601eq_chk2" id="txt1601eq_chk2" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_chk3" id="txt1601eq_chk3" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_chk4" id="txt1601eq_chk4" value=""></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle" colspan="2"><b>33 </b>Tax Debit Memo</td>
                                        <th><input type="text" class="form-control input-sm" name="txt1601eq_txdm1" id="txt1601eq_txdm1" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_txdm2" id="txt1601eq_txdm2" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_txdm3" id="txt1601eq_txdm3" value=""></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle" colspan="5"><b>34 </b>Others (specify below)</td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_othr0" id="txt1601eq_othr0" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_othr1" id="txt1601eq_othr1" value=""></td>
                                        <th><input type="text" class="form-control input-sm" name="txt1601eq_othr2" id="txt1601eq_othr2" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_othr3" id="txt1601eq_othr3" value=""></td>
                                        <td><input type="text" class="form-control input-sm" name="txt1601eq_othr4" id="txt1601eq_othr4" value=""></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>  -->
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