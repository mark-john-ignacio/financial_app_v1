<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Quote";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Quote_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//PRINT
	$printstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Quote_print'");
	if(mysqli_num_rows($sql) == 0){
		$printstat = "False";
	}


	if(isset($_REQUEST['txtctranno'])){
			$txtctranno = $_REQUEST['txtctranno'];
	}
	else{
			$txtctranno = $_REQUEST['txtcsalesno'];
	}

	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.cpricever,c.cname as cdelname from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join customers_secondary c on a.compcode=c.compcode and a.cdelcode=c.ccode where a.ctranno = '$txtctranno' and a.compcode='$company'");

	/*
	function listcurrencies(){ //API for currency list
		$apikey = $_SESSION['currapikey'];
	
		$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
		//$obj = json_decode($json, true);
	
		return $json;
	}
	*/

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	$postbilling = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'qotype_billing'");
	if(mysqli_num_rows($sql) == 0){
		$postbilling = "False";
	}


	$postquote = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'qotype_quote'");
	if(mysqli_num_rows($sql) == 0){
		$postquote = "False";
	}
	 
	@$arrname = array();
	$directory = "../../Components/assets/QO/{$company}_{$txtctranno}/";
	if(file_exists($directory)){
		if (is_dir_empty($directory)) {
		}else{
			@$arrname = file_checker($directory);
		}
		
	}

	function is_dir_empty($dir) {
		return (count(scandir($dir)) == 2);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
   	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" href="../../include/summernote/summernote.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bootstrap-tagsinput/bootstrap-tagsinput.css?t=<?php echo time();?>"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
	<script src="../../Bootstrap/bootstrap-tagsinput/bootstrap-tagsinput.js" type="text/javascript"></script>

	<script src="../../include/summernote/summernote.js"></script>

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

	<script src="../../global/custom.js?h=<?php echo time();?>"></script>

	<style>
		fieldset.scheduler-border {
			border: 1px groove #ddd !important;
			padding: 0 0.5em 0.5em 0.5em !important;
			margin: 0 0 0 0 !important;
			-webkit-box-shadow:  0px 0px 0px 0px #000;
					box-shadow:  0px 0px 0px 0px #000;
		}

		legend.scheduler-border {
			font-size: 1.2em !important;
			font-weight: bold !important;
			text-align: left !important;
			width:auto;
			padding:0 10px;
			border-bottom:none;
		}

		input:required {
		border: 1px solid #f18888 !important;
		outline: none;
		}
	</style>
</head>

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus(); ">
	<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

<?php


if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];

		$CustCodeDel = $row['cdelcode'];
		$CustNameDel = $row['cdelname']; 

		$Gross = $row['ngross']; // gross base sa Main currency ng system
		$BaseGross = $row['nbasegross']; //gross base sa currency selected

		$ccontname = $row['ccontactname'];
		$ccontdesg = $row['ccontactdesig'];
		$ccontdept = $row['ccontactdept'];
		$ccontemai = $row['ccontactemail'];
		$ccontsalt = $row['ccontactsalut'];

		$cAccpBy = $row['cacceptedby'];

		$cvattyp = $row['cvattype'];
		$cterms = $row['cterms'];
		$cdelinfo = $row['cdelinfo'];
		$cservinfo = $row['cservinfo'];
		$cSelType = $row['csalestype'];

		$cQOType = $row['quotetype']; 
		$cRCType = $row['crecurrtype'];

		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$QuoteDate = $row['dtrandate'];
		$cpricever = $row['cpricever'];

		$CurrCode = $row['ccurrencycode'];
		$CurrRate = $row['nexchangerate'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid']; 

		$cemailstoo = $row['cemailto'];
		$cemailsccc = $row['cemailcc'];
		$cemailsbcc = $row['cemailbcc'];
		$cemailsbjc = $row['cemailsubject'];
		$cemailsbod = $row['cemailbody'];
		$cemailsentby = $row['cemailsentby'];
		$cemailsentdate = $row['demailsent'];
	}
	
	
	if(!file_exists("../../imgemps/".$CustCode .".jpg")){
		$imgsrc = "../../images/blueX.png";
	}
	else{
		$imgsrc = "../../imgemps/".$CustCode .".jpg";
	}

	//get last email body
	if($cemailsbod==""){
		$sqlno = mysqli_query($con,"select cemailbody from quote where compcode='$company' and quotetype='$cQOType' and ccode='$CustCode' Order By ddate DESC LIMIT 1");
		if (mysqli_num_rows($sqlno)!=0) {
			while($row = mysqli_fetch_array($sqlno, MYSQLI_ASSOC)){
				$cemailsbod = $row['cemailbody'];
			}
		}
	}

?>
<form action="Quote_edit.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post">
	<fieldset>
    	<legend>
			<div class="col-xs-6 nopadding"> Quotation Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
			<li class="active"><a href="#home">Quotation Details</a></li>
			<li><a href="#attc">Attachments</a></li>
		</ul>

		<div class="tab-content">
			<div id="home" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

				<div class="col-xs-12 nopadwtop">
					<div class="col-xs-1 nopadwtop2x"><b>&nbsp;&nbsp;Trans. No.</b></div> 
					<div class="col-xs-3 nopadding">
						<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
						<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
						<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
						<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
					</div>
					<div class="col-xs-2 nopadwleft">
						
					</div>
					<div class="col-xs-5" style="text-align: right;">
						<div id="statmsgz" class="small" style="display:inline"></div>
					</div>
					
				</div>

				<div class="col-xs-12 nopadwtop">
					<div class="col-xs-1 nopadwtop2x">
						<b>&nbsp;&nbsp;Quote Type</b>
					</div>
					<div class="col-xs-2 nopadding">
						<select id="selqotyp" name="selqotyp" class="form-control input-sm selectpicker"  tabindex="1">
							<?php						
								if($postquote=="True"){ 
							?>
								<option value="quote" <?php if($cQOType=="quote") { echo "selected"; }  ?> >Quote</option>
							<?php
								}
													
								if($postbilling=="True"){
							?>
								<option value="billing" <?php if($cQOType=="billing") { echo "selected"; }  ?> >Billing</option>
							<?php
								}
							?>
														
						</select>
					</div>
					<div class="col-xs-1 nopadwtop2x">
							<b>&nbsp;&nbsp;&nbsp;&nbsp;Reccur Every</b>
						</div>
					<div class="col-xs-2 nopadding">
						<select id="selrecurrtyp" name="selrecurrtyp" class="form-control input-sm selectpicker"  tabindex="1">
							<option value="one" <?php if($cRCType=="one") { echo "selected"; }  ?>>One Time Only</option>
							<option value="weekly" <?php if($cRCType=="weekly") { echo "selected"; }  ?> >Weekly</option>
							<option value="monthly" <?php if($cRCType=="monthly") { echo "selected"; }  ?> >Monthly</option>
							<option value="quartertly" <?php if($cRCType=="quartertly") { echo "selected"; }  ?> >Quartertly</option>
							<option value="yearly" <?php if($cRCType=="yearly") { echo "selected"; }  ?> >Annual</option>
							<option value="semi_annual" <?php if($cRCType=="semi_annual") { echo "selected"; }  ?>>Semi Annual</option>
						</select>
					</div>
					<div class="col-xs-1 nopadwtop2x">
						<b>&nbsp;&nbsp;&nbsp;&nbsp;Sales Type</b>
					</div>
					<div class="col-xs-2 nopadding">
						<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
							<option value="Goods" <?php if($cSelType=="Goods") { echo "selected"; } ?> >Goods</option>
							<option value="Services" <?php if($cSelType=="Services") { echo "selected"; } ?>>Services</option>
						</select>
					</div>
					<div class="col-xs-1 nopadwtop2x">
						<b>&nbsp;&nbsp;&nbsp;&nbsp;Date</b>
					</div>
					<div class="col-xs-2 nopadding">
						<input type='text' class="form-control input-sm" id="date_trans" name="date_trans" value="<?php echo date_format(date_create($QuoteDate),'m/d/Y'); ?>" />
					</div>
				</div>


				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Customer Details</legend>

					<div class='col-xs-12 nopadwtop'>
						<div class="col-xs-2"><b>Billed To</b></div>
						<div class="col-xs-1 nopadding">
							<input type="text" id="txtcustid" name="txtcustid" class="required form-control input-sm" placeholder="Code..." tabindex="1" required="true" value="<?php echo $CustCode; ?>">
								<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
								<input type="hidden" id="hdnpricever" name="hdnpricever" value="">
						</div>
						<div class="col-xs-4 nopadwleft">
							<input type="text" class="required form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off" required="true" value="<?php echo $CustName; ?>">
						</div>
						<div class="col-xs-2"><b>Salutation</b></div>
						<div class="col-xs-3 nopadding"> 
							<input type="text" id="txtcontactsalut" name="txtcontactsalut" class="required form-control input-sm" placeholder="Salutation..." tabindex="1"  required="true" value="<?php echo $ccontsalt; ?>">
						</div>
					</div>

					<div class='col-xs-12 nopadwtop'>
						<div class="col-xs-2"><b>Delivered To</b></div>  
						<div class="col-xs-1 nopadding">
							<input type="text" id="txtcustiddel" name="txtcustiddel" class="form-control input-sm" placeholder="Code..." tabindex="1" value="<?=$CustCodeDel?>">
								<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
								<input type="hidden" id="hdnpricever" name="hdnpricever" value="">
						</div>
						<div class="col-xs-4 nopadwleft"> 
							<input type="text" class="form-control input-sm" id="txtcustdel" name="txtcustdel" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off" value="<?=$CustNameDel?>">
						</div>
						<div class="col-xs-2"><b>Designation</b></div>
						<div class="col-xs-3 nopadding"> 
							<input type="text" id="txtcontactdesig" name="txtcontactdesig" class="form-control input-sm" placeholder="Designation..." tabindex="1" value="<?php echo $ccontdesg; ?>">
						</div>
					</div>

					<div class='col-xs-12 nopadwtop'>
						<div class="col-xs-2"><b>Contact Person</b></div>
						<div class="col-xs-1 nopadding"> 
							<button class="btn btn-sm btn-block btn-warning" name="btnSearchCont" id="btnSearchCont" type="button">Search</button>
						</div>
						<div class="col-xs-4 nopadwleft">
							<input type="text" id="txtcontactname" name="txtcontactname" class="required form-control input-sm" placeholder="Contact Person Name..." tabindex="1"  required="true" value="<?php echo $ccontname; ?>">
						</div>

						<div class="col-xs-2"><b>Department</b></div>
						<div class="col-xs-3 nopadding">
							<input type="text" id="txtcontactdept" name="txtcontactdept" class="form-control input-sm" placeholder="Department..." tabindex="1" value="<?php echo $ccontdept; ?>">
						</div>

						
					</div> 

					<div class='col-xs-12 nopadwtop'>
						
						<div class="col-xs-2"><b>Accepted By</b></div>
						<div class="col-xs-5 nopadding">
							<textarea id="txtaccpetby" name="txtaccpetby" class="required form-control input-sm" placeholder="Enter Description..."><?php echo $cAccpBy; ?></textarea>
						</div>
						<div class="col-xs-2"><b>Email Address</b></div>
						<div class="col-xs-3 nopadding">
							<input type="text" id="txtcontactemail" name="txtcontactemail" class="required form-control input-sm" placeholder="Email Address..." tabindex="1" required="true" value="<?php echo $ccontemai; ?>">
						</div>

					</div>

				</fieldset>

				<br>

				<fieldset class="scheduler-border">
						<legend class="scheduler-border">Terms &amp; Conditions</legend>

						<div class='col-xs-12 nopadding'>
								<div class="col-xs-1"><b>Vat Type</b></div>
							<div class="col-xs-2 nopadwleft">
								<select class="form-control input-sm" name="selvattype" id="selvattype">
									<option value="VatEx" <?php if ($cvattyp=="VatEx") { echo "selected"; } ?>>VAT Exclusive</option>
									<option value="VatIn" <?php if ($cvattyp=="VatIn") { echo "selected"; } ?>>VAT Inclusive</option>
								</select>
							</div>
								<div class="col-xs-2" id="prcevallabel">
									<b><?=($cQOType=="billing") ? "Due Date" : "Price Validity"; ?></b>
								</div>
							<div class="col-xs-2 nopadwleft">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($Date),'m/d/Y'); ?>" />
							</div>
							<div class="col-xs-1"><b>Payment</b></div>
							<div class="col-xs-2 nopadwleft">
								<select class="form-control input-sm" name="selterms" id="selterms">
									<option value='' <?=($cterms=='') ? "selected" : "";?>>N/A</option>
									<?php
										$sqlters = mysqli_query($con,"Select ccode, cdesc From groupings Where compcode='$company' and ctype='TERMS' and cstatus='ACTIVE'");
										while($row = mysqli_fetch_array($sqlters, MYSQLI_ASSOC)){
											if ($cterms==$row['ccode']) { 
												$ctermstext = "selected"; 
											}else{
												$ctermstext = ""; 
											}
											echo "<option value='".$row['ccode']."' ".$ctermstext.">".$row['cdesc']."</option>";
										}
									?>

								</select>
							</div>
						</div>
						<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Delivery</b></div>
								<div class="col-xs-9 nopadwleft">
								<input type='text' class="required form-control input-sm" id="txtdelinfo" name="txtdelinfo" required="true" value="<?php echo $cdelinfo; ?>"/>
							</div>
						</div>

						<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Service</b></div>
								<div class="col-xs-9 nopadwleft">
								<input type='text' class="required form-control input-sm" id="txtservinfo" name="txtservinfo" required="true" value="<?php echo $cservinfo; ?>" />
							</div>
						</div>

						<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Remarks</b></div>
								<div class="col-xs-9 nopadwleft">
										
									<textarea rows="4" class="rcontent" name="txtremarks" id="txtremarks" style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $Remarks; ?></textarea>

							</div>
						</div>

						<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Currency</b></div>
								<div class="col-xs-3 nopadwleft">
								<select class="form-control input-sm" name="selbasecurr" id="selbasecurr">							
								<?php
									$nvaluecurrbase = "";	
									$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
									
										if (mysqli_num_rows($result)!=0) {
											$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
											
											$nvaluecurrbase = $all_course_data['cvalue']; 
												
										}
										else{
											$nvaluecurrbase = "";
										}
				
										//	$objcurrs = listcurrencies();
									//		$objrows = json_decode($objcurrs, true);
												
								///	foreach($objrows['results'] as $rows){
										$showcurrname = "";
										$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
										if (mysqli_num_rows($sqlhead)!=0) {
											while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

												if($CurrCode==$rows['id']){
													$showcurrname = $rows['currencyName'];
												}
								?>
										<option value="<?=$rows['id']?>" <?php if ($CurrCode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
								<?php
											}
										}
								?>
								</select>
								<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>">	
								<input type='hidden' id="currdesc" name="currdesc" value="<?=$showcurrname?>">	

						</div>  

						<div class="col-xs-1 nopadwleft">
								<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?php echo $CurrRate; ?>">	 
							</div>

							<div class="col-xs-5" id="statgetrate" style="padding: 4px !important"> 
									
						</div>
				</fieldset>

			</div>

			<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px;">
				<!-- Import Files Modal -->
				<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
				<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
				<input type="file" name="upload[]" id="file-0" multiple />

			</div>

		</div>

		<hr>
		<div class="col-xs-12 nopadwdown"><b>Details</b></div>

		<div class="col-xs-12 nopadwtop2x">
			<div class="col-xs-3 nopadwdown">
				<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">

				<input type="hidden" name="hdnqty" id="hdnqty">
				<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
				<input type="hidden" name="hdnunit" id="hdnunit">

			</div>
			<div class="col-xs-5 nopadwleft">
				<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="5">
			</div>
		</div>

		<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 1px solid #919b9c;width: 100%;height: 300px;text-align: left;overflow: auto">
		
				<table id="MyTable" class="MyTable table table-condensed" width="100%">
					<thead>
						<tr id="0">
							<th style="border-bottom:1px solid #999">Code</th>
							<th style="border-bottom:1px solid #999">Description</th>
							<th style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
							<th style="border-bottom:1px solid #999">UOM</th>
							<th style="border-bottom:1px solid #999">Factor</th>
							<th style="border-bottom:1px solid #999">Qty</th>
							<th style="border-bottom:1px solid #999">Price</th>
							<th style="border-bottom:1px solid #999">Amount</th>
							<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
							<th style="border-bottom:1px solid #999">&nbsp;</th>
						</tr>
					</thead>            
					<tbody class="tbody">
					</tbody>                    
				</table>
		</div>

		<div class="col-xs-12 nopadwtop2x">
				<div class="col-xs-7">

				<?php
					if($poststat == "True"){
				?>

					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Quote.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>&st=<?=isset($_REQUEST['hdnsrchsta']) ? $_REQUEST['hdnsrchsta'] : ""?>&stype=<?=isset($_REQUEST['hdnsrchtyp']) ? $_REQUEST['hdnsrchtyp'] : ""?>&sdtf=<?=isset($_REQUEST['hdnsrchdte']) ? $_REQUEST['hdnsrchdte'] : ""?>&dtfr=<?=isset($_REQUEST['hdnsrchdtef']) ? $_REQUEST['hdnsrchdtef'] : ""?>&dtto=<?=isset($_REQUEST['hdnsrchdtet']) ? $_REQUEST['hdnsrchdtet'] : ""?>';" id="btnMain" name="btnMain">
						Back to Main<br>(ESC)
					</button>
		
					<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Quote_new.php';" id="btnNew" name="btnNew">
						New<br>(F1)
					</button>

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
						Undo Edit<br>(F3)
					</button>
				<?php
					}

					if($printstat == "True"){
				?>
					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>','Print');" id="btnPrint" name="btnPrint">
						Print<br>(F4)
					</button>

					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>','PDF');" id="btnPDF" name="btnPDF">
						View PDF<br>&nbsp;
					</button>

					<?php
					}
					if($poststat == "True"){

						if($lPosted==1 && $lVoid==0){
					?>
					<button type="button" class="btn btn-info btn-sm" tabindex="6" id="btnEmail" name="btnEmail" onclick="sendEmail()">  
						Send Email<br>&nbsp;
					</button>
					<?php
						}
					?>
			
					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
						Edit<br>(F8)    
					</button>
			
					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
						Save<br>(F2)    
					</button>
				<?php
					}
				?>
				</div>	

				<div class="col-xs-2"  style="padding-top: 14px !important;">
						<b>TOTAL AMOUNT </b>
				</div>
				<div class="col-xs-3"  style="padding-top: 14px !important;">
					<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="<?php echo  number_format($BaseGross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
				</div>

				<div class="col-xs-12 nopadding">
					<div class="col-xs-7">
							
					</div>	

					<div class="col-xs-2">
							<b>TOTAL AMOUNT IN <?php echo $nvaluecurrbase; ?></b>
					</div>
					<div class="col-xs-3" >
						<input type="text" id="txtnGross" name="txtnGross" readonly value="<?php echo  number_format($Gross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
					</div>
				</div>
		</div>  
     
  </fieldset>
    
   
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

</form>

<?php
}
else{
?>
	<form action="Quote_edit.php" name="frmpos2" id="frmpos2">
		<fieldset>
			<legend>Quotation</legend>	
				<table width="100%" border="0">
					<tr>
						<tH width="100">TRANS NO.:</tH>
						<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
					</tr>
					<tr>
						<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Quotation No. DID NOT EXIST!</b></font></tH>
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


	<!-- MODAL FOR CONTACT NAME -->
	<div class="modal fade" id="ContactModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog vertical-align-top">
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

	<!--SEND EMAIL-->
	<div class="modal fade" id="SendEmailMod"  tabindex="-1" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			
				<form name="frmsendemail" id="frmsendemail" action="SendToEmail.php" method="POST">
					<input type="hidden" id="cemailtranno" name="cemailtranno" value="<?=$txtctranno?>">
					<input type="hidden" id="cemailtrantyp" name="cemailtrantyp" value="<?=$cQOType?>">

					<div class="modal-header">
						<div class="row nopadding">
							<div class="col-xs-4 nopadding">
								<h3 class="modal-title" id="invheader"> Send Email </h3>  
							</div>   
							<div class="col-xs-8 nopadwtop2x text-right">
								<?php
									echo ($cemailsentby!="") ? "<b>Last Sent By: </b>".$cemailsentby." <b>Date/Time: </b>".$cemailsentdate: "";
								?>
							</div>      
						</div>
					</div>

					<div class="modal-body" style="height: 40vh">
						<div class="row nopadding">
							<div class="col-xs-1 nopadwtop2x">
								<b>&nbsp;&nbsp;&nbsp;&nbsp;To </b>
							</div>	
							<div class="col-xs-11">
								<input type="text" id="cemailto" name="cemailto" value="<?=($cemailstoo=="") ? $ccontemai : $cemailstoo?>">
							</div>
							
						</div>
						<div class="row nopadwtop">
							<div class="col-xs-1 nopadwtop2x">
								<b>&nbsp;&nbsp;&nbsp;&nbsp;CC </b>
							</div>	
							<div class="col-xs-11">
								<input type="text" class="form-control input-sm" id="cemailcc" name="cemailcc" value="<?=$cemailsccc?>">
							</div>
							
						</div>
						<div class="row nopadwtop">
							<div class="col-xs-1 nopadwtop2x">
								<b>&nbsp;&nbsp;&nbsp;&nbsp;BCC </b>
							</div>	
							<div class="col-xs-11">
								<input type="text" class="form-control input-sm tags" id="cemailbcc" name="cemailbcc" value="<?=$cemailsbcc?>">
							</div>
							
						</div>
						<div class="row nopadwtop">
							<div class="col-xs-1 nopadwtop2x">
								<b>&nbsp;&nbsp;&nbsp;&nbsp;Subject </b>
							</div>	
							<div class="col-xs-11">
								<input type="text" class="form-control input-sm" id="cemailsubject" name="cemailsubject" value="<?=$cemailsbjc?>">
							</div>
							
						</div>
						<div class="row nopadwtop">
							<div class="col-xs-12 nopadwtop2x">
								<textarea rows="4" class="form-control input-sm" name="txtemailremarks" id="txtemailremarks"><?=($cemailsbod=="") ? $ccontsalt : $cemailsbod;?></textarea>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-success btn-sm">Send Email</button>
						<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="btnmodclose">Cancel</button>
					</div>
				</form>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<form action="PrintQuote.php" method="post" name="frmQPrint" id="frmQprint" target="_blank">
		<input type="hidden" name="hdntransid" id="hdntransid" value="<?php echo $txtctranno; ?>">
	</form>

</body>
</html>

<script type="text/javascript">
	var xChkBal = '';

	var xtoday = new Date();
	var xdd = xtoday.getDate();
	var xmm = xtoday.getMonth()+1; //January is 0!
	var xyyyy = xtoday.getFullYear();

	xtoday = xmm + '/' + xdd + '/' + xyyyy;

	var file_name = <?= json_encode(@$arrname) ?>;
	/**
	 * Checking of list files
	 */
	file_name.map(({name, ext}) => {
			console.log("Name: " + name + " ext: " + ext)
	});

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var list_file = [];
	var file_config = [];
	var extender;
	/**
	 * setting up an list of file and config of a file
	 */
	file_name.map(({name, ext}, i) => {
		$vrx = encodeURIComponent(name);
		list_file.push("<?=$AttachUrlBase?>QO/<?=$company."_".$txtctranno?>/" + $vrx)
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
	});

	console.log(file_config);

	$(document).keydown(function(e) {	 
		if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Quote_new.php';
			}
		}
		else if(e.keyCode == 113){//F2
			if($("#btnSave").is(":disabled")==false){
				return chkform();
			}
		}
		else if(e.keyCode == 119){//F8
			if($("#btnEdit").is(":disabled")==false){
				enabled();
			}
		}
		else if(e.keyCode == 115){//F4
			if($("#btnPrint").is(":disabled")==false){
				e.preventDefault();
				printchk('<?php echo $txtctranno;?>','Print');
			}
		}
		else if(e.keyCode == 114){//F3
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmpos');
			}
		}
		else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Quote.php';
			}
		}
	});

	$(document).on("keydown", "#frmsendemail", function(event) { 
		return event.key != "Enter";
	});
	
	$(document).ready(function(e) {
		
		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		if(file_name.length > 0){
			$('#file-0').fileinput({
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 10,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: list_file,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/QO/<?=$company."_".$txtctranno?>/{filename}',
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
				maxFileCount: 10,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}

		$("#cemailto, #cemailcc, #cemailbcc").tagsinput();
		$('#cemailto, #cemailcc, #cemailbcc').on('beforeItemAdd', function(event) {
			if(!IsEmail(event.item)){
				event.cancel = true;
			};
			// event.item: contains the item
			// event.cancel = true : set to true to prevent the item getting added
		});

		$("#txtremarks").summernote();
		$("#txtemailremarks").summernote({
			placeholder: 'Email Body',
			height: 240,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
			]
		});
		

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
				});
			}
		});
	
		if(xChkBal==1){
			$("#tblAvailable").hide();
		}
		else{
			$("#tblAvailable").show();
		}

		loaddetails();
		loaddetinfo();
	
	  	$('#txtprodnme').attr("disabled", true);
	  	$('#txtprodid').attr("disabled", true);
	  
		disabled();

		//$("#basecurrval").numeric();
		
		$('#date_delivery').datetimepicker({
			format: 'MM/DD/YYYY'
		});

		$('#date_trans').datetimepicker({
			format: 'MM/DD/YYYY'
		});

	
		$("#txtcustid").keyup(function(event){
			if(event.keyCode == 13){
			
				var dInput = this.value;
				
				$.ajax({
					type:'post',
					url:'../get_customerid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
						//alert(value);
						if(value!=""){
							var data = value.split(":");
							$('#txtcust').val(data[0]);
							$('#imgemp').attr("src",data[3]);
							$('#hdnpricever').val(data[2]);
											
							$('#hdnvalid').val("YES");
							
						}
						else{
							$('#txtcustid').val("");
							$('#txtcust').val("");
							$('#imgemp').attr("src","../../images/blueX.png");
							$('#hdnpricever').val("");
							
							$('#hdnvalid').val("NO");
						}
					},
					error: function(){
						$('#txtcustid').val("");
						$('#txtcust').val("");
						$('#imgemp').attr("src","../../images/blueX.png");
						$('#hdnpricever').val("");
						
						$('#hdnvalid').val("NO");
					}
				});

			}
			
		});

		$('#txtcust, #txtcustid').on("blur", function(){
			if($('#hdnvalid').val()=="NO"){
			$('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
			
			$('#txtprodnme').attr("disabled", true);
			$('#txtprodid').attr("disabled", true);
			}else{
				
			$('#txtprodnme').attr("disabled", false);
			$('#txtprodid').attr("disabled", false);
			
			$('#txtremarks').focus();
		
			}
		});

		//Search Cust name
		$('#txtcust').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_customer.php",
					dataType: "json",
					data: {
						query: $("#txtcust").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);
				$("#imgemp").attr("src",item.imgsrc);
				$("#hdnpricever").val(item.cver);
				
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
							
			}
		
		});

		$("#txtcustiddel").keyup(function(event){
			if(event.keyCode == 13){
			
				var dInput = this.value;
				
				$.ajax({
					type:'post',
					url:'../get_custchildid.php',
					data: 'c_id='+ dInput + '&m_id=' + $("#txtcustid").val(),                 
					success: function(value){
						//alert(value);
						if(value!=""){
							var data = value.split(":");
							$('#txtcustdel').val(data[1]);								
						}
						else{
							$('#txtcustiddel').val("");
							$('#txtcustdel').val("");								
						}
					},
					error: function(){
						$('#txtcustiddel').val("");
						$('#txtcustdel').val("");							
					}
				});

			}
			
		});

		//Search Cust name
		$('#txtcustdel').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_customerdel.php",
					dataType: "json",
					data: {
						query: $("#txtcustdel").val(), cmain: $("#txtcustid").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#txtcustdel').val(item.value).change(); 
				$("#txtcustiddel").val(item.id);
				
				$('#txtremarks').focus();				
				
			}
		
		});
		
		$('#txtprodnme').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_product.php?itmbal="+xChkBal,
					dataType: "json",
					data: {
						query: $("#txtprodnme").val(), styp: $("#selsityp").val()
					},
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
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);
				
				addItemName("","","","","");
				
				
			}
		
		});


		$("#txtprodid").keypress(function(event){
			if(event.keyCode == 13){

				$.ajax({
					url:'../get_productid.php?itmbal='+xChkBal,
					data: 'c_id='+ $(this).val()+"&styp="+ $("#selsityp").val(),                 
					success: function(value){
						var data = value.split(",");
						$('#txtprodid').val(data[0]);
						$('#txtprodnme').val(data[1]);
						$('#hdnunit').val(data[2]);
						$("#hdnqty").val(data[3]);
						$("#hdnqtyunit").val(data[4]);


						//if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
						//	var isItem = "NO";
						//	var disID = "";
				
							/*
							$("#MyTable > tbody > tr").each(function() {	
								disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

								if($("#txtprodid").val()==disID){
									
									isItem = "YES";

								}
							});	

						//if value is not blank
						}
						
						if(isItem=="NO"){		
						*/
						addItemName("","","","","");
				
							/*
							}
							else{
							
							addqty();
						}
						*/
									
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
						$("#hdnqty").val("");
						$("#hdnqtyunit").val("");

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
					url:'../get_contactinfonames.php',
					data: 'c_id='+ $('#txtcustid').val(),  
					dataType: "json",               
					success: function(data){
						
						$.each(data,function(index,item){

						//put to table
							$("<tr class='bdydeigid' style='cursor:pointer'>").append(
								$("<td class='disnme'>").text(item.cname),
								$("<td class='disndesig'>").text(item.cdesig),
								$("<td class='disdept'>").text(item.cdept),
								$("<td class='disemls'>").text(item.cemail)
							).appendTo("#ContactTbls tbody");

						});
					}
				});

				$("#ContactModal").modal("show");
			}else{
				alert("Customer Required!");
				document.getElementById("txtcust").focus();
				return false;
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
				if($(this).attr("class")=="disndesig"){
					$('#txtcontactdesig').val($(this).text());
				}
				if($(this).attr("class")=="disdept"){
					$('#txtcontactdept').val($(this).text());
				}
				if($(this).attr("class")=="disemls"){
					$("#txtcontactemail").val($(this).text());
				}
			});

			$("#ContactModal").modal("hide");
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
		});

		$("#selbasecurr").on("change", function (){
				
			//convertCurrency($(this).val());

			$("#basecurrval").val(dval);
			$("#statgetrate").html("");
			recomputeCurr();
			
		
		});

		$("#basecurrval").on("keyup", function () {
			recomputeCurr();
		});

		$("#selqotyp").on("change", function (){
			var dval = $(this).find(':selected').val();

			if(dval=="billing"){
				$("#selrecurrtyp").attr("disabled", false); 
				$("#selterms").attr("disabled", true); 
				$("#prcevallabel").html("<b>Due Date</b>");

				//xval = "<p>Kindly make all checks payable to <b>HRWEB INC</b><br>For bank transfer please deposit to:</p><b><u>For EASTWEST acct:</u></b><br><b>Account Name: HRWEB INC.</b><br><b>Account Number: 200044514167</b><br><b>Bank Address: IMUS Cavite</b><br><br><i>Note: Please settle your account to prevent service interruptions.</i><br><i>Kindly disregards pass due notice if payments have been made.</i><div><br></div><br><br><br><br><br>";

			}else{
				$("#selrecurrtyp").attr("disabled", true); 
				$("#selterms").attr("disabled", false);
				$("#prcevallabel").html("<b>Price Validity</b>");

				//xval = "Should you have questions regarding our offer, kindly let us know or you can call at Tel. no. 09175513200 / 09499974988 / 09338632777 or email at sales@serttech.com.  Thank you for your interest in our products and services.";
			}

			//$("#txtremarks").val(xval);

			getRemarks(dval)
			
		});

		
			

	});


	function addItemName(qty,price,amt,factr,nident){

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

			/*
			var isItem = "NO";
			var disID = "";

				$("#MyTable > tbody > tr").each(function() {	
					disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

					if($("#txtprodid").val()==disID){
						
						isItem = "YES";

					}
				});	

		if(isItem=="NO"){	
			*/
			myFunctionadd(qty,price,amt,factr,nident);
			
			ComputeGross();	

		//}
		
			
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			$("#hdnqty").val("");
			$("#hdnqtyunit").val("");
			
		}
	}

	function myFunctionadd(qty,pricex,amtx,factr,nident){
		//alert("hello");
		var itmcode = $("#txtprodid").val();
		var itmdesc = $("#txtprodnme").val();
		var itmunit = $("#hdnunit").val();
		var itmccode = $("#hdnpricever").val();
		//alert(itmqtyunit);
		if(qty=="" && pricex=="" && amtx=="" && factr==""){
			var itmtotqty = 1;
			var price = chkprice(itmcode,itmunit,itmccode,xtoday);
			
			var amtz = price;
			var factz = 1;
		}
		else{
			var itmtotqty = qty
			var price = pricex;
			var amtz = amtx;	
			var factz = factr;	
		}

		var baseprice = amtz * parseFloat($("#basecurrval").val());
		
		//alert(itmcode+","+itmunit+","+itmccode+","+xtoday);
			
			if(xChkBal==1){
				var avail = "";
				var itmqtyunit = $("#hdnqtyunit").val();
			}
			else{
				var itmqtyunit = $("#hdnqtyunit").val();
				var itmqty = $("#hdnqty").val();
				var avail = "<td> " + itmqty + " " + itmqtyunit +" </td>";
			}
			

			var uomoptions = "";
			var xz = $("#hdnitmfactors").val();
			var uomoptions = "<option value='"+itmunit+"' selected data-factor='1'>"+itmunit+"</option>";

			$.each(jQuery.parseJSON(xz), function() { 
				if(itmcode==this['cpartno']){
					if(itmqtyunit==this['cunit']){
						isselctd = "selected";
					}
					else{
						isselctd = ""; 
					}
					uomoptions = uomoptions + "<option value='"+this['cunit']+"' data-factor='"+this['nfactor']+"' "+isselctd+">"+this['cunit']+"</option>";

				}
			});		
											
		//var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		//var lastRow = tbl.length;

		if(nident==""){
			tbl = $('#MyTable tr:last').attr('id');
			var lastRow = parseInt(tbl) + 1;
		}else{
			var lastRow = nident;
		}
		
		var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"</td>";
		var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
		var tditmavail = avail;

		var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmunit+"'>"+uomoptions+"</select> </td>";

		isfactoread = "";
		if(itmqtyunit==itmunit){
			isfactoread = "readonly";
		}

		var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+factz+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";

		var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> </td>";
			
		var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric2 form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' \"> </td>";

		var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+amtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"'> </td>";
				
		var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' > </td>";
		
		var tditmdel = "<td width=\"90\" nowrap> <input class='btn btn-danger btn-xs' type='button' name='itmdel' id='del" + lastRow + "' value='delete'/> &nbsp; <input class='btn btn-primary btn-xs' type='button' name='itmins' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"','"+lastRow+"');\"/> </td>";


		$('#MyTable > tbody:last-child').append('<tr id="'+lastRow+'">'+tditmcode + tditmdesc + tditmavail + tditmunit + tditmfactor + tditmqty + tditmprice + tditmbaseamount + tditmamount + tditmdel + '</tr>');


			$("#del"+lastRow).on('click', function() {
				$(this).closest('tr').remove();
				Reindex();
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

				var xyz = chkprice(itmcode,$(this).val(),itmccode,xtoday);
				var mainuomdata = $(this).data("main");
				var fact = $(this).find(':selected').data('factor');

				if(fact!=0){
					$('#hdnfactor'+lastRow).val(fact);
				}

				if(mainuomdata!==$(this).val()){
					$('#hdnfactor'+lastRow).attr("readonly", false);
				}else{
					$('#hdnfactor'+lastRow).attr("readonly", true);
				}

				$('#txtnprice'+lastRow).val(xyz.trim());
				//alert($(this).attr('id'));
				ComputeAmt($(this).attr('id'));
				ComputeGross();

				//var fact = setfactor($(this).val(), itmcode);
				//alert(fact);

			});
			
			ComputeGross();
										
										
	}

	function Reindex(){
		$("#MyTable > tbody > tr").each(function(index) {
			$x = index+1;
			$(this).attr("id",$x);

			$(this).find('select[name="seluom"]').attr("id", "seluom"+$x);
			$(this).find('input[name="txtnqty"]').attr("id", "txtnqty"+$x);
			$(this).find('input[name="txtnprice"]').attr("id", "txtnprice"+$x);
			$(this).find('input[name="txtntranamount"]').attr("id", "txtntranamount"+$x);
			$(this).find('input[name="txtnamount"]').attr("id", "txtnamount"+$x);
			$(this).find('input[type="hidden"][name="hdnmainuom"]').attr("id", "hdnmainuom"+$x);
			$(this).find('input[name="hdnfactor"]').attr("id", "hdnfactor"+$x);

			$(this).find('inpu[type="button"][name="itmdel"]').attr("id", "del"+$x);
			$(this).find('input[type="button"][name="itmins"]').attr("id", "row_"+$x+"_info");
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
		
		namt = nqty * nprc;
		namt = namt.toFixed(4);

		namt2 = namt * parseFloat($("#basecurrval").val());
		namt2 = namt2.toFixed(4);

		
		$("#txtntranamount"+r).val(namt);		

		$("#txtnamount"+r).val(namt2);

		$("#txtntranamount"+r).autoNumeric('destroy');
		$("#txtnamount"+r).autoNumeric('destroy');

		$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
		$("#txtnamount"+r).autoNumeric('init',{mDec:2});

	}

	function ComputeGross(){
		var rowCount = $('#MyTable tr').length;
		
		var gross = 0;
		var amt = 0;
		
		//	if(rowCount>1){
		//		for (var i = 1; i <= rowCount-1; i++) {
		//			amt = $("#txtntranamount"+i).val().replace(/,/g,'');

		//			gross = gross + parseFloat(amt);
		//		}
		//	}

		$("#MyTable > tbody > tr").each(function() {

			myid = this.id;

			amt = $("#txtntranamount"+myid).val().replace(/,/g,'');					
			gross = gross + parseFloat(amt);

		});

		gross = gross.toFixed(4);

		gross2 = gross * parseFloat($("#basecurrval").val());
		gross2 = gross2.toFixed(4);

		
		$("#txtnBaseGross").val(gross);

		$("#txtnGross").val(gross2);

		$("#txtnBaseGross").autoNumeric('destroy');
		$("#txtnGross").autoNumeric('destroy');

		$("#txtnBaseGross").autoNumeric('init',{mDec:2});
		$("#txtnGross").autoNumeric('init',{mDec:2});
		
	}

	/*
	function addqty(){

		var itmcode = document.getElementById("txtprodid").value;

		var TotQty = 0;
		var TotAmt = 0;
		
		$("#MyTable > tbody > tr").each(function() {	
		var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
		
		//alert(disID);
			if(disID==itmcode){
				
				var itmqty = $(this).find("input[name='txtnqty']").val();
				var itmprice = $(this).find("input[name='txtnprice']").val();
				
				//alert(itmqty +" : "+ itmprice);
				
				TotQty = parseFloat(itmqty) + 1;
				$(this).find("input[name='txtnqty']").val(TotQty);
				
				TotAmt = TotQty * parseFloat(itmprice);
				$(this).find("input[name='txtnamount']").val(TotAmt.toFixed(4));
			}

		});
		
		ComputeGross();

	}
	*/

	function viewhidden(itmcde,itmnme,refident){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow2 = tbl.length-1;
		
		if(lastRow2>=1){
				$("#MyTable2 > tbody > tr").each(function() {	
				
					var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
					var citmnoident = $(this).find('input[type="hidden"][name="txtinforefident"]').val();

					//alert(citmno+"!="+itmcde);
					if(citmno!=itmcde && citmnoident!==refident){
						
						$(this).find('input[name="txtinfofld"]').attr("disabled", true);
						$(this).find('input[name="txtinfoval"]').attr("disabled", true);
						$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
						
					}
					else{
						$(this).find('input[name="txtinfofld"]').attr("disabled", false);
						$(this).find('input[name="txtinfoval"]').attr("disabled", false);
						$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
					}
					
				});
		}			
				
		addinfo(itmcde,itmnme,"","",refident);
		
		$('#MyDetModal').modal('show');
	}

	function addinfo(itmcde,itmnme,fldnme,cvlaz,refident){
		//alert(itmcde+","+itmnme);
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length;

		
		var tdinfocode = "<td><input type='hidden' value='"+refident+"' name='txtinforefident' id='txtinforefident"+lastRow+"'><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
		var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
		var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs' value=\""+fldnme+"\"></td>";
		var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs' value=\""+cvlaz+"\"></td>";
		var tdinfodel = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + lastRow + itmcde + "' value='delete' /></td>";

		//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
		
		$('#MyTable2 > tbody:last-child').append('<tr>'+tdinfocode + tdinfodesc + tdinfofld + tdinfoval + tdinfodel + '</tr>');

										$("#delinfo"+lastRow+itmcde).on('click', function() {
											$(this).closest('tr').remove();
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

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "Quote_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){

		$("#frmpos :input").not("#txtremarks").attr("disabled", true);
		
		
		$("#txtcsalesno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnPDF").attr("disabled", false);
		$("#btnEmail").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);

		$(".kv-file-zoom").attr("disabled", false);

	}

	function enabled(){
		if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
			if(document.getElementById("hdnposted").value==1){
				var msgsx = "POSTED"
			}
			
			if(document.getElementById("hdncancel").value==1){
				var msgsx = "CANCELLED"
			}
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{
			
			$("#frmpos :input").attr("disabled", false);
						
			//$("#txtremarks").data('wysihtml5').editor.composer.enable();


				$("#txtcsalesno").attr("readonly", true);
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnPDF").attr("disabled", true);
				$("#btnEmail").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);

				if($("#selqotyp").val()=="billing"){
					$("#selrecurrtyp").attr("disabled", false);
					$("#selterms").attr("disabled", true);
				}else{
					$("#selrecurrtyp").attr("disabled", true);
					$("#selterms").attr("disabled", false);
				}
						
			ComputeGross();
		
		}
	}

	function printchk(x,typx){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{

			if($("#selqotyp").val()=="billing"){

				if(typx=="Print"){
					$("#frmQprint").attr("action","PrintBilling.php");
				}else if(typx=="PDF"){
					$("#frmQprint").attr("action","PrintBilling_PDF.php");
				}//else if(typx=="Email"){
					//$("#frmQprint").attr("action","PrintBilling_Email.php");
				//}

			}else{

				if(typx=="Print"){
					$("#frmQprint").attr("action","PrintQuote.php");
				}else if(typx=="PDF"){
					$("#frmQprint").attr("action","PrintQuote_PDF.php");
				}//else if(typx=="Email"){
					//$("#frmQprint").attr("action","PrintQuote_Email.php");
				//}

			}

			$("#frmQprint").submit();

		}
	}

	function loaddetails(){
		//alert($("#txtcsalesno").val());
		
		$.ajax ({
			url: "th_loaddetails.php",
			data: { id: $("#txtcsalesno").val(), itmbal:xChkBal },
			async: false,
			dataType: "json",
			success: function( data ) {

				//console.log(data);
				$.each(data,function(index,item){
					
					$('#txtprodnme').val(item.desc); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit); 
					$("#hdnqty").val(item.nqty);
					$("#hdnqtyunit").val(item.cqtyunit);

					// myFunctionadd(qty,pricex,amtx,factr,nident
					addItemName(item.totqty,item.nprice,item.namount,item.nfactor,item.nident);
				});

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

					addinfo(item.id,item.desc,item.fldnme,item.cvalue,item.refident);

				});

			}
		});

	}

	function chkform(){
		var ISOK = "YES";
		
		
		if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){
			alert("Customer Required!");
			document.getElementById("txtcust").focus();
			return false;
			
			ISOK = "NO";
		}

		$(".required").each( function() {
			var check = $(this).val();

			if(check == '') {
				alert("Please fill-up all fields in red textbox!");
				ISOK = "NO";
			}
		});

		// ACTIVATE MUNA LAHAT NG INFO
		
		$("#MyTable2 > tbody > tr").each(function() {				

			var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
			
			$(this).find('input[name="txtinfofld"]').attr("disabled", false);
			$(this).find('input[name="txtinfoval"]').attr("disabled", false);
			$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

		});
		
		//alert(ISOK);



		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;

		if(lastRow == 0){
			alert("No details found!");
			return false;
			ISOK = "NO";
		}
		else{
			var msgz = "";
			var myqty = "";
			
			$("#MyTable > tbody > tr").each(function() {
				myqty = $(this).find('input[name="txtnqty"]').val();
				
				if(myqty == 0 || myqty == ""){
					msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
				}
				
			});
			
			if(msgz!=""){
				alert("Details Error: "+msgz);
				return false;
				ISOK = "NO";
			}
		}


		
		if(ISOK == "YES"){
		var isDone = "True";
		
			$("#currdesc").val($("#selbasecurr option:selected").text());
			$("#txtremarks").summernote('destroy');
			
			var formdata = new FormData($("#frmpos")[0]);
			/**
			 * @property JQuery formulate every file to compose to formdata 
			 * @property formdata.delete('#upload') delete an upload key without values
			 */
			formdata.delete('upload[]');
			jQuery.each(jQuery('#file-0')[0].files, function(i, file) {
				formdata.append('file-'+i, file);
			});

			//console.log(formdata);

			$.ajax ({
				url: "Quote_updatehdr.php",
				data: formdata,
				cache: false,
				processData: false,
				contentType: false,
				method: 'post',
				type: 'post',
				async: false,
				beforeSend: function(){
					$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING QUOTATION: </b> Please wait a moment...");
					$("#alertbtnOK").hide();
					$("#AlertModal").modal('show');
				},
				success: function( data ) {
					if(data.trim()!="False"){
						trancode = data.trim();
					}
					else{
						$("#AlertMsg").html(trancode);
					}
				}
			});
			
			if(trancode!=""){
				//Save Details
				$("#MyTable > tbody > tr").each(function(index) {	
				
					var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
					var cuom = $(this).find('select[name="seluom"]').val();
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nprice = $(this).find('input[name="txtnprice"]').val();
					var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
					var namt = $(this).find('input[name="txtnamount"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[name="hdnfactor"]').val();

					if(nqty!==undefined){
						nqty = nqty.replace(/,/g,'');
						nprice = nprice.replace(/,/g,'');
						namt = namt.replace(/,/g,'');
						nbaseamt = nbaseamt.replace(/,/g,'');
						nfactor = nfactor.replace(/,/g,'');
					}


					//alert("Quote_newsavedet.php?trancode="+trancode+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&nbaseamt="+nbaseamt+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor);

					$.ajax ({
						url: "Quote_newsavedet.php",
						data: { trancode: trancode, indx:this.id, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, nbaseamt:nbaseamt, namt:namt, mainunit:mainunit, nfactor:nfactor },
						async: false,
						success: function( data ) {
							if(data.trim()=="False"){
								isDone = "False";
							}
						}
					});
					
				});


				//Save Info
				$("#MyTable2 > tbody > tr").each(function(index) {	
				
					var nrefidx = $(this).find('input[type="hidden"][name="txtinforefident"]').val();
					var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
					var citmfld = $(this).find('input[name="txtinfofld"]').val();
					var citmvlz = $(this).find('input[name="txtinfoval"]').val();
				
					$.ajax ({
						url: "Quote_newsaveinfo.php",
						data: { trancode: trancode, indx: index, nrefidx:nrefidx, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
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
					$("#AlertMsg").html("<b>ERROR: </b> There's a problem updating your transaction...");
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
				amt = $("#txtntranamount"+i).val();			
				recurr = parseFloat(newcurate) * parseFloat(amt);

				$("#txtnamount"+i).val(recurr.toFixed(4));
			}
		}


		ComputeGross();


	}

	function getRemarks(type = $("selqotyp").find(":selected").val()) {
		let remarks = "";
			$.ajax({
				url: "../../System/th_loadQuotesPrint.php",
				dataType: 'json',
				async:false,
				success: function(res) {
					$.each(res,function(index,item){
						if(type == "billing")  {
							if(item.ccode == 'QUOTE_BILLING') {
								remarks = item.cdesc;
							}
						} else {
							if(item.ccode == 'QUOTE_RMKS') {
								remarks = item.cdesc;
							}
						}
						
						
					});     
				},
				error: function(msg) {
					console.log(msg)
				}
			})
			$("#txtremarks").summernote("code", remarks);
	}

	function sendEmail(){
		window.parent.parent.scrollTo(0,0);
		$("#SendEmailMod").modal("show");
	}

</script>