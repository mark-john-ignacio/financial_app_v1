<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "APV";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');


	$company = $_SESSION['companyid'];
	$ctranno = $_REQUEST['txtctranno'];

	function ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13))
			return $number. 'th';
		else
			return $number. $ends[$number % 10];
	}

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APV_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	$printstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APV_print'");
	if(mysqli_num_rows($sql) == 0){
		$printstat = "False";
	}

	$sqlhead = mysqli_query($con,"select a.ctranno, a.ccode, a.captype, a.cpaymentfor, a.cpayee, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.ngross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, a.lvoid, b.cname, c.nrate, b.newtcode, a.ccurrencycode, a.ccurrencydesc, a.nexchangerate from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join wtaxcodes c on b.compcode=c.compcode and b.newtcode=c.ctaxcode where a.compcode = '$company' and a.ctranno = '$ctranno'");

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Purchase' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => number_format($row['nrate'])); 
		}
	}

	@$arrwtxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `wtaxcodes` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrwtxlist[] = array('ctaxcode' => $row['ctaxcode'], 'cbase' => $row['cbase'], 'nrate' => $row['nrate']); 
		}
	}

	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/APV/'.$company.'_'.$ctranno.'/')) {
		$allfiles = scandir('../../Components/assets/APV/'.$company.'_'.$ctranno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		//echo "NO FILES";
	}

	
	///get default EWT acct code
	@$ewtpaydef = "";
	@$ewtpaydefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno'];
			@$ewtpaydefdsc = $row['cacctdesc']; 
		}
	}

	//get default Input tax acct code
	@$OTpaydef = "";
	@$OTpaydefdsc = ""; 
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='PURCH_VAT'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$OTpaydef = $row['cacctno']; 
			@$OTpaydefdsc = $row['cacctdesc']; 
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
  	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
  <script src="../../Bootstrap/js/jquery.numeric.js"></script>
  <script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->
    
	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

	<style>

		.rowpor:hover{
			background-color: #e5f1f9;
   			cursor: pointer;
		}
	</style>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus(); disabled();">
	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes"> 
	<input type="hidden" value='<?=json_encode(@$arrwtxlist)?>' id="hdnxtax">  
	<input type="hidden" value='<?=@$ewtpaydef?>' id="hdnewtpay"> 
	<input type="hidden" value='<?=@$ewtpaydefdsc?>' id="hdnewtpaydsc"> 
	<input type="hidden" value='<?=@$OTpaydef ?>' id="hdnoutaxpay">
	<input type="hidden" value='<?=@$OTpaydefdsc ?>' id="hdnoutaxpaydsc">

	<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

	<?php

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$CustCode = $row['ccode'];
			$CustName = $row['cname'];
			$Payee = $row['cpayee'];
			$Remarks = $row['cpaymentfor'];
			$DateAPV = $row['dapvdate'];
			$cAPType = $row['captype'];
			$nGross = $row['ngross'];

			$EWTCode = $row['newtcode'];
			$EWTRate = $row['nrate'];

			$ccurrcode = $row['ccurrencycode'];  
			$ccurrdesc = $row['ccurrencydesc']; 
			$ccurrrate = $row['nexchangerate'];
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
			$lPrinted = $row['lprintposted'];
			$lVoid = $row['lvoid'];
		}

	?>

	<form action="APV_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">
		<fieldset>

			<legend>
				<div class="col-xs-6 nopadding"> AP Voucher </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
				<li class="active"><a href="#items" data-toggle="tab">AP Voucher Details</a></li>
				<li><a href="#attc" data-toggle="tab">Attachments</a></li>
			</ul>
			
			<div class="tab-content">
				<div id="items" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

					<table width="100%" border="0">
						<tr>
							<tH>APV No.:</tH>
							<td style="padding:2px;"><div class="col-xs-4 nopadding">
								<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter APV No..." required  value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
								</div>
								<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ctranno;?>">
								
								<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
								<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
								<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
								&nbsp;&nbsp;
								
								
							</td>
							<td colspan="2" style="padding:2px;" align="right">
							<div id="statmsgz" class="small" style="display:inline"></div>    
							</td>
						</tr>
						<tr>
							<tH width="150">Paid To:</tH>
							<td style="padding:2px;" width="500">
								<div class="row nopadding">
									<div class="col-xs-3 nopadding">
										<input type="text" class="form-control input-sm" id="txtcustid" name="txtcustid" readonly placeholder="Supplier Code..." value="<?php echo $CustCode; ?>">
										<input type="hidden" id="hdncustewt" name="hdncustewt" value="<?=$EWTCode?>">
										<input type="hidden" id="hdncustewtrate" name="hdncustewtrate" value="<?=$EWTRate?>">
									</div>
									<div class="col-xs-7 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required autocomplete="off"  value="<?php echo $CustName; ?>">
									</div>
								</div>            
								<input type="hidden" id="txtcustchkr" name="txtcustchkr">
								<input type="hidden" id="seltype" name="seltype">
											
							</td>
							<tH width="150" style="padding:2px">AP Type:<input type="hidden" id="txtpayee" name="txtpayee"></tH>
							<td style="padding:2px;">
								<div class="row">
									<div class="col-xs-7">
										<select id="selaptyp" name="selaptyp" class="form-control input-sm selectpicker" tabindex="2">
											<option value="Purchases" <?php if($cAPType=="Purchases"){ echo "selected"; } ?>>Purchases (Credit)</option>
											<option value="PurchAdv" <?php if($cAPType=="PurchAdv"){ echo "selected"; } ?>>Purchases (Advance Payment)</option>
											<option value="PettyCash" <?php if($cAPType=="PettyCash"){ echo "selected"; } ?>>Petty Cash Replenishment</option>
											<option value="Others" <?php if($cAPType=="Others"){ echo "selected"; } ?>>Others</option>
										</select>
									</div>
									<div class="col-xs-4 nopadwleft">
										<button type="button" class="btn btn-warning btn-sm btn-block" tabindex="3" id="btnproforma" name="btnproforma"  <?php if($cAPType=="Purchases" || $cAPType=="PurchAdv"){ echo "style=\"display: none\""; } ?>>
											Proforma
										</button>	
									</div>

								</div>
							</td>
						</tr>
						<tr>
							<tH width="150" rowspan="2" valign="top">Remarks:</tH>
							<td rowspan="2" valign="top" style="padding:2px">
								<div class="col-xs-10 nopadding">
									<textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $Remarks;?></textarea>
								</div>
							</td>
							<tH width="150" style="padding:2px">AP Date:</tH>
							<td style="padding:2px">
								<div class="row">
									<div class="col-xs-7">
										<input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $DateAPV; ?>" />
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<tH style="padding:2px">Total Amount :</tH>
							<td style="padding:2px">
								<div class="row">
									<div class="col-xs-7">
										<input type="text" class="form-control input-sm" id="txtnGross" name="txtnGross" tabindex="1" required value="<?php echo number_format($nGross,2);?>" style="font-weight:bold; color:#F00; text-align:right" readonly>
									</div> 
								</div>   
						</td>
						</tr>
						<tr>
							<tH width="150" rowspan="2" valign="top">Currency:</tH>
							<td rowspan="2" valign="top" style="padding:2px">
								<div class="row nopadding">
									<div class="col-xs-7 nopadding">
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

												$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
												if (mysqli_num_rows($sqlhead)!=0) {
													while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
											?>
												<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>" data-desc="<?=$rows['currencyName']?>"><?=$rows['currencyName']?></option>
											<?php
													}
												}
											?>
										</select>
										<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?=$nvaluecurrbase; ?>"> 	
										<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?=$ccurrdesc; ?>"> 
									</div>
									<div class="col-xs-2 nopadwleft">
										<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?=$ccurrrate; ?>">	 
									</div>
									<div class="col-xs-3" id="statgetrate" style="padding: 4px !important"> 																	
									</div>
								</div>
							</td>
							<tH style="padding:2px">&nbsp;</tH>
							<td style="padding:2px">&nbsp;</td>
						</tr>
					</table>

				</div>

				<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

					<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
					<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
					<input type="file" name="upload[]" id="file-0" multiple />

				</div>
			</div>

			<hr>
			<div class="col-xs-12 nopadwdown"><b>Details</b></div>

			<ul class="nav nav-tabs">
				<li id="lidet" <?=($cAPType=="Purchases" || $cAPType=="PurchAdv") ? "class='active'" : ""?>><a href="#1" data-toggle="tab">Details</a></li>
				<li id="liacct" <?=($cAPType!=="Purchases" && $cAPType!=="PurchAdv") ? "class='active'" : ""?>><a href="#2" data-toggle="tab">Accounting</a></li>
			</ul>

			<div class="tab-content nopadwtop2x">

				<div class="tab-pane <?=($cAPType=="Purchases"|| $cAPType=="PurchAdv") ? "active" : ""?>" id="1">  

					<div class="alt2" dir="ltr" style="
						margin: 0px;
						padding: 3px;
						border: 1px solid #919b9c;
						width: 100%;
						height: 250px;
						text-align: left;
						overflow: scroll">
					
							<table id="MyTable" border="1" bordercolor="#CCCCCC" width="100%">
								<thead>
								<tr>
									<th width="120" style="border-bottom:1px solid #999">Ref No.</th>
									<!--<th style="border-bottom:1px solid #999">Supplier SI</th>-->
									<!--<th style="border-bottom:1px solid #999">Description</th>-->
									<th style="border-bottom:1px solid #999">Amount</th>
									<th scope="col" class="text-center" nowrap>Total CM</th>
									<!--<th scope="col" class="text-center" nowrap>Total Disc.</th>
									<th style="border-bottom:1px solid #999">Remarks</th>                                               
									<th scope="col" class="text-center" nowrap>VATCode</th>
									<th scope="col" class="text-center" nowrap>VATRate(%)</th>
									<th scope="col" class="text-center" nowrap>VATAmt</th>
									<th scope="col" class="text-center" nowrap>NetofVat</th>
									<th scope="col" class="text-center" nowrap>EWTCode</th>                            
									<th scope="col" class="text-center" nowrap>EWTRate(%)</th>
									<th scope="col" class="text-center" nowrap>EWTAmt</th>
									<th scope="col" class="text-center" nowrap>Payments</th> -->
									<th scope="col" class="text-center" nowrap>Total Due</th>
									<th width="120" scope="col" class="text-center" nowrap>Account Code</th>
									<th scope="col" class="text-center" nowrap>Account Title</th>
									<!--  <th scope="col" class="text-center" nowrap>Amt Applied&nbsp;</th> -->
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
								</thead>
								<tbody class="tbody">
									<?php 
										$sqlbody = mysqli_query($con,"select a.*, B.cacctdesc from apv_d a left join accounts B on A.compcode=B.compcode and A.cacctno = B.cacctid where a.compcode = '$company' and a.ctranno = '$ctranno' order by a.nidentity");

										if (mysqli_num_rows($sqlbody)!=0) {
											$cntr = 0;
											while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
												$cntr = $cntr + 1;
									?>
									<tr>
										<td width="130px" style="padding:1px"> <input type='text' name="txtrefno" id="txtrefno<?php echo $cntr;?>" class="form-control input-sm" required value="<?php echo $rowbody['crefno'];?>" readonly> <input type='hidden' name="txtrefsi<?php echo $cntr;?>" id="txtrefsi<?php echo $cntr;?>" value="<?php echo $rowbody['crefinv'];?>"> </td>

										<td  width="150px" style="padding:1px"><input type='text' name="txtnamount" id="txtnamount<?php echo $cntr;?>" class="numeric form-control input-sm" value="<?php echo $rowbody['namount'];?>" style="text-align:right" readonly></td>
				
										<td  width="150px" style="padding:1px"><div class="input-group"><input type='text' name="txtncm" id="txtncm<?php echo $cntr;?>" class="numeric form-control input-sm" value="<?php echo $rowbody['napcm'];?>" style="text-align:right" readonly><span class="input-group-btn"><button class="btn btn-primary btn-sm" name="btnaddcm" id="btnaddcm<?php echo $cntr;?>" type="button" onclick="addCM('<?php echo $rowbody['crefno'];?>','txtncm<?php echo $cntr;?>')"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button></span></div></td>
												
										<td  width="150px" style="padding:1px"><input type='text' name="txtDue" id="txtDue<?php echo $cntr;?>" class="numeric form-control input-sm" value="<?php echo $rowbody['ndue'];?>" style="text-align:right" readonly></td>
										
										<td nowrap><input type='text' value='<?=$rowbody['cacctno']?>' class='form-control input-sm' name="txtrefacctno" id='txtrefacctno<?php echo $cntr;?>' readonly> </td>
												
										<td nowrap style="padding-left:2px"><input type='text' value='<?=$rowbody['cacctdesc']?>' class='cacctdesc form-control input-sm' name="txtacctname" id='txtacctname<?php echo $cntr;?>'></td>

										<td width="50px" style="padding:1px"><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $rowbody['crefno'].$cntr;?>_delete' class='delete' value='delete' onClick="deleteRow1(this);"/></td>
												
									</tr>                           
									<script>
										$("#txtnvatcode<?php echo $cntr;?>").select2({ width: '100%' });
										$("#txtewtcode<?php echo $cntr;?>").select2({ width: '100%' }); 
										$("#txtewtcode<?php echo $cntr;?>").on('select2:select', function (e) {

											//$("#txtewtcode"+lastRow).val(item.ctaxcode).change(); 
											xcrate = $(e.params.data.element).data('rate');
											$("#txtewtrate"+lastRow).val(xcrate);
											
											var xcb = 0;
											var xcbdue = 0;
											//alert(item.cbase)
											xcbase = $(e.params.data.element).data('cbase');
											if(xcbase=="NET"){
												xcb = parseFloat($("#txtvatnet"+lastRow).val().replace(/,/g,''))*(xcrate/100);
											}else{
												xcb = parseFloat($("#txtnamount"+lastRow).val().replace(/,/g,''))*(xcrate/100);
											}
											
											xcnmdue = parseFloat($("#txtDue"+lastRow).val().replace(/,/g,''));
											$("#txtewtamt"+lastRow).val(xcb)
											$("#txtewtamt"+lastRow).autoNumeric('destroy');
											$("#txtewtamt"+lastRow).autoNumeric('init',{mDec:2});
											//recompute due
											var ndiscs = $("#txtndiscs"+lastRow).val().replace(/,/g,''); 
											xcbdue = xcnmdue - xcb - parseFloat(ndiscs);
												
											$("#txtDue"+lastRow).val(xcbdue);
											$("#txtDue"+lastRow).autoNumeric('destroy');
											$("#txtDue"+lastRow).autoNumeric('init',{mDec:2}); 
											
											//recomlines();
											compgross1();
											//setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
												
										});

										$("#txtnvatcode<?php echo $cntr;?>").on("change", function() {
											var zxc = $(this).find(':selected').data('id');
											var zxcamt = $("#txtnamount"+lastRow).val().replace(/,/g,'');

											compvat(lastRow,zxc,zxcamt);
											compgross1();

										});
									</script>                            
											
								<?php
															}
														}															
													?>

								</tbody>
										
							</table>
							<input type="hidden" name="hdnRRCnt" id="hdnRRCnt"> 

					</div>
	
				</div>

				<div class="tab-pane <?=($cAPType!=="Purchases" && $cAPType!=="PurchAdv") ? "active" : ""?>" id="2">

					<div class="alt2" dir="ltr" style="
						margin: 0px;
						padding: 3px;
						border: 1px solid #919b9c;
						width: 100%;
						height: 250px;
						text-align: left;
						overflow: auto">
					
						<table id="MyTable2" cellpadding="3px" width="100%" border="0">
							<thead>
								<tr>
									<th style="border-bottom:1px solid #999">Acct#</th>
									<th style="border-bottom:1px solid #999">Account Title</th>
									<th style="border-bottom:1px solid #999">Debit</th>
									<th style="border-bottom:1px solid #999">Credit</th>
									<!--<th style="border-bottom:1px solid #999">Subsidiary</th>-->
									<th style="border-bottom:1px solid #999">Remarks</th>
									<th style="border-bottom:1px solid #999">EWT/VAT Code</th>
									<!--<th style="border-bottom:1px solid #999">Type</th>-->
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$sqlbody = mysqli_query($con,"select a.cacctno, a.ctitle, a.ndebit, a.ncredit, a.cremarks, a.csubsidiary, a.cacctrem, a.cewtcode, a.newtrate from apv_t a where a.compcode = '$company' and a.ctranno = '$ctranno' order by a.nidentity");

									if (mysqli_num_rows($sqlbody)!=0) {
										$cntr = 0;
										while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
											$cntr = $cntr + 1;
								?>
									<tr>
										<td style="padding:1px" width="100px">
											<input type='text' name="txtacctno<?php echo $cntr;?>" id="txtacctno<?php echo $cntr;?>" class="form-control input-sm" value="<?php echo $rowbody['cacctno'];?>" style="text-transform:uppercase" readOnly>
										</td>
										<td style="padding:1px" width="400px">
											<input type='text' name="txtacctitle<?php echo $cntr;?>" id="txtacctitle<?php echo $cntr;?>" class="form-control input-sm" value="<?php echo $rowbody['ctitle'];?>" readonly>
										</td>
										<td style="padding:1px" width="150px">
											<input type='text' name="txtdebit<?php echo $cntr;?>" id="txtdebit<?php echo $cntr;?>" class="numeric form-control input-sm" value="<?php echo $rowbody['ndebit'];?>" style="text-align:right"  onkeyup="compgross();" required>
										</td>
										<td style="padding:1px" width="150px">
											<input type='text' name="txtcredit<?php echo $cntr;?>" id="txtcredit<?php echo $cntr;?>" class="numeric form-control input-sm" value="<?php echo $rowbody['ncredit'];?>" style="text-align:right" onkeyup="compgross();" required>
										</td>
										<!--<td style="padding:1px">
											<input type='text' name="txtsubs<?php //echo $cntr;?>" id="txtsubs<?php //echo $cntr;?>" class="form-control input-sm" placeholder="Search Name..." onkeyup="searchSUBS(this.name);" value="<?php //echo $rowbody['csubsidiary'];?>"> <input type='hidden' name="txtsubsid<?php//echo $cntr;?>" id="txtsubsid<?php//echo $cntr;?>" value="<?php //echo $rowbody['csubsidiary'];?>">
										</td>-->
										<td style="padding:1px">
											<input type='text' name="txtacctrem<?php echo $cntr;?>" id="txtacctrem<?php echo $cntr;?>" class="form-control input-sm" value='<?php echo $rowbody['cremarks'];?>'>
										</td>

										<td style="padding:1px">
											<?php
												$optns = "";
												$optnsselc = "";
												$isslctd = "";
												if($rowbody['cacctno']==@$ewtpaydef){     
													$optns = @$arrwtxlist;
												}elseif($rowbody['cacctno']==@$OTpaydef){
													$optns = @$arrtaxlist;
												}
												if($optns!=""){
													foreach($optns as $rsx){
														if($rsx['ctaxcode']==$rowbody['cewtcode']){
															$isslctd = "selected";
														}else{
															$isslctd = "";
														}
														$optnsselc = $optnsselc."<option value='".$rsx['ctaxcode']."' data-rate='".$rsx['nrate']."' ".$isslctd.">".$rsx['ctaxcode'].": ".$rsx['nrate']."%</option>";
													}
												}
											?>

											<select class='form-control input-xs' name="txtewtcodeothers<?php echo $cntr;?>" id="txtewtcodeothers<?php echo $cntr;?>" style="width: 100%">
												<option value="">&nbsp;</option>
												<?=$optnsselc?>
											</select> 

											<input type='hidden' name="txtewtrateothers<?php echo $cntr;?>" id="txtewtrateothers<?php echo $cntr;?>" value="<?php echo $rowbody['newtrate'];?>">
										</td>
										<!--<td style="padding:1px">
											<input type='text' name="txtewtcodeothers<?//php echo $cntr;?>" id="txtewtcodeothers<?//php echo $cntr;?>" class="form-control input-sm" autocomplete="off" value='<?//php echo $rowbody['cewtcode'];?>'> 
										</td>
										
										<td style="padding:1px" width="100px">
											<select name="selacctpaytyp<?//php echo $cntr;?>" id="selacctpaytyp<?//php echo $cntr;?>" class="form-control input-sm selectpicker">
												<option value="Payables" <?//php if($rowbody['cacctrem']=="Payables") { echo "selected"; } ?>>Payables</option>
												<option value="Others"<?//php if($rowbody['cacctrem']=="Others") { echo "selected"; } ?>>Others</option>
											</select>
										</td>   
										-->                             
										<td>
											<?//php if($cAPType=="Savings" or $cAPType=="Others") {?>
												<input class='btn btn-danger btn-xs' type='button' id='row2_<?php echo $cntr;?>_delete' value='delete' onClick="deleteRow2(this);"/>
											<?//php}?>
										</td>                            	
									</tr>

									<script type="text/javascript">
										$("#txtewtcodeothers<?php echo $cntr;?>").select2({ width: '100%' });
										$("#txtewtcodeothers<?php echo $cntr;?>").on("select2:select", function(event) {
											$("#txtewtrateothers<?php echo $cntr;?>").val($(this).select2().find(":selected").data("rate"));
										});

										/*$("#txtewtcodeothers<?//php echo $cntr;?>").typeahead({
											items: 10,
											source: function(request, response) {
												$.ajax({
													url: "../th_ewtcodes.php",
													dataType: "json",
													data: {
														query: $("#txtewtcodeothers<?//php echo $cntr;?>").val()
													},
													success: function (data) {
														response(data);														
													}
												});
											},
											autoSelect: true,
												displayText: function (item) {
													return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.ctaxcode + '</span><br><small>' + item.cdesc + "</small></div>";
											},
											highlighter: Object,
											afterSelect: function(item, event) { 
												$("#txtewtcodeothers<?//php echo $cntr;?>").val(item.ctaxcode).change(); 		
												$("#txtewtrateothers<?//php echo $cntr;?>").val(item.nrate); 																	
											}
										});*/
									</script>
								<?php
										}
									}                        
								?>
							</tbody>
													
						</table>
						<input type="hidden" name="hdnACCCnt" id="hdnACCCnt">
					</div>

				</div>

			</div>
			
			<?php
				if($poststat == "True" || $printstat == "True"){
			?>
			<br>
			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td width="50%">
						<?php
							if($poststat == "True"){
						?>
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='APV.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>&st=<?=isset($_REQUEST['hdnsrchsta']) ? $_REQUEST['hdnsrchsta'] : ""?>';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>

						<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='APV_new.php';" id="btnNew" name="btnNew">
							New<br>(F1)
						</button>

						<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv('Purchases','supplier','MyDRDetList','DRListHeader','th_rrlistings','RR','mySIModal');" id="btnqo" style="display:none">
							Supp. Inv<br> (Insert)
						</button>

						<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv('POAdv','supplier','MyDRDetList','DRListHeader','th_polistings','PO','mySIModal');" id="btnpo" style="display:none">
							P.O<br> (Insert)
						</button>
						
						<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="addacct();" id="btnacc" style="display:none">
							New Line<br> (Accounting)
						</button>
					
						<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
							Undo Edit<br>(CTRL+Z)
						</button>

						<?php
							}

							if($printstat == "True"){
						?>

						<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $ctranno;?>');" id="btnPrint" name="btnPrint">
							Print<br>(CTRL+P)
						</button>
						 
						<?php
							}

							if($poststat == "True"){
						?>

						<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
							Edit<br>(CTRL+E)    
						</button>
							
						<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
						<?php
							}
						?>
					</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
			<?php
				}
			?>

   		</fieldset>
	
		<!-- add CM Module -->
		<div class="modal fade" id="MyDetModal" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close"  aria-label="Close"  onclick="chkCloseInfo();"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="invheader"> 
							Additional AP Credit Memo <button class="btn btn-sm btn-primary" name="btnaddcm" id="btnaddcm" type="button">Add</button>
						</h4>           
					</div>
			
					<div class="modal-body">
						<input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2"> 
						<input type="hidden" name="txthdnCMinfo" id="txthdnCMinfo"> 
						<input type="hidden" name="txthdnCMtxtbx" id="txthdnCMtxtbx"> 
					
						<table id="MyTableCMx" class="MyTable table table-sm" width="100%">
							<thead>
								<tr>
									<th style="border-bottom:1px solid #999">AP CM No.</th>
									<th style="border-bottom:1px solid #999">Date</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">Remarks</th>
									<th style="border-bottom:1px solid #999">Acct No.</th>
									<th style="border-bottom:1px solid #999">Acct Desc.</th>
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
							</thead>
							<tbody class="tbody">
								<?php 
									$sqlbody2 = mysqli_query($con,"select a.ctranno, a.cidentity, a.nidentity, a.cwithref, a.crefrr, a.crefapcm, a.drefapcmdate, a.ctype, a.namount, a.cremarks, a.cacctno, b.cacctdesc from apv_deds a left join accounts b on a.cacctno=b.cacctno where a.compcode = '$company' and a.ctranno = '$ctranno' AND a.ctype='CM' order by a.nidentity");

									if (mysqli_num_rows($sqlbody2)!=0) {
										$cntr2 = 0;
										while($rowbody2 = mysqli_fetch_array($sqlbody2, MYSQLI_ASSOC)){
											$cntr2 = $cntr2 + 1;	

								?>	
								<tr>
									<td><input type='hidden' name='txtcmrr' id='txtcmrr<?php echo $cntr2;?>' value='<?php echo $rowbody2['crefrr'];?>'><input type='hidden' name='txtcmithref' id='txtcmithref<?php echo $cntr2;?>' value='<?php echo $rowbody2['cwithref'];?>'><input type='text' name='txtapcmdm' id='txtapcmdm<?php echo $cntr2;?>' class='form-control input-xs' value='<?php echo $rowbody2['crefapcm'];?>'></td>

									<td><input type='text' name='txtapdte' id='txtapdte<?php echo $cntr2;?>' class='form-control input-xs' readonly value='<?php echo $rowbody2['drefapcmdate'];?>'></td>

									<td><input type='text' name='txtapamt' id='txtapamt<?php echo $cntr2;?>' class='form-control input-xs text-right' readonly value='<?php echo $rowbody2['namount'];?>'></td>

									<td><input type='text' name='txtremz' id='txtremz<?php echo $cntr2;?>' class='form-control input-xs' value='<?php echo $rowbody2['cremarks'];?>'></td>

									<td><input type='text' name='txtaccapcm' id='txtaccapcm<?php echo $cntr2;?>' class='form-control input-xs' value='<?php echo $rowbody2['cacctno'];?>'></td>

									<td><input type='text' name='txtaccapcmdec' id='txtaccapcmdec<?php echo $cntr2;?>' class='form-control input-xs' value='<?php echo $rowbody2['cacctdesc'];?>'></td>

									<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo<?php echo $cntr2;?>' value='delete' /></td>
								</tr>
							
									<script>
										$("#delinfo<?php echo $cntr2;?>").on('click', function() { 
											$(this).closest('tr').remove();
										});

										$("#txtaccapcm<?php echo $cntr2;?>").on("keyup", function(event) {
											if(event.keyCode == 13){

												var dInput = this.value;

													$.ajax({
													type:'post',
													url:'../getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														//alert(value);
														if(value.trim()!=""){
															$("#txtaccapcmdec<?php echo $cntr2;?>").val(value.trim());
														}
													}
													});

											}
										});

										$("#txtaccapcmdec<?php echo $cntr2;?>").typeahead({

											items: 10,
											source: function(request, response) {
												$.ajax({
													url: "../th_accounts.php",
													dataType: "json",
													data: {
														query: $("#txtaccapcmdec<?php echo $cntr2;?>").val()
													},
													success: function (data) {
														response(data);
													}
												});
											},
											autoSelect: true,
											displayText: function (item) {
												return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + '</small></div>';
											},
											highlighter: Object,
											afterSelect: function(item) { 
												$("#txtaccapcmdec<?php echo $cntr2;?>").val(item.name).change(); 
												$("#txtaccapcm<?php echo $cntr2;?>").val(item.id);
											}
										});  

										$("#txtapcmdm<?php echo $cntr2;?>").typeahead({
											items: 10,
											source: function(request, response) {
												var apcmlist = "";
												$("#MyTableCMx > tbody > tr").each(function(index) {	
													if(index>0){

														var citmfld1 = $(this).find('input[name="txtapcmdm"]').val();
														if(index>1){
															apcmlist = apcmlist + ",";
														}

														apcmlist = apcmlist + citmfld1;
													}

												});

												$.ajax({
													url: "th_getapcm.php",
													dataType: "json",
													data: {
														query: $("#txtapcmdm<?php echo $cntr2;?>").val(), code: $("#txtcustid").val(), lst: apcmlist
													},
													success: function (data) {
														response(data);
													}
												});
											},
											autoSelect: true,
											displayText: function (item) {
												return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.ddate + ' - ' +  item.ngross + '</small><br><small>' + item.crem + '</small></div>';
											},
											highlighter: Object,
											afterSelect: function(item) { 
												$("#txtapcmdm<?php echo $cntr2;?>").val(item.id).change(); 
												$("#txtapdte<?php echo $cntr2;?>").val(item.ddate);
												$("#txtapamt<?php echo $cntr2;?>").val(item.ngross);
												$("#txtremz<?php echo $cntr2;?>").val("");
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
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

	</form>

	<?php
	}
	else{
	?>

		<form action="APV_edit.php" name="frmpos2" id="frmpos2" method="post">
			<fieldset>
				<legend>AP Invoice</legend>	
				<table width="100%" border="0">
					<tr>
						<tH width="100">APV NO:</tH>
						<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
					</tr>
					<tr>
						<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>AP No. DID NOT EXIST!</b></font></tH>
					</tr>
				</table>
			</fieldset>
		</form>

	<?php
		}
	?>

	<!-- DETAILS ONLY -->
	<div class="modal fade" id="mySIModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="DRListHeader">Supplier's Invoice List</h3>
				</div>
							
				<div class="modal-body pre-scrollable">
				
					<table name='MyDRDetList' id='MyDRDetList' class="table table-small">
						<thead>
							<tr>
								<th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /> </th>
								<th>Trans No</th>
								<th>Reference</th>
								<th>Supp Inv Date</th>
								<th style="text-align: right">Gross</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>         	
						
						
				<div class="modal-footer">
					<button type="button" id="btnSave" onClick="InsertSI()" class="btn btn-primary">Insert</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End Bootstrap modal -->

	<!-- Proforma -->
	<div class="modal fade" id="ProformaMod" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
            	<div class="modal-header">
                	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h3 class="modal-title" id="DRListHeader">Proforma List</h3>
            	</div>
            
            	<div class="modal-body pre-scrollable">
            
					<table name='MrProFormaDet' id='MrProFormaDet' class="table table-small">
						<thead>
							<tr>
								<th>Description</th>
								<th width="80px">Frequency</th>
								<th width="100px">Due</th>
								<th width="100px" style="text-align: right">Gross Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sqlfreq = "Select * from proforma_ap_freq where compcode='$company'";
								$resfreq=mysqli_query($con,$sqlfreq);
								@$arrayallfreq = array();
								while($row = mysqli_fetch_array($resfreq, MYSQLI_ASSOC))
								{
									@$arrayallfreq[] = $row;
								}

								@$arrayall = array();
								$respro = mysqli_query($con,"select A.*, B.cacctdesc as CRDesc, C.cacctdesc as DRDesc from proforma_ap A left join accounts B on A.compcode=B.compcode and A.cacctcodecr=B.cacctid left join accounts C on A.compcode=C.compcode and A.cacctcodedr=C.cacctid where A.compcode='$company' order by A.nidentity"); 
								if (mysqli_num_rows($respro)!=0) {
									while($row = mysqli_fetch_array($respro, MYSQLI_ASSOC)){
										@$arrayall[] = $row;
							?>
								<tr class="rowpor" onclick="showproqty(<?=$row['nidentity'];?>,'<?=$row['ngross'];?>');">
									<td><?=$row['cdescription']?></td>
									<td><?=$row['cfrequency']?>	</td>
									<td>
										<?php
											foreach(@$arrayallfreq as $rvb){
												if($rvb['proforma_ap_id']==$row['nidentity']){
													if($row['cfrequency']=="Monthly"){
														echo "Every ". ordinal($rvb['nmonthly']);
													}else if($row['cfrequency']=="Quarterly"){
														$curMonth = date("m", time());
														$curQuarter = ceil($curMonth/3);

														$str = $rvb['cq'.$curQuarter];
														$arr = explode("/", $str);

														$dateObj   = DateTime::createFromFormat('!m', $arr[0]);
														$monthName = $dateObj->format('F');

														echo $monthName."-".$arr[1];

													}else if($row['cfrequency']=="Semi"){
														$curMonth = date("m", time());
														$curQuarter = ceil($curMonth/6);

														$str = $rvb['cs'.$curQuarter];
														$arr = explode("/", $str);

														$dateObj   = DateTime::createFromFormat('!m', $arr[0]);
														$monthName = $dateObj->format('F');

														echo $monthName."-".$arr[1];
													}else if($row['cfrequency']=="Annual"){
														echo $rvb['dannual'];
													}
												}
											}
										?>
									</td>
									<td style="text-align: right"><?=number_format($row['ngross'],2)?></td>
								</tr> 
							
							<?php
									}
								}
							?>
									
						</tbody>
					</table>
           	 	</div>         	
         			
           	 	<div class="modal-footer">
                	<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
           		</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End Proforma modal -->

	<div class="modal fade" id="ModalProQty" >
		<div class="modal-dialog" style="width: 200px !important">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Enter Amount</h4>
				</div>
				<div class="container"></div>
				<div class="modal-body" style="height: 10vh;">
					<input type="text" class="nnumeric form-control input-sm text-right" id="txtprogross" name="txtprogross" placeholder="0.00" value="0.00" tabindex="1">
					<input type="hidden" id="txtproid" name="txtproid" value="">
				</div>
				<div class="modal-footer">
					<button type="button" id="btnInsPro" class="btn btn-success">Insert</a>
					<button type="button" id="btnCancelPro" data-dismiss="modal" class="btn btn-danger">Cancel</a>					
				</div>
			</div>
		</div>
	</div>


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

	<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-contnorad">   
				<div class="modal-bodylong" style="height: 11in !important">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
			
					<iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:98%; display:block; margin:0px; padding:0px; border:0px"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</body>
</html>

<script type="text/javascript">

	var fileslist = [];
	/*
	var xz = $("#hdnfiles").val();
	$.each(jQuery.parseJSON(xz), function() { 
		fileslist.push(xz);
	});
	*/

	console.log(fileslist);
	var filesconfigs = [];
	var xzconfig = JSON.parse($("#hdnfileconfig").val());

	//alert(xzconfig.length);

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var xtc = "";
	for (var i = 0; i < xzconfig.length; i++) {
    var object = xzconfig[i];
		//alert(object.ext + " : " + object.name);
		$vrx = encodeURIComponent(object.name);
		fileslist.push("<?=$AttachUrlBase?>APV/<?=$company."_".$ctranno?>/" + object.name)

		if(jQuery.inArray(object.ext, arroffice) !== -1){
			xtc = "office";
		}else if(jQuery.inArray(object.ext, arrimg) !== -1){
			xtc = "image";
		}else if(object.ext=="txt"){
			xtc = "text";
		}else{
			xtc = object.ext;
		}

		filesconfigs.push({
			type : xtc, 
			caption : object.name,
			width : "120px",
			url: "th_filedelete.php?id="+object.name+"&code=<?=$ctranno?>", 
			key: i + 1
		});
	}

	<?php
		if($poststat == "True" || $printstat == "True"){
	?>

	$(document).keydown(function(e) {	 
		<?php
			if($poststat == "True"){
		?>

		if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='APV_new.php';
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

		<?php
			}
			if($printstat == "True"){
		?>

		else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
			if($("#btnPrint").is(":disabled")==false){
				e.preventDefault();
				printchk('<?php echo $ctranno;?>');
			}
		}
		<?php
			}
			if($poststat == "True"){
		?>
		else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmpos');
			}
		}
		else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				$("#btnMain").click();
			}
		}
		else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
			e.preventDefault();
			$('#txtprodnme').focus();
		}
		else if(e.keyCode == 45) { //Insert
			if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
				//openinv();

				if($("#selaptyp").val()=="Purchases"){
					$('#btnqo').trigger('click');
				}else if($("#selaptyp").val()=="PurchAdv"){
					$('#btnpo').trigger('click');
				}
			}
		}
		<?php
			}
		?>
	});

	<?php
		}
	?>
	
	$(document).ready(function(e) {

		$("input.numeric").autoNumeric('init',{mDec:2});
	
		$('.datepick').datetimepicker({
			format: 'MM/DD/YYYY'
		});

		if(fileslist.length>0){
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: fileslist,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/APV/<?=$company."_".$ctranno?>/{filename}',
				initialPreviewConfig: filesconfigs
			});
		}else{
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}

		$('#txtcust').typeahead({
		
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../th_supplier.php",
					dataType: "json",
					data: {
						query: $("#txtcust").val(), x: $("#selaptyp").val()
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
				$("#txtpayee").val(item.value);
				$("#hdncustewt").val(item.cewtcode);
				$("#hdncustewtrate").val(item.newtrate);

				$("#selbasecurr").val(item.cdefaultcurrency).change();
				$("#basecurrval").val($("#selbasecurr").find(':selected').data('val'));
				$("#hidcurrvaldesc").val($("#selbasecurr").find(':selected').data('desc'));
			}
		});

		$("#allbox").click(function () {
			if ($("#allbox").is(':checked')) {
				$("input[name='chkSales[]']").each(function () {
					$(this).prop("checked", true);
				});

			} else {
				$("input[name='chkSales[]']").each(function () {
					$(this).prop("checked", false);
				});
			}
		});
	
	
		$("#selaptyp").on("change", function() {

			aptypechange($(this).val());
							
			$("#MyTable tbody > tr").remove();
			$("#MyTable2 tbody > tr").remove();

			//$("#txtcustid").val("");
			//$("#txtcust").val("");
			//$("#txtcust").attr("readonly", false);
			//$("#txtpayee").val("");
			//$("#txtnGross").val("");
									
		});
	
	
		$("#btnaddcm").on("click", function(){
			
			
			var tbl = document.getElementById('MyTableCMx').getElementsByTagName('tr');
			var lastRow = tbl.length;
			var xrrno = $("#txthdnCMinfo").val();

			var tdapcm = "<td><input type='hidden' name='txtcmrr' id='txtcmrr"+lastRow+"' value='"+xrrno+"'><input type='hidden' name='txtcmithref' id='txtcmithref"+lastRow+"' value='0'><input type='text' name='txtapcmdm' id='txtapcmdm"+lastRow+"' class='form-control input-xs'></td>";
			var tddate = "<td><input type='text' name='txtapdte' id='txtapdte"+lastRow+"' class='form-control input-xs' readonly></td>"
			var tdamt = "<td><input type='text' name='txtapamt' id='txtapamt"+lastRow+"' class='form-control input-xs text-right' readonly></td>";
			var tdrem = "<td><input type='text' name='txtremz' id='txtremz"+lastRow+"' class='form-control input-xs'></td>";
			var tdacc = "<td><input type='text' name='txtaccapcm' id='txtaccapcm"+lastRow+"' class='form-control input-xs'></td>";
			var tdaccdc = "<td><input type='text' name='txtaccapcmdec' id='txtaccapcmdec"+lastRow+"' class='form-control input-xs'></td>";
			var tdels = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + xrrno + lastRow + "' value='delete' /></td>";

			$('#MyTableCMx > tbody:last-child').append('<tr>'+tdapcm + tddate + tdamt + tdrem + tdacc + tdaccdc + tdels + '</tr>'); 

				$("#delinfo"+xrrno+lastRow).on('click', function() { 
					$(this).closest('tr').remove();
				});
			
				$("#txtaccapcm"+lastRow).on("keyup", function(event) {
					if(event.keyCode == 13){

						var dInput = this.value;

							$.ajax({
							type:'post',
							url:'../getaccountid.php',
							data: 'c_id='+ $(this).val(),                 
							success: function(value){
								//alert(value);
								if(value.trim()!=""){
									$("#txtaccapcmdec"+lastRow).val(value.trim());
								}
							}
							});

					}
				});
			
				$("#txtaccapcmdec"+lastRow).typeahead({

					items: 10,
					source: function(request, response) {
						$.ajax({
							url: "../th_accounts.php",
							dataType: "json",
							data: {
								query: $("#txtaccapcmdec"+lastRow).val()
							},
							success: function (data) {
								response(data);
							}
						});
					},
					autoSelect: true,
					displayText: function (item) {
						return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + '</small></div>';
					},
					highlighter: Object,
					afterSelect: function(item) { 
						$("#txtaccapcmdec"+lastRow).val(item.name).change(); 
						$("#txtaccapcm"+lastRow).val(item.id);
					}
				});  
		
				$("#txtapcmdm"+lastRow).typeahead({
					items: 10,
					source: function(request, response) {
						var apcmlist = "";
						$("#MyTableCMx > tbody > tr").each(function(index) {	
							if(index>0){

								var citmfld1 = $(this).find('input[name="txtapcmdm"]').val();
								if(index>1){
									apcmlist = apcmlist + ",";
								}
								
								apcmlist = apcmlist + citmfld1;
							}

						});
						
						$.ajax({
							url: "th_getapcm.php",
							dataType: "json",
							data: {
								query: $("#txtapcmdm"+lastRow).val(), code: $("#txtcustid").val(), lst: apcmlist
							},
							success: function (data) {
								response(data);
							}
						});
					},
					autoSelect: true,
					displayText: function (item) {
						return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.ddate + ' - ' +  item.ngross + '</small><br><small>' + item.crem + '</small></div>';
					},
					highlighter: Object,
					afterSelect: function(item) { 
						$("#txtapcmdm"+lastRow).val(item.id).change(); 
						$("#txtapdte"+lastRow).val(item.ddate);
						$("#txtapamt"+lastRow).val(item.ngross);
					}
				});
		
		});
	
		$('#MyDetModal,#MyDiscsModal').on('hidden.bs.modal', function (e) {
			recomlines();
		});

		$("#selbasecurr").on("change", function (){
	
			var dval = $(this).find(':selected').attr('data-val');
			var ddesc = $(this).find(':selected').attr('data-desc');

			$("#basecurrval").val(dval);
			$("#hidcurrvaldesc").val(ddesc);
			$("#statgetrate").html("");
				
		});

		$("#btnproforma").on("click", function(){
			$("#ProformaMod").modal("show");
		});

		$(document).on('show.bs.modal', '.modal', function() {
			const zIndex = 1040 + 10 * $('.modal:visible').length;
			$(this).css('z-index', zIndex);
			setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
		});
		
		$("#btnInsPro").on("click", function(){
			var id = $("#txtproid").val();
			var ngross = $("#txtprogross").val();

			$xc = '<?=json_encode(@$arrayall);?>';
			$.each(jQuery.parseJSON($xc), function() {
				if(this['nidentity']==id){
					//nacct,ntitle,ndr,ncr,nvcode,nvrate

					//VAT AND NET COMPUTATION//
					$nnet = 0;
					$nvats =0;
					$newt = 0;

					$tonrate = this['cvatrate'];
					if(parseFloat($tonrate) > 0){
						$nnet = parseFloat(ngross.replace(/,/g,'')) / (1+($tonrate/100));
						$nvats = $nnet * ($tonrate/100);
					}else{
						$nnet = ngross.replace(/,/g,'');
					}

					$xewtrate = this['cewtrate'];
					$tonewtrate = 0;
					if($xewtrate!=""){
						var array = $xewtrate.split(",");
						$.each(array,function(i){
							$tonewtrate = $tonewtrate + parseFloat(array[i]);
						});

						$newt = $nnet * ($tonewtrate/100);
					}
								
					addacct(this['cacctcodedr'],this['DRDesc'],$nnet,0,'',0);  
					if(this['cvatcode']!=""){
						addacct($("#hdnoutaxpay").val(),$("#hdnoutaxpaydsc").val(),$nvats,0,this['cvatcode'],this['cvatrate']);

						var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
						var lastRow = tbl.length-1;

						$xdisvatcode = this['cvatcode'];
									
						var xz1 = chkDef($("#hdnoutaxpay").val(),'PAYABLES');
						$("#selacctpaytyp"+lastRow).val(xz1);

						var xz = $("#hdntaxcodes").val();
						var ewtoptions = "";
						var ewtslctd = "";

						$.each(jQuery.parseJSON(xz), function() { 
							if(this['ctaxcode']==$xdisvatcode){
								isselctd = "selected";
								ewtslctd = this['nrate'];
							}else{
								isselctd = "";
							}
							ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
						});

						$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
						$("#txtewtrateothers"+lastRow).val(ewtslctd);

					}

					$cpayable = parseFloat(ngross) - parseFloat($newt);
					addacct(this['cacctcodecr'],this['CRDesc'],0,$cpayable,'',0);
					if(this['txtewtcode']!=""){
						addacct($("#hdnewtpay").val(),$("#hdnewtpaydsc").val(),0,$newt,this['cewtcode'],this['cewtrate']);

						var $xdisewtcode = this['cewtcode'];

						var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
						var lastRow = tbl.length-1;
						
						var xz = $("#hdnxtax").val();
						var ewtoptions = "";
						var ewtslctd = "";

						var ctg = 0;
						$.each(jQuery.parseJSON(xz), function() { 
							ctg++;

							if($xdisewtcode==this['ctaxcode']){
								isselctd = "selected";
								ewtslctd = this['nrate'];
							}else{
								isselctd = "";
								if(ctg==1){
									ewtslctd = this['nrate'];
								}
							}

							ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
						});

						ewtoptions = ewtoptions + "<option value='none' data-rate='0'>N/A</option>";

						$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
						$("#txtewtrateothers"+lastRow).val(ewtslctd);
					}
				}

			});


			$("#ModalProQty").modal("hide"); 
			$("#ProformaMod").modal("hide");

			compgross();
		});	
	
	});

	function aptypechange(valx){
		if(valx=="Purchases"){
			
			$("#btnqo").css("display", "inline");
			$("#btnpo").css("display", "none");
			//$("#btnlo").css("display", "none");
			$("#btnacc").css("display", "none");
			
			$("#lidet").attr("class", "active");
			$("#liacct").attr("class", "");
			
			$("#1Det").attr("class", "tab-pane active");
			$("#2Acct").attr("class", "tab-pane");
		
			$("#btnproforma").hide();
		}else if(valx=="PurchAdv"){

			$("#btnqo").css("display", "none");
			$("#btnpo").css("display", "inline");
			//$("#btnlo").css("display", "none");
			$("#btnacc").css("display", "none");
			
			$("#lidet").attr("class", "active");
			$("#liacct").attr("class", "");
			
			$("#1Det").attr("class", "tab-pane active");
			$("#2Acct").attr("class", "tab-pane");

			$("#btnproforma").hide();

		}else if(valx=="PettyCash"){

			$("#btnqo").css("display", "none");
			$("#btnpo").css("display", "none");
			//$("#btnlo").css("display", "none");
			$("#btnacc").css("display", "inline");

			$("#lidet").attr("class", "");
			$("#liacct").attr("class", "active");

			$("#1Det").attr("class", "tab-pane");
			$("#2Acct").attr("class", "tab-pane active");

			$("#btnproforma").show();

		}else if(valx=="Others"){

			$("#btnqo").css("display", "none");
			$("#btnpo").css("display", "none");
			//$("#btnlo").css("display", "none");
			$("#btnacc").css("display", "inline");

			$("#lidet").attr("class", "");
			$("#liacct").attr("class", "active");

			$("#1Det").attr("class", "tab-pane");
			$("#2Acct").attr("class", "tab-pane active");

			$("#btnproforma").show();

		}
	}

	//function addrrdet(rrno,amt,acctno,ctitle,crem,suppsi,nadvpaydue){
	function addrrdet(rrno,amt,netvat,vatval,vatcode,vatrate,ewtamt,ewtcode,ewtrate,acctno,suppsi,nadvpaydue,cmamt,acctname){

		//addrrdet(rrno,amt,vtamt,vttp,vtrt,ewtamt,ewttp,ewtrt,acttno,suppsi,advpaydue);   

		var paymeth = $("#selaptyp").val();
		var isread="";
		if(paymeth=="PurchAdv"){
			isread = "readonly";
		}

		var nncmx = cmamt;

		ndue = parseFloat(amt) - parseFloat(nncmx);

		if(document.getElementById("txtcustid").value!=""){
			
			$('#txtcust').attr('readonly', true);
				
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length;

			var a = "<td  width=\"130px\" style=\"padding:1px\"> <input type='text' name=\"txtrefno\" id=\"txtrefno"+lastRow+"\" class=\"txtrefsi form-control input-sm\" required value=\""+rrno+"\" readonly> <input type='hidden' name=\"txtrefacctno\" id=\"txtrefacctno"+lastRow+"\" value=\""+acctno+"\"> <input type='hidden' name=\"txtrefsi\" id=\"txtrefsi"+lastRow+"\" value=\""+suppsi+"\"> </td>";

			var b = "<td  width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtnamount\" id=\"txtnamount"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+amt+"\" style=\"text-align:right\" readonly></td>";

			var gcm = "<td  width=\"150px\" style=\"padding:1px\"><div class=\"input-group\"><input type='text' name=\"txtncm\" id=\"txtncm"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+nncmx+"\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-sm\" name=\"btnaddcm\" id=\"btnaddcm"+lastRow+"\" type=\"button\" onclick=\"addCM('"+rrno+"','txtncm"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div></td>";  

			//
				/*var gdisc = "<td  width=\"150px\" style=\"padding:1px\"><div class=\"input-group\"><input type='text' name=\"txtndiscs\" id=\"txtndiscs"+lastRow+"\" class=\"numeric form-control input-sm\" value=\"0.00\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-sm\" type=\"button\" name=\"btnadddc\" id=\"btnadddc"+lastRow+"\" onclick=\"addDISCS('"+rrno+"','txtndiscs"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div></td>"; 


					var xz = $("#hdntaxcodes").val();
					taxoptions = "";
					$.each(jQuery.parseJSON(xz), function() { 
						if(vatcode==this['ctaxcode']){
							isselctd = "selected";
						}else{
							isselctd = "";
						}
						taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
					});*/

				//VAT
				/*var c = "<td  width=\"100px\" style=\"padding:1px\"><select class='form-control input-sm' name=\"txtnvatcode\" id=\"txtnvatcode"+lastRow+"\"> " + taxoptions + " </select> </td>"; 
				var c1 = "<td  width=\"50px\" style=\"padding:1px\"><input type='text' class=\"numeric form-control input-sm text-right\" name=\"txtnvatrate\" id=\"txtnvatrate"+lastRow+"\" value=\""+vatrate+"\" readonly></td>"; 
				var c2 = "<td  width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtnvatval\" id=\"txtnvatval"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+vatval+"\" style=\"text-align:right\" readonly></td>"; 

				//NETVAT
				var d = "<td  width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtvatnet\" id=\"txtvatnet"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+netvat+"\" style=\"text-align:right\" readonly></td>"; */

				//EWT 
				//var e = "<td width=\"100px\" style=\"padding:1px\"><input type='text' name=\"txtewtcode\" id=\"txtewtcode"+lastRow+"\" class=\"form-control input-sm\" value=\""+ewtcode+"\" autocomplete=\"off\"></td>";

				//EWT 
				/*var xz = $("#hdnxtax").val();
				ewtoptions = "";

				$.each(jQuery.parseJSON(xz), function() {  
					if(ewtcode==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' data-base='"+this['cbase']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
				});

				//tditmewts = "<td width=\"150\" nowrap>  </td>";
				
				var e = "<td width=\"100px\" style=\"padding:1px\"><select class='form-control input-xs' name=\"txtewtcode\" id=\"txtewtcode"+lastRow+"\" style=\"width: 100%\"> <option value=\"\" data-rate='0' data-base='NET'>None</option>" + ewtoptions + "</select></td>";

				var f = "<td width=\"50px\" style=\"padding:1px\"><input type='text' name=\"txtewtrate\" id=\"txtewtrate"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+ewtrate+"\" style=\"text-align:right\" readonly></td>";
				var g = "<td width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtewtamt\" id=\"txtewtamt"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+ewtamt+"\" style=\"text-align:right\" readonly></td>";*/

				/*
				var h = "<td  width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtpayment\" id=\"txtpayment"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+npaymnt+"\" style=\"text-align:right\" readonly></td>";
				*/
			//

			var i = "<td  width=\"150px\" style=\"padding:1px\"><input type='text' name=\"txtDue\" id=\"txtDue"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+ndue.toFixed(4)+"\" style=\"text-align:right\" readonly></td>";
			
			var j = "<td nowrap><input type='text' value='"+acctno+"' class='form-control input-sm' name=\"txtrefacctno\" id='txtrefacctno"+lastRow+"' readonly> </td>";

			var k = "<td nowrap style=\"padding-left:2px\"><input type='text' value='"+acctname+"' class='cacctdesc form-control input-sm' name=\"txtacctname\" id='txtacctname"+lastRow+"'></td>";

			/*
			var j = "<td style=\"padding:1px\"><input type='text' name=\"txtnapplied\" id=\"txtnapplied"+lastRow+"\" class=\"numeric form-control input-sm text-right\" value=\""+nadvpaydue+"\" onkeyup=\"compgross1();\"  autocomplete=\"off\" "+isread+"></td>";
			*/
			
			var l = "<td width=\"50px\" style=\"padding:1px\"><input class='btn btn-danger btn-xs' type='button' id='row_"+rrno+lastRow+"_delete' class='delete' value='delete' onClick=\"deleteRow1(this);\"/></td>";

			$('#MyTable > tbody:last-child').append('<tr>'+a + b + gcm + i + j + k + l + '</tr>');
			
			compgross1();	

			$("input.numeric").autoNumeric('init',{mDec:2});
		}
		else{
			alert("Paid To Required!");
		}
	}

	function compgross1(){

		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var cnt = tbl.length;
	
		cnt = cnt - 1;

		var xgrs = 0;
		
		for (i = 1; i <= cnt; i++) {
			xgrs = xgrs + parseFloat($('#txtDue'+i).val().replace(/,/g,''));
		}
		
		$("#txtnGross").val(xgrs);

		$("#txtnGross").autoNumeric('destroy');
		$("#txtnGross").autoNumeric('init',{mDec:2});
	}

	/*
	function recomlines(){
		 $("#MyTable > tbody > tr").each(function(index) {	
			  
			//if(index>0){ parseFloat(newts) +   parseFloat(npaysds)

				var nmounts = $(this).find('input[name="txtnamount"]').val().replace(/,/g,''); 
				var ncms = $(this).find('input[name="txtncm"]').val().replace(/,/g,''); 
				var ndiscs = $(this).find('input[name="txtndiscs"]').val().replace(/,/g,''); 
				
				var xcz = parseFloat(ncms) + parseFloat(ndiscs);
				var dmt = parseFloat(nmounts) - xcz;

				var vatrate = $(this).find('input[name="txtnvatrate"]').val().replace(/,/g,''); 

				var nnet = parseFloat(dmt) / parseFloat(1 + (parseInt(vatrate)/100));
				$(this).find('input[name="txtvatnet"]').val(nnet);
				$(this).find('input[name="txtvatnet"]').autoNumeric('destroy');
				$(this).find('input[name="txtvatnet"]').autoNumeric('init',{mDec:2});

				var vatz = nnet * (parseInt(vatrate)/100);    
				$(this).find('input[name="txtnvatval"]').val(vatz);
				$(this).find('input[name="txtnvatval"]').autoNumeric('destroy');
				$(this).find('input[name="txtnvatval"]').autoNumeric('init',{mDec:2});

				var newts = $(this).find('input[name="txtewtamt"]').val().replace(/,/g,''); 
				
				var remain = dmt - newts;
				$(this).find('input[name="txtDue"]').val(remain);
				$(this).find('input[name="txtDue"]').autoNumeric('destroy');
				$(this).find('input[name="txtDue"]').autoNumeric('init',{mDec:2});

			//}

		  });

	}
	*/

	function openinv(typ,suppcust,tblid,hdrid,url,msg,modz){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid "+suppcust+"!");
		}
		else{
			
			$("#txtcustid").attr("readonly", true);
			$("#txtcust").attr("readonly", true);

			//clear table body if may laman
			$('#'+tblid+' tbody').empty(); 
			

			var y;
			var salesnos = "";
			var cnt = 0;
			var rc = $('#MyTable tr').length;

			for(y=1;y<=rc-1;y++){ 
				cnt = cnt + 1;
				if(cnt>1){
					salesnos = salesnos + ",";
				}
			// alert("value: " + document.getElementById("txtrefno"+y).value);
				salesnos = salesnos + $('#txtrefno'+y).val();
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#'+hdrid+'').html(""+msg+" List: " + $('#txtcust').val())

			var xstat = "YES";
				
				
				if(modz=="YES"){
					var modcust = "";
				}
				else{
					var modcust = $('#txtcustid').val();
				}

				//alert(''+url+'.php?x='+x+'&cust='+modcust+'&y='+salesnos+'&typ='+$('#selaptyp').val()+'&curr='+$('#selbasecurr').val());

				$.ajax({
					url: ''+url+'.php',
					data: { x:x, cust:modcust, y:salesnos, typ:$('#selaptyp').val(), curr:$('#selbasecurr').val() },
					dataType: 'json',
					async:false,
					method: 'post',
					success: function (data) {

						console.log(data);
						$.each(data,function(index,item){
							
							if(item.crrno=="NONE"){
								$("#txtcustid").attr("readonly", false);
								$("#txtcust").attr("readonly", false);
							}
							else{
								var gross = parseFloat(item.ngross);
								gross = gross.toLocaleString('en-US', {minimumFractionDigits: 1, maximumFractionDigits: 1});

								$("<tr>").append(
									$("<td>").html("<input type='checkbox' name=\"chkSales[]\" value='"+item.crrno+"' data-gross = '"+item.ngross+"' data-basegross = '"+item.nbasegross+"' data-grossbef = '"+item.ngrossbefore+"' data-nvat = '"+item.nvat+"' data-nnet = '"+item.nnet+"' data-newt = '"+item.newt+"' data-acct = '"+item.cacctno+"' data-ncm = '"+item.ncm+"' data-refsi = '"+item.crefsi+"' data-cacctile='"+item.ctitle+"'>"),
									$("<td>").text(item.crrno),
									$("<td>").text(item.crefsi),
									$("<td>").text(item.ddate),
									$("<td style='text-align: right'>").text(gross)
								).appendTo("#"+tblid+" tbody");
							}

						});
						
						if(xstat=="YES" && modz!="YES" && modz!="NO"){
							$('#'+modz+'').modal('show');
						}
					},
					error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
				});
			
		}
	}

	function InsertSI(){	
		var totGross = 0;
		var modnme = "";
		var vttp="";
		var vtrt=""; 
		var suppsi = "";
		cnt = 0;

		$("input[name='chkSales[]']:checked").each( function () {

			var rrno = $(this).val();

			var amt=$(this).data("basegross");
			vtamt=$(this).data("nvat");
			netamt=$(this).data("nnet");
			ewtamt=$(this).data("newt");
			cmamt=$(this).data("ncm");
			suppsi =$(this).data("refsi"); 
			acttno = $(this).data("acct");
			acttname = $(this).data("cacctile");

			modnme = "mySIModal";

			//rrno,amt,netvat,vatval,vatcode,vatrate,ewtamt,ewtcode,ewtrate,acctno,suppsi,nadvpaydue,cmamt
			addrrdet(rrno,amt,netamt,vtamt,"","",ewtamt,"","",acttno,suppsi,"",cmamt,acttname);		 
			//totGross = parseFloat(totGross) + parseFloat(amt) ;

		});

		$('#'+modnme+'').modal('hide');
		$('#'+modnme+'').on('hidden.bs.modal', function (e) {

			//$("#txtnGross").val(totGross);
			
			//if($('#selaptyp').val()=="Purchases"){
				//GetAccts();
			//}
				
		});
		
		//alert(modnme);
		//$('#'+modnme+'').modal('hide');
		

	}

	function chkDef(acctid,codez){
		var ydsc = "";
		$.ajax({
			type:'post',
			url:'th_chkDef.php',
			async:false,
			data: 'c_id='+ acctid + "&codez=" + codez,                 
			success: function(value){
				ydsc = value.trim();
			}
		});
		
		return ydsc;
	}

	function addacct(nacct="",ntitle="",ndr=0,ncr=0,nvcode="",nvrate=0){

		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length;

		var a=document.getElementById('MyTable2').insertRow(-1);

		var u=a.insertCell(0);
			u.style.padding = "1px";
			u.style.width = "100px";
		var v=a.insertCell(1);
			v.style.padding = "1px";
			v.style.width = "400px";
		var w=a.insertCell(2);
			w.style.padding = "1px";
			w.style.width = "150px";
		var x=a.insertCell(3);
			x.style.width = "150px";
			x.style.padding = "1px";
		//var y=a.insertCell(4);
		//	y.style.padding = "1px";
		var z=a.insertCell(4);
			z.style.padding = "1px";
		var t=a.insertCell(5);
			t.style.padding = "1px";
			t.style.width = "100px";
		var b=a.insertCell(6);
			b.style.width = "50px";
			b.style.padding = "1px";

		u.innerHTML = "<input type='text' name=\"txtacctno"+lastRow+"\" id=\"txtacctno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Enter Acct Code...\" style=\"text-transform:uppercase\" autocomplete=\"off\" value=\""+nacct+"\">";

		v.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Acct Desc...\" style=\"text-transform:uppercase\" autocomplete=\"off\" value=\""+ntitle+"\">";

		w.innerHTML = "<input type='text' name=\"txtdebit"+lastRow+"\" id=\"txtdebit"+lastRow+"\" class=\"numeric form-control input-sm\" style=\"text-align:right\" value=\""+ndr+"\" onkeyup=\"compgross();\" required autocomplete=\"off\">";

		x.innerHTML = "<input type='text' name=\"txtcredit"+lastRow+"\" id=\"txtcredit"+lastRow+"\" class=\"numeric form-control input-sm\" style=\"text-align:right\" value=\""+ncr+"\" onkeyup=\"compgross();\" required autocomplete=\"off\">";
		//y.innerHTML = "<input type='text' name=\"txtsubs"+lastRow+"\" id=\"txtsubs"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Name...\" onkeyup=\"searchSUBS(this.name);\"> <input type='hidden' name=\"txtsubsid"+lastRow+"\" id=\"txtsubsid"+lastRow+"\" autocomplete=\"off\">";
		z.innerHTML = "<input type='text' name=\"txtacctrem"+lastRow+"\" id=\"txtacctrem"+lastRow+"\" class=\"form-control input-sm\" autocomplete=\"off\">";


		//tditmewts = "<td width=\"150\" nowrap>  </td>";

		t.innerHTML = "<select class='form-control input-sm' name=\"txtewtcodeothers"+lastRow+"\" id=\"txtewtcodeothers"+lastRow+"\" style=\"width: 100%\"><option value=\"\">&nbsp;</option> </select> <input type='hidden' name=\"txtewtrateothers"+lastRow+"\" id=\"txtewtrateothers"+lastRow+"\" value=\""+nvrate+"\"  autocomplete=\"off\">";

		//t.innerHTML = "<input type='text' name=\"txtewtcodeothers"+lastRow+"\" id=\"txtewtcodeothers"+lastRow+"\" class=\"form-control input-sm\" value=\"\"  autocomplete=\"off\"> <input type='hidden' name=\"txtewtrateothers"+lastRow+"\" id=\"txtewtrateothers"+lastRow+"\" value=\"0\"  autocomplete=\"off\">";
			
		//t.innerHTML = "<select name=\"selacctpaytyp"+lastRow+"\" id=\"selacctpaytyp"+lastRow+"\" class=\"form-control input-sm selectpicker\"><option value=\"Payables\">Payables</option><option value=\"Others\">Others</option></select>";
	
		b.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row2_"+lastRow+"_delete' value='delete' onClick=\"deleteRow2(this);\"/>";

		$("#txtacctitle"+lastRow).focus(); 

		$("input.numeric").autoNumeric('init',{mDec:2});

		$("input.numeric").on("focus", function () {
			$(this).select();
		});
								
		$("#txtacctno"+lastRow).on("keyup", function(event) {
			if(event.keyCode == 13){
				
				var dInput = this.value;

				$.ajax({
					type:'post',
					url:'../getaccountid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
						//alert(value);
						if(value.trim()!=""){
							$("#txtacctitle"+lastRow).val(value.trim());
							
							var xz = chkDef(dInput,'PAYABLES');
							$("#selacctpaytyp"+lastRow).val(xz);

							if(dInput==$("#hdnewtpay").val()){

								var xz = $("#hdnxtax").val();
								var ewtoptions = "";
								var ewtslctd = "";

								var ctg = 0;
								$.each(jQuery.parseJSON(xz), function() { 
									ctg++;
									if($("#hdncustewt").val()==this['ctaxcode']){
										isselctd = "selected";
										ewtslctd = this['nrate'];
									}else{
										isselctd = "";
										if(ctg==1){
											ewtslctd = this['nrate'];
										}
									}

									ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
								});

								ewtoptions = ewtoptions + "<option value='none' data-rate='0'>N/A</option>";

								$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
								$("#txtewtrateothers"+lastRow).val(ewtslctd);
							}

							if(dInput==$("#hdnoutaxpay").val()){
								var xz = $("#hdntaxcodes").val();
								var ewtoptions = "";
								var ewtslctd = "";
								var $cnt = 0;
								$.each(jQuery.parseJSON(xz), function() { 
									$cnt++;
									if($cnt==1){
										isselctd = "selected";
										ewtslctd = this['nrate'];
									}else{
										isselctd = "";
									}
									ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
								});

								$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
								$("#txtewtrateothers"+lastRow).val(ewtslctd);
							}
						}
					}
				});
				
			}
		});

		$("#txtacctitle"+lastRow).typeahead({

			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../th_accounts.php",
					dataType: "json",
					data: {
						query: $("#txtacctitle"+lastRow).val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$("#txtacctitle"+lastRow).val(item.name).change(); 
				$("#txtacctno"+lastRow).val(item.id);
				$("#txtdebit"+lastRow).focus();
				
				var xz = chkDef(item.id,'PAYABLES');
				$("#selacctpaytyp"+lastRow).val(xz);

				if(item.id==$("#hdnewtpay").val()){

					var xz = $("#hdnxtax").val();
					var ewtoptions = "";
					var ewtslctd = "";

					var ctg = 0;
					$.each(jQuery.parseJSON(xz), function() { 
						if($("#hdncustewt").val()==this['ctaxcode']){
							isselctd = "selected";
							ewtslctd = this['nrate'];
						}else{
							isselctd = "";
							if(ctg==1){
								ewtslctd = this['nrate'];
							}
						}
						ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
					});

					ewtoptions = ewtoptions + "<option value='none' data-rate='0'>N/A</option>";

					$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
					$("#txtewtrateothers"+lastRow).val(ewtslctd);

					//$("#txtewtcodeothers"+lastRow).removeAttr('disabled');
					//$("#txtewtcodeothers"+lastRow).val($("#hdncustewt").val()).trigger('change');
					//$("#txtewtrateothers"+lastRow).val($("#hdncustewtrate").val());
				}

				if(item.id==$("#hdnoutaxpay").val()){
					var xz = $("#hdntaxcodes").val();
					var ewtoptions = "";
					var ewtslctd = "";
					var $cnt = 0;
					$.each(jQuery.parseJSON(xz), function() { 
						if($("#hdncustewt").val()==this['ctaxcode']){
							isselctd = "selected";
							ewtslctd = this['nrate'];
						}else{
							isselctd = "";
						}
						ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
					});

					$("#txtewtcodeothers"+lastRow).select2('destroy').html(ewtoptions).select2();
					$("#txtewtrateothers"+lastRow).val(ewtslctd);
				}

			}
		});

		$("#txtewtcodeothers"+lastRow).select2();
		$("#txtewtcodeothers"+lastRow).on("select2:select", function(event) {
			$("#txtewtrateothers"+lastRow).val($(this).select2().find(":selected").data("rate"));
		});

	}

	function compgross(){
			var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
			var cnt = tbl.length;
		
			cnt = cnt - 1;

			var xdeb = 0;
			var xcrd = 0;
			
			for (i = 1; i <= cnt; i++) {
				xdeb = xdeb + parseFloat($('#txtdebit'+i).val().replace(/,/g,''));
				xcrd = xcrd + parseFloat($('#txtcredit'+i).val().replace(/,/g,''));
			}

			var totdebit = xdeb.toFixed(2);
			var totcredit = xcrd.toFixed(2);
			
			if(totdebit==totcredit){
				$("#txtnGross").val(totdebit);
				$("#txtnGross").autoNumeric('destroy');
				$("#txtnGross").autoNumeric('init',{mDec:2});
				//document.getElementById("grosmsg").innerHTML = "";
			}
			else{
				$("#txtnGross").val('(DR: '+totdebit+', CR: '+totcredit+')');
				//document.getElementById("txtnGross").value = 'UNBALANCED TRANSACTION';
				//document.getElementById("grosmsg").innerHTML = "UNBALANCED TRANSACTION";
			}

	}

	function deleteRow1(r){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.parentNode.rowIndex;
		
		document.getElementById('MyTable').deleteRow(i);

		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;
		
		
		for (z=i+1; z<=lastRow; z++){	

			var temprefno = document.getElementById('txtrefno' + z);
			var refnoval = temprefno.value;
			
			var tempsuppSI = document.getElementById('txtrefsi' + z); 
			var tempacctNo = document.getElementById('txtrefacctno' + z);
			var tempacctit = document.getElementById('txtacctname' + z);  

			var tempamnt = document.getElementById('txtnamount' + z);
			//var tempvatcode = document.getElementById('txtnvatcode' + z);
			//var tempvatrate = document.getElementById('txtnvatrate' + z);
			//var tempvatvals = document.getElementById('txtnvatval' + z);
			//var tempvatnets = document.getElementById('txtvatnet' + z);
			//var tempewtcode = document.getElementById('txtewtcode' + z);
			//var tempewtrate = document.getElementById('txtewtrate' + z);
			//var tempewtamts = document.getElementById('txtewtamt' + z);
			var tempcmdms = document.getElementById('txtncm' + z);
			//var tempdiscs = document.getElementById('txtndiscs' + z);  
			//var temppaymnts = document.getElementById('txtpayment' + z);
			var tempdueamts = document.getElementById('txtDue' + z);
			//var tempappamts = document.getElementById('txtnapplied' + z); 			
			var tempbtnaddcm = document.getElementById('btnaddcm' + z); 
			//var tempbtnaddsc = document.getElementById('btnadddc' + z); 
			
			var tempbtn= document.getElementById('row_' + refnoval + z + '_delete');
			
			var x = z-1;
			
			$('#btnaddcm'+z).click(function() {
				addCM(refnoval,"txtncm"+x); 
			});
																	
			temprefno.id = "txtrefno" + x;
			//temprefno.name = "txtrefno" + x;
			tempsuppSI.id = "txtrefsi" + x;
			//tempsuppSI.name = "txtrefsi" + x;			
			tempacctNo.id = "txtrefacctno" + x;
			tempacctit.id = "txtacctname" + x;

			tempamnt.id = "txtnamount" + x;
			//tempamnt.name = "txtnamount" + x;
			//tempvatcode.id = "txtnvatcode" + x;
			//tempvatcode.name = "txtnvatcode" + x;
			//tempvatrate.id = "txtnvatrate" + x;
			//tempvatrate.name = "txtnvatrate" + x;
			//tempvatvals.id = "txtnvatval" + x;
			//tempvatvals.name = "txtnvatval" + x;			
			//tempvatnets.id = "txtvatnet" + x;
			//tempvatnets.name = "txtvatnet" + x;
			//tempewtcode.id = "txtewtcode" + x;
			//tempewtcode.name = "txtewtcode" + x;
			//tempewtrate.id = "txtewtrate" + x;
			//tempewtrate.name = "txtewtrate" + x;
			//tempewtamts.id = "txtewtamt" + x;
			//tempewtamts.name = "txtewtamt" + x;
			tempcmdms.id = "txtncm" + x;
			//tempdiscs.id = "txtndiscs" + x;
			//temppaymnts.id = "txtpayment" + x;
			//temppaymnts.name = "txtpayment" + x;
			tempdueamts.id = "txtDue" + x;
			//tempdueamts.name = "txtDue" + x;
			//tempappamts.id = "txtnapplied" + x;
			//tempappamts.name = "txtnapplied" + x;
			tempbtn.id = "row_" + refnoval+ x + "_delete";
			//tempbtn.name = "row_" + x + "_delete";

			tempbtnaddcm.id = "btnaddcm" + x;
			//tempbtnaddsc.id = "btnadddc" + x;
			
			
			
		
		}
		compgross1();
		
		if(lastRow==1){
			document.getElementById('txtcust').readOnly=false;
		} 
		
		//alert(cRRNo);
		//delAcctDet(cRRNo);
		
	}

	function deleteRow2(r){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.parentNode.rowIndex;
		
		document.getElementById('MyTable2').deleteRow(i);

		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;
		
			for (z=i+1; z<=lastRow; z++){	
				var tempacctno = document.getElementById('txtacctno' + z);
				var temptitle = document.getElementById('txtacctitle' + z);
				var tempdbet = document.getElementById('txtdebit' + z);
				var tempcdet = document.getElementById('txtcredit' + z);
				var tempracrem = document.getElementById('txtacctrem' + z);
				var tempptyps = document.getElementById('selacctpaytyp' + z);
				var tempotewt = document.getElementById('txtewtcodeothers' + z);
				var tempotewtrate = document.getElementById('txtewtrateothers' + z);
				
				var tempbtn= document.getElementById('row2_' + z + '_delete');
				
				var x = z-1;
				tempacctno.id = "txtacctno" + x;
				tempacctno.name = "txtacctno" + x;
				temptitle.id = "txtacctitle" + x;
				temptitle.name = "txtacctitle" + x;
				tempdbet.id = "txtdebit" + x;
				tempdbet.name = "txtdebit" + x;
				tempcdet.id = "txtcredit" + x;
				tempcdet.name = "txtcredit" + x;
				tempracrem.id = "txtacctrem" + x;
				tempracrem.name = "txtacctrem" + x;
				tempptyps.id = "selacctpaytyp" + x;
				tempptyps.name = "selacctpaytyp" + x;
				
				tempotewt.id = "txtewtcodeothers" + x;
				tempotewt.name = "txtewtcodeothers" + x;

				tempotewtrate.id = "txtewtrateothers" + x;
				tempotewtrate.name = "txtewtrateothers" + x;

				tempbtn.id = "row2_" + x + "_delete";
				tempbtn.name = "row2_" + x + "_delete";						
			}
			
			compgross();
	}

	function disabled(){

		$("#frmpos :input").attr("disabled", true);
		
		
		$("#txtctranno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);

		$(".kv-file-zoom").attr("disabled", false);

	}

	function enabled(){
		var msgsx = "";
		
		if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
			if(document.getElementById("hdnposted").value==1){
				if(document.getElementById("hdnvoid").value==1){
				var msgsx = "VOIDED";
				}else{
					var msgsx = "POSTED";
				}
			}
						
			if(document.getElementById("hdncancel").value==1){
				var msgsx = "CANCELLED";
			}
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{

				$("#frmpos :input").attr("disabled", false);
			
				
				$("#txtctranno").attr("readonly", true);
				$("#txtctranno").val($("#hdnorigNo").val());
				
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);	

				aptypechange($("#selaptyp").val());
				
		}

	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "APV_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function printchk(x){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{
				var url = "APV_print.php?x="+x;
				
				$("#myprintframe").attr('src',url);


			$("#PrintModal").modal('show');
			

		}
	}
		
	function addCM(xytran,txtbx){
		var tbl = document.getElementById('MyTableCMx').getElementsByTagName('tr');
		var lastRow2 = tbl.length-1;

		if(lastRow2>=1){
			$("#MyTableCMx > tbody > tr").each(function() {	
			
				var citmno = $(this).find('input[type="hidden"][name="txtcmrr"]').val();
				//alert(citmno+"!="+itmcde);
				if(citmno!=xytran){
					
					$(this).find('input[name="txtapcmdm"]').attr("readonly", true);
					//$(this).find('input[name="txtcmamt"]').attr("readonly", true);
					$(this).find('input[name="txtremz"]').attr("readonly", true);   
					$(this).find('input[name="txtaccapcm"]').attr("readonly", true);
					$(this).find('input[name="txtaccapcmdec"]').attr("readonly", true);
					
					$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled",true);
					
				}
				else{
					if($(this).find('input[type="hidden"][name="txtcmithref"]').val()==0){ 
						$(this).find('input[name="txtapcmdm"]').attr("readonly", false);
						//$(this).find('input[name="txtapamt"]').attr("readonly", false);
						$(this).find('input[name="txtremz"]').attr("readonly", false);
						$(this).find('input[name="txtaccapcm"]').attr("readonly", false);
						$(this).find('input[name="txtaccapcmdec"]').attr("readonly", false);
						$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs");
						$(this).find('input[type="button"][name="delinfo"]').prop("disabled",false);
					}
				}
				
			});
		}			
			
		$('#txthdnCMinfo').val(xytran); 
		$("#txthdnCMtxtbx").val(txtbx);
		$('#MyDetModal').modal('show');
	}
		
	function addCMReturn(xytran,txtbx){
		$cnt = 0;
		$.ajax({
			url: 'th_getrefreturns.php',
			data: 'rrid='+xytran,
			dataType: 'json',
			async:false,
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){
					$cnt = $cnt + 1;
					var tbl = document.getElementById('MyTableCMx').getElementsByTagName('tr');
					var lastRow = tbl.length;

					var tdapcm = "<td><input type='hidden' name='txtcmrr' id='txtcmrr"+lastRow+"' value='"+item.crefrr+"'><input type='hidden' name='txtcmithref' id='txtcmithref"+lastRow+"' value='1'><input type='text' name='txtapcmdm' id='txtapcmdm"+lastRow+"' class='form-control input-xs' value='"+item.ctranno+"'></td>";
					var tddate = "<td><input type='text' name='txtapdte' id='txtapdte"+lastRow+"' class='form-control input-xs' readonly value='"+item.ddate+"'></td>"
					var tdamt = "<td><input type='text' name='txtapamt' id='txtapamt"+lastRow+"' class='form-control input-xs text-right' readonly value='"+item.ngross+"'></td>";
					var tdrem = "<td><input type='text' name='txtremz' id='txtremz"+lastRow+"' class='form-control input-xs' value='Purchase Return'></td>";
					var tdacc = "<td><input type='text' name='txtaccapcm' id='txtaccapcm"+lastRow+"' class='form-control input-xs' value='"+item.cacctno+"'></td>";
					var tdaccdc = "<td><input type='text' name='txtaccapcmdec' id='txtaccapcmdec"+lastRow+"' class='form-control input-xs' value='"+item.cacctdesc+"'></td>";
					var tdels = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + item.ctranno + lastRow + "' value='delete' /></td>";

					$('#MyTableCMx > tbody:last-child').append('<tr>'+tdapcm + tddate + tdamt + tdrem + tdacc + tdaccdc + tdels + '</tr>');

						$("#delinfo"+item.ctranno+lastRow).on('click', function() { 
							$(this).closest('tr').remove();
						});
					
					$("#txtaccapcm"+lastRow).on("keyup", function(event) {
						if(event.keyCode == 13){

							var dInput = this.value;

								$.ajax({
								type:'post',
								url:'../getaccountid.php',
								data: 'c_id='+ $(this).val(),                 
								success: function(value){
									//alert(value);
									if(value.trim()!=""){
										$("#txtaccapcmdec"+lastRow).val(value.trim());
									}
								}
								});

						}
					});

					$("#txtaccapcmdec"+lastRow).typeahead({

						items: 10,
						source: function(request, response) {
							$.ajax({
								url: "../th_accounts.php",
								dataType: "json",
								data: {
									query: $("#txtaccapcmdec"+lastRow).val()
								},
								success: function (data) {
									response(data);
								}
							});
						},
						autoSelect: true,
						displayText: function (item) {
							return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + '</small></div>';
						},
						highlighter: Object,
						afterSelect: function(item) { 
							$("#txtaccapcmdec"+lastRow).val(item.name).change(); 
							$("#txtaccapcm"+lastRow).val(item.id);
						}
					});
					
				});

			}
		});
		
		if($cnt>0){
			recomlines();
		}
	}
		
	function chkCloseInfo(){
		var isInfo = "TRUE";
		
		$("#MyTableCMx > tbody > tr").each(function(index) {	
				
				var citmfld1 = $(this).find('input[name="txtapcmdm"]').val();
				var citmfld2 = $(this).find('input[name="txtremz"]').val();
				var citmfld3 = $(this).find('input[name="txtaccapcm"]').val();
				var citmfld4 = $(this).find('input[name="txtaccapcmdec"]').val();

				if(citmfld1=="" || citmfld2=="" || citmfld3=="" || citmfld4==""){
					isInfo = "FALSE";
				}
					
		});

		
		if(isInfo == "TRUE"){
			//recompute details
			var tot = 0;
			var xinfo = $("#txthdnCMinfo").val();
			var dsc = $("#txthdnCMtxtbx").val();

			$("#MyTableCMx > tbody > tr").each(function(index) {	
				var x = $(this).find('input[name="txtapamt"]').val().replace(/,/g,'');
				var y = $(this).find('input[type="hidden"][name="txtcmrr"]').val();

					if(xinfo==y){
						tot = tot + parseFloat(x);
					}	
				
			});
			
			if(parseFloat(tot)>0){
				$("#"+dsc).val(tot);

				$("#"+dsc).autoNumeric('destroy');
				$("#"+dsc).autoNumeric('init',{mDec:2});
			}
			
												
			$('#MyDetModal').modal('hide');	
		}
		else{
			alert("Incomplete info values!");
		}
	}

	function chkform(){

		var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRowRR = tbl1.length-1;

		var tbl2 = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRowAcc = tbl2.length-1;
			
		var tbl3 = document.getElementById('MyDetModal').getElementsByTagName('tr');
		var lastRow2 = tbl3.length-1;
					
		var isOK = "YES";
		if(lastRowRR==0 && lastRowAcc==0){  
			alert("Transaction has NO Details!");
			return false;
		}
		else{
			
			if(document.getElementById("txtnGross").value==0 || document.getElementById("txtnGross").value==""){
				//alert();
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("No amount detected. Please check your details!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK=="NO";
				return false;
			}

			if($.isNumeric($("#txtnGross").val().replace(/,/g,''))==false){
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Unbalanced Transaction!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK=="NO";
				return false;
			}

			//chkewtcode  
			for($ix=1;$ix<=lastRowAcc;$ix++){

				$chkifewt = $("#txtacctno" + $ix).val();

				if($chkifewt==$("#hdnewtpay").val()){
					if($("#txtewtcodeothers" + $ix).val()==""){
						$("#AlertMsg").html("");
									
						$("#AlertMsg").html("EWT Code required!");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

						isOK=="NO";
						return false;
					}
				}
			};

		
			
			if(isOK=="YES"){
				document.getElementById("hdnRRCnt").value = lastRowRR;  
				document.getElementById("hdnACCCnt").value = lastRowAcc; 
				document.getElementById("hdnrowcnt2").value = lastRow2;			
				
				//rename input name
				var tx = 0;
				$("#MyTable > tbody > tr").each(function(index) {
					tx = index + 1;
					$(this).find('input[name="txtrefno"]').attr("name","txtrefno"+tx);
					$(this).find('input[type=hidden][name="txtrefsi"]').attr("name","txtrefsi"+tx);			
					$(this).find('input[name="txtnamount"]').attr("name","txtnamount" + tx);
					$(this).find('input[name="txtncm"]').attr("name","txtncm" + tx);
					//$(this).find('input[name="txtndiscs"]').attr("name","txtndiscs" + tx);
					//$(this).find('select[name="txtnvatcode"]').attr("name","txtnvatcode" + tx);
					//$(this).find('input[name="txtnvatrate"]').attr("name","txtnvatrate" + tx);
					//$(this).find('input[name="txtnvatval"]').attr("name","txtnvatval" + tx);
					//$(this).find('input[name="txtvatnet"]').attr("name","txtvatnet" + tx);
					//$(this).find('select[name="txtewtcode"]').attr("name","txtewtcode" + tx);
					//$(this).find('input[name="txtewtrate"]').attr("name","txtewtrate" + tx);
					//$(this).find('input[name="txtewtamt"]').attr("name","txtewtamt" + tx);  
					//$(this).find('input[name="txtpayment"]').attr("name","txtpayment" +tx);
					$(this).find('input[name="txtDue"]').attr("name","txtDue" + tx);
					$(this).find('input[name="txtrefacctno"]').attr("name","txtrefacctno"+tx);
					$(this).find('input[name="txtacctname"]').attr("name","txtacctname"+tx);
					//$(this).find('input[name="txtnapplied"]').attr("name","txtnapplied" + tx); 

				});
				
				var tx2 = 0;
				$("#MyTableCMx > tbody > tr").each(function(index) {   
					tx2 = index;
					$(this).find('input[name="txtcmrr"]').attr("name","txtcmrr"+tx2);
					$(this).find('input[name="txtcmithref"]').attr("name","txtcmithref"+tx2);
					$(this).find('input[name="txtapcmdm"]').attr("name","txtapcmdm" + index);
					$(this).find('input[name="txtapdte"]').attr("name","txtapdte" + tx2);
					$(this).find('input[name="txtapamt"]').attr("name","txtapamt" + tx2);
					$(this).find('input[name="txtremz"]').attr("name","txtremz" + tx2);
					$(this).find('input[name="txtaccapcm"]').attr("name","txtaccapcm" + tx2);
					$(this).find('input[name="txtaccapcmdec"]').attr("name","txtaccapcmdec" + tx2);
				});
				
				$("#frmpos").submit();
			
			}

		}

	}

	function showproqty(id,gross){
		
		$("#txtproid").val(id);

		if(parseFloat(gross)==0){
			$('#txtprogross').val(gross);
			$("#ModalProQty").modal("show");

			$('#txtprogross').focus();
			$('#txtprogross').select();	

		}else{
			$('#txtprogross').val(gross);
			$('#btnInsPro').click();	
		}		
		
	}

</script>