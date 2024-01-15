<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "POS.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	if(isset($_REQUEST['txtctranno'])){
		$txtctranno = $_REQUEST['txtctranno'];
	}
	else{
		$txtctranno = $_REQUEST['txtcsalesno'];
	}
		
	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'POS_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit, b.cvattype from sales a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.ctranno = '$txtctranno' and a.compcode='$company'");	

	/*
	function listcurrencies(){ //API for currency list
		$apikey = $_SESSION['currapikey'];
		
		//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
		//$obj = json_decode($json, true);

		$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
		
		return $json;
	}
	*/

$getdcnts = mysqli_query($con,"SELECT * FROM `discounts_list` where compcode='$company' order By nident"); 
	if (mysqli_num_rows($getdcnts)!=0) {
		while($row = mysqli_fetch_array($getdcnts, MYSQLI_ASSOC)){
			@$arrdisclist[] = array('ident' => $row['nident'], 'ccode' => $row['ccode'], 'cdesc' => $row['cdesc'], 'acctno' => $row['cacctno']); 
		}
	}

	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Sales' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => $row['nrate']); 
		}
	}

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	@$arrewtlist = array();
	$getewt = mysqli_query($con,"SELECT * FROM `wtaxcodes` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $rows['ctaxcode'], 'nrate' => $rows['nrate']); 
		}
	}


	$nicomeaccount = "";
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INCOME_ACCOUNT'"); 								
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$nicomeaccount = $all_course_data['cvalue']; 							
	}

	@$incactsarr = array();
	$getinct = mysqli_query($con,"SELECT A.cdescription, B.cacctno FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid WHERE A.compcode='$company' and A.ccode='INCOME_ACCOUNT'"); 
	if (mysqli_num_rows($getinct)!=0) {
		while($rows = mysqli_fetch_array($getinct, MYSQLI_ASSOC)){
			@$incactsarr[] = array('acctno' => $rows['cacctno'], 'ccode' => $rows['cdescription'], 'cdesc' => $rows['cdescription']); 
		}
	}

	@$arrcterms = array();
	$getewt = mysqli_query($con,"SELECT * FROM `groupings` WHERE compcode='$company' and ctype='TERMS'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrcterms[] = array('ccode' => $rows['ccode'], 'cdesc' => $rows['cdesc']); 
		}
	}

	@$arrname = array();
	$directory = "../../Components/assets/SI/{$company}_{$txtctranno}";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
	} 

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?=time();?>">
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>
		
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
-->
<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

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

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus(); ">
<input type="hidden" value='<?=json_encode(@$arrdisclist)?>' id="hdndiscs"> 
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">
<input type="hidden" value='<?=json_encode(@$arrewtlist)?>' id="hdnewtlist">
<input type="hidden" value='<?=$nicomeaccount?>' id="incmracct"> 

<?php


if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$Gross = $row['ngross'];
		$cpricever = $row['cpricever'];
		$nlimit = $row['nlimit'];
		$creinv = $row['creinvoice'];
    	$cslstypz = $row['csalestype'];
		$cpaytypz = $row['cpaytype'];
		$selsiseries = $row['csiprintno'];
		$nnetvat = $row['nnet'];
		$nvat = $row['nvat'];

		$cewtcode = $row['cewtcode'];

		$cterms = $row['cterms'];

		$cdefvat = $row['cvattype']; 

		$refmods = $row['crefmodule'];
		$refmodstran = $row['crefmoduletran']; 

		$nbasegross = $row['nbasegross'];
		$ccurrcode = $row['ccurrencycode']; 
		$ccurrdesc = $row['ccurrencydesc']; 
		$ccurrrate = $row['nexchangerate']; 

		$GrossDiscount = $row['ngrossdisc'];

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];

	}
	
	
	if(!file_exists("../../imgcust/".$CustCode .".jpg")){
		$imgsrc = "../../images/blueX.png";
	}
	else{
		$imgsrc = "../../imgcust/".$CustCode .".jpg";
	}

?>

<form action="SI_edit.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post">
		<fieldset>
    	<legend>
        <div class="col-xs-6 nopadding">Sales Invoice Details</div>
				<div id="salesstat" class="text-right nopadding">
					<?php
					if($lCancelled==1){
							echo "<font color='#FF0000'><b>CANCELLED</b></font>";
					}
					
					if($lPosted==1){
						if($lVoid==1){
							echo "<font color='#FF0000'><b>VOIDED</b></font>";
						}else{
							echo "<font color='#FF0000'><b>POSTED</b></font>";
						}
					}
					?>
        </div>
      </legend>	

				<ul class="nav nav-tabs">
					<li class="active"><a href="#home" data-toggle="tab">Details</a></li>
					<li><a href="#attc" data-toggle="tab">Attachments</a></li>
				</ul>

				<div class="tab-content">
					<div id="home" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px">

						<table width="100%" border="0">
							<tr>
								<tH nowrap>&nbsp;Transaction No. &nbsp;</tH>
								<td style="padding:2px">
									<div class="col-xs-4 nopadding">
									
										<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?=$txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
										
										<input type="hidden" name="hdnposted" id="hdnposted" value="<?=$lPosted;?>">
										<input type="hidden" name="hdncancel" id="hdncancel" value="<?=$lCancelled;?>">
										<input type="hidden" name="hdncsalesno" id="hdncsalesno" value="<?=$txtctranno;?>">
										<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
										&nbsp;

										<button type="button" class="btn btn-entry btn-sm" id="btnentry">
											<i class="fa fa-bar-chart" aria-hidden="true"></i>
										</button>
									
								</td>
								<tH width="150">Invoice Date:</tH>
								<td style="padding:2px;">
									<div class="col-xs-11 nopadding">
											<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?=date_format(date_create($Date),'m/d/Y'); ?>" />
									</div>
								</td>
							</tr>
							
							<tr>
								<tH>&nbsp;SI Series No.</tH>
								<td style="padding:2px;">
									<div class="col-xs-4 nopadding">
										<input type='text' class="form-control input-sm" id="csiprintno" name="csiprintno" value="<?=$selsiseries;?>" autocomplete="off"/>
									</div>
									<div id="statmsgz" style="display:inline"></div>
								</td>
								<td><b>Sales Type:</b></td>
								<td style="padding:2px">
													
									<div class="col-xs-11 nopadding">
										<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
											<option value="Goods" <?php if($cslstypz=="Goods"){ echo "selected"; } ?>>Goods</option>
											<option value="Services" <?php if($cslstypz=="Services"){ echo "selected"; } ?>>Services</option> 
										</select>
									</div>

									<div class="col-xs-1 nopadding">
										&nbsp;
									</div>
								</td>
							</tr>

							<tr>
								<tH width="100">&nbsp;Customer:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?=$CustCode; ?>" readonly>
												<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
												<input type="hidden" id="hdnpricever" name="hdnpricever" value="<?=$cpricever;?>">
												<input type="hidden" id="hdndefVAT" name="hdndefVAT" value="<?=$cdefvat?>">
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" value="<?=$CustName; ?>" readonly>
										</div> 
									</div>
								</td>

								<?php
									if($nicomeaccount=="si"){
								?>
								<tH width="100"><b>Income Account:</b></tH>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding">
										<select id="selpaytyp" name="selpaytyp" class="form-control input-sm selectpicker"  tabindex="1">
											<?php
												foreach(@$incactsarr as $xr){
											?>
											<option value="<?=$xr['ccode']?>" data-id="<?=$xr['acctno']?>" <?php if($cpaytypz==$xr['ccode']){ echo "selected"; } ?>><?=$xr['ccode']?></option> 
											<?php
												}
											?>
										</select>
									</div>
								</td>
								<?php
										}else{
											echo "<th width=\"100\">&nbsp;</th><td style=\"padding:2px\"><input type=\"hidden\" id=\"selpaytyp\" name=\"selpaytyp\" value=\"Credit\"></td>";
										}
									?>
							</tr>

							<tr>
								<td>&nbsp;<b>Currency:</b></td>
								<td style="padding:2px">             
									<div class="col-xs-4 nopadding">
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
																	<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=strtoupper($rows['currencyName'])?></option>
											<?php
														}
													}
											?>
										</select>
										<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?=$nvaluecurrbase; ?>"> 	
										<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?=$ccurrdesc; ?>"> 
									</div>
									<div class="col-xs-2 nopadwleft">
										<input type='text' class="required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?=$ccurrrate; ?>">	 
									</div>
									<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 										
									</div>
								</td>   
								
								<tH width="100">Terms</tH>
										<td style="padding:2px">
											<div class="col-xs-11 nopadding">
												<select id="selcterms" name="selcterms" class="form-control input-sm selectpicker"  tabindex="3">
													<?php
														$xstat = "";
														foreach(@$arrcterms as $rows){
															if($cterms==$rows['ccode']){
																$xstat = "selected";
															}else{
																$xstat = "";
															}

															echo "<option value=\"".$rows['ccode']."\" ".$xstat." >".$rows['cdesc']."</option>";
														}
													?>															
												</select>
											</div>
										</td>	

								
							</tr>

							<tr>
								<tH width="100">&nbsp;Remarks:</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?=$Remarks; ?>"></div>
								</td>	
								
								<tH width="100"><div id="isewt">EWT Code</div></tH>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding" id="isewt2">
										<select id="selewt" name="selewt[]" class="form-control input-sm selectpicker"  tabindex="3" multiple>
												<?php
													$isselctd = "";
													foreach(@$arrewtlist as $rows){
													//	echo $rows['ctaxcode'].":".$cewtcode;
														if(in_array($rows['ctaxcode'], explode(",",$cewtcode))){
															$isselctd = "selected";
														}else{
															$isselctd = "";
														}

														echo "<option value=\"".$rows['ctaxcode']."\" ".$isselctd.">".$rows['ctaxcode'].": ".$rows['nrate']."%</option>";
													}
												?>                         
														
										</select>
									</div>
								</td>

								

							</tr>
						
							<tr>
								<tH width="100">Reference:</tH>
								<td style="padding:2px">
									<div class="col-xs-2 nopadding">
										<input type="text" class="form-control input-sm" id="txtrefmod" name="txtrefmod" readonly value="<?=$refmods?>">
									</div> 
									<div class="col-xs-9 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtrefmodnos" name="txtrefmodnos" readonly value="<?=$refmodstran?>">
									</div>
								</td>		
								
								<td><!--<b><div class="chklimit">Credit Limit:</div></b>--></td>
								<td style="padding:2px;" align="right">
									<!--<div class="chklimit col-xs-11 nopadding" id="ncustlimit"><b><font size='+1'><?//=number_format($nlimit,4);?></font></b></div>
									<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">-->
								</td>

								
								
							</tr>
<!--
							<tr>
								<tH width="100">&nbsp;</tH>
								<td style="padding:2px">&nbsp;</td>		
								
								<th><div class="chklimit">Balance:</div></th>
								<td style="padding:2px;"  align="right">                  
									<div class="chklimit col-xs-11 nopadding" id="ncustbalance"></div>
									<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
								</td>

								
							</tr>

							<tr>
								<td style="padding:2px" colspan="2">&nbsp;</td>
								<th>&nbsp;</th>
								<td style="padding:2px;" align="right">
									<div class="chklimit col-xs-11 nopadwright" id="ncustbalance2"></div>
								</td>								
							</tr>
												-->
						</table>

					</div>
					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px">

						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />

					</div>
				</div>

				<hr>
				<div class="col-xs-12 nopadwdown">					
					<div class="col-xs-3 nopadding">
						<b>Details</b>
					</div>
					<div class="col-xs-9 nopadwleft">
						<div class="chkitmsadd col-xs-3 nopadwdown">
							<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">
						</div>
						<div class="chkitmsadd col-xs-9 nopadwleft">
							<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="5">
							<input type="hidden" name="hdnqty" id="hdnqty">
							<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
							<input type="hidden" name="hdnunit" id="hdnunit"> 
							<input type="hidden" name="hdnctype" id="hdnctype">
							<input type="hidden" name="hdncvat" id="hdncvat"> 
							<input type="hidden" name="hdncvat" id="hdncewt"> 

							<input type="hidden" name="hdnacctno" id="hdnacctno">  
							<input type="hidden" name="hdnacctid" id="hdnacctid"> 
							<input type="hidden" name="hdnacctdesc" id="hdnacctdesc"> 
						</div>
					</div>
				</div>

				<div style="border: 1px solid #919b9c; height: 40vh; overflow: auto">
					<div id="tableContainer" class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					width: 1550px;
					height: 300px;
					text-align: left;">
		
						<table id="MyTable" class="MyTable table-sm table-bordered" border="1">
							<thead>
								<tr>
									<th width="100px" style="border-bottom:1px solid #999">Code</th>
									<th width="250px" style="border-bottom:1px solid #999">Description</th>
									<th width="150px" style="border-bottom:1px solid #999" class="chkVATClass">EWTCode</th>
									<th width="150px" style="border-bottom:1px solid #999" class="chkVATClass">VAT</th>
									<th width="100px" style="border-bottom:1px solid #999">UOM</th>
									<th width="100px" style="border-bottom:1px solid #999">Qty</th>
									<th width="100px" style="border-bottom:1px solid #999">Price</th>
									<th width="100px" style="border-bottom:1px solid #999">Discount</th>
									<th width="100px" style="border-bottom:1px solid #999">Amount</th>
									<th width="100px" style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
									<th width="80px" style="border-bottom:1px solid #999" class="chkinctype">Acct Code</th>
									<th width="200px" style="border-bottom:1px solid #999" class="chkinctype">Acct Title</th>
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
							</thead>
							<tbody class="tbody">
							</tbody>
												
						</table>
					</div>
				</div>

				</div>
			<br>


			<div class="row nopadwtop2x">
					<div class="col-xs-7">
						<?php
							$xc = check_credit_limit($company);
							if($xc==1){
						?>
						<div class="portlet blue-hoki box" id="creditport">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i>Credit Info
								</div>
								<div class="status" id="ncustbalance2">
									
								</div>
							</div>
							<div class="portlet-body">
								<div class="row static-info">
									<div class="col-md-3 name">
										 Credit Limit:
									</div>
									<div class="col-md-9 value">
										<div class="chklimit col-xs-10 nopadding" id="ncustlimit"></div>
										<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">
									</div>
								</div>
								<div class="row static-info">
									<div class="col-md-3 name">
										Balance:
									</div>
									<div class="col-md-9 value">
										<div class="chklimit col-xs-10 nopadding" id="ncustbalance"></div>
										<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
									</div>
								</div>
													
							</div>
						</div>
						<?php
							}
						?>
						<div class="portlet">
							<div class="portlet-body">
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

								<?php
									if($poststat == "True"){
								?>

								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SI.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
								</button>
							
								<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='SI_new.php';" id="btnNew" name="btnNew">
									New<br>(F1)
								</button>

								<div class="dropdown" style="display:inline-block !important;">
									<button type="button" data-toggle="dropdown" class="btn purple btn-sm dropdown-toggle">
										Reference <br>(Insert) <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="javascript:;" onClick="openinv('QO');">Billing</a></li>
										<li><a href="javascript:;" onClick="openinv('SO');">Sales Order</a></li>
										<li><a href="javascript:;" onClick="openinv('DR');">Delivery</a></li>
									</ul>
								</div>
								
								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>

								<?php
									$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'POS_print'");

									if(mysqli_num_rows($sql) == 1){
									
								?>
										<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$txtctranno;?>');" id="btnPrint" name="btnPrint">
											Print<br>(CTRL+P)
										</button>

								<?php		
									}

								?>
							
								<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
									Edit<br>(CTRL+E)
								</button>
								
								<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
									Save<br>(CTRL+S)
								</button>

								<?php
									}
								?>
							</div>
						</div>
					</div>
					<!--<div class="col-xs-5">
						<div class="well">							
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total NET Sales:
									<input type="hidden" id="txtnNetVAT" name="txtnNetVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnNetVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Add VAT:
									<input type="hidden" id="txtnVAT" name="txtnVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total Amount:
									<input type="hidden" id="txtnGross" name="txtnGross" value="0">
									<input type="hidden" id="txtnBaseGross" name="txtnBaseGross" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnGross">
									0.00
								</div>
							</div>
						</div>
					</div>-->

					<div class="col-xs-5">
						<div class="well">							
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Vatable Sales:
									<input type="hidden" id="txtnNetVAT" name="txtnNetVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnNetVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									VATExempt Sales:
									<input type="hidden" id="txtnExemptVAT" name="txtnExemptVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnExemptVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									ZERO Rated Sales:
									<input type="hidden" id="txtnZeroVAT" name="txtnZeroVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnZeroVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Add VAT:
									<input type="hidden" id="txtnVAT" name="txtnVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total Gross:
									<input type="hidden" id="txtnGrossBef" name="txtnGrossBef" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnGrossBef"> 
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Less Gross Discount:
									
								</div>
								<div class="col-xs-4 value">
									<input type="text" class="form-control input-xs text-right" id="txtnGrossDisc" name="txtnGrossDisc" value="<?=$GrossDiscount?>">
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									<b>Total Amount: </b>
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

    </fieldset>
    
   
	<!-- add details modal -->
	<div class="modal fade" id="MyDetModal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close"  aria-label="Close"  onclick="chkCloseInfo();"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="invheader"> Additional Details Info</h3>           
							</div>
		
				<div class="modal-body">
					<input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2">
					<table id="MyTable2" class="MyTable table table-condensed" width="100%">
						<thead>
							<tr>
								<th style="border-bottom:1px solid #999">Code</th>
								<th style="border-bottom:1px solid #999">Description</th>
								<th style="border-bottom:1px solid #999">Field Name</th>
								<th style="border-bottom:1px solid #999">Value</th>
								<th style="border-bottom:1px solid #999">&nbsp;</th>
							</tr>
						</thead>
						<tbody class="tbody">
						</tbody>
					</table>   
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- FULL DR LIST REFERENCES-->
	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog modal-lg">
					<div class="modal-content">
							<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title" id="InvListHdr">DR List</h3>
							</div>
							
							<div class="modal-body" style="height:40vh">
							
								<div class="col-xs-12 nopadding">

									<div class="form-group">
											<div class="col-xs-3 nopadding pre-scrollable" style="height:37vh">
														<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
															<thead>
																<tr>
																	<th>DR No</th>
																	<th>Amount</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
											</div>

											<div class="col-xs-9 nopadwleft pre-scrollable" style="height:37vh">
														<table name='MyInvDetList' id='MyInvDetList' class="table table-small small">
														<thead>
															<tr>
																<th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
																<th>SO No.</th> 
																<th>Item No</th>
																<th>Description</th>
																<th>UOM</th>
																<th>Qty</th>
																<th>Price</th>
																<th>Amount</th>
																<th>Cur</th>
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
									<button type="button" id="btnInsDet" onClick="InsertSI()" class="btn btn-primary">Insert</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

							</div>
					</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End FULL INVOICE LIST -->


	<!-- discount modal -->
	<div class="modal fade" id="MyDiscModal" role="dialog">
			<div class="modal-dialog modal-lg">
					<div class="modal-content">
							<div class="modal-header">
									<button type="button" class="close"  aria-label="Close" onclick="chkCloseDiscs();"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title" id="invdiscounthdr"> Discounts </h3>           
							</div>
			
							<div class="modal-body">
									<input type="hidden" id="currentITM" value="">
									<input type="hidden" name="hdnrowcnt3" id="hdnrowcnt3">
									<table id="MyTable3" class="MyTable table table-condensed" width="100%">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999" width="50%">Description</th>
												<th style="border-bottom:1px solid #999">Type</th>
												<th style="border-bottom:1px solid #999">Value</th>
											</tr>
										</thead>
										<tbody class="tbody">
											<?php
												$sqldisc = mysqli_query($con,"Select A.ctranno, A.citemnoident, A.citemno, A.discounts_list_code, A.cdisctype, A.nvalue, A.namount, B.cacctno, B.nident, B.cdesc from sales_t_disc A left join discounts_list B on a.compcode=B.compcode and A.discounts_list_code=B.ccode where A.ctranno = '$txtctranno' and B.compcode='$company'");
												if (mysqli_num_rows($sqldisc)!=0) {
													while($rowdisc = mysqli_fetch_array($sqldisc, MYSQLI_ASSOC)){

														$nident = $rowdisc['nident'];
														$idnum = $rowdisc['citemnoident'];
											?>
												<tr class="<?=$idnum?>">
													<td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;">
														<input type='hidden' value='<?=$rowdisc['discounts_list_code']?>' name='txtdiscscode' id='txtdiscscode<?=$nident.$idnum?>'> <?=$rowdisc['cdesc']?> 
														<input type='hidden' value='<?=$rowdisc['cacctno']?>' name='txtdiscacctno' id='txtdiscacctno<?=$nident.$idnum?>'> 
														<input type='hidden' value='<?=$rowdisc['citemno']?>' name='txtdiscitemno' id='txtdiscitemno<?=$nident.$idnum?>'>
													</td>
													<td>
														<select class="form-control input-sm" name="secdiscstyp" id="secdiscstyp<?=$nident.$idnum?>">
															<option value="fix" <?=($rowdisc['cdisctype']=="fix") ? "selected" : ""; ?>>FIX</options>
															<option value="percentage" <?=($rowdisc['cdisctype']=="percentage") ? "selected" : ""; ?>>PERCENTAGE</options>
														</select>
													</td>
													<td>
															<input type='text' name='txtdiscsval' id='txtdiscsval<?=$nident.$idnum?>' class='form-control input-xs' value='<?=$rowdisc['nvalue']?>'>
													</td>
													<td>
															<input type='text' name='txtdiscsamt' id='txtdiscsamt<?=$nident.$idnum?>' class='form-control input-xs' value='<?=$rowdisc['namount']?>' readonly>
													</td>
												</tr>
												<script type="text/javascript">
													$("#txtdiscsval<?=$nident.$idnum?>").on('keyup', function(event) {
														if($("#secdiscstyp<?=$nident.$idnum?>").val()=="fix"){
															xamty = parseFloat($(this).val());
															$("#txtdiscsamt<?=$nident.$idnum?>").val(xamty.toFixed(2));
														}else{
															//getprice
															xprice = $("#txtnprice<?=$idnum?>").val();

															xamty = parseFloat(xprice) * (parseFloat($("#txtdiscsval<?=$nident.$idnum?>").val()) / 100);
															$("#txtdiscsamt<?=$nident.$idnum?>").val(xamty.toFixed(2));
														}
													});
												</script>
											<?php
													}
												}
											?>
										</tbody>
									</table>
			
							</div>

							<div class="modal-footer">

							</div>
					</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
	</div>
	<!-- /discount.modal -->

	<!-- QUOTE/BILLING-->
	<div class="modal fade" id="myQORef" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="QOListHdr">Billing Statement List</h3>
				</div>
							
				<div class="modal-body" style="height:40vh">							
					<div class="col-xs-12 nopadding">
						<div class="form-group">
							<div class="col-xs-3 nopadding pre-scrollable" style="height:37vh">
								<table name='MyQOTbl' id='MyQOTbl' class="table table-small table-highlight small">
									<thead>
										<tr>
											<th>Bill No</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>

							<div class="col-xs-9 nopadwleft pre-scrollable" style="height:37vh">
								<table name='MyQODetList' id='MyQODetList' class="table table-small small">
									<thead>
										<tr>
											<th align="center"> <input name="allboxqo" id="allboxqo" type="checkbox" value="Check All" /></th>
											<th>Item No</th>
											<th>Description</th>
											<th>UOM</th>
											<th>Qty</th>
											<th>Price</th>
											<th>Amount</th>
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
					<button type="button" id="btnQOInsDet" onClick="InsertSI('QO')" class="btn btn-primary">Insert</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End LIST REFERENCES -->

</form>

<?php
}
else{
?>
<form action="SI_edit.php" name="frmpos2" id="frmpos2">
  <fieldset>
   	<legend>Sales Invoice</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">TRANS NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?=$txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>SI No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>


<!-- 1) Alert Modal -->
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

				<!--modal entry view-->
				<div class="modal fade" id="modGLEntry" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" id="btn-closemod" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title">GL Entry</h3>
							</div>
							<div class="modal-body">
									
								<table width="100%" border="0" class="table table-condensed table-bordered atble-hover" id="TblGLEntry">
									<thead>
										<tr>
											<td>Account Code</td>
											<td>Account Title</td>
											<td>Account Debit</td>
											<td>Account Credit</td>  
										</tr>		
										<?php
											$getewtcd = mysqli_query($con,"SELECT acctno,ctitle,sum(ndebit) as ndebit,sum(ncredit) as ncredit FROM glactivity where compcode='$company' and ctranno='$txtctranno' Group By acctno,ctitle"); 
											if (mysqli_num_rows($getewtcd)!=0) {
												while($row = mysqli_fetch_array($getewtcd, MYSQLI_ASSOC)){
										?>					
											<tr>
												<td><?=$row['acctno']?></td>
												<td><?=$row['ctitle']?></td>
												<td align="right"><?=(floatval($row['ndebit']) != 0) ? number_format($row['ndebit'],2) : ""?></td>
												<td align="right"><?=(floatval($row['ncredit']) != 0) ? number_format($row['ncredit'],2) : ""?></td>  
											</tr>	
										<?php
												}
											}
										?>
								</table>
									
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>

<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
               <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height: 99%; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


</body>
</html>

<script type="text/javascript">
var xChkBal = "";
var xChkLimit = "";
var xChkLimitWarn = "";
var xChkVatableStatus = "";

var slctqryvatc = "";
var slctvatsoption = "";


var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;


var file_name = <?= json_encode(@$arrname) ?>;
/**
 * Checking of list files
 */
if(file_name.length != 0){
	file_name.map(({name, ext}) => {
		console.log("Name: " + name + " ext: " + ext)
	})

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var list_file = [];
	var file_config = [];
	var extender;
	/**
	 * setting up an list of file and config of a file
	 */
	file_name.map(({name, ext}, i) => {
		list_file.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/<?=$company."_".$txtctranno?>/" + name)
		console.log(ext);

		if(jQuery.inArray(ext, arroffice) !== -1){
			extender = "office";
		} else if (jQuery.inArray(ext, arrimg) !== -1){
			extender = "image";
		} else if (ext == "txt"){
			extender = "text";
		} else {
			extender =  ext;
		}

		console.log(extender)
		file_config.push({
			type : extender, 
			caption : name,
			width : "120px",
			url: "th_filedelete.php?id="+name+"&code=<?=$txtctranno?>", 
			key: i + 1
		});
	})
}

	<?php
		if($poststat == "True"){
	?>
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='SI_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?=$txtctranno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='SI.php';
		}
	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $("#btnIns").is(":disabled")==false){
			openinv();
		}
	  }

	});
	<?php
		}
	?>

	$(document).ready(function(e) {	
		$.ajax({
			url : "../../include/th_xtrasessions.php",
			type: "Post",
			async:false,
			dataType: "json",
			success: function(data)
			{	
				console.log(data);
            	$.each(data,function(index,item){
					xChkBal = item.chkinv; //0 = Check ; 1 = Dont Check
					xChkLimit = item.chkcustlmt; //0 = Disable ; 1 = Enable
					xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
              		xChkVatableStatus = item.chkcompvat;						   
				});
			}
		});


		if(file_name.length > 0){
			$('#file-0').fileinput({
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: list_file,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/RFP_Files/<?=$company."_".$txtctranno?>/{filename}',
				initialPreviewConfig: file_config
			});
		} else {
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}
				
	//	if(xChkBal==1){
	//		$("#tblAvailable").hide();
	//	}
	//	else{
	//		$("#tblAvailable").show();
	//	}

	if(xChkVatableStatus==1){
		$(".chkVATClass").show();	
		$("#isewt").show();
		$("#isewt2").show();
	}
	else{
		$(".chkVATClass").hide();
		$("#isewt").hide();
		$("#isewt2").hide();
	}

	if(xChkLimit==0){
		$(".chklimit").hide();
	}
	else{
		$(".chklimit").show();
	}

	if($("#incmracct").val()=="item"){
		$(".chkinctype").show();
	}else{
		$(".chkinctype").hide();
	}

	$("#basecurrval").autoNumeric('init',{mDec:4});
	$("#selewt").select2();
	$("#txtnGrossDisc").autoNumeric('init',{mDec:2});

	loaddetails();
	loaddetinfo();

    ComputeGross();

    if($("#selsityp").val()=="Goods"){
      $(".chkitmsadd").hide();
    }else{
      $(".chkitmsadd").show();

      $('#txtprodnme').attr("disabled", true);
      $('#txtprodid').attr("disabled", true);
    }
	  
	  disabled();

  
	$('#date_delivery').datetimepicker({
      	format: 'MM/DD/YYYY',
		//minDate: new Date(),
     });
		
		$('#date_delivery').on('dp.change', function(e){ 
			if($("#txtcustid").val()!=""){
				$.ajax({
				type:'post',
				url:'../get_customerid.php',
				data: 'c_id='+ $("#txtcustid").val(),                 
				success: function(value){
					//alert(value);
					if(value.trim()!=""){
						var data = value.split(":");
						
            if(xChkLimit==1){
              
              limit = Number(data[3]).toLocaleString('en', { minimumFractionDigits: 4 }); 

              $('#ncustbalance2').html("");
              $('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
              $('#hdncustlimit').val(data[3]);
              
              checkcustlimit(dInput, data[3]);

            }
					}
				}
				});
			}

		
		});

		$('body').on('focus',".cacctdesc", function(){
			var $input = $(".cacctdesc");

			var id = $(document.activeElement).attr('id');	
			var numid = id.replace("txtacctname","");

			$("#"+id).typeahead({
				items: 10,
				source: function(request, response) {
					$.ajax({
						url: "../th_accounts.php",
						dataType: "json",
						data: {
							query: $("#"+id).val()
						},
						success: function (data) {
							console.log(data);
							response(data);
						}
					});
				},
				autoSelect: true,
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.acct + '</span><br><small>' + item.name + '</small></div>';
				},
				highlighter: Object,
				afterSelect: function(item) { 

					$('#'+id).val(item.name).change(); 
					$("#txtacctno"+numid).val(item.id); 
					$("#txtacctcode"+numid).val(item.acct);

				}
			});

		});

		$("#allbox").click(function(e){
				var table= $(e.target).closest('table');
				$('td input:checkbox',table).not(this).prop('checked', this.checked);
			});

			$("#allboxqo").click(function(e){
				var table= $(e.target).closest('table');
				$('td input:checkbox',table).not(this).prop('checked', this.checked);
			});

	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_product.php",
				dataType: "json",
				data: { query: $("#txtprodnme").val(), itmbal: xChkBal, styp: $("#selsityp").val() },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtprodnme').val(item.desc).change(); 
			$('#txtprodid').val(item.id); 
			$("#hdnunit").val(item.cunit); 
			$("#hdnctype").val(item.citmcls);
			$("#hdnqty").val(item.nqty);
			$("#hdnqtyunit").val(item.cqtyunit);
			if($("#hdndefVAT").val()==""){
				$("#hdncvat").val(item.ctaxcode); 
			}else{
				$("#hdncvat").val($("#hdndefVAT").val()); 
			}	
			$("#hdncewt").val("");

			$("#hdnacctno").val(item.cacctno); 
			$("#hdnacctid").val(item.cacctid); 
			$("#hdnacctdesc").val(item.cacctdesc); 
			
	  		addItemName("","","","","","","","","");
			
			
		}
	
	});


	$("#txtprodid").keypress(function(event){
		if(event.keyCode == 13){

		$.ajax({
			url:'../get_productid.php',
			data: 'c_id='+ $(this).val() + "&itmbal="+xChkBal+"&styp="+ $("#selsityp").val(),                 
			success: function(value){
				var data = value.split(",");
				$('#txtprodid').val(data[0]);
				$('#txtprodnme').val(data[1]);
			    $('#hdnunit').val(data[2]);
				$("#hdnqty").val(data[3]);
				$("#hdnqtyunit").val(data[4]);
				$("#hdnctype").val(data[5]);
				if($("#hdndefVAT").val()==""){
					$("#hdncvat").val(data[6]);
				}else{
					$("#hdncvat").val($("#hdndefVAT").val()); 
				}
				$("#hdncewt").val("");

				$("#hdnacctno").val(data[7]); 
				$("#hdnacctid").val(data[8]); 
				$("#hdnacctdesc").val(data[9]); 

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
			var isItem = "NO";
			var disID = "";
			
			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				if($("#txtprodid").val()==disID){
					
					isItem = "YES";

				}
			});	

		//if value is not blank
		 }else{
			 alert("ITEM BARCODE NOT EXISTING!");
			 $('#txtprodnme').focus();
		 }
		 
		if(isItem=="NO"){		

			myFunctionadd("","","","","","","","","","");
			ComputeGross();	
			
	    }
	    else{
			
			addqty();
		}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		$("#hdnctype").val("");
    $("#hdncvat").val("");
		$("#hdncewt").val("");

	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if enter is clicked
		}
		
	});
	

  $("#selsityp").on("change", function(){

      var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
    var lastRow = tbl.length-1;

    if(lastRow > 0){
      var x = confirm("Changing this will erase all details!");
      if (x == true) {
        $("#MyTable").find("tr:gt(0)").remove();
      }
    }
    else{
      $("#MyTable").find("tr:gt(0)").remove();
    }

    if($(this).val()=="Goods"){

      $(".chkitmsadd").hide();

    }else{
      $(".chkitmsadd").show();
    }
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
	

		$("#selewt").on("change", function(){ 

			var rowCount = $('#MyTable tr').length;
			if(rowCount>1){
				if($(this).val()!=""){			
						for (var i = 1; i <= rowCount-1; i++) {

							$("#selitmewtyp"+i).attr("disabled", false);

							var slctdvalid = $(this).val();

							$("#selitmewtyp"+i).val(slctdvalid);
							$("#selitmewtyp"+i).trigger('change'); 
							
						}
				}else{
					for (var i = 1; i <= rowCount-1; i++) {
						$("#selitmewtyp"+i).attr("disabled", false);
					}
				}

			}
		});

		$("#btnentry").on("click", function(){		
			$("#modGLEntry").modal("show");
		});

		$("#txtnGrossDisc").on("keyup", function(){
			var ttgross = $("#txtnGrossBef").val();

			TotAmt = parseFloat(ttgross) -  parseFloat($(this).val());

			$("#txtnGross").val(TotAmt);
			$("#divtxtnGross").text(TotAmt.toFixed(2));
			$("#divtxtnGross").formatNumber();  

		});


});

function checkcustlimit(id,xcred){
  //Check Credit Limit BALNCE here

  var xBalance = 0;
  var xinvs = 0;
  var xors = 0;
  var dte = $("#date_delivery").val();
  
    $.ajax ({
      url: "../th_creditlimit.php",
      data: { id: id },
      async: false,
      dataType: "json",
      success: function( data ) {
                      
        console.log(data);
        $.each(data,function(index,item){
          if(item.invs!=null){
            xinvs = item.invs;
          }
          
          if(item.ors!=null){
            xors = item.ors;
          }
          
        });
      }
    });
  
  //alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
    
  xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
  $("#hdncustbalance").val(xBalance);
  
  	if(xBalance > 0){
		xBalance = Number(xBalance).toLocaleString('en', { minimumFractionDigits: 2 });
		$("#ncustbalance").html("<b><font size='+1'>"+xBalance+"</font></b>");
	}
	else{
		if(parseFloat(xcred) > 0){

			if(xChkLimitWarn==0) { //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
				$("#ncustbalance").html("<b><i><font color='red'>Max Limit Reached</font></i></b>");
			}
			else if(xChkLimitWarn==1) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='white' size='+1'>Delivery is blocked</font></i></b>");
			}
			else if(xChkLimitWarn==2) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='white' size='+1'>ORDERS BLOCKED</font></i></b>");
				$("#btnSave").attr("disabled", true);
				$("#btnIns").attr("disabled", true);

				if($("#selsityp").val()!="Goods"){
					$('#txtprodnme').attr("disabled", true);
					$('#txtprodid').attr("disabled", true);
				}

			}
		}else{
			$("#ncustbalance").html("<b><i><font color='red'>Unlimited Credit Limit</font></i></b>");
		}
	}

}

function addItemName(qty,price,ndisc,curramt,amt,factr,cref,nrefident,citmcls,cvat){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

	//	var isItem = "NO";
	//	var disID = "";

	//		$("#MyTable > tbody > tr").each(function() {	
	//			disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
	//			disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				
	//			if($("#txtprodid").val()==disID && cref==disref){
					
	//				isItem = "YES";

	//			}
	//		});	

	// if(isItem=="NO"){	
		myFunctionadd(qty,price,ndisc,curramt,amt,factr,cref,nrefident,citmcls,cvat);
		//myFunctionadd(item.totqty,item.nprice,item.ndisc,item.namount,item.nfactor,item.xref,item.citmtyp)
		
		ComputeGross();	

//	 }
	// else{

	//	addqty();	
			
	// }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		$("#hdnctype").val("");
		
	 }

}

function myFunctionadd(qty,pricex,ndisc,curramt,amtx,factr,cref,nrefident,citmcls,cvat){

	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	var itmacctno = $("#hdnacctno").val(); 
	var itmacctid = $("#hdnacctid").val(); 
	var itmacctnm = $("#hdnacctdesc").val();
	
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = chkprice(itmcode,itmunit,itmccode,$("#date_delivery").val());
		var curramtz = price;
		//var amtz = price;
		var itmctype= $("#hdnctype").val();
		var factz = 1;
		var xdisc = 0;
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var curramtz = curramt;
		//var amtz = amtx;	
		var factz = factr;
		var itmctype= citmcls;
		var xdisc = ndisc;
	}	

	var baseprice = curramtz * parseFloat($("#basecurrval").val());
	
	var uomoptions = "";

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	if(cref==null || cref==""){
		cref = ""
			var qtystat = "";
			var isselctd = "";			
			
			
			var xz = $("#hdnitmfactors").val();
			var uomoptions = "<option value='"+itmunit+"' selected>"+itmunit+"</option>";

				$.each(jQuery.parseJSON(xz), function() { 
					if(itmcode==this['cpartno']){

						uomoptions = uomoptions + "<option value='"+this['cunit']+"'>"+this['cunit']+"</option>";

					}
				});

			
			uomoptions = " <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">" + uomoptions + "</select>";
		
	}else{
		uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom"+lastRow+"\">"+itmunit;
		qtystat = "readonly";
	}

		
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode"+lastRow+"\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+nrefident+"' name=\"txtcrefident\" id=\"txtcrefident\"> <input type='hidden' value='"+itmctype+"' name=\"hdncitmtype\" id=\"hdncitmtype"+lastRow+"\"> </td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmunit = "<td width=\"100\" nowrap>"+uomoptions+"</td>";

		var tditmewts = "";
		if(xChkVatableStatus==1){ 
			
			var gvnewt = $("#selewt").val();
				var xz = $("#hdnewtlist").val();
				ewtoptions = "";

				$.each(jQuery.parseJSON(xz), function() { 

					var newtcode = $("#hdncewt").val();
					var splitString = newtcode.split(',');
					var ewtFound;
					for (var i = 0; i < splitString.length; i++) {
						var stringPart = splitString[i];
						if (stringPart != this['ctaxcode']) continue;

						ewtFound = true;
						break;
					}

					if(ewtFound){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
				});

				if(gvnewt==""){
					isdisabled = "disabled";
				}else{
					isdisabled = "";
				}

				tditmewts = "<td width=\"150\" nowrap> <select class='form-control input-xs' name=\"selitmewtyp\" id=\"selitmewtyp"+lastRow+"\" "+isdisabled+" multiple> <option value=\"none\">None</option>" + ewtoptions + "</select> </td>";

		}

  var tditmvats = "";
  var itsvats = "";
    if(xChkVatableStatus==1){ 

			var xz = $("#hdntaxcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if($("#hdncvat").val()==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
				});

			tditmvats = "<td width=\"100\" nowrap> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";


    }

	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric2  form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' > </td>";
	
	var tditmdisc = "<td width=\"100\" nowrap> <input type='text' value='"+xdisc+"' class='numeric form-control input-xs' style='text-align:right; cursor: pointer' name=\"txtndisc\" id='txtndisc"+lastRow+"' readonly onclick=\"getdiscount('"+itmcode+"', "+lastRow+")\"> </td>";

	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";
			
	var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> </td>";

	if($("#incmracct").val()=="item"){
			var tdglaccount = "<td nowrap><input type='text' value='"+itmacctid+"' class='form-control input-xs' name=\"txtacctcode\" id='txtacctcode"+lastRow+"' readonly> <input type='hidden' value='"+itmacctno+"' name=\"txtacctno\" id='txtacctno"+lastRow+"'> </td>";

			var tdgltitle = "<td nowrap><input type='text' value='"+itmacctnm+"' class='cacctdesc form-control input-xs' name=\"txtacctname\" id='txtacctname"+lastRow+"'></td>";

			var tditmdel = "<td nowrap> <input class='btn btn-danger btn-xs btn-block' type='button' id='del"+ itmcode +"' value='delete' data-var='"+lastRow+"'/></td>";
		}else{
			var tdglaccount = "";
			var tdgltitle = "";
			var tditmdel = "<td nowrap> <input type='hidden' value='' name=\"txtacctcode\" id='txtacctcode"+lastRow+"'> <input type='hidden' value='"+itmacctno+"' name=\"txtacctno\" id='txtacctno"+lastRow+"'>  <input type='hidden' value='' name=\"txtacctname\" id='txtacctname"+lastRow+"'> <input class='btn btn-danger btn-xs btn-block' type='button' id='del"+ itmcode +"' value='delete' data-var='"+lastRow+"'/></td>";

		}
	
	var tditmdel = "<td width=\"90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del"+ lastRow +"' value='delete' data-var='"+lastRow+"'/> &nbsp; </td>"; // <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/>

	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmewts + tditmvats + tditmunit + tditmqty + tditmprice + tditmdisc + tditmbaseamount + tditmamount + tdglaccount + tdgltitle + tditmdel + '</tr>');

									$("#del"+lastRow).on('click', function() {
										
											var xy = $(this).data('var');
											
											$(this).attr("data-var",parseInt(xy)-1);

											//remove discounts rows
											$("#MyTable3 > tbody > tr").each(function() {					
												varxc = $(this).attr("class");
												if(parseInt(varxc)!==parseInt(lastRow)){
													$(this).remove();
												}
											});
											
											$(this).closest('tr').remove();
											
											ReIdentity(xy);
											ComputeGross();									
										
									});

									$("input.numeric2").autoNumeric('init',{mDec:4});
									$("input.numeric").autoNumeric('init',{mDec:2});

									$("#selitmewtyp"+lastRow).select2();

									$("#selitmvatyp"+lastRow).on("change", function() {
												ComputeGross();
									});
									/*
                  $("input.numeric").numeric(
                    {negative: false}
                  );

                  $("input.numericdec").numeric(
                    {
                      negative: false,
                      decimalPlaces: 4
                    }
                  );
									*/

									$("input.numeric, input.numeric2").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric, input.numeric2").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});
									
									$(".xseluom").on('change', function() {

										var xyz = chkprice(itmcode,$(this).val(),itmccode,xtoday);
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										//alert($(this).attr('id'));
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										var fact = setfactor($(this).val(), itmcode);
										//alert(fact);
										$('#hdnfactor'+lastRow).val(fact.trim());
										
									});
									
									ComputeGross();
									
									
}

			
	function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val().replace(/,/g,'');
			nprc = parseFloat(nprc);
			
			ndsc = $("#txtndisc"+r).val().replace(/,/g,'');
			ndsc = parseFloat(ndsc);
			
			if (parseFloat(ndsc) != 0) {
				nprc = parseFloat(nprc) - parseFloat(ndsc);
			}
			
			namt = nqty * nprc;
			namt2 = namt * parseFloat($("#basecurrval").val().replace(/,/g,''));
						
			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).val(namt);	

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2}); 

	}
	
	function ComputeGross(){
		var rowCount = $('#MyTable tr').length;
		
		var gross = 0;
		var nvatz = 0;
		var nvatble = 0;

		var nexmptTot = 0;
		var nzeroTot = 0;
		var nvatbleTot = 0;
		var vatzTot = 0;

		var totewt = 0;

		var xcrate = 0;

		var TotAmtDue = 0;

		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {
		
				var slctdval = $("#selitmvatyp"+i+" option:selected").data('id'); //data-id is the rate
				var slctdvalid = $("#selitmvatyp"+i+" option:selected").val();

				if(slctdvalid=="VT" || slctdvalid=="VTGOV"){
					nvatble = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
					vatz = nvatble * (parseInt(slctdval)/100);

					nvatbleTot = nvatbleTot + nvatble;
					vatzTot = vatzTot + vatz;
					
				}else if(slctdvalid=="VE"){
					nexmptTot = nexmptTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}else if(slctdvalid=="ZR"){
					nzeroTot = nzeroTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}

				
				gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
			}
		}

		//VATABLE
		$("#txtnNetVAT").val(nvatbleTot);
		$("#divtxtnNetVAT").text(nvatbleTot.toFixed(2));
		$("#divtxtnNetVAT").formatNumber();

		//EXEMPT
		$("#txtnExemptVAT").val(nexmptTot);
		$("#divtxtnExemptVAT").text(nexmptTot.toFixed(2));
		$("#divtxtnExemptVAT").formatNumber();

		//ZERO RATED
		$("#txtnZeroVAT").val(nzeroTot);
		$("#divtxtnZeroVAT").text(nzeroTot.toFixed(2));
		$("#divtxtnZeroVAT").formatNumber();
		
		// LESS VAT
		$("#txtnVAT").val(vatzTot);
		$("#divtxtnVAT").text(vatzTot.toFixed(2));
		$("#divtxtnVAT").formatNumber();

		//TOTAL GROSS
		$("#txtnGrossBef").val(gross);
		$("#divtxtnGrossBef").text(gross.toFixed(2));
		$("#divtxtnGrossBef").formatNumber();

		//Total Amount
		$gettmtt = gross - parseFloat($("#txtnGrossDisc").val());
		gross2 = $gettmtt * parseFloat($("#basecurrval").val().replace(/,/g,''));
		
		$("#txtnGross").val(gross2);
		$("#txtnBaseGross").val($gettmtt);
		$("#divtxtnGross").text($gettmtt.toFixed(2));		
		$("#divtxtnGross").formatNumber();

			
	}
		


	function ReIdentity(xy){
		
		var rowCount = $('#MyTable tr').length;
					
		if(rowCount>1){
			for (var i = xy+1; i <= rowCount; i++) {
				//alert(i);
				var ITMCode = document.getElementById('txtitemcode' + i);
				var SelUOM = document.getElementById('seluom' + i); 
				var ItmTyp = document.getElementById('hdncitmtype' + i); 
				var SelVAT = document.getElementById('selitmvatyp' + i);
				var nQty = document.getElementById('txtnqty' + i);
				var MainUom = document.getElementById('hdnmainuom' + i);
				var nFactor = document.getElementById('hdnfactor' + i);
				var nPrice = document.getElementById('txtnprice' + i);
				var nDisc = document.getElementById('txtndisc' + i); 
				var nTranAmount = document.getElementById('txtntranamount' + i);
				var nAmount = document.getElementById('txtnamount' + i);

				var cacctcode = document.getElementById('txtacctcode' + i); 
				var cacctno = document.getElementById('txtacctno' + i);
				var cacctnm = document.getElementById('txtacctname' + i);

				var RowInfo = document.getElementById('row_' + i + '_info');					
				
				var za = i - 1;
				
				//alert(za);
				ITMCode.id = "txtitemcode" + za;
				SelUOM.id = "seluom" + za;
				ItmTyp.id = "hdncitmtype" + za;
				SelVAT.id = "selitmvatyp" + za;
				nQty.id = "txtnqty" + za;
				MainUom.id = "hdnmainuom" + za;
				nFactor.id = "hdnfactor" + za;
				nPrice.id = "txtnprice" + za;
				nDisc.id = "txtndisc" + za;
				nTranAmount.id = "txtntranamount" + za;
				nAmount.id = "txtnamount" + za;
				cacctcode.id = "txtacctcode" + za;
				cacctno.id = "txtacctno" + za;
				cacctnm.id = "txtacctname" + za;
				RowInfo.id = "row_" + za + "_info";


				$("#MyTable3 > tbody > tr").each(function() {					
					varxc = $(this).attr("class");
					if(parseInt(varxc)!==parseInt(i)){
						$(this).removeClass(i)
						$(this).addClass(za);
					}
				});

				
			}
		}
	}
		
function addqty(){

	var itmcode = document.getElementById("txtprodid").value;

	var TotQty = 0;
	var TotAmt = 0;
	
	$("#MyTable > tbody > tr").each(function() {	
	var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
	
	//alert(disID);
		if(disID==itmcode){
			
			var itmqty = $(this).find("input[name='txtnqty']").val();
			var itmprice = $(this).find("input[name='txtnprice']").val().replace(/,/g,'');
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
			TotAmt = TotQty * parseFloat(itmprice);
			$(this).find("input[name='txtnamount']").val(TotAmt);
		}

	});
	
	ComputeGross();

}


function viewhidden(itmcde,itmnme){
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow2 = tbl.length-1;
	
	if(lastRow2>=1){
			$("#MyTable2 > tbody > tr").each(function() {	
			
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				//alert(citmno+"!="+itmcde);
				if(citmno!=itmcde){
					
					$(this).find('input[name="txtinfofld"]').attr("disabled", true);
					$(this).find('input[name="txtinfoval"]').attr("disabled", true);
					//$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled", true);
					
				}
				else{
					$(this).find('input[name="txtinfofld"]').attr("disabled", false);
					$(this).find('input[name="txtinfoval"]').attr("disabled", false);
				//	$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled", false);
				}
				
			});
	}			
			
	addinfo(itmcde,itmnme);
	
	$('#MyDetModal').modal('show');
}

function addinfo(itmcde,itmnme){
	//alert(itmcde+","+itmnme);
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	
	var tdinfocode = "<td><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
	var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
	var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfodel = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + lastRow + itmcde + "' value='delete' /></td>";

	//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
	
	$('#MyTable2 > tbody:last-child').append('<tr>'+tdinfocode + tdinfodesc + tdinfofld + tdinfoval + tdinfodel + '</tr>');

									$("#delinfo"+lastRow+itmcde).on('click', function() {
										//alert($(this).prop('disabled'));
										//if($(this).prop('disabled')!==false){
											$(this).closest('tr').remove();
										//}
									});

}

function chkCloseInfo(){
	var isInfo = "TRUE";
	
	$("#MyTable > tbody > tr").each(function(index) {	
			
		var citmfld = $(this).find('input[name="txtinfofld"]');
		var citmval = $(this).find('input[name="txtinfoval"]');
		
		if(citmfld=="" || citmval==""){
			isInfo = "FALSE";
		}
				
	});

	
	if(isInfo == "TRUE"){
		$('#MyDetModal').modal('hide');	}
	else{
		alert("Incomplete info values!");
	}
}


function chkprice(itmcode,itmunit,ccode,datez){
	var result;
			
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit, dte: datez },
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


function openinv(typ){
			if($('#txtcustid').val() == ""){
				alert("Please pick a valid customer!");
			}
			else{
				
				$("#txtcustid").attr("readonly", true);
				$("#txtcust").attr("readonly", true);

				//clear table body if may laman
				if(typ=="DR"){
					$('#MyInvTbl tbody').empty(); 
					$('#MyInvDetList tbody').empty();
				}else if(typ=="QO" || typ=="SO"){
					$('#MyQOTbl tbody').empty(); 
					$('#MyQODetList tbody').empty();
				}
				
				//get salesno na selected na
				var y;
				var salesnos = "";

				//ajax lagay table details sa modal body
				var x = $('#txtcustid').val();
				if(typ=="DR"){
					$('#InvListHdr').html("SO List: " + $('#txtcust').val());
					$("#btnInsDet").attr("onclick","InsertSI('DR')");
				}else if(typ=="QO"){
					$('#QOListHdr').html("Billing List: " + $('#txtcust').val());
					$("#btnQOInsDet").attr("onclick","InsertSI('QO')");
				}else if(typ=="SO"){
					$('#QOListHdr').html("Sales Order List: " + $('#txtcust').val());
					$("#btnQOInsDet").attr("onclick","InsertSI('SO')");
				}

				var xstat = "YES";
				
				//disable escape insert and save button muna
				fltrsalestyp = "";
				if(typ=="QO"){
					fltrsalestyp = $("#selsityp").val();
				}
				
				$.ajax({
					url: 'th_qolist.php',
					data: 'x='+x+'&typ='+typ+'&styp='+fltrsalestyp,
					dataType: 'json',
					method: 'post',
					success: function (data) {

						$("#allbox").prop('checked', false);
							
							console.log(data);
							$.each(data,function(index,item){

									
								if(item.cpono=="NONE"){
									if(typ=="DR"){
										$("#AlertMsg").html("No Deliver Receipt Available");
									}else if(typ=="QO"){
										$("#AlertMsg").html("No For Billing Available");
									}else{
										$("#AlertMsg").html("No Sales Order Available");
									}
									
									$("#alertbtnOK").show();
									$("#AlertModal").modal('show');

									xstat = "NO";
									
									$("#txtcustid").attr("readonly", false);
									$("#txtcust").attr("readonly", false);

								}
								else{

									if(typ=="DR"){

										$("<tr>").append(
										$("<td id='td"+item.cpono+"'>").text(item.cpono),
										$("<td>").text(item.ngross)
										).appendTo("#MyInvTbl tbody");

									}else if(typ=="QO" || typ=="SO"){

										$("<tr>").append(
										$("<td id='td"+item.cpono+"'>").text(item.cpono),
										$("<td>").text(item.ngross)
										).appendTo("#MyQOTbl tbody");
									}
									
									
									$("#td"+item.cpono).on("click", function(){
										opengetdet($(this).text(),typ);
									});
									
									$("#td"+item.cpono).on("mouseover", function(){
										$(this).css('cursor','pointer');
									});

								}

							});
							

							if(xstat=="YES"){
								if(typ=="DR"){
									$('#mySIRef').modal('show');
								}else if(typ=="QO" || typ=="SO"){
									$('#myQORef').modal('show');
								}
							}
					},
					error: function (req, status, err) {
							//alert();
							console.log('Something went wrong', status, err);
							$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
					}
				});
				
				
				
			}

	}

	function opengetdet(valz,typ){
		var drno = valz;

		$("#txtrefSI").val(drno);

		if(typ=="DR"){
			$('#InvListHdr').html("DR List: " + $('#txtcust').val() + " | DR Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
		}else if(typ=="QO"){
			$('#QOListHdr').html("Billing List: " + $('#txtcust').val() + " | Billing Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
		}else if(typ=="SO"){
			$('#QOListHdr').html("SO List: " + $('#txtcust').val() + " | SO Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
		}
		
		if(typ=="DR"){
			$('#MyInvDetList tbody').empty();
			//$('#MyDRDetList tbody').empty();
		}else if(typ=="QO" || typ=="SO"){
			$('#MyQODetList tbody').empty();
			//$('#MyDRDetList tbody').empty();
		}
			
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

						//alert('th_qolistdet.php?x='+drno+"&y="+salesnos+"&typ="+typ);
						$.ajax({
							url: 'th_qolistdet.php',
							data: 'x='+drno+"&y="+salesnos+"&typ="+typ,
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
								
										if (item.nqty>=1){

											if(typ=="DR"){
												$("<tr>").append(
												$("<td>").html("<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\" data-curr=\""+item.ccurrencycode+"\">"),
												$("<td>").text(item.creference),
												$("<td>").text(item.citemno),
												$("<td>").text(item.cdesc),
												$("<td>").text(item.cunit),
												$("<td>").text(item.nqty),
												$("<td>").text(item.nprice),
												$("<td>").text(item.nbaseamount),
												$("<td>").text(item.ccurrencycode)
												).appendTo("#MyInvDetList tbody");
											}else if(typ=="QO" || typ=="SO"){
												$("<tr>").append(
												$("<td>").html("<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\" data-curr=\""+item.ccurrencycode+"\">"),
												$("<td>").text(item.citemno),
												$("<td>").text(item.cdesc),
												$("<td>").text(item.cunit),
												$("<td>").text(item.nqty),
												$("<td>").text(item.nprice),
												$("<td>").text(item.namount)
												).appendTo("#MyQODetList tbody");
											}

										}										
									}
								});
							},
							complete: function(){
								$('#loadimg').hide();
							},
							error: function (req, status, err) {
								//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
								console.log('Something went wrong', status, err);
								$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
								$("#alertbtnOK").show();
								$("#AlertModal").modal('show');
							}
						});

	}

	function InsertSI(typ){	
		//check muna if pareparehas ng currency
		
		//get defsult curr if may laman na ang details
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var tblrowcnt = tbl.length;

		var trannocurr = "";
		var trannocurrcnt = 0;

			if(tblrowcnt>1){
				trannocurr = $("#selbasecurr").val();
				trannocurrcnt = 1;
			}

			$("input[name='chkSales[]']:checked").each( function () {

				if(trannocurr != $(this).data("curr")){
					trannocurr = $(this).data("curr");
					trannocurrcnt++;
				}
			});

		if(trannocurrcnt>1){
			alert("Multi currency in one invoice is not allowed!");
		}
		else{


				$("input[name='chkSales[]']:checked").each( function () {
			
		
					var tranno = $(this).data("id");
						var id = $(this).val();
		
						//alert("th_qolistput.php?id=" + tranno + "&itm=" + id + "&typ=" + typ);

						$.ajax({
							url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&typ=" + typ,
							type: "GET",
							dataType: "JSON",
							async: false,
							success: function(data)
							{	
								console.log(data);
								$.each(data,function(index,item){
								
									$('#txtprodnme').val(item.desc); 
									$('#txtprodid').val(item.id); 
									$("#hdnunit").val(item.cunit); 
									$("#hdnqty").val(item.nqty);
									$("#hdnqtyunit").val(item.cqtyunit);
									if(typ=="SO"){
										if($("#hdndefVAT").val()==""){
											$("#hdncvat").val(item.ctaxcode);
										}else{
											$("#hdncvat").val($("#hdndefVAT").val()); 
										}
									}else{
										$("#hdncvat").val(item.ctaxcode);
									}
									$("#hdncewt").val("");

									$("#hdnacctno").val(item.cacctno); 
									$("#hdnacctid").val(item.cacctid); 
									$("#hdnacctdesc").val(item.cacctdesc); 
									
									if(index==0){
										$("#selbasecurr").val(item.ccurrencycode).change();
										$("#hidcurrvaldesc").val(item.ccurrencydesc);
										convertCurrency(item.ccurrencycode);
									}


									addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.crefident,item.citmcls,item.ctaxcode)
													
							});
							
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							alert(jqXHR.responseText);
						}
						
					});

				});

			//alert($("#hdnQuoteNo").val());
		
			if(typ=="DR"){
				//$('#mySIModal').modal('hide');
				$('#mySIRef').modal('hide');
			}else if(typ=="QO" || typ=="SO"){
			//	$('#myQOModal').modal('hide');
				$('#myQORef').modal('hide');
			}
		}


	}


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "SI_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	$("#txtcsalesno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

	if(document.getElementById("hdnposted").value==1 && document.getElementById("hdnvoid").value==0){
		$("#btnentry").attr("disabled", false);
	}

		$("#btn-closemod").attr("disabled", false); 

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
				if(document.getElementById("hdnvoid").value==1){
					var msgsx = "VOIDED";
				}else{
					var msgsx = "POSTED";
				}
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			$("#txtcsalesno").val($("#hdncsalesno").val());
			$("#txtcsalesno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
		ComputeGross();

		$("#selewt").trigger('change');
		
		
		
			if(xChkLimit==1){
					
					$.ajax({
						type:'post',
						url:'../get_customerid.php',
						data: 'c_id='+ $('#txtcustid').val(),                 
						success: function(value){
							//alert(value);
							if(value.trim()!=""){
								var data = value.split(":");

                limit = Number(data[3]).toLocaleString('en', { minimumFractionDigits: 4 }); 

                $('#ncustbalance2').html("");
                $('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
                $('#hdncustlimit').val(data[3]);
          
                checkcustlimit($('#txtcustid').val(), data[3]);

							}
						}
					});
								

				}
		

	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		  var url = "SI_confirmprint.php?x="+x;
		//   var url = "SI_printv1.php?tranno="+x;
		  
		  $("#myprintframe").attr('src',url);


		  $("#PrintModal").modal('show');

	}
}


function loaddetails(){
	//alert($("#txtcsalesno").val());
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.desc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);
       			$("#hdncvat").val(item.ctaxcode);
				$("#hdncewt").val(item.cewtcode);
				$("#hdnacctno").val(item.cacctno); 
				$("#hdnacctid").val(item.cacctid); 
				$("#hdnacctdesc").val(item.cacctdesc); 
				
				//alert(item.citmtyp); myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident,citmcls)
				//alert(item.ndisc);
				myFunctionadd(item.totqty,item.nprice,item.ndisc,item.nbaseamount,item.namount,item.nfactor,item.xref,item.nrefident,item.citmtyp,item.ctaxcode);
				//addItemName(item.totqty,item.nprice,item.namount,item.nfactor,item.xref);
			});
			
				$("#txtprodid").val("");
				$("#txtprodnme").val("");
				$("#hdnunit").val("");
				$("#hdnqty").val("");
				$("#hdnqtyunit").val("");
      	$("#hdncvat").val("");
				$("#hdncewt").val("");


		}
	});

}

function loaddetinfo(){
	$.ajax ({
		url: "th_loaddetinfo.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				addinfo(item.id,item.desc,item.fldnme,item.cvalue);

			});

		}
	});

}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Customer Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	// ACTIVATE MUNA LAHAT NG INFO
	
	$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});

	// Check pag meron wla Qty na Order
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
			myav = $(this).find('input[type="hidden"][name="hdnavailqty"]').val();
			myfacx = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
			
			myprice = $(this).find('input[name="txtnamount"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}else{
				var myqtytots = parseFloat(myqty) * parseFloat(myfacx);

				if(parseFloat(myav) < parseFloat(myqtytots)){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
				}
			}
			
		//	if(myprice == 0 || myprice == ""){
		//		msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
		//	}

		});

		// Check if Credit Limit activated (kung sobra)
		if(xChkLimit==1){
			
			if(parseFloat($("#txtnGross").val().replace(/,/g, ''))>parseFloat($("#hdncustbalance").val().replace(/,/g, ''))){
					ISOK = "NO";
					msgz = "Available Credit Limit is not enough!";
					//$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Available Credit Limit is not enough!");
					//$("#alertbtnOK").show();
					//$("#AlertModal").modal('show');
					
					//return false;
					


			}
		}
		
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
	var VARHDRSTAT = "";
	var VARHDRERR = "";
	
		//Saving the header
		var trancode = $("#txtcsalesno").val();
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val();
		var selreinv = $("#selreinv").val();
    	var selsitypz = $("#selsityp").val();
		var siprintno = $("#csiprintno").val();
		var nnetvat = $("#txtnNetVAT").val(); 
		var nvat = $("#txtnVAT").val();
				
		//alert("SO_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		var myform = $("#frmpos").serialize();
		var formdata = new FormData($('#frmpos')[0]);
		formdata.delete('upload[]');
		jQuery.each($('#file-0')[0].files, function(i, file){
			formdata.append('file-'+i, file);
		})

		//console.log(formdata);
		//alert(formdata);
		$.ajax ({
			url: "SI_updatehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING SALES INVOICE: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				//alert(data.trim());
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			},
            error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
				console.log('Something went wrong', status, err);

				VARHDRSTAT = status;
				VARHDRERR = err;

            }
			
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
				//if(index>0){
				
					$(this).find('select[name="selitmewtyp"]').attr("disabled", false);

					var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					var crefident = $(this).find('input[type="hidden"][name="txtcrefident"]').val();
					var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
					var ewtcode = $(this).find('select[name="selitmewtyp"]').val();

						//getrate of selected
						var ewtrate = "";
						var cnt = 0;
						$(this).find('select[name="selitmewtyp"] > option:selected').each(function() {
							//	alert($(this).data("rate"));
							cnt++;
							if(cnt>1){
								ewtrate = ewtrate + ";" + $(this).data("rate");
							}else{
								ewtrate = ewtrate + $(this).data("rate");
							}
						});

					var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
					var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id'); 
					var cuom = $(this).find('select[name="seluom"]').val();
						
						if(cuom=="" || cuom==null){
							var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
						}
						
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nprice = $(this).find('input[name="txtnprice"]').val();
					var ndiscount = $(this).find('input[name="txtndisc"]').val(); 
					var ntranamt = $(this).find('input[name="txtntranamount"]').val();
					var namt = $(this).find('input[name="txtnamount"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();

						if($("#incmracct").val()=="item"){
							var acctcode = $(this).find('input[name="txtacctcode"]').val();
							var acctid = $(this).find('input[name="txtacctno"]').val();
							var acctname = $(this).find('input[name="txtacctname"]').val();
						}else if($("#incmracct").val()=="si"){
							var acctcode = "";
							var acctid = $('select[name="selpaytyp"] option:selected').data('id');
							var acctname = "";
						}else if($("#incmracct").val()=="customer"){ 
							var acctcode = "";
							var acctid = $("#hdncacctcodesalescr").val();
							var acctname = "";
						}

						if(nqty!==undefined){
							nqty = nqty.replace(/,/g,'');
							ndiscount = ndiscount.replace(/,/g,'');
							nprice = nprice.replace(/,/g,'');
							namt = namt.replace(/,/g,'');
							ntranamt = ntranamt.replace(/,/g,'');
						}

						$xinx = parseInt(index) + 1;
						//alert("SI_newsavedet.php?trancode="+trancode+"&crefno="+crefno+"&crefident="+crefident+"&indx="+$xinx+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+ nprice+"&ndiscount="+ndiscount+"&ntranamt="+ntranamt+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&ccode="+ccode+"&vatcode="+vatcode+"&nrate="+nrate+"&ewtcode="+ewtcode+"&ewtrate="+ewtrate+"&acctid="+acctid);

						$.ajax ({
							url: "SI_newsavedet.php",
							data: { trancode: trancode, crefno: crefno, crefident:crefident, indx:$xinx, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, ndiscount:ndiscount, ntranamt:ntranamt, namt:namt, mainunit:mainunit, nfactor:nfactor, ccode:ccode, vatcode:vatcode, nrate:nrate, ewtcode:ewtcode, ewtrate:ewtrate, acctid: acctid },
							async: false,
							success: function( data ) {

								if(data.trim()=="False"){
									isDone = "False";
								}
							}
						});
				//}
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "SI_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});

			//show all
			$("#MyTable3 > tbody > tr").each(function() {	
				
				$(this).show();

			});	

			//Save Discounts
				$("#MyTable3 > tbody > tr").each(function(index) {	
					
					var discnme = $(this).find('input[type="hidden"][name="txtdiscscode"]').val();
					var seldisctyp = $(this).find('select[name="secdiscstyp"]').val();
					var discval = $(this).find('input[name="txtdiscsval"]').val();
					var discamt = $(this).find('input[name="txtdiscsamt"]').val(); 
					var discacctno = $(this).find('input[type="hidden"][name="txtdiscacctno"]').val();  
					var discitmno = $(this).find('input[type="hidden"][name="txtdiscitemno"]').val();
					var discitmnoident =  $(this).attr("class");

				
					$.ajax ({
						url: "SI_newsavediscs.php",
						data: { trancode: trancode, indx: index, discnme: discnme, seldisctyp: seldisctyp, discval: discval, discamt: discamt, discacctno: discacctno, discitmno: discitmno, discitmnoident: discitmnoident},
						async: false,
						success: function( data ) {
							if(data.trim()=="False"){
								isDone = "False";
							}
						}
					});
					
				});
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY UPDATED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							$("#txtcsalesno").val(trancode);
							$("#frmpos").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}
			
		}
		else{
			$("#AlertMsg").html("Something went wrong<br>Status: "+VARHDRSTAT +"<br>Error: "+VARHDRERR);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}


	}
		
		

}


function convertCurrency(fromCurrency) {
  
  toCurrency = $("#basecurrvalmain").val(); //statgetrate
   $.ajax ({
	 url: "../th_convertcurr.php",
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
		 $("#txtnamount"+i).autoNumeric('destroy');
		 $("#txtnamount"+i).autoNumeric('init',{mDec:2});
	 }
 }


 ComputeGross();


}

function getdiscount(xyz,idnum){ //txtndisc txtnprice

	var xnprice = $("#txtnprice"+idnum).val().replace(/,/g,'');
	var xnitemno = $("#txtitemcode"+idnum).val()

	alert(xnprice);

	$("#currentITM").val(idnum);

	if(parseFloat(xnprice)>0){
		var cnt = 0;
		$("#MyTable3 > tbody > tr").each(function() {	
			
			varxc = $(this).attr("class");

			if(parseInt(varxc)!==parseInt(idnum)){
				$(this).hide();
			}else{
				$(this).show();
				cnt++;
			}
					
		});	


		if(cnt==0){
			var xz = $("#hdndiscs").val();
			$.each(jQuery.parseJSON(xz), function() { 

				var tbl = document.getElementById('MyTable3').getElementsByTagName('tr');
				var lastRow = tbl.length;

				var ident = this['ident'];
				
				var tddesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"><input type='hidden' value='"+this['ccode']+"' name='txtdiscscode' id='txtdiscscode"+ident+idnum+"'> "+this['cdesc']+" <input type='hidden' value='"+this['acctno']+"' name='txtdiscacctno' id='txtdiscacctno"+ident+idnum+"'> <input type='hidden' value='"+xnitemno+"' name='txtdiscitemno' id='txtdiscitemno"+ident+idnum+"'></td>";
				var tdtype = "<td><select class=\"form-control input-sm\" name=\"secdiscstyp\" id=\"secdiscstyp"+ident+idnum+"\"><option value=\"fix\" selected>FIX</options><option value=\"percentage\">PERCENTAGE</options></select></td>"
				var tdvals = "<td><input type='text' name='txtdiscsval' id='txtdiscsval"+ident+idnum+"' class='form-control input-xs' value='0'></td>";
				var tdamount = "<td><input type='text' name='txtdiscsamt' id='txtdiscsamt"+ident+idnum+"' class='form-control input-xs' value='0' readonly></td>";
				
				$('#MyTable3 > tbody:last-child').append('<tr class="'+idnum+'">'+tddesc + tdtype + tdvals + tdamount + '</tr>');

				$("#txtdiscsval"+ident+idnum).on('keyup', function(event) {
					if($("#secdiscstyp"+ident+idnum).val()=="fix"){
						xamty = parseFloat($(this).val());
						$("#txtdiscsamt"+ident+idnum).val(xamty.toFixed(2));
					}else{
						//getprice

						xamty = parseFloat(xnprice) * (parseFloat($("#txtdiscsval"+ident+idnum).val()) / 100);
						$("#txtdiscsamt"+ident+idnum).val(xamty.toFixed(2));
					}
				});


			});
		}

		$('#invdiscounthdr').text('Discounts: '+ $("#txtitemcode"+idnum).val());
		$('#MyDiscModal').modal('show');
	}else{
		$("#AlertMsg").html("Cannot add discount for zero price items!");
		$("#alertbtnOK").show();
		$("#AlertModal").modal('show');
	}

}

function chkCloseDiscs(){

	idnum = $("#currentITM").val();

	vcvxg = 0;
	$("#MyTable3 > tbody > tr").each(function() {
		varxc = $(this).attr("class");

		if(parseInt(varxc)==parseInt(idnum)){
			vcvxg = vcvxg + parseFloat($(this).find('input[name="txtdiscsamt"]').val());
		}

	});

	$("#txtndisc"+idnum).val(vcvxg.toFixed(2));

	ComputeAmt(idnum); 
	
	ComputeGross();

	$('#MyDiscModal').modal('hide');
}

</script>