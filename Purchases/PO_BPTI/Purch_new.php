<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Purch_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$compname = $rowcomp['compname'];
			$compadd = $rowcomp['compadd'];
			$compemail = $rowcomp['email'];
			$compphone = $rowcomp['cpnum'];
			$compfax = $rowcomp['faxnum']; 
		}

	}

	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_PR'"); 
											
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
		$xAllowPR = $all_course_data['cvalue']; 												
	}
	else{
		$xAllowPR = "";
	}

	$xAllowITMCH = "0";
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_PO_ITEM_CHANGE'");
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
		$xAllowITMCH = $all_course_data['cvalue']; 												
	}

	//parameters def delivery
	$arrdefdel = array();
	$result = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode in ('PODEFDELTO','PODEFDELADD','PODEFDELEMAIL','PODEFDELPHONE','PODEFDELFAX')");      
    if (mysqli_num_rows($result)!=0) {
      while($comprow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                 
		$arrdefdel[$comprow['ccode']] = $comprow['cdesc'];

      }
                    
    }

	@$arrewtlist = array();
	$getewt = mysqli_query($con,"SELECT * FROM `wtaxcodes` WHERE compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $rows['ctaxcode'], 'nrate' => $rows['nrate']); 
		}
	}

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype='Purchase' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => number_format($row['nrate'])); 
		}
	}

	//get last approvedby
	$sql = "SELECT capprovedby, ccheckedby From purchase WHERE compcode='$company' ORDER BY ddate DESC LIMIT 1";
	$result=mysqli_query($con,$sql);														
	$clastapprvby = "";   
	$clastchkdby = "";                                    							
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$clastapprvby = $row['capprovedby']; 
		$clastchkdby = $row['ccheckedby'];
	}

	@$arrempslist = array();
	$getempz = mysqli_query($con,"SELECT nid, cdesc, csign FROM `mrp_operators` where compcode='$company' and cstatus='ACTIVE' order By cdesc"); 
	if (mysqli_num_rows($getempz)!=0) {
		while($row = mysqli_fetch_array($getempz, MYSQLI_ASSOC)){
			@$arrempslist[] = array('nid' => $row['nid'], 'cdesc' => $row['cdesc'], 'csign' => $row['csign']); 
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.css?t=<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>

	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	-->

	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	<!--
	--
	-- FileType Bootstrap Scripts and Link
	--
	-->
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px">

	<input type="hidden" value='<?=json_encode(@$arrewtlist)?>' id="hdnewtlist"> 
	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes"> 

	<form action="Purch_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">

		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-list"></i>New Purchase Order
				</div>
			</div>
			<div class="portlet-body">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#home">PO Details</a></li>
					<li><a href="#menu1">Delivery/Billing</a></li>
					<li><a href="#attc">Attachments</a></li>
				</ul>

				<div class="tab-content">  

					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px">

						<table width="100%" border="0">
							<tr>
								<tH width="150">Supplier:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="" readonly>
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="">
										</div> 
									</div>
								</td>
								<tH width="150" style="padding:2px">PO Date:</tH>
								<td width="250" style="padding:2px;">
									<div class="col-xs-5 nopadding">
										<input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>"/>
									</div>
								</td>
							</tr>

							<tr>
								<tH>Contact:</tH>
								<td style="padding:2px">
									<div class="col-xs-3 nopadding"> 
										<button class="btn btn-sm btn-block btn-warning" name="btnSearchCont" id="btnSearchCont" type="button">Search</button>
									</div>
									<div class="col-xs-8 nopadwleft">
										<input type="text" id="txtcontactname" name="txtcontactname" class="required form-control input-sm" placeholder="Contact Person Name..." tabindex="1"  required="true">
									</div>
								</td>
								<tH width="150" style="padding:2px">Date Needed:</tH>
								<td style="padding:2px">
								<div class="col-xs-5 nopadding">

								<input type='text' class="datepick form-control input-sm" id="date_needed" name="date_needed" />

								</div>
								</td>
							</tr>

							<tr>
								<tH>Contact Details:</tH>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding">
										<div class="col-xs-4 nopadding">
											<input type='text' class="form-control input-sm" id="contact_email" name="contact_email" placeholder="Email Address"/>
										</div>
										<div class="col-xs-4 nopadwleft">
											<input type='text' class="form-control input-sm" id="contact_mobile" name="contact_mobile" placeholder="Mobile No." />
										</div>
										<div class="col-xs-4 nopadwleft">
											<input type='text' class="form-control input-sm" id="contact_fax" name="contact_fax" placeholder="Fax No." />
										</div>
									</div>
								</td>
								<tH width="150" style="padding:2px">Terms: </tH> 
								<td style="padding:2px">				
										<select id="selterms" name="selterms" class="form-control input-sm selectpicker">  
											<?php
												$sql = "Select * From groupings where compcode='$company' and ctype='TERMS'";
												$result=mysqli_query($con,$sql);
												if (!mysqli_query($con, $sql)) {
													printf("Errormessage: %s\n", mysqli_error($con));
												}			
																												
												while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
												{
											?>
												<option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
											<?php
												}
											?>
										</select>
								</td>
							</tr>

							<tr>
								<tH>Currency:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-6 nopadding">
											<select class="form-control input-sm" name="selbasecurr" id="selbasecurr"> 						
												<?php
														$nvaluecurrbase = "";	
														$nvaluecurrbasedesc = "";	
														$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
														
															if (mysqli_num_rows($result)!=0) {
																$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
																
																$nvaluecurrbase = $all_course_data['cvalue']; 
																	
															}
															else{
																$nvaluecurrbase = "";
															}

															/*
									
																$objcurrs = listcurrencies();
																$objrows = json_decode($objcurrs, true);
																	
														foreach($objrows as $rows){
															if ($nvaluecurrbase==$rows['currencyCode']) {
																$nvaluecurrbasedesc = $rows['currencyName'];
															}

															if($rows['countryCode']!=="Crypto" && $rows['currencyName']!==null){

																*/

																$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
																if (mysqli_num_rows($sqlhead)!=0) {
																	while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
												?>
															<option value="<?=$rows['id']?>" <?php if ($nvaluecurrbase==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
												<?php
															}
														}
												?>
											</select>
												<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
												<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $nvaluecurrbasedesc; ?>"> 
										</div>
										<div class="col-xs-2 nopadwleft">
											<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
										</div>

										<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
													
										</div>
									</div>
								</td>
								<tH width="150" style="padding:2px">Payment Type: </tH>
								<td style="padding:2px">
									<select class="form-control input-sm" name="selpaytype" id="selpaytype">
										<option value="0">Credit (Paid After Delivery)</option>
										<option value="1">Advance (Payment Before Delivery)</option>
									</select>
								</td>
							</tr>

							<tr>
								<tH>Remarks:</tH>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding">
										<textarea class="form-control" id="txtremarks" name="txtremarks" rows='3' tabindex="2"></textarea>
									</div>
								</td>
								
								<tH width="150" style="padding:2px" valign='top'><div id="setewt">EWT Code: </div> </tH>
								<td style="padding:2px" valign='top'>
									<div id="setewtval"> 
										<select id="selewt" name="selewt[]" class="form-control input-sm selectpicker"  tabindex="3">
											<?php
												foreach(@$arrewtlist as $rows){
													echo "<option value=\"".$rows['ctaxcode']."\"  data-rate=\"".$rows['nrate']."\">".$rows['ctaxcode'].": ".$rows['nrate']."%</option>";
												}
											?>
												
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<tH>Checked/Approved:</tH>
								<td style="padding:2px">

									<div class="col-xs-5 nopadding">
										<input type='text' class="form-control input-sm" id="chkdby" name="chkdby" placeholder="Enter Checked By..." value="<?=$clastchkdby?>">
									</div>
									<div class="col-xs-6 nopadwleft">
										<input type='text' class="form-control input-sm" id="apprby" name="apprby" placeholder="Enter Approved By..." value="<?=$clastapprvby?>">
									</div>
								</td>
								<tH width="150">Prepared By: </tH>
								<td style="padding:2px;">
									<select class='xsel2 form-control input-sm' id="selprepby" name="selprepby" required>
										<?php
											foreach(@$arrempslist as $rsx){
												echo "<option value='".$rsx['nid']."'> ".$rsx['cdesc']." </option>";
											}
										?>
									</select>
								</td>
							</tr>
						</table>
					
					</div>

					<div id="menu1" class="tab-pane fade" style="padding-left:5px; padding-top:10px">
						<table width="100%" border="0">
							<tr>
								<td width="150"><b>Deliver To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Enter Deliver To..."  size="60" autocomplete="off" value="<?=(isset($arrdefdel["PODEFDELTO"])) ? $arrdefdel["PODEFDELTO"] : ""?>">
										</div> 
									</div>						
								</td>
							</tr>
							<tr>
								<td><b>Delivery Address</b></td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><textarea class="form-control input-sm" id="txtdeladd" name="txtdeladd" placeholder="Enter Delivery Address..." autocomplete="off"><?=(isset($arrdefdel["PODEFDELADD"])) ? $arrdefdel["PODEFDELADD"] : ""?></textarea></div></td>
							</tr>					
							<tr>
								
								<tH width="100">Contact Details:</tH>   
								<td style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-4 nopadding">
											<input type='text' class="form-control input-sm" id="textdelemail" name="textdelemail" placeholder="Email Address" value="<?=(isset($arrdefdel["PODEFDELEMAIL"])) ? $arrdefdel["PODEFDELEMAIL"] : ""?>"/>
										</div>
										<div class="col-xs-4 nopadwleft">
											<input type='text' class="form-control input-sm" id="textdelphone" name="textdelphone" placeholder="Mobile No." value="<?=(isset($arrdefdel["PODEFDELPHONE"])) ? $arrdefdel["PODEFDELPHONE"] : ""?>" />
										</div>
										<div class="col-xs-4 nopadwleft">
											<input type='text' class="form-control input-sm" id="textdelfax" name="textdelfax" placeholder="Fax No." value="<?=(isset($arrdefdel["PODEFDELFAX"])) ? $arrdefdel["PODEFDELFAX"] : ""?>" />
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td width="150"><b>Delivery Notes</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="textdelnotes" name="textdelnotes" width="20px" tabindex="1" placeholder="Enter Delivery Notes..."  size="60" autocomplete="off">
										</div> 
									</div>						
								</td>
							</tr>

							<tr>
								<td width="150"><b>Bill To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtbillto" name="txtbillto" width="20px" tabindex="1" placeholder="Enter Bill To..."  size="60" autocomplete="off" value="<?=$compname?>">
										</div> 
									</div>						
								</td>
							</tr>

							<tr>
								<td width="150" colspan="2"><br><br></td>

							</tr>

						</table>
					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top:10px">
						<!--
						--
						-- Import Files Modal
						--
						-->
						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />
						
					</div>

				</div>
				
				
				<div class="portlet light bordered" style="margin-top: 20px">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details
						</div>
						<div class="inputs">
							<div class="portlet-input input-inline">
								<div class="col-xs-12 nopadding">

									<input type="hidden" name="hdnxrefrpr" id="hdnxrefrpr">
									<input type="hidden" name="hdnxrefrprident" id="hdnxrefrprident">

									<input type="hidden" name="hdnunit" id="hdnunit">
									<input type="hidden" name="hdnqty" id="hdnqty">
									<input type="hidden" name="hdnfact" id="hdnfact">
									<input type="hidden" name="hdnmainunit" id="hdnmainunit"> 
									<input type="hidden" name="txtpartnme" id="txtpartnme">	

									<?php
										if($xAllowPR==0){
									?>
									
									<div class="col-xs-4 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." tabindex="4"></div>
									<div class="col-xs-8 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>

									<?php
										}else{
									?>
										<input type="hidden" name="txtprodid" id="txtprodid">
										<input type="hidden" name="txtprodnme" id="txtprodnme">
									<?php
										}
									?>

								</div> 
							</div>	  
						</div>
					</div>
					<div class="portlet-body" style="overflow: auto">
						<div style="min-height: 30vh; width: 1700px;">

							<table id="MyTable" class="MyTable table-sm table-bordered" border="1">
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999" width="50px">&nbsp;</th>
										<?php
											if($xAllowITMCH==1){
										?>	
										<th style="border-bottom:1px solid #999" width="25px">&nbsp;</th>
										<?php
											}
										?>
										<th width="250px" style="border-bottom:1px solid #999">Part No.</th>						
										<th width="350px" style="border-bottom:1px solid #999">Description</th>
										<th width="100px" style="border-bottom:1px solid #999">Item Code</th>
										<!--<th width="100px" style="border-bottom:1px solid #999;" class="codeshdn">EWT Code</th>-->
										<th width="200px" style="border-bottom:1px solid #999;" class="codeshdn">VAT</th>
										<th width="100px" style="border-bottom:1px solid #999">UOM</th>
										<th width="100px" style="border-bottom:1px solid #999">Qty</th>
										<th width="100px" style="border-bottom:1px solid #999">Price</th>
										<th width="100px" style="border-bottom:1px solid #999">Amount</th>
										<!--<th width="100px" style="border-bottom:1px solid #999">Date Needed</th>-->
										<th width="100px" style="border-bottom:1px solid #999">Remarks</th>
										<th style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>
								<tbody class="tbody">
								</tbody>                   
							</table>

						</div>
					
					</div>

				</div>

				<div class="row nopadwtop2x">
					<div class="col-xs-6">
						<div class="portlet">
							<div class="portlet-body">
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Purch.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>

								<button type="button" class="btn purple btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
									PR<br>(Insert)
								</button>
								
								<button type="button" class="btn green btn-sm" id="btnSave" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="well">	
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Vatable Purchase:
									<input type="hidden" id="txtnNetVAT" name="txtnNetVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnNetVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Non-Vatable Purchase:
									<input type="hidden" id="txtnExemptVAT" name="txtnExemptVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnExemptVAT"> 
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									add VAT:
									<input type="hidden" id="txtnVAT" name="txtnVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total Purchase:
									<input type="hidden" id="txtnGrossBef" name="txtnGrossBef" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnGrossBef"> 
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									less EWT:
									<input type="hidden" id="txtnEWT" name="txtnEWT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnEWT"> 
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									<b>Total Amount Payable: </b>
									<input type="hidden" id="txtnGross" name="txtnGross" value="0">
									<input type="hidden" id="txtnBaseGross" name="txtnBaseGross" value="0">								
								</div>
								<div class="col-xs-4 value" id="divtxtnGross" style="border-top: 1px solid #ccc">
									0.00
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	

		</fieldset>
	</form>

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-top">
				<div class="modal-content">
					<div class="alert-modal-danger">
						<p id="AlertMsg"></p>
						<p><center>
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
						</center></p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL FOR CONTACT NAME -->
	<div class="modal fade" id="ContactModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg vertical-align-top">
			<div class="modal-content">
				<div class="modal-header">
					Select Contact Person
				</div>
				<div class="modal-body">
					<table id="ContactTbls" class="table table-condensed" width="100%">
						
						<thead>
							<tr>
								<th>Name</th>
								<th>Designation</th>
								<th>Department</th>
								<th>Email</th>
								<th>Mobile</th>
								<th>Fax</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning btn-sm" data-dismiss="modal" id="btnmodclose">CLOSE</button>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL FOR CHANGE ITEM -->
	<div class="modal fade" id="ChangeItmMod" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog vertical-align-top">
			<div class="modal-content">
				<div class="modal-header">
					Select Item
				</div>
				<div class="modal-body" style="height: 10vh">
					<input type="text" class="form-control input-sm" id="txtchangeitm" name="txtchangeitm" placeholder="Search Item Code/Description..."  autocomplete="off" value="">

					<input type="hidden" id="txtchangeitmID" name="txtchangeitmID" value="">

					<input type="hidden" id="txtchangeitmtxtval" name="txtchangeitmtxtval" value="">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success btn-sm" id="btnchangeitm">Change</button>
					<button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div> 

	<!-- FULL PO LIST REFERENCES-->
	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-full">			
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">Purchase Request List</h3>
				</div>
							
				<div class="modal-body" style="height:45vh">
							
					<div class="col-xs-12 nopadding">
						<div class="form-group">
							<div class="col-xs-3 pre-scrollable" style="height:42vh; border-right: 2px solid #ccc">
								<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
									<thead>
										<tr>
											<th>PR No</th>
											<th>Section</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>

							<div class="col-xs-9 pre-scrollable" style="height:42vh; border-right: 2px solid #ccc">
								<table name='MyInvDetList' id='MyInvDetList' class="table table-small">
									<thead>
										<tr>
											<th style="text-align: center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
											<th width="100px">Item No</th>
											<th>Description</th>
											<th>UOM</th>
											<th>Qty</th>
										</tr>
									</thead>
									<tbody>
																
									</tbody>
								</table>
							</div>
						</div>
					</div>	

				</div>
				
				<div class="modal-footer">
					<button type="button" id="btnInsDet" onClick="InsertPRDets()" class="btn btn-primary">Insert</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End FULL INVOICE LIST -->

	<form method="post" name="frmedit" id="frmedit" action="Purch_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" value="">
	</form>

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		e.preventDefault();
		$('#txtprodnme').focus();
      }
	  else if(e.keyCode == 27){ //ESC
		e.preventDefault();
		window.location.replace("Purch.php");

	  }

	});

	$(document).on("click", "tr.bdydeigid" , function() {
		var $row = $(this).closest("tr"),       // Finds the closest row <tr> 
		$tds = $row.find("td");             // Finds all children <td> elements

		$.each($tds, function() {               // Visits every single <td> element
		// alert($(this).attr("class"));        // Prints out the text within the <td>

			if($(this).attr("class")=="disnme"){
				$('#txtcontactname').val($(this).text());
			}
			if($(this).attr("class")=="disemls"){
				$("#contact_email").val($(this).text());
			}
			if($(this).attr("class")=="dismbls"){
				$("#contact_mobile").val($(this).text());
			}
			if($(this).attr("class")=="disxfax"){
				//alert($(this).text());
				$("#contact_fax").val($(this).text());
			}  
		});

		$("#ContactModal").modal("hide");
	});

	$(document).ready(function() {
		$('.datepick').datetimepicker({
			format: 'MM/DD/YYYY',
			useCurrent: false,
			//minDate: moment(),
			defaultDate: moment(),
		});

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		$("#file-0").fileinput({
			uploadUrl: '#',
			showUpload: false,
			showClose: false,
			allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
			overwriteInitial: false,
			maxFileSize:100000,
			maxFileCount: 5,
			browseOnZoneClick: true,
			fileActionSettings: { showUpload: false, showDrag: false,}
		});

		$("#selbasecurr").on("change", function (){
			
			//convertCurrency($(this).val());
	
			var dval = $(this).find(':selected').attr('data-val');
	
			$("#basecurrval").val(dval);
			$("#statgetrate").html("");
			recomputeCurr();
		
		});
		
		$("#basecurrval").on("keyup", function () {
			recomputeCurr();
		});

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
		
		$('#txtcust').typeahead({
		
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../th_supplier.php",
					dataType: "json",
					data: {
						query: $("#txtcust").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);

				$("#selbasecurr").val(item.cdefaultcurrency).change(); //val
				$("#basecurrvalmain").val($("#selbasecurr").data("val"));

				$("#selterms").val(item.cterms).change();
				

				getcontact(item.id);
			}
		});

		document.getElementById('txtcust').focus();
		
		$('#txtprodnme').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_product.php",
					dataType: "json",
					data: {
						query: $("#txtprodnme").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					

				//$('.datepick').each(function(){
				//	$(this).data('DateTimePicker').destroy();
				//});
			
					$('#txtprodnme').val(item.cname).change();
					$('#txtpartnme').val(item.cname); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit);
					$("#hdnqty").val(1);
					$("#hdnfact").val(1); 
					$("#hdnmainunit").val(item.cunit);
					$("#hdnxrefrpr").val("");
					$("#hdnxrefrprident").val("");

					addItemName("");	
					
				$('.datepick').each(function(){
					$(this).datetimepicker({format: 'MM/DD/YYYY'});	
				});
				
			}
		
		});


		$("#txtprodid").keydown(function(e){
			if(e.keyCode == 13){

				$.ajax({
					url:'../get_productid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
						
						var data = value.split(",");
						$('#txtprodid').val(data[0]);
						$('#txtprodnme').val(data[1]);
						$('#txtpartnme').val(data[1]); 
						$('#hdnunit').val(data[2]);
						$('#hdnqty').val(1);
						$("#hdnfact").val(1); 
						$("#hdnmainunit").val(data[2]);
						$("#hdnxrefrpr").val("");
						$("#hdnxrefrprident").val("");


						if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
							var rowCount = $('#MyTable tr').length;
							var isItem = "NO";
							var itemindex = 1;
						
							if(rowCount > 1){
							var cntr = rowCount-1;
							
							for (var counter = 1; counter <= cntr; counter++) {
								// alert(counter);
								if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
									isItem = "YES";
									itemindex = counter;
									//alert($("#txtitemcode"+counter).val());
									//alert(isItem);
								//if prd id exist
								}
							//for loop
							}
							//if rowcount >1
							}
						//if value is not blank
						}
						
						if(isItem=="NO"){		

					
								myFunctionadd();
								ComputeGross();	
								
									
							}
							else{
							//alert("ITEM NOT IN THE MASTERLIST!");
							addqty();
						}
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$('#txtpartnme').val(""); 
						$("#hdnunit").val("");
						$("#hdnqty").val("");
						$("#hdnfact").val("");
						$("#hdnmainunit").val("");
						$("#hdnxrefrpr").val("");
						$("#hdnxrefrprident").val("");
						//closing for success: function(value){
					}
				}); 

			//if ebter is clicked
			}
			
		});


		$("#btnSearchCont").on("click", function(){

			//get contact names
			if($('#txtcustid').val()!="" && $('#txtcust').val()!=""){
				$('#ContactTbls tbody').empty(); 

				$.ajax({
					url:'get_contactinfonames.php',
					data: 'c_id='+ $('#txtcustid').val(),  
					dataType: "json",               
					success: function(data){
						
						$.each(data,function(index,item){

							//put to table
							$("<tr class='bdydeigid' style='cursor:pointer'>").append(
								$("<td class='disnme'>").text(item.cname),
								$("<td class='disndesig'>").text(item.cdesig),
								$("<td class='disdept'>").text(item.cdept),
								$("<td class='disemls'>").text(item.cemail),
								$("<td class='dismbls'>").text(item.cphone),
								$("<td class='disxfax'>").text(item.cfaxno)
							).appendTo("#ContactTbls tbody");

						});
					} 
				});

				$("#ContactModal").modal("show");
			}else{
				alert("Supplier Required!");
				document.getElementById("txtcust").focus();
				return false;
			}


		});

		$("#selpaytype").on("change", function(){
			if($(this).val()==1){
				$("#selterms").attr("disabled", true);

				//$("#setewtval").show();  
				//$("#setewt").show(); 
				//$(".codeshdn").show();

			}else{
				$("#selterms").attr("disabled", false);

				//$("#setewtval").hide();
				//$("#setewt").hide();
				//$(".codeshdn").hide();

			}
		});
		
		$("#selprepby").select2();
		$("#selewt").select2();

		$("#selewt").on("change", function(){ 
			/*var rowCount = $('#MyTable tr').length;

			if(rowCount>1){
				if($(this).val()!=="multi"){			
					for (var i = 1; i <= rowCount-1; i++) {

						$("#selitmewtyp"+i).attr("disabled", false);

						var slctdvalid = $("#selitmewtyp"+i).val($(this).val());

						$("#selitmewtyp"+i).attr("disabled", true);
					}
				}else{
					for (var i = 1; i <= rowCount-1; i++) {
						$("#selitmewtyp"+i).attr("disabled", false);
					}
				}

			}*/
			ComputeGross();
		});

		$('#txtchangeitm').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_product.php",
					dataType: "json",
					data: {
						query: $("#txtchangeitm").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 

				$('#txtchangeitm').val(item.cname).change(); 
				$('#txtchangeitmID').val(item.id); 
				
			}
		});

		$('#btnchangeitm').on("click", function(){
			var cnghb = $("#txtchangeitmtxtval").val();

			$("#txtitemcode"+cnghb).val($('#txtchangeitmID').val());  
			$("#txtitemdesc"+cnghb).val($('#txtchangeitm').val());
			$("#txtitempartdesc"+cnghb).val($('#txtchangeitm').val());

			$('#txtchangeitm').val("").change(); 
			$('#txtchangeitmID').val(""); 

			$("#ChangeItmMod").modal("hide");
			 
		});
		

	});

	function addItemName(tranno){
		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
			var rowCount = $('#MyTable tr').length;
			var isItem = "NO";
			var itemindex = 1;
			
			if(rowCount > 1){
				var cntr = rowCount-1;
				
				for (var counter = 1; counter <= cntr; counter++) {
					// alert(counter);
					if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
						isItem = "YES";
						itemindex = counter;
					}
				}
			}
			
			if(isItem=="NO"){	

					myFunctionadd();		
					ComputeGross();	
			}
			else{
				
				addqty();	
					
			}
			
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			$("#hdnqty").val("");
			$("#hdnfact").val("");
			$("#hdnmainunit").val("");
			$("#hdnxrefrpr").val("");
			$("#hdnxrefrprident").val("");
		}

	}

	function myFunctionadd(){

		var crefPR = document.getElementById("hdnxrefrpr").value;
		var crefPRIdent = document.getElementById("hdnxrefrprident").value;

		var itmcode = document.getElementById("txtprodid").value;
		var itmdesc = document.getElementById("txtprodnme").value; 
		var itmpartdesc = document.getElementById("txtpartnme").value; 
		var itmunit = document.getElementById("hdnunit").value;
		var itmnqty = document.getElementById("hdnqty").value; 
		var itmnfact = document.getElementById("hdnfact").value;
		var itmnmain = document.getElementById("hdnmainunit").value;

		var dneeded= document.getElementById("date_needed").value;

		var itmprice = chkprice(itmdesc,itmunit);
		var itmamt = parseFloat($("#basecurrval").val())*parseFloat(itmamt); 
		var itmbaseamt = parseFloat(itmnqty)*parseFloat(itmprice);

		var uomoptions = ""; 
		
		$.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: itmcode },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.id==itmunit){
						isselctd = "selected";
					}
					else{
						isselctd = "";
					}
					
					uomoptions = uomoptions + '<option value='+item.id+' '+isselctd+'>'+item.name+'</option>';
				});
						
											
			}
		});
			
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;


		var tdxnum = "<td align=\"center\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtnum"+lastRow+"\" value=\""+lastRow+"\" readonly></td>";

		var tdedt = "";
		<?php
			if($xAllowITMCH==1){
		?>
			//tditmchange = "<td style=\"padding-right: 2px\" align='center'> <button type='button' class=\"btn btn-xs btn-success\"><i class=\"fa fa-edit\"></i></button> </td>";
			var tdedt = "<td width=\"30\" align=\"center\"><button type=\"button\" class=\"btn btn-success btn-xs\" onclick=\"ChangeItem('"+lastRow+"');\" name=\"txtedtitm\" id=\"txtedtitm"+lastRow+"\"><i class=\"fa fa-pencil\"></i></button></td>";
		<?php
			} 
		?>
		var tditmpartdesc = "<td><input type='text' class='form-control input-xs' value='"+itmpartdesc+"' name=\"txtitempartdesc\" id=\"txtitempartdesc"+lastRow+"\" readonly></td>";

		var tditmdesc = "<td style=\"padding: 1px\" nowrap><input type='text' class='form-control input-xs' value='"+itmdesc+"' name=\"txtitemdesc\" id=\"txtitemdesc"+lastRow+"\"></td>";

		var tditmcode = "<td><input type='text' class='form-control input-xs' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode"+lastRow+"\" readonly> <input type='hidden' value='"+crefPR+"' name=\"hdncreference\" id=\"hdncreference\"> <input type='hidden' value='"+crefPRIdent+"' name=\"hdnrefident\" id=\"hdnrefident\"> <input type='hidden' value='"+itmcode+"' name=\"txtolditemcode\" id=\"txtolditemcode\">  </td>";
		
		var tditmunit = "<td style=\"padding: 1px\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select> </td>";


			/*var ewtstyle="";
			
			var gvnewt = $("#selewt").val();
			var xz = $("#hdnewtlist").val();
			ewtoptions = "";
			$.each(jQuery.parseJSON(xz), function() { 
				if(gvnewt==this['ctaxcode']){
					isselctd = "selected";
				}else{
					isselctd = "";
				}
				ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
			});

			if(gvnewt=="none" || gvnewt=="multi"){
				isdisabled = "disabled";
			}else{
				isdisabled = "";
			}

			var ewttd = "<td nowrap style=\""+ewtstyle+"\" class=\"codeshdn\"> <select class='form-control input-xs' name=\"selitmewtyp\" id=\"selitmewtyp"+lastRow+"\" "+isdisabled+"> <option value=\"none\">None</option>" + ewtoptions + "</select> </td>";*/

			var xz = $("#hdntaxcodes").val();
			taxoptions = "";
			$.each(jQuery.parseJSON(xz), function() { 
				if($("#hdncvat").val()==this['ctaxcode']){
					isselctd = "selected";
				}else{
					isselctd = "";
				}
				taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['nrate']+"%: "+this['ctaxdesc']+"</option>";
			});

			var vattd = "<td width=\"120px\" nowrap class=\"codeshdn\"> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";


		var tditmqty = "<td style=\"padding: 1px\" nowrap> <input type='text' value='"+itmnqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmnmain+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmnfact+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
			
		var tditmprice = "<td style=\"padding: 1px\" nowrap> <input type='text' value='"+itmprice+"' class='numeric2 form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> </td>";
				
		var tditmbaseamount = "<td style=\"padding: 1px\" nowrap> <input type='text' value='"+itmbaseamt+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> <input type='hidden' value='"+itmamt+"' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>"; 

		var tdneeded = "<td style=\"padding: 1px; position:relative;\" nowrap><input type='text' class='form-control input-xs' id='dneed"+lastRow+"' name='dneed' value='"+dneeded+"' /></td>"

		var tditmremarks = "<td> <input type='text' class='form-control input-xs' value='' name=\"txtitemrem\" id=\"txtitemrem" + lastRow + "\" maxlength=\"255\"></td>";

		var tditmdel = "<td style=\"padding: 1px\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' data-var='"+lastRow+"'/> </td>";

		//tdneeded
		$('#MyTable > tbody:last-child').append('<tr>'+tdxnum+tdedt + tditmpartdesc + tditmdesc + tditmcode + vattd + tditmunit + tditmqty + tditmprice + tditmbaseamount + tditmremarks + tditmdel + '</tr>');


			$("#del"+lastRow).on('click', function() {
				var xy = $(this).data('var');

				$(this).closest('tr').remove();
				ReIndexTbl(xy);
				ComputeGross();
			});

			$("input.numeric2").autoNumeric('init',{mDec:4});
			$("input.numeric").autoNumeric('init',{mDec:2});

			//$("input.numeric").numeric();
			$("input.numeric, input.numeric2").on("click", function () {
				$(this).select();
			});
			
			$("input.numeric, input.numeric2").on("keyup", function () {
				ComputeAmt($(this).attr('id'));
				ComputeGross();
			});
			
			$("#seluom"+lastRow).on('change', function() {

				var xyz = chkprice(itmdesc,$(this).val());
				
				$('#txtnprice'+lastRow).val(xyz.trim());
				
				ComputeAmt($(this).attr('id'));
				ComputeGross();
				
				var fact = setfactor($(this).val(), itmcode);
				
				$('#hdnfactor'+lastRow).val(fact.trim());
				
			});

			ComputeGross();

			/*$('#dneed'+lastRow).datetimepicker({
				format: 'MM/DD/YYYY',
				useCurrent: false,
				minDate: moment().format('L'),
				defaultDate: moment().format('L'),
				widgetPositioning: {
						horizontal: 'right',
						vertical: 'bottom'
				}
			});*/
										
	}

	function ReIndexTbl(xy){
		var rowCount = $('#MyTable tr').length;						
		if(rowCount>1){
			for (var i = xy+1; i <= rowCount; i++) {

				
				var ITMtxtnum = document.getElementById('txtnum' + i);
				var ITMedt = document.getElementById('txtedtitm' + i);
				var ITMCode = document.getElementById('txtitemcode' + i);
				var ITMDesc = document.getElementById('txtitemdesc' + i);
				//var ITMewt = document.getElementById('selitmewtyp' + i);
				var ITMvats = document.getElementById('selitmvatyp' + i);
				var ITMuom = document.getElementById('seluom' + i);
				var ITMqty = document.getElementById('txtnqty' + i);
				var ITMmauom = document.getElementById('hdnmainuom' + i);
				var ITMfctr = document.getElementById('hdnfactor' + i);
				var ITMprce = document.getElementById('txtnprice' + i);
				var ITMtramnt = document.getElementById('txtntranamount' + i); 
				var ITMamnt = document.getElementById('txtnamount' + i); 
				//var ITMneed = document.getElementById('dneed' + i);
				var ITMdelx = document.getElementById('del' + i);
				var ITMremx = document.getElementById('txtitemrem' + i);

				var za = i - 1;

				ITMedt.setAttribute('onclick','ChangeItem('+za+')');
				ITMedt.id = "txtedtitm" + za;

				ITMCode.id = "txtitemcode" + za;
				ITMDesc.id = "txtitemdesc" + za;
				//ITMewt.id = "selitmewtyp" + za;
				ITMvats.id = "selitmvatyp" + za;
				ITMuom.id = "seluom" + za;
				ITMqty.id = "txtnqty" + za;
				ITMmauom.id = "hdnmainuom" + za;
				ITMfctr.id = "hdnfactor" + za;
				ITMprce.id = "txtnprice" + za;
				ITMtramnt.id = "txtntranamount" + za;
				ITMamnt.id = "txtnamount" + za;
				//ITMneed.id = "dneed" + za;

				ITMdelx.setAttribute('data-var',''+za+'');
				ITMdelx.id = "del" + za;
	
				ITMremx.id = "txtitemrem" + za;

				ITMtxtnum.id = "txtnum" + za;
				ITMtxtnum.value = za;
			}
		}
	}

	function ComputeAmt(nme){
		var r = nme.replace( /^\D+/g, '');
		var nnet = 0;
		var nqty = 0;
		
		nqty = $("#txtnqty"+r).val().replace(/,/g,'');
		nqty = parseFloat(nqty)

		nprc = $("#txtnprice"+r).val().replace(/,/g,'');
		nprc = parseFloat(nprc);

		//ndsc = $("#txtndisc"+r).val();
		//ndsc = parseFloat(ndsc);
		
		//if (parseFloat(ndsc) != 0) {
		//	nprcdisc = parseFloat(nprc) * (parseFloat(ndsc) / 100);
		//	nprc = parseFloat(nprc) - nprcdisc;

		//}
		
		namt = nqty * nprc;

		namt2 = namt * parseFloat($("#basecurrval").val());

		$("#txtntranamount"+r).val(namt);
		$("#txtntranamount"+r).autoNumeric('destroy');
		$("#txtntranamount"+r).autoNumeric('init',{mDec:2});

		$("#txtnamount"+r).val(namt2);
		//$("#txtnamount"+r).autoNumeric('destroy');
		//$("#txtnamount"+r).autoNumeric('init',{mDec:2});


	}

	/*function ComputeGross(){
		var rowCount = $('#MyTable tr').length;

		var gross = 0;
		var amt = 0;
		
		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {
				amt = $("#txtntranamount"+i).val().replace(/,/g,'');
				gross = gross + parseFloat(amt);
				
			}
			
			
		}
		gross2 = gross * parseFloat($("#basecurrval").val());

		//	$("#txtnGross").val(Number(gross2).toLocaleString('en', { minimumFractionDigits: 4 }));
		//	$("#txtnBaseGross").val(Number(gross).toLocaleString('en', { minimumFractionDigits: 4 }));

		$("#txtnBaseGross").val(gross);
		$("#txtnGross").val(gross2);

		$("#divtxtnBaseGross").text(gross.toFixed(2));
		$("#divtxtnBaseGross").formatNumber();
		
	}*/

	function ComputeGross(){
		var rowCount = $('#MyTable tr').length;

		var gross = 0;
		var nwvat = 0;
		var nvat = 0;
		var nwovat = 0;
		var totewt = 0;
		var xcrate = 0;
		var TotAmtDue = 0;

		var nvatble = 0;
		var vatzTot = 0;

		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {

				var slctdval = $("#selitmvatyp"+i+" option:selected").data('id'); //data-id is the rate

				if(parseFloat(slctdval)>0){
					nvatble = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
					nvat = nvatble * (parseInt(slctdval)/100);

					nwvat = nwvat + nvatble;
					vatzTot = vatzTot + nvat;
					
				}else{
					nwovat = nwovat + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}

				gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				
			}
						
		}

		//VATABLE
		$("#txtnNetVAT").val(nwvat);
		$("#divtxtnNetVAT").text(nwvat.toFixed(2));
		$("#divtxtnNetVAT").formatNumber();

		//NO VAT
		$("#txtnExemptVAT").val(nwovat);
		$("#divtxtnExemptVAT").text(nwovat.toFixed(2));
		$("#divtxtnExemptVAT").formatNumber();

		// ADD VAT
		$("#txtnVAT").val(vatzTot);
		$("#divtxtnVAT").text(vatzTot.toFixed(2));
		$("#divtxtnVAT").formatNumber();

		//TOTAL GROSS
		$("#txtnGrossBef").val(gross);
		$("#divtxtnGrossBef").text(gross.toFixed(2));
		$("#divtxtnGrossBef").formatNumber();

		// LESS EWT
		$xtotewrate = 0;
		ewtTotz = 0;
		$('#selewt > option:selected').each(function() {
			$xtotewrate = $xtotewrate + parseFloat($(this).data("rate"));
		});
		if(parseFloat($xtotewrate)>0){
			ewtTotz = (parseFloat(nwvat) + parseFloat(nwovat)) * ($xtotewrate/100);
		}
		$("#txtnEWT").val(ewtTotz);
		$("#divtxtnEWT").text(ewtTotz.toFixed(2));  
		$("#divtxtnEWT").formatNumber();


		//Total Amount
		$gettmtt = gross - parseFloat(ewtTotz);
		gross2 = $gettmtt * parseFloat($("#basecurrval").val().replace(/,/g,''));
		
		$("#txtnGross").val(gross2);
		$("#txtnBaseGross").val($gettmtt);
		$("#divtxtnGross").text($gettmtt.toFixed(2));		
		$("#divtxtnGross").formatNumber();

		
	}

	function addqty(){

		var itmcode = document.getElementById("txtprodid").value;

		var TotQty = 0;
		var TotAmt = 0;
		
		$("#MyTable > tbody > tr").each(function() {	
		var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
		
		//alert(disID);
			if(disID==itmcode){
				
				var itmqty = $(this).find("input[name='txtnqty']").val().replace(/,/g,'');
				var itmprice = $(this).find("input[name='txtnprice']").val().replace(/,/g,'');
				
				//alert(itmqty +" : "+ itmprice);
				
				TotQty = parseFloat(itmqty) + 1;
				$(this).find("input[name='txtnqty']").val(TotQty);
				
				TotAmt = TotQty * parseFloat(itmprice);
				$(this).find("input[name='txtntranamount']").val(TotAmt); 

				$("#txtntranamount"+r).autoNumeric('destroy');
				$("#txtntranamount"+r).autoNumeric('init',{mDec:2});

				namt2 = TotAmt * parseFloat($("#basecurrval").val());
				$(this).find("input[type='hidden'][name='txtnamount']").val(namt2); 

				//$("#txtnamount"+r).autoNumeric('destroy');
				//$("#txtnamount"+r).autoNumeric('init',{mDec:2});
			}

		});
		
		ComputeGross();

	}

	function chkprice(itmcode,itmunit){
		var result;
		var ccode = document.getElementById("txtcustid").value;
				
		$.ajax ({
			url: "../th_checkitmpoprice.php",
			data: { itm: itmcode, cust: ccode, cunit: itmunit},
			async: false,
			success: function( data ) {
				result = data;
			}
		});
				
		return result;
		
	}

	function setfactor(itmunit, itmcode){
		var result;
				
		$.ajax ({
			url: "../th_checkitmfactor.php",
			data: { itm: itmcode, cunit: itmunit },
			async: false,
			success: function( data ) {
				result = data;
			}
		});
				
		return result;
		
	}

	function chkform(){
		var ISOK = "YES";
		
		if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){

				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;Supplier Required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			document.getElementById("txtcust").focus();
			return false;

			
			ISOK = "NO";
		}
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(lastRow == 0){
				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;NO details found!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
		else{
			var msgz = "";
			var myqty = "";
			var myav = "";
			var myfacx = "";
			var myprice = "";

			$("#MyTable > tbody > tr").each(function(index) {
				
				myqty = $(this).find('input[name="txtnqty"]').val();
				myprice = $(this).find('input[name="txtnprice"]').val();
				
				if(myqty == 0 || myqty == ""){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
				}
				
				if(myprice == 0 || myprice == ""){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
				}

			});
			
			if(msgz!=""){
				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				return false;
				ISOK = "NO";
			}
		}
		
		if(ISOK == "YES"){
		var trancode = "";
		var isDone = "True";

			$("#btnSave").attr("disabled", true);
			/*Saving the header
			var ccode = $("#txtcustid").val();
			var crem = $("#txtremarks").val();
			var ddate = $("#date_needed").val(); 
			var dpodate = $("#date_delivery").val();
			var ngross = $("#txtnGross").val();

			var myform = $("#frmpos").serialize();	*/	
			var formdata = new FormData($('#frmpos')[0]);
			formdata.delete('upload[]');
			jQuery.each($('#file-0')[0].files, function(i, file){
				formdata.append('file-'+i, file);
			})	
			$.ajax ({
				url: "Purch_newsave.php",
				//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross },
				data: formdata,
				cache: false,
				processData: false,
				contentType: false,
				method: 'post',
				type: 'post',
				async: false,
				beforeSend: function(){
					$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW PO: </b> Please wait a moment...");
					$("#alertbtnOK").hide();
					$("#AlertModal").modal('show');
				},
				success: function( data ) {
					if(data.trim()!="False"){
						trancode = data.trim();
					}
				}
			});
			
			
			if(trancode!=""){
				//Save Details
				$("#MyTable > tbody > tr").each(function(index) {	

					var crefpr = $(this).find('input[type="hidden"][name="hdncreference"]').val(); 
					var crefprident = $(this).find('input[type="hidden"][name="hdnrefident"]').val();

					var citmpartno = $(this).find('input[name="txtitempartdesc"]').val();
					var citmnoOLD = $(this).find('input[type="hidden"][name="txtolditemcode"]').val();
					var citmno = $(this).find('input[name="txtitemcode"]').val();
					var citmdesc = $(this).find('input[name="txtitemdesc"]').val();
					var cuom = $(this).find('select[name="seluom"]').val();
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nprice = $(this).find('input[name="txtnprice"]').val();
					var ntranamt = $(this).find('input[name="txtntranamount"]').val();
					var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
					//var dneed = $(this).find('input[name="dneed"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val(); 
					var citmremarks = $(this).find('input[name="txtitemrem"]').val();

					//var ewtcode = $(this).find('select[name="selitmewtyp"]').val();
					//var ewtrate = $(this).find('select[name="selitmewtyp"] option:selected').data('rate'); 
					var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
					var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id'); 

					if(nqty!==undefined){
						nqty = nqty.replace(/,/g,'');
						nprice = nprice.replace(/,/g,'');
						namt = namt.replace(/,/g,'');
						ntranamt = ntranamt.replace(/,/g,'');
					}

					//alert("Purch_newsavedet.php?trancode="+ trancode + "&dneed="+ dneed + "&indx="+ index + "&citmno="+ citmno+ "&cuom="+ cuom+ "&nqty="+ nqty + "&nprice="+ nprice+ "&namt=" + namt + "&mainunit="+ mainunit + "&nfactor=" + nfactor + "&citmdesc=" + citmdesc + "&citmremarks=" + citmremarks + "&ntranamt=" + ntranamt);
					
					$.ajax ({
						url: "Purch_newsavedet.php",
						data: { trancode: trancode, crefpr:crefpr, crefprident:crefprident, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, ntranamt:ntranamt, citmremarks:citmremarks, vatcode:vatcode, nrate:nrate, ewtcode:'', ewtrate:0, citmpartno:citmpartno, citmnoOLD:citmnoOLD, citmdesc:citmdesc },
						async: false,
						success: function( data ) {
							if(data.trim()=="False"){
								isDone = "False";
							}
						}
					});
					
				});
				
				
				if(isDone=="True"){
					$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
					$("#alertbtnOK").hide();

						setTimeout(function() {
							$("#AlertMsg").html("");
							$('#AlertModal').modal('hide');
				
								$("#txtctranno").val(trancode);
								$("#frmedit").submit();
				
						}, 3000); // milliseconds = 3seconds

					
				}

			}
			else{
					$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...<br><br>" + trancode);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
			}



		}

	}

	function convertCurrency(fromCurrency) {
	
	toCurrency = $("#basecurrvalmain").val(); //statgetrate
	$.ajax ({
		url: "../../Sales/th_convertcurr.php",
		data: { fromcurr: fromCurrency, tocurr: toCurrency },
		async: false,
		beforeSend: function () {
			$("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
		},
		success: function( data ) {

			$("#basecurrval").val(data);
			$("#hidcurrvaldesc").val($( "#selbasecurr option:selected" ).text()); 

		},
		complete: function(){
			$("#statgetrate").html("");
			recomputeCurr();
		}
	});

	}

	function recomputeCurr(){

		var newcurate = $("#basecurrval").val();
		var rowCount = $('#MyTable tr').length;
				
		var gross = 0;
		var amt = 0;

		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {
				amt = $("#txtntranamount"+i).val().replace(/,/g,'');			
				recurr = parseFloat(newcurate) * parseFloat(amt);

				$("#txtnamount"+i).val(recurr);

				//$("#txtnamount"+i).autoNumeric('destroy');
				//$("#txtnamount"+i).autoNumeric('init',{mDec:2});
			}
		}


		ComputeGross();


	}

	function getcontact(cid){

	$.ajax({
		url:'get_contactinfo.php',
		data: 'c_id='+ cid,                 
		success: function(value){
			if(value!=""){
				if(value.trim()=="Multi"){
					$("#btnSearchCont").click();
				}else{
					var data = value.split(":");
						
					$('#txtcontactname').val(data[0]);
					//$('#txtcontactdesig').val(data[1]);
					//$('#txtcontactdept').val(data[2]);  
					$("#contact_email").val(data[3]);
					$("#contact_mobile").val(data[4]);
					$("#contact_fax").val(data[6]);
				}
			}
		}
	});

	}

	function openinv(){

		if($("#txtcust").val()=="" || $("#txtcustid").val()==""){

			$("#AlertMsg").html("Please pick a supplier!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}else{

			$('#MyInvTbl').DataTable().destroy();

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();
					
			//get salesno na selected na
			var y;
			var salesnos = "";
			var xstat =  "YES";

			$.ajax({ //		data: 'x='+x,
				url: 'th_prlist.php',
				dataType: 'json',
				method: 'post',
				success: function (data) {

					$("#allbox").prop('checked', false);
									
					console.log(data);
					$.each(data,function(index,item){
											
						if(item.cpono=="NONE"){
							$("#AlertMsg").html("No Purchase Request Available");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

							xstat = "NO";
										
							$("#txtcustid").attr("readonly", false);
							$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
								$("<td id='td"+item.cprno+"'>").text(item.cprno),
								$("<td>").text(item.cdesc)
							).appendTo("#MyInvTbl tbody");
										
										
							$("#td"+item.cprno).on("click", function(){
								opengetdet($(this).text());
							});
										
							$("#td"+item.cprno).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
						}

					});
							
					if(xstat=="YES"){
						$('#mySIRef').modal('show');
					}

					$('#MyInvTbl').DataTable({
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": true,
						"bInfo": false,
						"bAutoWidth": false,
						"dom": '<"pull-left"f><"pull-right"l>tip',
						language: {
							search: "",
							searchPlaceholder: "Search PR/Section "
						}
					});

					$('#MyInvTbl_filter input').addClass('form-control input-sm');
					$('#MyInvTbl_filter input').css(
						{'width':'100%','display':'inline-block'}
					);

				},
				error: function (req, status, err) {
					console.log('Something went wrong', status, err);
					$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}
			});

		}

	}

	function opengetdet(valz){
		var drno = valz;

		$("#txtrefSI").val(drno);

		$('#InvListHdr').html("Purchase Request Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
		
		$('#MyInvDetList').DataTable().destroy();
		
		$('#MyInvDetList tbody').empty();
		$('#MyDRDetList tbody').empty();
			
		$('#loadimg').show();
		
		var salesnos = "";
		var cnt = 0;
				
		$("#MyTable > tbody > tr").each(function() {
			myxref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					
			if(myxref == drno){
				cnt = cnt + 1;
						
				if(cnt>1){
					salesnos = salesnos + ",";
				}
								
				salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
			}				
		});

		//alert('th_prlistdet.php?x='+drno+"&y="+salesnos);
		$.ajax({
			url: 'th_prlistdet.php',
			data: 'x='+drno+"&y="+salesnos,
			dataType: 'json',
			method: 'post',
			success: function (data) {

				$("#allbox").prop('checked', false); 					   
				console.log(data);
				$.each(data,function(index,item){
					if(item.citemno==""){
						alert("NO more items to add!")
					}
					else{
							
						$("<tr>").append(
							$("<td align='center'>").html("<input type='checkbox' value='"+item.nident+"' name='chkSales[]' data-id=\""+drno+"\" data-ident=\""+item.nident+"\" data-itm='"+item.citemno+"' data-itmdesc='"+item.cdesc+"' data-partdesc='"+item.cpartdesc+"' data-itmunit='"+item.cunit+"' data-qty='"+item.nqty+"' data-factor='"+item.nfactor+"'>"),
							$("<td>").text(item.citemno),
							$("<td>").text(item.cdesc),
							$("<td>").text(item.cunit),
							$("<td>").text(item.nqty),
						).appendTo("#MyInvDetList tbody");
					}
				});

				$('#MyInvDetList').DataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": true,
					"bInfo": false,
					"bAutoWidth": false,
					"dom": '<"pull-left"f><"pull-right"l>tip',
					language: {
						search: "",
						searchPlaceholder: "Search Item "
					}
				});

				$('#MyInvDetList_filter input').addClass('form-control input-sm');
				$('#MyInvDetList_filter input').css(
					{'width':'200%','display':'inline-block'}
				);
			},
			complete: function(){
				$('#loadimg').hide();
			},
			error: function (req, status, err) {
					console.log('Something went wrong', status, err);
					$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
			}
		});

	}

	function InsertPRDets(){
		var i = 0;
		var rcnt = 0;
		
		$("input[name='chkSales[]']:checked").each( function () {

			$("#hdnxrefrpr").val($(this).data("id"));
			$("#hdnxrefrprident").val($(this).data("ident"));

			$("#txtprodid").val($(this).data("itm"));
			$("#txtprodnme").val($(this).data("itmdesc")); 
			$("#txtpartnme").val($(this).data("partdesc"));
			$("#hdnunit").val($(this).data("itmunit"));
			$("#hdnqty").val($(this).data("qty"));
			$("#hdnfact").val($(this).data("factor"));

			myFunctionadd();

			$('#mySIRef').modal('hide');

		});
	}

	function ChangeItem(za){
		$("#txtchangeitmtxtval").val(za);
		$("#ChangeItmMod").modal("show");
	}
</script>
