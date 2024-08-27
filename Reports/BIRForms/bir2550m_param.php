<?php include('layouts/default.php'); ?>
<!-- TODO: Create the prepare page for 2550M -->

<body>
    <form action="bir1601eq.php" name="frmpos" id="frmpos" method="post" target="_blank" data-api-url="<?= $UrlBase . "system_management/api/pdf/2550m"?>">
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
                                                <li><input tabindex="3" type="radio" id="tax_relief_yes" name="tax_relief" value="Y"><label for="tax_relief_yes">&nbsp;Yes</label></li>
                                                <li><input tabindex="3" type="radio" id="tax_relief_no" name="tax_relief" value="N" checked><label for="tax_relief_no">&nbsp;No</label></li>
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
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vat_sales_12a" id="vat_sales_12a" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">12B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vat_sales_12b" id="vat_sales_12b" value="<?= "0.00"?>"> 
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
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="zero_rated_sales_14" id="zero_rated_sales_14" value="<?= "0.00"?>">
                            </td>
                            <td>  
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 15 </b> Exempt Sales/Receipts </td>
                            <td>
                                <b class="inline-block">&nbsp;&nbsp;15</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="exempt_sales_15" id="exempt_sales_15" value="<?= "0.00"?>">
                            </td>
                            <td>  
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 16 </b> Total Sales/Receipts and Output Tax Due </td>
                            <td>
                                <b class="inline-block">16A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="tot_sales_16a" id="tot_sales_16a" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">16B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="tot_sales_16b" id="tot_sales_16b" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 17 </b> Less: Allowable Input Tax</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17A </b> Input Tax Carried Over from Previous Period</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="input_tax_carried_over_17a" id="input_tax_carried_over_17a" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17B </b> Input Tax Deferred on Capital Goods Exceeding ₱1Million from Previous Period</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="input_tax_deferred_capital_goods_17b" id="input_tax_deferred_capital_goods_17b" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17C </b> Transitional Input Tax</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17C</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="transitional_input_tax_17c" id="transitional_input_tax_17c" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17D </b> Presumptive Input Tax</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17D</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="presumptive_input_tax_17d" id="presumptive_input_tax_17d" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17E </b> Others</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17E</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="others_17e" id="others_17e" value="<?= "0.00"?>">
                            </td>
                        </tr>  
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><font color="white">Less: </font> <b> 17F </b> Total (Sum of 17A to 17E)</td>                                       
                            <td>
                            </td>
                            <td>  
                                <b class="inline-block">17F</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_less_17f" id="total_less_17f" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 18 </b> Current Transactions</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18A/B </b> Purchase of Capital Goods [not exceeding ₱1Million] (see sch.2)</td>
                            <td>
                                <b class="inline-block">18A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="purchase_capital_goods_not_exceeding_1m_18a" id="purchase_capital_goods_not_exceeding_1m_18a" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="purchase_capital_goods_not_exceeding_1m_18b" id="purchase_capital_goods_not_exceeding_1m_18b" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18C/D </b> Purchase of Capital Goods [exceeding ₱1Million] (see sch.3)</td>
                            <td>
                                <b class="inline-block">18C</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="purchase_capital_goods_exceeding_1m_18c" id="purchase_capital_goods_exceeding_1m_18c" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18D</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="purchase_capital_goods_exceeding_1m_18d" id="purchase_capital_goods_exceeding_1m_18d" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18E/F </b> Domestic Purchases of Goods Other Than Capital Goods </td>
                            <td>
                                <b class="inline-block">18E</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="domestic_purchases_goods_non_capital_18e" id="domestic_purchases_goods_non_capital_18e" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18F</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="domestic_purchases_goods_non_capital_18f" id="domestic_purchases_goods_non_capital_18f" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18G/H </b> Importation of Goods Other Than Capital Goods </td>
                            <td>
                                <b class="inline-block">18G</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="importation_goods_non_capital_18g" id="importation_goods_non_capital_18g" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18H</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="importation_goods_non_capital_18h" id="importation_goods_non_capital_18h" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18I/J </b> Domestic Purchase of Services</td>
                            <td>
                                <b class="inline-block">18I</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="domestic_purchase_services_18i" id="domestic_purchase_services_18i" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18J</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="domestic_purchase_services_18j" id="domestic_purchase_services_18j" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18K/L </b> Services rendered by Non-Resident</td>
                            <td>
                                <b class="inline-block">18K</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="services_rendered_non_resident_18k" id="services_rendered_non_resident_18k" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18L</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="services_rendered_non_resident_18l" id="services_rendered_non_resident_18l" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18M </b> Purchases Not Qualified for Input Tax</td>
                            <td>
                                <b class="inline-block">18M</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="purchases_not_qualified_for_input_tax_18m" id="purchases_not_qualified_for_input_tax_18m" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18N/O </b> Others</td>
                            <td>
                                <b class="inline-block">18N</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="others_18n" id="others_18n" value="<?= "0.00"?>">
                            </td>
                            <td>  
                                <b class="inline-block">18O</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="others_18o" id="others_18o" value="<?= "0.00"?>"> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 18P </b> Total Current Purchases (Sum of 18A, 18C, 18E, 18G, 18I, 18K, 18M, 18N)</td>
                            <td>
                                <b class="inline-block">18P</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_current_purchases_18p" id="total_current_purchases_18p" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 19 </b> Total Available Input Tax (Sum of 17F, 18B, 18D, 18F, 18H, 18J, 18L, 18O)</td>
                            <td>

                            </td>
                            <td>
                                <b class="inline-block">19</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_available_input_tax_19" id="total_available_input_tax_19" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="vertical-align: middle;"><b> 20 </b> Less: Deductions from Input Tax </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20A </b> Input Tax on Purchases of Capital Goods [exceeding ₱1Million] deferred for the succeeding period (Sch.3)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="input_tax_purchases_capital_goods_exceeding_1m_deferred_20a" id="input_tax_purchases_capital_goods_exceeding_1m_deferred_20a" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20B </b> Input Tax on Sale to Government closed to expense (Sch.4)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="input_tax_sale_to_government_closed_expense_20b" id="input_tax_sale_to_government_closed_expense_20b" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20C </b> Input Tax Allocable to Exempt Sales (Sch.5)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20C</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="input_tax_allocable_exempt_sales_20c" id="input_tax_allocable_exempt_sales_20c" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20D </b> VAT Refund/TCC claimed</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20D</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vat_refund_tcc_claimed_20d" id="vat_refund_tcc_claimed_20d" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20E </b> Others</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20E</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="others_20e" id="others_20e" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 20F </b> Total (Sum of 20A to 20E)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">20F</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_20f" id="total_20f" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 21 </b> Total Allowable Input Tax (Item 19 less Item 20F)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">21</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_allowable_input_tax_21" id="total_allowable_input_tax_21" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 22 </b> Net VAT Payable (Item 16B less Item 21)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">22</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="net_vat_payable_22" id="net_vat_payable_22" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="vertical-align: middle;"><b> 23 </b> Less: Tax Credits/Payments</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23A </b> Creditable Value-Added Tax Withheld (Sch.6)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="creditable_vat_withheld_23a" id="creditable_vat_withheld_23a" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23B </b> Advance Payment for Sugar and Flour Industries (Sch.7)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="advance_payment_sugar_flour_industries_23b" id="advance_payment_sugar_flour_industries_23b" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23C </b> VAT withheld on Sales to Government (Sch.8)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23C</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vat_withheld_sales_to_government_23c" id="vat_withheld_sales_to_government_23c" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23D </b> VAT paid in return previously filed, if this is an amended return</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23D</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="vat_paid_return_previously_filed_23d" id="vat_paid_return_previously_filed_23d" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23E </b> Advance Payments made (please attach proof of payments - BIR Form No. 0605)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23E</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="advance_payments_made_23e" id="advance_payments_made_23e" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23F </b> Others</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23F</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="others_23f" id="others_23f" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 23G </b> Total Tax Credits/Payments (Sum of 23A to 23F)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">23G</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_tax_credits_payments_23g" id="total_tax_credits_payments_23g" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 24 </b> Tax Still Payable/(Overpayment) (Item 22 less Item 23G)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">24</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="tax_still_payable_overpayment_24" id="tax_still_payable_overpayment_24" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="vertical-align: middle;"><b> 25 </b> Add: Penalties</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 25A </b> Surcharge</td>
                            <td></td>
                            <td>
                                <b class="inline-block">25A</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="surcharge_25a" id="surcharge_25a" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 25B </b> Interest</td>
                            <td></td>
                            <td>
                                <b class="inline-block">25B</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="interest_25b" id="interest_25b" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 25C </b> Compromise</td>
                            <td></td>
                            <td>
                                <b class="inline-block">25C</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="compromise_25c" id="compromise_25c" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b class="padded-label"> 25D </b> Total Penalties (Sum of 25A to 25C)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">25D</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_penalties_25d" id="total_penalties_25d" value="<?= "0.00"?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="vertical-align: middle;"><b> 26 </b> Total Amount Payable/(Overpayment) (Item 24 plus Item 25D)</td>
                            <td></td>
                            <td>
                                <b class="inline-block">26</b>
                                <input type="text" class="xcompute form-control input-sm text-right inline-block input-auto-width" name="total_amount_payable_overpayment_26" id="total_amount_payable_overpayment_26" value="<?= "0.00"?>">
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
<script src="js/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const taxReliefSpecify = $('#tax_relief_specify');

        // Bind iCheck events
        $('#tax_relief_yes').on('ifChecked', function() {
            taxReliefSpecify.show();
        });

        $('#tax_relief_no').on('ifChecked', function() {
            taxReliefSpecify.hide();
        });
    });
</script>