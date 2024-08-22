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

    $month = str_pad($_POST['selmonth'], 2, "0", STR_PAD_LEFT);
    $year = $_POST['years'];

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
    <script src="js/bir0619e_param.js"></script>

    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>

    <form action="bir1601eq.php" name="frmpos" id="frmpos" method="post" target="_blank" data-url-base="<?= $UrlBase ?>">
        <input type="hidden" value="<?= $comprdo['bir_sig_sign']?>" name="signature_image">
        <div class="container">
            <br>
            <div class="row">
                <div class="col-sm-10">
                &nbsp;
                </div>
                <!-- <div class="col-sm-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-print"></i>&nbsp;PRINT PDF</button>
                </div> -->
                <div class="col-sm-2">
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
                            <td align="center" width="150px"> BIR FORM No.<h3 class="nopadding">0619-E</h3>January 2018 (ENCS)<br>Page 1</td>
                            <td align="center" style="vertical-align: middle !important;"><h3 class="nopadding">Monthy Remittance Form</h3><h4 class="nopadding">of Creditable Income Taxes Withheld (Expanded)</h4></td>
                            <td align="center" width="200px" style="vertical-align: middle !important;"><img src="../../bir_forms/hdr1601eq.jpg" width="100%"> </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 0px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                    <tr>
                        <td align="center" width="20%">
                            <b>1.</b> For the Month of (MM/YYYY)
                            <table>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control input-sm" name="month" id="month" value="<?= isset($month) ? $month : "00" ?>" readonly style="width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" name="year" id="year" value="<?=$year?>" readonly style="width: 80px;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="center" width="20%">
                            <b>2.</b> Due Date (MM/DD/YYYY)
                            <table>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control input-sm" name="due_month" id="due_month" value="<?= isset($due_month) ? $due_month :  $month ?>" style="width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" name="due_day" id="due_day" value="<?= isset($due_day) ? $due_day :  "01" ?>" style="width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" name="due_year" id="due_year" value="<?= isset($due_year) ? $due_year :  $year ?>" style="width: 80px;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="center" width="20%">
                            <b>3.</b> Amended Form?
                            <div class="input-group">
                                <div style="margin-top: 5px">
                                    <ul class="ichecks list-inline" style="margin: 0px !important">
                                        <li>
                                            <input tabindex="3" type="radio" id="amended_yes" name="amended_form" value="Y">
                                            <label for="amended_yes">&nbsp;YES</label>
                                        </li>
                                        <li>
                                            <input tabindex="3" type="radio" id="amended_no" name="amended_form" value="N" checked>
                                            <label for="amended_no">&nbsp;NO</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td align="center" width="20%">
                            <b>4.</b> Any Taxes Withheld?
                            <div class="input-group">
                                <div style="margin-top: 5px">
                                    <ul class="ichecks list-inline" style="margin: 0px !important">
                                        <li>
                                            <input tabindex="3" type="radio" id="taxes_withheld_yes" name="taxes_withheld" value="Y">
                                            <label for="taxes_withheld_yes">&nbsp;YES</label>
                                        </li>
                                        <li>
                                            <input tabindex="3" type="radio" id="taxes_withheld_no" name="taxes_withheld" value="N" checked>
                                            <label for="taxes_withheld_no">&nbsp;NO</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td align="center" width="10%">
                            <b>5.</b> ATC
                            <input type="text" class="form-control input-sm" name="atc" id="atc" value="<?= "WME10" ?>" readonly style="width: 80px;">
                        </td>
                        <td align="center" width="10%">
                            <b>6.</b> Tax Type Code
                            <input type="text" class="form-control input-sm" name="tax_type_code" id="tax_type_code" value="<?= "WE" ?>" readonly style="width: 80px;">
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
                            <td><input type="text" class="form-control input-sm" name="taxpayer_tin" id="taxpayer_tin" value="<?=$comprdo['comptin']?>" readonly></td>
                            <td align="right" nowrap width="100"> <b> 8 </b> RDO Code </td>
                            <td width="100"><input type="text" class="form-control input-sm" name="rdo_code" id="rdo_code" value="<?=$comprdo['comprdo']?>" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <b> 9 </b> Withholding Agent's Name (Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)
                                <input type="text" class="form-control input-sm" name="withholding_agent_name" id="withholding_agent_name" value="<?=$comprdo['compname']?>" readonly>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="4">
                            <b> 10 </b> Registered Address 
                            <small>(Indicate complete address. If branch, indicate the branch address. If registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small>
                            <input type="text" class="form-control input-sm" name="registered_address" id="registered_address" value="<?=substr($comprdo['compadd'],0,40)?>" readonly>
                        </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control input-sm" name="registered_address_continued" id="registered_address_continued" value="<?=(strlen($comprdo['compadd']) > 40) ? substr($comprdo['compadd'],40,71) : ""?>" readonly>
                            </td>
                            <td align="right" style="vertical-align: middle">
                                <b> 10A </b> ZIP Code
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" name="zip_code" id="zip_code" value="<?=$comprdo['compzip']?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0px !important"> 
                                <table class="table table-sm table-borderedless" style="margin: 0px !important">
                                <tr>
                                    <td width="200px" style="vertical-align: middle; border-right: 1px solid #dddddd !important">
                                        <b> 11 </b> Contact Number
                                    </td>
                                    <td width="250px" style="border-right: 1px solid #dddddd !important"> 
                                        <input type="text" class="form-control input-sm" name="contact_number" id="contact_number" value="<?=$comprdo['bir_sig_phone']?>"> 
                                    </td>
                                    <td width="250px" style="vertical-align: middle;">
                                        <b> 12 </b> Category of Withholding Agent
                                    </td>
                                    <td style="vertical-align: middle;"> 
                                        <div class="input-group">
                                            <ul class="ichecks list-inline" style="margin: 0px !important">
                                                <li>
                                                    <input tabindex="3" type="radio" id="category_private" name="withholding_agent_category" value="P" checked>
                                                    <label for="category_private">&nbsp;PRIVATE</label>
                                                </li>
                                                <li>
                                                    <input tabindex="3" type="radio" id="category_government" name="withholding_agent_category" value="G">
                                                    <label for="category_government">&nbsp;GOVERNMENT</label>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </table>
                            </td> 
                            <tr>
                                <td style="vertical-align: middle;"> <b> 13 </b> Email Address </td>
                                <td colspan="3">
                                    <input type="text" class="form-control input-sm" name="email_address" id="email_address" value="<?=$comprdo['bir_sig_email']?>">
                                </td>
                            </tr>                                          
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="5"> <b> Part II - Tax Remittance</b></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 14 </b> Amount of Remittance </td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="amount_of_remittance" id="amount_of_remittance" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 15 </b> Less: Amount Remitted from Previously Filed Form, if this is an amended form</td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="amount_remitted_previous" id="amount_remitted_previous" value="0.00"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 16 </b> Net Amount of Remittance <i>(Item 14 Less Item 15)</i></td>
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="net_amount_of_remittance" id="net_amount_of_remittance" value="0.00" readonly> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 17 </b> Add: Penalties</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties</font> <b> 17A </b> Surcharge</td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="penalty_surcharge" id="penalty_surcharge" value="0.00"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 17B </b> Interest</td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="penalty_interest" id="penalty_interest" value="0.00"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 17C </b> Compromise</td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="penalty_compromise" id="penalty_compromise" value="0.00"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><font color="white">Add: Penalties </font><b> 17D </b> Total Penalties <i>(Sum of Items 17A to 17C) </i></td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="total_penalties" id="total_penalties" value="0.00" readonly> 
                            </td>
                        </tr> 
                        <tr> 
                            <td colspan="4" style="vertical-align: middle;"><b> 18 Total Amount of Remittance</b> <i>(Sum of Items 16 and 17D)</i></td>                                       
                            <td>  
                                <input type="text" class="xcompute form-control input-sm text-right" name="total_amount_of_remittance" id="total_amount_of_remittance" value="0.00" readonly> 
                            </td>
                        </tr>                        
                    </table>
                </div>  
            </div>
        </div>
               
        <br><br><br><br>
    </form>
</body>
</html>