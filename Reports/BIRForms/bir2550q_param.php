<?php 
    include 'layouts/default.php';

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

    // $year = date("Y", strtotime($_POST['years']));
    $year = $_POST['years'];
    
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
    
    // echo $sql."<br>";
    $query = mysqli_query($con, $sql);               
    while($row = $query -> fetch_assoc()){
        $apv[] = $row;
    }
?>



<body>
<form action="#" name="frmpos" id="frmpos" method="post" target="_blank" data-api-url="<?= $UrlBase . "system_management/api/pdf/2550q"?>">
<!-- <input  class="btn btn-primary" type="button" value="Get Values" onclick="logFormValues()"> -->
        <div class="container">
            <br>
            <div class="row">
                <div class="col-sm-10">
                &nbsp;
                </div>
                <div class="col-sm-2">
                    <!-- <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-print"></i>&nbsp;PRINT PDF</button> -->
                    <button type="button" id="btnPrintPdf" class="btn btn-success btn-sm btn-block">
                        <i class="fa fa-print"></i>&nbsp;PRINT PDF
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12"><img src="../../bir_forms/birheader.jpg" width="100%"></div>

                <div class="col-12" style="padding-top: 5px; padding-bottom: 0px">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" width="150px"> BIR FORM No.<h3 class="nopadding">2550-Q</h3>April 2024 (ENCS)<br>Page 1</td>
                            <td align="center" style="vertical-align: middle !important;"><h3 class="nopadding">Quarterly Value Added Tax (VAT) Return</h3><h4 class="nopadding">of Creditable Income Taxes Withheld (Expanded)</h4></td>
                            <td align="center" width="200px" style="vertical-align: middle !important;"><img src="../../bir_forms/hdr1601eq.jpg" width="100%"> </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-12" style="margin-top: 0px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" width="">                    
                                <div class="input-group">
                                    <div style="display: flex; align-items: center; margin-top: 13px;">
                                        <b>1.</b> For the
                                        <div style="margin-left: 10px;">
                                            <ul class="ichecks list-inline" style="margin: 0px !important">

                                                <li><input tabindex="3" type="radio" id="txt2550q_accountingperiods1" name="txt2550q_accountingperiods" <?=($comprdo['reporting_period_type']=="calendar") ? "checked" : "disabled"?> value="C"><label for="txt2550q_accountingperiods1">&nbsp;Calendar</li>

                                                <li><input tabindex="3" type="radio" id="txt2550q_accountingperiods2" name="txt2550q_accountingperiods" <?=($comprdo['reporting_period_type']=="fiscal") ? "checked" : "disabled"?> value="F"><label for="txt2550q_accountingperiods2">&nbsp;Fiscal</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td align="center">
                                <div class="input-group">
                                    <div style="display: flex; align-items: center; margin-top: 10px;"> 
                                        <b>2.</b> Year Ended (MM/YYYY)
                                        <div style="margin-left: 10px;">
                                            <input type="text" class="form-control input-sm" name="txt2550q_year_end_M" id="txt2550q_year_end_M" value="" placeholder="MM" style="text-align: center; Width: 60px" readonly>
                                            <input type="text" class="form-control input-sm" name="txt2550q_year_end_Y" id="txt2550q_year_end_Y" value="" placeholder="YYYY" style="text-align: center; Width: 100px" readonly>
                                            <!-- <input type="month"  class="form-control input-sm" id="" name="" style="text-align: center; Width: 150px"> -->
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td align="center">
                                <b>3.</b> Quarter
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt2550q_qrtr1" name="txt2550q_qrtr" <?=($_POST['selqrtr']==1) ? "checked" : "disabled"?> value="1"><label for="txt2550q_qrtr1">&nbsp;1st</li>

                                            <li><input tabindex="3" type="radio" id="txt2550q_qrtr2" name="txt2550q_qrtr" <?=($_POST['selqrtr']==2) ? "checked" : "disabled"?> value="2"><label for="txt2550q_qrtr2">&nbsp;2nd</li>

                                            <li><input tabindex="3" type="radio" id="txt2550q_qrtr3" name="txt2550q_qrtr" <?=($_POST['selqrtr']==3) ? "checked" : "disabled"?> value="3"><label for="txt2550q_qrtr3">&nbsp;3rd</li>

                                            <li><input tabindex="3" type="radio" id="txt2550q_qrtr4" name="txt2550q_qrtr" <?=($_POST['selqrtr']==4) ? "checked" : "disabled"?> value="4"><label for="txt2550q_qrtr4">&nbsp;4th</li>
                                        
                                        </ul>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-12" style="margin-top: 0px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                           <td align="center">
                                <div class="input-group">
                                    <div style="display: flex; align-items: center; margin-top: 10px; flex-wrap: wrap; justify-content: center;">
                                        <b>4.</b>
                                        <span style="margin-right: 10px;">Return Period (MM/DD/YYYY)</span>
                                        <div style="display: flex; align-items: center;">
                                            <label for="from" style="margin-right: 5px;">From:</label>
                                            <input type="text" class="form-control input-sm" name="return_preiod_from" id="from" value="" placeholder="MM/DD/YYYY" style="text-align: center; width: 150px; margin-right: 10px;" readonly>
                                            <label for="to" style="margin-right: 5px;">to:</label>
                                            <input type="text" class="form-control input-sm" name="return_preiod_to" id="to" value="" placeholder="MM/DD/YYYY" style="text-align: center; width: 150px;" readonly>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td align="center" width="23%">
                                <b>5.</b> Amended Return?
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt2550q_amndY" name="txt2550q_amnd" value="Y"><label for="txt2550q_amndY">&nbsp;YES</li>
                                            
                                            <li><input tabindex="3" type="radio" id="txt2550q_amndN" name="txt2550q_amnd" value="N" checked><label for="txt2550q_amndN">&nbsp;NO</li>

                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td align="center" width="23%">
                                <b>6.</b> Short Period Return?
                                <div class="input-group">
                                    <div style="margin-top: 5px">
                                        <ul class="ichecks list-inline" style="margin: 0px !important">
                                            <li><input tabindex="3" type="radio" id="txt2550q_sprY" name="txt2550q_spr" value="Y"><label for="txt2550q_sprY">&nbsp;YES</li>
                                            <li><input tabindex="3" type="radio" id="txt2550q_srpN" name="txt2550q_spr" value="N" checked><label for="txt2550q_srpN">&nbsp;NO</li>
                                        </ul>
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
                            <td width="20%" style="text-align: center; vertical-align: middle;">
                                <b>6</b> Taxpayer Identification Number (TIN)
                            </td>
                            <td><input type="text" class="form-control input-sm" name="txt2550q_tin" id="txt2550q_tin" value="<?=$comprdo['comptin']?>" readonly></td>
                            <td width="15%" style="text-align: center; vertical-align: middle;">
                                <b> 7 </b> RDO Code 
                            </td>
                            <td width="100"><input type="text" class="form-control input-sm" name="txt2550q_rdo" id="txt2550q_rdo" value="<?=$comprdo['comprdo']?>" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="4"> <b> 8 </b> Taxpayer’s Name  (Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual) <input type="text" class="form-control input-sm" name="txt2550q_taxpayer_name" id="txt2550q_taxpayer_name" value="<?=$comprdo['compname']?>" readonly>
                        </tr>
                        <tr>
                            <td colspan="4">
                                 <b> 9 </b> Registered Address 
                                 <small>(Indicate complete address. If branch, indicate the branch address. If registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small>
                                <input type="text" class="form-control input-sm" name="txt2550q_add" id="txt2550q_add" value="<?=substr($comprdo['compadd'],0,40)?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"> 
                                <input type="text" class="form-control input-sm" name="txt2550q_add2" id="txt2550q_add2" value="<?=(strlen($comprdo['compadd']) > 40) ? substr($comprdo['compadd'],40,71) : ""?>" readonly> 
                            </td>
                            <td align="right" style="vertical-align: middle"> <b> 10A </b> ZIP Code</td>
                            <td> <input type="text" class="form-control input-sm" name="txt2550q_zip" id="txt2550q_zip" value="<?=$comprdo['compzip']?>" readonly> </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <table class="table table-sm table-borderedless" style="margin: 0px !important">
                                    <tr>
                                        <td width="40%" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                            <b> 11 </b> Contact Number (Landline/Cellphone No.)
                                            <input type="text" class="form-control input-sm" name="txt2550q_signum" id="txt2550q_signum" value="<?=$comprdo['bir_sig_phone']?>"> 
                                        </td>
                                        <td width="60%" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                            <b> 12 </b> Email Address 
                                            <input type="text" class="form-control input-sm" name="txt2550q_email" id="txt2550q_email" value="<?=$comprdo['bir_sig_email']?>">
                                        </td>
                                    </tr>
                                </table>
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" width="" colspan="4">                    
                                <div class="input-group">
                                    <div style="display: flex; align-items: center; padding: 10px;">
                                        <b> 13 </b> Tax Payer Classification
                                        <div style="margin-left: 10px;">
                                            <ul class="ichecks list-inline" style="margin: 0px !important">
                                                <li><input tabindex="3" type="radio" id="txt2550q_tax_payer_classification1" name="txt2550q_tax_payer_classification" <?=($comprdo['taxpayer_size_class']=="Micro") ? "checked" : "disabled"?> value="micro"><label for="txt2550q_tax_payer_classification1">&nbsp;Mirco</li>

                                                <li><input tabindex="3" type="radio" id="txt2550q_tax_payer_classification2" name="txt2550q_tax_payer_classification" <?=($comprdo['taxpayer_size_class']=="Small") ? "checked" : "disabled"?> value="small"><label for="txt2550q_tax_payer_classification2">&nbsp;Small</li>

                                                <li><input tabindex="3" type="radio" id="txt2550q_tax_payer_classification3" name="txt2550q_tax_payer_classification" <?=($comprdo['taxpayer_size_class']=="Medium") ? "checked" : "disabled"?> value="medium"><label for="txt2550q_tax_payer_classification3">&nbsp;Medium</li>

                                                <li><input tabindex="3" type="radio" id="txt2550q_tax_payer_classification4" name="txt2550q_tax_payer_classification" <?=($comprdo['taxpayer_size_class']=="Large") ? "checked" : "disabled"?> value="Large"><label for="txt2550q_tax_payer_classification4">&nbsp;Large</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <td colspan="4" style="padding: 0px !important"> 
                            <table class="table table-sm table-borderedless" style="margin: 0px !important">
                                <tr>
                                    <td width="35%" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                        <div style="display: flex; align-items: center;">
                                            <b>14</b>
                                            <span style="margin-left: 8px;">Are you availing of tax relief under Special Law or International Tax Treaty?</span>
                                            <div style="margin-left: auto;">
                                                <ul class="ichecks list-inline" style="margin: 0; padding: 0; list-style-type: none; display: flex; align-items: center;">
                                                    <li style="display: flex; align-items: center; margin-right: 15px;">
                                                        <input tabindex="3" type="radio" id="txt2550q_14Y" name="txt2550q_14" value="Y">
                                                        <label for="txt2550q_14Y" style="margin-left: 5px; margin-top: 6px">YES</label>
                                                    </li>
                                                    <li style="display: flex; align-items: center;">
                                                        <input tabindex="3" type="radio" id="txt2550q_14N" name="txt2550q_14" value="N" checked>
                                                        <label for="txt2550q_14N" style="margin-left: 5px; margin-top: 6px">NO</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="70%" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                        <div style="display: flex; align-items: center; width: 100%;">
                                            <b>14A &nbsp;</b> if yes, specify
                                            <div style="flex: 1; margin-left: 10px;">
                                                <input type="text" class="form-control input-sm" name="txt2550q_14A" id="txt2550q_14A" value="" style="width: 100%;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td> 
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
                                    <!-- <tr>
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
                                    ?> -->

                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 15 </b> Net VAT Payable/(Excess Input Tax) <i>(From Part IV, Item 61) </i></td>                                       
                                            <td>  <input type="text" class="form-control input-sm text-right xcompute" name="net_vat_payable" id="net_vat_payable" value="" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="vertical-align: middle;">&emsp; &emsp;Less: Tax Credits/Payments</td>                                       
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 16 </b>  Creditable VAT Withheld <i>(From Part V - Schedule 3, Column D)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="creditable_vat_withhelding" id="creditable_vat_withhelding" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 17 </b>  Advance VAT Payments <i> (From Part V - Schedule 4)</i></td>
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="advance_vat_payments" id="advance_vat_payments" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 18 </b>   VAT paid in return previously filed, if this is an amended return </td>
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="txt2550q_18" id="txt2550q_18" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle; ">
                                                <div style="display: flex; align-items: center;">
                                                    <b style="margin-right: 2px;">19</b> Other Credits/Payment (Specify)
                                                    <input type="text" class="form-control input-sm" name="specify" id="specify" value="" style=" margin: 3px 3px 3px 10px; width: 70%;">
        
                                                </div>
                                            </td>                                    
                                            <td  style="vertical-align: middle;;">  
                                                <input type="text" class="xcompute form-control input-sm text-right" name="other_credits_payment" id="other_credits_payment" value="0.00"> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 20 </b> Total Tax Credits/Payment <i> (Sum of Items 16 to 19)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_tax_credits_payments" id="total_tax_credits_payments" value="0.00" readonly> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><b> 21 </b> Tax Still Payable/(Excess Credits) <i>(Item 15 Less Item 20)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="tax_still_payable" id="tax_still_payable" value="0.00" readonly> </td>
                                        </tr>
            
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;">Add: Penalties <b> 22 </b> Surcharge</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="surcharge" id="surcharge" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 23 </b> Interest</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="interest" id="interest" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 24 </b> Compromise</td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="compromise" id="compromise" value="0.00"> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 25 </b> Total Penalties <i>(Sum of Items 22 to 24)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_penalties" id="total_penalties" value="0.00" readonly> </td>
                                        </tr> 
                                        <tr> 
                                            <td colspan="4" style="vertical-align: middle;"><b> 26 TOTAL AMOUNT PAYABLE/(Excess Credits)</b> <i>(Sum of Items 21 and 25)</i></td>                                       
                                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_amount_payable" id="total_amount_payable" value="0.00" readonly> </td>
                                        </tr>
                                    
                                </table>
                            </td>
                        </tr>
                        <!-- <tr>
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
                        </tr> -->
                        <!-- <tr>
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
                        </tr>    -->
                        <!-- <div class="col-12" style="margin-top: 0 !important;">
                            <table class="table table-sm table-bordered" style="margin: 0 !important;">
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; margin-top: 5px; width: 100%;">
                                            <span style="flex: 1; padding-left: 0; text-align: center">Tax Agent Accreditation No./Attorney’s Roll No.<i>(If applicable)</i></span>
                                            <div style="flex: 1.7; margin-left: 10px;">
                                                <input type="text" class="form-control input-sm" name="txt1601eq_email" id="txt1601eq_email" value="" style="width: 100%;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; margin-top: 5px; width: 100%;">
                                            <div style="flex: 1; text-align: center">
                                                <div>Date of Issue</div>
                                                <div style="font-style: italic;">(MM/DD/YYYY)</div>
                                            </div>
                                            <div style="flex: 2; margin-left: 10px;">
                                                <input type="text" class="form-control input-sm" name="txt1601eq_yr" id="txt1601eq_yr" value="" placeholder="MM/YYYY" style="text-align: center; width: 100%;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; margin-top: 5px; width: 100%;">
                                            <div style="flex: 1; text-align: center">
                                                <div>Expiry Date</div>
                                                <div style="font-style: italic;">(MM/DD/YYYY)</div>
                                            </div>
                                            <div style="flex: 2; margin-left: 10px;">
                                                <input type="text" class="form-control input-sm" name="txt1601eq_yr" id="txt1601eq_yr" value="" placeholder="MM/YYYY" style="text-align: center; width: 100%;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div> -->
                    </table>
                <!-- </div>   -->

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
        <!-- </div> -->
    <!-- </div> -->
               
        <!-- <br><br><br><br> -->

                <!-- ==================== START OF PAGE 2 ==================== -->

    <!-- <div class="container"> -->
            <!-- <br> -->

            <!-- <div class="row mt-2"> -->
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
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="vatable_sales_A" id="vatable_sales_A" value="0.00" ></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="vatable_sales_B" id="vatable_sales_B" value="0.00" ></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 32 </b> Zero-Rated Sales </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="zero_rated_sales" id="zero_rated_sales" value="0.00"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 33 </b> Exempt Sales </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="exempt_sales" id="exempt_sales" value="0.00"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px"><b> 34 Total Sales & Output Tax Due </b><br><small><i>(Sum of Items 31A to 33A) / (Item 31B)</i></small></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="total_sales_output_tax_due_A" id="total_sales_output_tax_due_A" value="0.00" readonly></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="total_sales_output_tax_due_B" id="total_sales_output_tax_due_B" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 35 </b> Less: Output VAT on Uncollected Receivable</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="output_vat_on_uncollected_recievable" id="output_vat_on_uncollected_recievable" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 36 </b> Add: Output VAT on Recovered Uncollected Receivables Previously Deducted</td>
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="output_vat_on_recovered_uncollected_recievable" id="output_vat_on_recovered_uncollected_recievable" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 37 Total Adjusted Output Tax Due </b> <i>(Item 34B Less Item 35B Add Item 36B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_adjusted_output_tax_due" id="total_adjusted_output_tax_due" value="0.00" readonly> </td>
                        </tr>
                        <tr>
                            <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Less: Allowable Input Tax </b></td>
                            <td align="center"> <b> B. Input Tax </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 38 </b> Input Tax Carried Over from Previous Quarter </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_tax_carreid_over_from_previous_quarter" id="input_tax_carreid_over_from_previous_quarter" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 39 </b> Input Tax Deferred on Capital Goods Exceeding P1 Million from Previous Quarter <i>(From Part V - Schedule 1 Col E)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_tax_deferred_on_capital_goods" id="input_tax_deferred_on_capital_goods" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 40 </b> Transitional Input Tax</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="transitional_input_tax" id="transitional_input_tax" value="0.00"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 41 </b> Presumptive Input Tax</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="presumptive_input_tax" id="presumptive_input_tax" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2">
                                <div style="display: flex; align-items: center;">
                                    <b style="margin-right: 2px;"> 42 </b> Others <i>(Specify)</i>
                                    <input type="text" class="form-control input-sm" name="others_42_txt" id="others_42" value="" style=" margin: 3px 3px 3px 10px; width: 80%;">
                                </div>
                            </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="others_42_num" id="others_42_num" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 43 Total</b> <i>(Sum of Items 38B to 42B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_43" id="total_43" value="0.00" readonly> </td>
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
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="domestic_purchases_A" id="domestic_purchases_A" value="0.00" ></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="domestic_purchases_B" id="domestic_purchases_B" value="0.00" ></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 45 </b> Services Rendered by Non-Residents </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="services_rendered_by_non_resident_A" id="services_rendered_by_non_resident_A" value="0.00" ></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="services_rendered_by_non_resident_B" id="services_rendered_by_non_resident_B" value="0.00" ></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 46 </b> Importations </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="importations_A" id="importations_A" value="0.00" ></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="importations_B" id="importations_B" value="0.00" ></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;">
                                <div style="display: flex; align-items: center;">
                                    <b style="margin-right: 2px;"> 47 </b> Others <i>(Specify)</i>
                                    <input type="text" class="form-control input-sm" name="others_47_A_txt" id="others_47_A_txt" value="" style=" margin: 3px 3px 3px 10px; width: 70%;">
                                </div>
                            </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="others_47_A_num" id="others_47_A_num" value="0.00" ></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="others_47_B_num" id="others_47_B_num" value="0.00" ></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 48 </b> Domestic Purchases with No Input Tax </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="domestic_purchases_with_no_input_tax" id="domestic_purchases_with_no_input_tax" value="0.00"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;"><b> 49 </b> VAT- Exempt Importations </td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="vat_exempt_importations" id="vat_exempt_importations" value="0.00"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px"><b> 50 </b> Total Current Purchases/Input Tax<br><small><i>(Sum of Items 44A to 49A)/(Sum of Items 44B to 47B)</i></small></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="total_current_purchases_input_tax_A" id="total_current_purchases_input_tax_A" value="0.00" readonly></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="total_current_purchases_input_tax_B" id="total_current_purchases_input_tax_B" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px" colspan="2"><b> 51 Total Available Input Tax </b><i>(Sum of Items 43B and 50B)</i></td>
                            <td><input type="text" class="form-control input-sm text-right xcompute" name="total_available_input_tax" id="total_available_input_tax" value="0.00" readonly></td>
                           
                        </tr>
                        <tr>
                            <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Less: Adjustment/Deductions from Input Tax </b></td>
                            <td align="center"> <b> B. Input Tax </b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; line-height:12px" colspan="2"><b> 52 </b> Input Tax on Purchases/Importation of Capital Goods exceeding P1 Million deferred for the succeeding period<br><small><i>(From Part V Schedule 1, Column I)</i></small> </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_tax_on_purchases" id="input_tax_on_purchases" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 53 </b> Input Tax Attributable to VAT Exempt Sales <i>(From Part V - Schedule 2)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_tax_attributable_to_vat_exempt_sales" id="input_tax_attributable_to_vat_exempt_sales" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 54 </b> VAT Refund/TCC Claimed </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="vat_refund_tcc_claimed" id="vat_refund_tcc_claimed" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2">
                                <b> 55 </b> Input VAT on Unpaid Payables 
                            </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_vat_on_unpaid_payable" id="input_vat_on_unpaid_payable" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2">
                                <div style="display: flex; align-items: center;">
                                    <b style="margin-right: 2px;"> 56 </b> Others <i>(Specify)</i>
                                    <input type="text" class="form-control input-sm" name="others_56_txt" id="others_56_txt" value="" style=" margin: 3px 3px 3px 10px; width: 80%;">
                                </div>
                            </td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="others_56_num" id="others_56_num" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 57 </b> Total Deductions from Input Tax <i>(Sum of Items 52B to 56B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_deductions_from_input_tax" id="total_deductions_from_input_tax" value="0.00" readonly> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 58 </b> Add: Input VAT on Settled Unpaid Payables Previously Deducted</td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="input_vat_on_settled_unpaid_payables_previously_deducted" id="input_vat_on_settled_unpaid_payables_previously_deducted" value="0.00"> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 59 </b> Adjusted Deductions from Input Tax <i>(Sum of Items 57B and 58B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="adjusted_deductions_from_input_tax" id="adjusted_deductions_from_input_tax" value="0.00" readonly> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 60 </b> Total Allowable Input Tax <i>(Item 51B Less Item 59B)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="total_allowable_input_tax" id="total_allowable_input_tax" value="0.00" readonly> </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;" colspan="2"><b> 61 Net VAT Payable/(Excess Input Tax)</b> <i>(Item 37B Less Item 60B) (To Part II, Item 15)</i></td>                                       
                            <td>  <input type="text" class="xcompute form-control input-sm text-right" name="net_vat_payable_excess_input_tax" id="net_vat_payable_excess_input_tax" value="0.00" readonly> </td>
                        </tr>
                    </table>
                </div>
        </div>
    </div>

    <br><br><br><br>
                <!-- ==================== END OF PAGE 2 ==================== -->

</form>



</body>
</html>
<script>
        var year =  <?php echo json_encode($year);?>;
        var yearEndDB =  <?php echo json_encode($comprdo['fiscal_month_start_end']); ?>;
        var yearEnd = <?php echo json_encode($year); ?>;
</script>
<script src="js/script.js"></script>
<script src="js/bir2550q_param.js"></script>

