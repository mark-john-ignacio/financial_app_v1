<?php include('layouts/default.php'); ?>
<!-- TODO: Create the prepare page for 2550M -->

<body>
    <form action="bir1601eq.php" name="frmpos" id="frmpos" method="post" target="_blank" data-api-url="<?= $UrlBase . "system_management/api/pdf/0619e"?>">
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
                            <td align="center" width="150px"> BIR FORM No.<h3 class="nopadding">2550M</h3>February 2007 (ENCS)<br>Page 1</td>
                            <td align="center" style="vertical-align: middle !important;"><h3 class="nopadding">Monthy Value Added Tax</h3><h4 class="nopadding">Declaration</h4></td>
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
                            <b>2.</b> Amended Return?
                            <div class="input-group">
                                <div style="margin-top: 5px">
                                    <ul class="ichecks list-inline" style="margin: 0px !important">
                                        <li>
                                            <input tabindex="3" type="radio" id="amended_yes" name="amended_return" value="Y">
                                            <label for="amended_yes">&nbsp;YES</label>
                                        </li>
                                        <li>
                                            <input tabindex="3" type="radio" id="amended_no" name="amended_return" value="N" checked>
                                            <label for="amended_no">&nbsp;NO</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td align="center" width="20%">
                            <b>3.</b> No. of Sheets Attached
                            <input type="text" class="form-control input-sm" name="no_of_sheets" id="no_of_sheets" value="0">
                        </td>
                    </tr>
                    </table>
                </div>

                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="5"><b>Part I - Background Information</b></td>
                        </tr>
                        <tr>
                            <td width="20%"><b>4</b> Taxpayer Identification Number (TIN)</td>
                            <td width="30%"><input type="text" class="form-control input-sm" name="taxpayer_tin" id="taxpayer_tin" value="<?=$comprdo['comptin']?>" readonly></td>
                            <td width="10%"><b>5</b> RDO Code</td>
                            <td width="10%"><input type="text" class="form-control input-sm" name="rdo_code" id="rdo_code" value="<?=$comprdo['comprdo']?>" readonly></td>
                            <td width="30%"><b>6</b> Line of Business<input type="text" class="form-control input-sm" name="line_of_business" id="line_of_business" value="<?=$comprdo['compbustype']?>" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <b>7</b> Withholding Agent's Name (Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)
                                <input type="text" class="form-control input-sm" name="withholding_agent_name" id="withholding_agent_name" value="<?=$comprdo['compname']?>" readonly>
                            </td>
                            <td>
                                <b>8</b> Telephone Number
                                <input type="text" class="form-control input-sm" name="telephone_number" id="telephone_number" value="<?=$comprdo['bir_sig_phone']?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <b>9</b> Registered Address 
                                <small>(Indicate complete address. If branch, indicate the branch address. If registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small>
                                <input type="text" class="form-control input-sm" name="registered_address" id="registered_address" value="<?=substr($comprdo['compadd'],0,100)?>" readonly>
                            </td>
                            <td>
                                <b>10</b> ZIP Code
                                <input type="text" class="form-control input-sm" name="zip_code" id="zip_code" value="<?=$comprdo['compzip']?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="padding: 0px !important"> 
                                <table class="table table-sm table-borderless" style="margin: 0px !important">
                                    <td colspan="4">
                                        <b>11</b> Are you availing tax relief under Special Law or International Tax Treaty?
                                        <div style="margin-top: 5px">
                                            <ul class="ichecks list-inline" style="margin: 0px !important">
                                                <li><input tabindex="3" type="radio" id="tax_relief_yes" name="tax_relief" value="yes"><label for="tax_relief_yes">&nbsp;Yes</label></li>
                                                <li><input tabindex="3" type="radio" id="tax_relief_no" name="tax_relief" value="no" checked><label for="tax_relief_no">&nbsp;No</label></li>
                                            </ul>
                                        </div>
                                        </td>
                                    <td colspan="1">
                                        <div id="tax_relief_specify" style="display: none; margin-top: 5px;">
                                            <label for="tax_relief_details">If yes, specify:</label>
                                            <input type="text" class="form-control input-sm" name="tax_relief_details" id="tax_relief_details">
                                        </div>
                                    </td> 
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12" style="margin-top: 3px !important">
                    <table class="table table-sm table-bordered" style="margin: 0px !important">
                        <tr>
                            <td align="center" colspan="5"> <b> Part II - Computation of Tax</b></td>
                        </tr>
                        <tr>
                        <tr>
                            <td align="center" colspan="3" width="60%"> <b>&nbsp;  </b> </td>
                            <td align="center" nowrap> <b> Sales Reciepts for the Month </b> </td>
                            <td align="center" nowrap> <b> Output Tax Due for the Month </b> </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 12 </b> VATable Sales/Receipt-Private (Sch .1) </td>
                            <td>
                                <b class="inline-block">12A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vatable_sales_receipt_private_12a" id="vatable_sales_receipt_private_12a" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">12B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vatable_sales_receipt_private_12b" id="vatable_sales_receipt_private_12b" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 13 </b> Sales to Government </td>
                            <td>
                                <b class="inline-block">13A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="sales_to_government_13a" id="sales_to_government_13a" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">13B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="sales_to_government_13b" id="sales_to_government_13b" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 14 </b> Zero Rated Sales/Receipts </td>
                            <td>
                                <b class="inline-block">&nbsp;&nbsp;14</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="zero_rated_sales_receipts" id="zero_rated_sales_receipts" value="<?= "0.00"?>">
                            </td>
                            <td>  
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 15 </b> Zero Rated Sales/Receipts </td>
                            <td>
                                <b class="inline-block">&nbsp;&nbsp;14</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="zero_rated_sales_receipts" id="zero_rated_sales_receipts" value="<?= "0.00"?>">
                            </td>
                            <td>  
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
<script src="js/bir0619e_param.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const taxReliefSpecify = document.getElementById('tax_relief_specify');

        // Initialize iCheck
        $(".ichecks input").iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

        // Bind iCheck events
        $('#tax_relief_yes').on('ifChecked', function() {
            taxReliefSpecify.style.display = 'block';
        });

        $('#tax_relief_no').on('ifChecked', function() {
            taxReliefSpecify.style.display = 'none';
        });
    });
</script>