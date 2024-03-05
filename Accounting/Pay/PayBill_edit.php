<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PayBill.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$ccvno = $_REQUEST['txtctranno'];
	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'PayBill_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}
		
	$arrnoslist = array();
	$sqlempsec = mysqli_query($con,"select ifnull(ccheckno,'') as ccheckno, ifnull(cpayrefno,'') as cpayrefno,ctranno from paybill where compcode='$company' and lcancelled=0 and ctranno <> '$ccvno'");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){

		if($row0['ccheckno']!==""){
			$arrnoslist[] = array('noid' => $row0['ccheckno'], 'ctranno' => $row0['ctranno']);
		}

		if($row0['cpayrefno']!==""){
			$arrnoslist[] = array('noid' => $row0['cpayrefno'], 'ctranno' => $row0['ctranno']);
		}
		
	}

	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/PV/'.$company.'_'.$ccvno.'/')) {
		$allfiles = scandir('../../Components/assets/PV/'.$company.'_'.$ccvno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		//echo "NO FILES";
	}

$_SESSION['myxtoken'] = gen_token();

	$nvalue = "";
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_APV'"); 										
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
		$nvalue = $all_course_data['cvalue']; 												
	}

	//get locations of cost center
	@$clocs = array();
	$gettaxcd = mysqli_query($con,"SELECT nid, cdesc FROM `locations` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$clocs[] = $row; 
		}
	}

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

	//get default EWT acct code
	@$ewtpaydef = "";
	$gettaxcd = mysqli_query($con,"SELECT * FROM `accounts_default` where compcode='$company' and ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno']; 
		}
	}

	//get default Input tax acct code
	@$OTpaydef = "";
	$gettaxcd = mysqli_query($con,"SELECT * FROM `accounts_default` where compcode='$company' and ccode='PURCH_VAT'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$OTpaydef = $row['cacctno']; 
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
  	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" />
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

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

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus();">
	<input type="hidden" id="costcenters" value='<?=json_encode($clocs)?>'>
	<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">   
	<input type="hidden" value='<?=json_encode(@$arrwtxlist)?>' id="hdnxtax">
	<input type="hidden" value='<?=@$ewtpaydef?>' id="hdnewtpay">
	<input type="hidden" value='<?=@$OTpaydef ?>' id="hdnoutaxpay">

<?php
    $sqlchk = mysqli_query($con,"Select a.cacctno, c.cacctdesc, a.ccode, a.cpaymethod, a.cbankcode, a.ccheckno, a.ccheckbook, a.cpaydesc, a.cpayrefno, e.cname as cbankname, a.cpayee, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dcheckdate,'%m/%d/%Y') as dcheckdate, a.ngross, a.npaid, a.lapproved, a.lcancelled, a.lvoid, a.lprintposted, a.lnoapvref, b.cname, d.cname as custname, c.cacctdesc, a.cparticulars, a.cpaytype, a.ccurrencycode, a.ccurrencydesc, a.nexchangerate
		From paybill a 
		left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
		left join accounts c on a.cacctno=c.cacctid 
		left join customers d on a.compcode=d.compcode and a.ccode=d.cempid 
		left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode 
		where a.compcode='$company' and a.ctranno='$ccvno'");
				
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cCode = $row['ccode'];
			if($row['cname']!=""){
				$cName = $row['cname'];
			}else{
				$cName = $row['custname'];
			}

			$cpaymeth = $row['cpaymethod']; 
			$cpaytype = $row['cpaytype'];

			$lnoAPVRef = $row['lnoapvref'];

			$cpartic = $row['cparticulars'];

			$cBank = $row['cbankcode'];
			$cBankName = $row['cbankname'];
			$cCheckNo = $row['ccheckno'];
			$cCheckBK = $row['ccheckbook'];

			$cPayDesc = $row['cpaydesc'];
			$cPayRefr = $row['cpayrefno'];

			$cAcctID = $row['cacctno'];
			$cAcctDesc = $row['cacctdesc'];
			
			$cPayee = $row['cpayee'];
			$dDate = $row['ddate'];
			$dCheckDate = $row['dcheckdate'];
			$nAmount = $row['ngross'];
			$nPaid = $row['npaid'];

			$ccurrcode = $row['ccurrencycode'];  
			$ccurrdesc = $row['ccurrencydesc']; 
			$ccurrrate = $row['nexchangerate'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
			$lVoid = $row['lvoid'];
		}

?>

<input type="hidden" id="existingnos" value='<?=json_encode($arrnoslist)?>'>


<form action="PayBill_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" onsubmit="return chkform();"enctype="multipart/form-data">
	<fieldset>
   	  <legend>
			 <div class="col-xs-6 nopadding"> Bills Payment Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
						<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Bills Payment Details</a></li>
						<li><a href="#attc" data-toggle="tab">Attachments</a></li>
					</ul>

					<div class="tab-content nopadwtop2x">
						<div class="tab-pane active" id="1Det">

							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<tH>Tran No.:</tH>
									<td style="padding:2px;">
										<div class="col-xs-12"  style="padding-left:2px">
											<div class="col-xs-5 nopadding">
												<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
											</div>
											<div class="col-xs-1 nopadwleft">
											
												<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
												
												<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
												<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
												<input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>"> 
												<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>"> 
												

												<button type="button" class="btn btn-entry btn-sm" id="btnentry">
													<i class="fa fa-bar-chart" aria-hidden="true"></i>
												</button>
											</div>
										</div>

									</td>
									<td colspan="2" style="padding:2px;" align="right">
										<div id="statmsgz" class="small" style="display:inline;"></div>
									</td>
								</tr>

								<?php
									if($nvalue==0){
								?>
									
								<tr>
									<td><span style="padding:2px"><b>Reference:</b></span></td>
									<td> 
										<div class="col-xs-12"  style="padding-left:2px">
											<div class="col-xs-6 nopadding">

												<select id="isNoRef" name="isNoRef" class="form-control input-sm selectpicker" onchange="changeDet();">
													<option value="0" <?=($lnoAPVRef==0) ? "selected" : ""?>>With AP Voucher</option>
													<option value="1" <?=($lnoAPVRef==1) ? "selected" : ""?>>No AP Voucher Reference</option>
												</select> 
											</div>
										</div>
										</td>
										<td colspan="2">&nbsp;</td> 
								</tr>
								<?php
									}
								?>

								<tr>
									<td><span style="padding:2px"><b>Paid To:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px">
											<div class="col-xs-2 nopadding">
													<input type="text" class="form-control input-sm"  id="txtcustid" name="txtcustid" readonly value="<?php echo $cCode;?>">
													<input type="hidden" value=""  id="hdncustewt">
											</div>
											<div class="col-xs-10 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4" value="<?php echo $cName;?>">
											</div>
										</div>
									</td>
									<td><span style="padding:2px"><b>Payee:</b></span></td>
									<td>
									<div class="col-xs-12"  style="padding-bottom:2px">
											<div class='col-xs-12 nopadding'>
													<input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" tabindex="5" value="<?php echo $cPayee;?>">
											</div>
									</div>
									</td>
								</tr>
							
										<tr>
											<td colspan="4">&nbsp;</td>
										</tr>


								<tr>
									
								<td width="150"><span style="padding:2px"><b>Payment Method</b></span></td>
								<td>
									<div class="row nopadwleft" style="padding-left:2px">
										<div class="col-xs-3 nopadding">
											<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
												<option value="cheque" <?=($cpaymeth=="cheque") ? "selected" : ""?>>Cheque</option>
												<option value="cash" <?=($cpaymeth=="cash") ? "selected" : ""?>>Cash</option>
												<option value="bank transfer" <?=($cpaymeth=="bank transfer") ? "selected" : ""?>>Bank Transfer</option>
												<option value="mobile payment" <?=($cpaymeth=="mobile payment") ? "selected" : ""?>>Mobile Payment</option>
												<option value="credit card" <?=($cpaymeth=="credit card") ? "selected" : ""?>>Credit Card</option>
												<option value="debit card" <?=($cpaymeth=="debit card") ? "selected" : ""?>>Debit Card</option>
											</select>
										</div>
										<div class="col-xs-9 nopadwleft">

													<div class="col-xs-7 nopadding" id="paymntrefrdet" style="<?=($cpaymeth!=="cheque") ? "; display: none" : ""?>">

														<div class="col-xs-7 nopadding">
															<input type='text' class='noref form-control input-sm' name='txtCheckNo' id='txtCheckNo' value="<?php echo $cCheckNo; ?>" readonly required placeholder="Check No."/>
															<input type='hidden' name='txtChkBkNo' id='txtChkBkNo' value="" />
														</div>	
														<div class="col-xs-5 nopadwleft">
															<button type="button" class="btn btn-danger btn-sm disabled" name="btnVoid" id="btnVoid" data-toggle="popover" data-content="Void Check" data-trigger="hover" data-placement="top" disabled><i class="fa fa-ban" aria-hidden="true"></i></button> 
															
															<button type="button" class="btn btn-warning btn-sm disabled" name="btnreserve" id="btnreserve" data-toggle="popover" data-content="Reserve Check" data-trigger="hover" data-placement="top" disabled><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></button> 	
														</div>
													</div>

													<div class="col-xs-7 nopadding" style="<?=($cpaymeth=="cheque" || $cpaymeth=="cash") ? "; display: none" : ""?>" id="payrefothrsdet">
														<input type="text" id="txtPayRefrnce" class="noref form-control input-sm" name="txtPayRefrnce"  value="<?php echo $cPayRefr; ?>" placeholder="Reference No.">
													</div>

													<div class="col-xs-5 nopadding">
														<div class="input-sm no-border" style="color: red" id="chknochek">
														
														</div>
													</div>

										</div>																												
									</td>
									<td width="120"><span style="padding:2px"><b>Payment Date:</b></span></td>
									<td>
										<div class='col-xs-12' style="padding-bottom:2px">
											<div class="col-xs-6 nopadding">
												<input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dDate; ?>" tabindex="3"  />
											</div>
										</div>
									</td>
								</tr>
								<tr>
								<td width="150">
										<span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span> 
									</td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px" id="paymntdescdet">
											<div class="col-xs-3 nopadding">
												<input type="text" id="txtBank" class="form-control input-sm" name="txtBank" placeholder="Bank Code" readonly value="<?php echo $cBank;?>">
											</div>
											<div class="col-xs-1 nopadwleft">
												<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchbank" id="btnsearchbank"><i class="fa fa-search"></i></button> 
											</div>
											<div class="col-xs-8 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtBankName" name="txtBankName" width="20px" tabindex="1" placeholder="Bank Name..." required value="<?php echo $cBankName;?>" autocomplete="off" readonly>   
											</div>
										</div>  

									</td>		
									<td><span style="padding:2px" id="chkdate"><b><?=($cpaymeth=="cash" || $cpaymeth=="cheque") ? "Check Date" : "Transfer Date"?>:</b></span></td>
									<td>
									<div class="col-xs-12"  style="padding-bottom:2px">
											<div class='col-xs-6 nopadding'>
													<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo $dCheckDate; ?>" <?=($cpaymeth=="cash") ? "disbaled=true" : ""?>/>
											</div>
									</div>
									</td>							
								</tr>
								<tr>
									
								<td><span style="padding:2px"><b>Payment Acct (Cr): </b></span></td>
									<td>
									<div class="col-xs-12"  style="padding-left:2px">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcacctid" class="form-control input-sm" name="txtcacctid" placeholder="Account Code" value="<?php echo $cAcctID; ?>">
										</div>
										<div class="col-xs-9 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required autocomplete="off" value="<?php echo $cAcctDesc; ?>">
										</div>
										
									</div>
									</td>
									<td><span style="padding:2px" id="chkdate"><b>Amount Paid:</b></span></td>
									<td>
										<div class='col-xs-12' style="padding-bottom:2px">
											<div class="col-xs-7 nopadding"> 
												<input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="<?php echo number_format($nPaid,2); ?>" style="font-size:16px; font-weight:bold; text-align:right" > 

												
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td><span style="padding:2px" id="paymntrefr"><b>Currency</b></span></td>
									<td>
										<div class="row nopadwleft">
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
									<td><span style="padding:2px" id="chkdate"><b>Amount Payable:</b></span></td>
									<td>
										<div class='col-xs-12' style="padding-bottom:2px">
											<div class="col-xs-7 nopadding">  
												<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" <?php echo number_format($nAmount,2); ?> style="font-size:16px; font-weight:bold; text-align:right" readonly> 
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td><span style="padding:2px"><b>Particulars:</b></span></td>
									<td> 
										<div class="col-xs-12"  style="padding-left:2px">
											<div class='col-xs-12 nopadding'>
												<textarea class="form-control" rows="2" id="txtparticulars" name="txtparticulars"><?=$cpartic?></textarea>
											</div>
										</div>
									</td>
									<td colspan="2"> &nbsp; </td>
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


							<div class="col-xs-12 nopadwdown">
								<div class="col-xs-1 nopadwright"><button type="button" class="btn btn-xs btn-warning btn-block" id="btnaddline">Add Payable</button></div>
							</div>

							<div id="tableContainer" class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								border: 1px solid #919b9c;
								width: 100%;
								height: 250px;
								text-align: left;
								overflow: auto">
									<table width="150%" border="0" cellpadding="0" id="MyTable">
										<thead>
											<tr>
												<th scope="col" id="hdnRefTitle" nowrap>APV No&nbsp;&nbsp;&nbsp;</th>
												<th scope="col">Ref No</th>
												<th scope="col">Date</th>
												<th scope="col" class="text-right" width="120px">Amount</th>
												<th scope="col" class="text-right" width="120px">Payed&nbsp;&nbsp;&nbsp;</th>
												<th scope="col" width="120px" class="text-right">Total Owed&nbsp;&nbsp;&nbsp;</th>
												<th scope="col" width="120px" class="text-center">Amount Applied</th>
												<th scope="col">Account Code</th>
												<th scope="col">Account Title</th>
												<th scope="col" id="tblewt" <?=($lnoAPVRef==0) ? "style='display: none'" : ""?>>EWT Code</th>
												<th scope="col">Type</th>												
												<th scope="col">Cost Center</th>
												<th scope="col">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
							</div>

						<?php
							if($poststat=="True"){
						?>
            <br>
						<table width="100%" border="0" cellpadding="3">
							<tr>
								<td width="60%" rowspan="2">
										<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
											<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
												Back to Main<br>(ESC)
											</button>
										
											<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='PayBill_new.php';" id="btnNew" name="btnNew">
												New<br>(F1)
											</button>

											<button type="button" class="btn btn-info btn-sm" tabindex="6" id="btnAPVIns" name="btnAPVIns">
												APV<br>(Insert)
											</button>

											<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
												Undo Edit<br>(CTRL+Z)
											</button>
									
											<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk();" id="btnPrint" name="btnPrint">
												Print<br>(F4)
											</button>
								    
											<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
												Edit<br>(CTRL+E)
											</button>
											
											<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
												Save<br>(CTRL+S)
											</button>

								</td>
								<td align="right">&nbsp;</td>
							</tr>
							<tr>
								<td align="right">&nbsp;</td>
							</tr>
						</table>
						<?php
						}
					?>

    </fieldset>

</form>

<?php
}
else{
?>

	<form action="PayBill_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Pay Bills</legend>	
			<table width="100%" border="0">
				<tr>
					<tH width="100">PV NO:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
					</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PV No. DID NOT EXIST!</b></font></tH>
					</tr>
			</table>
		</fieldset>
	</form>

<?php
}
?>

				<!-- DETAILS ONLY -->
				<div class="modal fade" id="myAPModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">AP List</h3>
							</div>
										
							<div class="modal-body pre-scrollable">
										
								<table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th><input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
											<th>AP No.</th>
											<th>Ref No.</th>
											<th>Date</th>
											<th>Acct Code</th>
											<th>Acct Desc</th>
											<th>Payable Amount</th>
										</tr>
									</thead>
									<tbody>
																			
									</tbody>
								</table>

							</div> 
										
							<div class="modal-footer">
								<button type="button" id="btnSave2" onClick="InsertSI()" class="btn btn-primary">Insert</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->

				<!-- Banks List -->
				<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="BanksListHeader">Bank List</h3>
							</div>
							
							<div class="modal-body pre-scrollable">
							
								<table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>Bank Code</th>
											<th>Bank Name</th>
											<th>Bank Acct No</th>
											<th class="bnkchk">Checkbook No.</th>
											<th class="bnkchk">Check No.</th>
										</tr>
									</thead>
									<tbody>																
									</tbody>
								</table>
							</div>         	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Banks modal -->

				<!-- override modal -->
				<div class="modal fade" id="mychkover" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">Authentication</h3>
							</div>
							
							<div class="modal-body">
								<form action="index.php" method="post">
									<div class="form-group">
										<input type="text" class="form-control" name="authen_id" id="authen_id" placeholder="Username" required value="" autocomplete="off">		
									</div>
														
									<div class="form-group">
										<input type="password" class="form-control" name="authen_pass" id="authen_pass" placeholder="Password" required  value=""  autocomplete="off">	
									</div>
																											
									<div class="form-group" id="add_err">
																			
									</div>

								</form>
							</div> 
							
							<div class="modal-footer">
								<button type="button" id="btnauthenticate" class="btn btn-primary">Proceed</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End override modal -->

				<!-- reason modal -->
				<div class="modal fade" id="reasonmod" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">Reason</h3>

								<input type="hidden" name="modevent" id="modevent" value="">
								<input type="hidden" name="authcode" id="authcode" value="">
							</div>
							
							<div class="modal-body">
								<div class="form-group">
									<label for="comment">Reason:</label>
									<textarea class="form-control" rows="5" id="txtreason"></textarea>
								</div> 
							</div> 
							
							<div class="modal-footer">
								<button type="button" id="btnresonok" class="btn btn-primary">Proceed</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End override modal -->

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
				<!-- End Alert modal -->


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
											$getewtcd = mysqli_query($con,"SELECT * FROM glactivity where compcode='$company' and ctranno='$ccvno'"); 
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


				<form action="print_voucher.php" name="frmvoucher" id="frmvoucher" method="post" target="_blank">
					<input type="hidden" name="id" id="id" value="<?php echo $ccvno;?>">
					<input type="submit" style="display: none" id="btnvoucher">
				</form>

				<form action="print_check.php" name="frmchek" id="frmchek" method="post" target="_blank"> 
					<input type="hidden" name="id" id="id" value="<?php echo $ccvno;?>">
					<input type="submit" style="display: none" id="btncheck"> 
				</form>

				<form action="bir2307.php" name="frmbir2307" id="frmbir2307" method="post" target="_blank"> 
					<input type="hidden" name="id" id="id" value="<?php echo $ccvno;?>">
					<input type="submit" style="display: none" id="btn2307"> 
				</form>

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
		fileslist.push(encodeURI("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/PV/<?=$company."_".$ccvno?>/" + object.name))

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
			url: "th_filedelete.php?id="+object.name+"&code=<?=$ccvno?>", 
			key: i + 1
		});
	}


	<?php
		if($poststat=="True"){
	?>						
	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='PayBill_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){ 
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#myChkModal').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			var custid = $("#txtcustid").val();
			showapvmod(custid)
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk();
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
			$("#btnMain").click();
		}
	  }
	});
	<?php
		}
	?>


	$(document).ready(function() {
		$("#txttotpaid").autoNumeric('init',{mDec:2});
		$("#txtnGross").autoNumeric('init',{mDec:2});

		$("#txtnGross,#txttotpaid").on("focus", function () {
			$(this).select();
		});

		$('body').on('focus',".datepick", function(){
			$(this).datetimepicker({
				format: 'MM/DD/YYYY',
				widgetPositioning:{
          horizontal: 'auto',
          vertical: 'bottom'
        }
			});
		});

		$('body').on('focus',".cacctdesc", function(){
			var $input = $(".cacctdesc");

			var id = $(document.activeElement).attr('id');	
			var numid = id.replace("cacctdesc","");

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
					return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + '</small></div>';
				},
				highlighter: Object,
				afterSelect: function(item) { 

					$('#'+id).val(item.name).change(); 
					$("#cacctno"+numid).val(item.id);

					//if(item.id==$("#hdnewtpay").val()){
					//	$("#napvewt"+numid).val($("#hdncustewt").val());
					//} 

					if(item.id==$("#hdnewtpay").val()){
						var xz = $("#hdnxtax").val();
						var ewtoptions = "";

						$.each(jQuery.parseJSON(xz), function() { 
							if($("#hdncustewt").val()==this['ctaxcode']){
								isselctd = "selected";
							}else{
								isselctd = "";
							}
							ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
						});

						$("#napvewt"+numid).select2('destroy').html(ewtoptions).select2();
					}

					if(item.id==$("#hdnoutaxpay").val()){
						var xz = $("#hdntaxcodes").val();
						var ewtoptions = "";
						var $cnt = 0;
						$.each(jQuery.parseJSON(xz), function() { 
							$cnt++;
							if($cnt==1){
								isselctd = "selected";
							}else{
								isselctd = "";
							}
							ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
						});

						$("#napvewt"+numid).select2('destroy').html(ewtoptions).select2();
					}

				}
			});

		});

		$('body').on('focus',".napvewt", function(){
			var $input = $(".napvewt");
			var id = $(document.activeElement).attr('id');

			$("#"+id).typeahead({
				items: 10,
				source: function(request, response) {
					$.ajax({
						url: "../th_ewtcodes.php",
						dataType: "json",
						data: { query: $("#"+id).val() },
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
					//alert(item.ctaxcode);				
					$("#"+id).val(item.ctaxcode).change(); 																	
				}
			});
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
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/PV/<?=$company."_".$ccvno?>/{filename}',
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

		$('[data-toggle="popover"]').popover();
		
		loadDets();
		
		disabled();

		$('#txtcacct').typeahead({
		
			source: function (query, process) {
				return $.getJSON(
					'../th_accounts.php',
					{ query: query },
					function (data) {
						newData = [];
						map = {};
						
						$.each(data, function(i, object) {
							map[object.name] = object;
							newData.push(object.name);
						});
						
						process(newData);
					});
			},
			updater: function (item) {	
					
					$('#txtcacctid').val(map[item].id);
					$('#txtnbalance').val(map[item].balance);
					return item;
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
			
		$("#btnVoid").on("click", function() { 		
			$("#modevent").val("void");
			$("#mychkover").modal("show");
		});

		$("#btnreserve").on("click", function() {
			$("#modevent").val("reserve");
			$("#mychkover").modal("show");
		});

		$("#btnauthenticate").on("click", function() {
			if($("#authen_pass").val()=="" || $("#authen_id").val()==""){
				$("#AlertMsg").html("<b>ERROR: </b>Username and Password is required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}else{

				$.ajax ({
					url: "PayBill_authenticate.php",
					data: { id: $("#authen_id").val(), pass: $("#authen_pass").val(), xval: "<?=$_SESSION['myxtoken']?>" },
					async: false,
					success: function( data ) {
	
						$("#mychkover").modal("hide");

						if(data.trim()=="True"){

							$("#authcode").val($("#authen_id").val());

							$("#authen_id").val("");
							$("#authen_pass").val("");
							$("#reasonmod").modal("show");
					
						}else{

							$("#AlertMsg").html("<b>ERROR: </b>Authentication Failed!<br>"+data.trim());
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

						}
					}
				});
			}
		});

		$("#btnresonok").on("click", function() {

			if($("#txtreason").val()==""){

				$("#AlertMsg").html("<b>ERROR: </b> Enter a valid reason!<br>");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			}else{

				//alert("PayBill_voidchkno.php?id="+$("#txtBank").val()+"&chkno="+$("#txtCheckNo").val()+"&chkbkno="+$("#txtChkBkNo").val()+"&rem="+$("#txtreason").val()+"&xtyp="+$("#modevent").val()+"&authcode="+$("#authcode").val());

				$.ajax ({
					url: "PayBill_voidchkno.php",
					data: { id: $("#txtBank").val(), chkno: $("#txtCheckNo").val(), chkbkno: $("#txtChkBkNo").val(), rem: $("#txtreason").val(), xtyp: $("#modevent").val(), authcode: $("#authcode").val() },
					async: false,
					success: function( data ) {
						if(data.trim()!="False"){

							$str = data.split(":");
							$("#txtCheckNo").val($str[0]);
							$("#txtChkBkNo").val($str[1]);

							$("#txtreason").text("");
							$("#reasonmod").modal("hide");
						}
					}
				});

			}

		});

		$("#btnsearchbank").on("click", function() {

			if($("#selpayment").val()=="cheque"){
				$(".bnkchk").show();

				$("#BanksListHeader").text("Bank/Cheque No.");
			}else{
				$(".bnkchk").hide();

				$("#BanksListHeader").text("Banks");
			}

			$('#MyDRDetList tbody').empty();

				$.ajax({
					url: 'th_banklist.php',
					data: { id: $("#selpayment").val() },
					dataType: 'json',
					async:false,
					method: 'post',
					success: function (data) {
					// var classRoomsTable = $('#mytable tbody');
						console.log(data);
						$.each(data,function(index,item){

							if($("#selpayment").val()=="cheque"){
								$("<tr id=\"bank"+index+"\">").append(
									$("<td>").text(item.ccode),
									$("<td>").text(item.cname),
									$("<td>").text(item.cbankacctno),
									$("<td>").text(item.ccheckno),
									$("<td>").text(item.ccurrentcheck)
								).appendTo("#MyDRDetList tbody");

							}else{
								$("<tr id=\"bank"+index+"\">").append(
									$("<td>").text(item.ccode),
									$("<td>").text(item.cname),
									$("<td>").text(item.cbankacctno)
								).appendTo("#MyDRDetList tbody");
							}
									
							$("#bank"+index).on("click", function() {
								$("#txtBank").val(item.ccode);
								$("#txtBankName").val(item.cname);
								$("#txtcacctid").val(item.cacctno);
								$("#txtcacct").val(item.cacctdesc);

								if($("#selpayment").val()=="cheque"){
									$("#txtCheckNo").val(item.ccurrentcheck);
									$("#txtChkBkNo").val(item.ccheckno);

									if(item.ccurrentcheck!==""){

										$("#btnVoid").attr("disabled", false);
										$("#btnreserve").attr("disabled", false);

										$("#btnVoid").removeClass("disabled");
										$("#btnreserve").removeClass("disabled");

									}else{

										$("#btnVoid").attr("disabled", true);
										$("#btnreserve").attr("disabled", true);

										$("#btnVoid").addClass("disabled");
										$("#btnreserve").addClass("disabled");

									}


								}
										
								$("#myChkModal").modal("hide");
							});

						});

					},
					error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
				});

			$("#myChkModal").modal("show");
		});
		
		$("#btnAPVIns").on("click", function() {
			var custid = $("#txtcustid").val();
			showapvmod(custid)
		});

		/*
		$("#selpaytype").on("change", function() {
			$('#MyTable > tbody').empty();

			if($(this).val()=="apv"){
				$("#btnAPVIns").html("APV<br>(Insert)"); 
				$("#hdnRefTitle").text("APV No");
			}else if($(this).val()=="po"){
				$("#btnAPVIns").html("PO<br>(Insert)");
				$("#hdnRefTitle").text("PO No");
			}
		});
		*/

		$("#selpayment").on("change", function(){  

			$("#txtBank").val("");
			$("#txtBankName").val("");
			$("#txtcacctid").val("");
			$("#txtcacct").val("");
			$("#txtCheckNo").val("");
			$("#txtPayRefrnce").val("");


			if($(this).val()=="cash"){       //paymntdesc paymntdescdet
				//$("#paymntdesc").html(" ");
				//$("#paymntrefr").html(" ");		
				
				//$("#paymntdescdet").hide();
				$("#paymntrefrdet").hide();
				$("#payrefothrsdet").hide(); 

				$("#btnsearchbank").attr("disabled", true);
				$("#chkdate").html("<b>Check Date</b>");
				$("#txtChekDate").attr("disabled", true);    

				$("#txtBank").prop("required", false);
				$("#txtBankName").prop("required", false); 
				$("#txtCheckNo").prop("required", false); 
				$("#txtPayRefrnce").prop("required", false); 

			}else if($(this).val()=="cheque"){	
			//	$("#paymntdesc").html("<b>Bank Name</b>");	
			//	$("#paymntrefr").html("<b>Check No.</b>");

				$("#paymntdescdet").show();
				$("#paymntrefrdet").show();

				$("#paymntothrsdet").hide();
				$("#payrefothrsdet").hide();

				$("#btnsearchbank").attr("disabled", false);
				$("#chkdate").html("<b>Transfer Date</b>"); 
				$("#txtChekDate").attr("disabled", false); 

				$("#txtBank").prop("required", true);
				$("#txtBankName").prop("required", true); 
				$("#txtCheckNo").prop("required", true); 
				$("#txtPayRefrnce").prop("required", false);

			}else if($(this).val()=="bank transfer"){
				//$("#paymntdesc").html("<b>Bank Name</b>");
				//$("#paymntrefr").html("<b>Reference No.</b>");

				$("#paymntdescdet").show();
				$("#paymntrefrdet").hide();

				$("#paymntothrsdet").hide();
				$("#payrefothrsdet").show();

				$("#btnsearchbank").attr("disabled", false);
				$("#chkdate").html("<b>Transfer Date</b>"); 
				$("#chkdate").html("<b>Transfer Date</b>"); 
				$("#txtChekDate").attr("disabled", false); 

				$("#txtBank").prop("required", true);
				$("#txtBankName").prop("required", true); 
				$("#txtCheckNo").prop("required", false); 
				$("#txtPayRefrnce").prop("required", true);
			}else{
				//$("#paymntdesc").html("<b>Bank Name</b>");
				//$("#paymntrefr").html("<b>Reference No.</b>");

				$("#paymntdescdet").show();
				$("#paymntrefrdet").hide();

				$("#paymntothrsdet").show();
				$("#payrefothrsdet").show();

				$("#btnsearchbank").attr("disabled", false);
				$("#chkdate").html("<b>Transfer Date</b>"); 
				$("#txtChekDate").attr("disabled", false); 

				$("#txtBank").prop("required", false);
				$("#txtBankName").prop("required", false); 
				$("#txtCheckNo").prop("required", false); 
				$("#txtPayRefrnce").prop("required", true);
			}
		});

		$(".noref").on("keyup", function() {

			var disval = $(this).val();
			var xz = $("#existingnos").val();

			if(xz!=""){
				$.each(jQuery.parseJSON(xz), function() { 
					if(disval==this['noid']){
						$("#chknochek").text("With Reference: " + this['ctranno']);
						return false; // breaks
					}else{
						$("#chknochek").text("");
					}
				});
			}
			
		});

		$('#btnaddline').on('click', function(e) {
				
				addrrdet("","",0,0,0,"",0,"","",0);

		});

		$("#btnentry").on("click", function(){		
			$("#modGLEntry").modal("show");
		});

		$("#isNoRef").change(function() {
			if($(this).find(":selected").val()==1) {
				$("#btnAPVIns").attr("disabled", true);  
				$("#tblewt").show(); 
			}else{
				$("#btnAPVIns").attr("disabled", false);
				$("#tblewt").hide(); 
			}
		});

		$("#selbasecurr").on("change", function (){
	
			var dval = $(this).find(':selected').attr('data-val');
			var ddesc = $(this).find(':selected').attr('data-desc');

			$("#basecurrval").val(dval);
			$("#hidcurrvaldesc").val(ddesc);
			$("#statgetrate").html("");

			$('#MyTable tbody').empty();
				
		});

	});

	function showapvmod(custid){
		$('#MyAPVList tbody').empty(); /*, typ: $("#selpaytype").val() */
			
		$.ajax({
			url: 'th_APVlist.php',
			data: { code: custid },
			dataType: 'json',
			async:false,
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){

					if(item.ctranno=="NO"){
						alert("No Available APV.");
						$('#txtcust').val("").change(); 
						$("#txtcustid").val("");
					}
					else{							
						$("<tr id=\"APV"+index+"\">").append(
							$("<td>").html("<input type='checkbox' value='"+index+"' name='chkSales[]'>"),
							$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'> <input type='hidden' id='hdnAPVewt"+index+"' name='hdnAPVewt"+index+"' value='"+item.newtamt+"'>"),
							$("<td>").html(item.crefno+"<input type='hidden' id='APVrrno"+index+"' name='APVrrno"+index+"' value='"+item.crefno+"'>"),
							$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
							$("<td>").html(item.cacctno+"<input type='hidden' id='APVacctno"+index+"' name='APVacctno"+index+"' value='"+item.cacctno+"'>"),
							$("<td>").html(item.cacctdesc+"<input type='hidden' id='APVacctdesc"+index+"' name='APVacctdesc"+index+"' value='"+item.cacctdesc+"'>"),
							$("<td>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'> <input type='hidden' id='APVpayed"+index+"' name='APVpayed"+index+"' value='"+item.napplied+"'>")
						).appendTo("#MyAPVList tbody");
								
						$("#myAPModal").modal("show");							
					}
				});

			},
			error: function (req, status, err) {
				alert('Something went wrong\nStatus: '+status +"\nError: "+err);
				console.log('Something went wrong', status, err);
			}
		});
				
	}

	function InsertSI(){	
		var totGross = 0;
		var modnme = "";
				
		$("input[name='chkSales[]']:checked").each( function () {
			var xyz = $(this).val();
					
				var a = $("#APVtxtno"+xyz).val();
				var a2 = $("#APVrrno"+xyz).val();
				var b = $("#APVdte"+xyz).val();
				var c = $("#APVacctno"+xyz).val();
				var d = $("#APVamt"+xyz).val().replace(/,/g,'');
				var e = $("#APVpayed"+xyz).val();
				var f = $("#APVacctdesc"+xyz).val(); 
				var g = $("#hdnAPVewt"+xyz).val();

			var owed = parseFloat(d) - parseFloat(e);

			addrrdet(a,b,d,e,owed,c,owed,f,a2,g);
			
			//totGross = parseFloat(totGross) + parseFloat(owed) ;

		});


		$('#myAPModal').modal('hide');
		$('#myAPModal').on('hidden.bs.modal', function (e) {

				//$("#txtnGross").val(totGross);
			//	$("#txtnGross").autoNumeric('destroy');
			//	$("#txtnGross").autoNumeric('init',{mDec:2});
		
		});
		

	}

	function addrrdet(ctranno,ddate,namount,npayed,ntotowed,cacctno,napplied,cacctdesc,refno,ewtamt,ewtcode="",entrytyp="",costcent=""){

		//var ctypref = $("#selpaytype").val();
		ctyprefval = "";
		//if(ctypref=="apv"){
		//	ctyprefval = "readonly";
		//}

		if(document.getElementById("txtcustid").value!=""){
			
			$('#txtcust').attr('readonly', true);
				
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length;
			
			var u = "<td>"+ctranno+"<input type=\"hidden\" name=\"cTranNo\" id=\"cTranNo"+lastRow+"\" value=\""+ctranno+"\" /></td>";

			if ($('#isNoRef').find(":selected").val()==1) {

				var u2 = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"form-control input-sm\" name=\"cRefRRNo\" id=\"cRefRRNo"+lastRow+"\" value=\""+refno+"\" /> </td>";
			
				var v = "<td style=\"padding:2px\" align=\"center\"><div class=\"controls\" style=\"position: relative\"><input type=\"text\" class=\"datepick form-control input-sm\" name=\"dApvDate\" id=\"dApvDate"+lastRow+"\" value=\""+$("#date_delivery").val()+"\" /></div></td>";

				var w = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"form-control input-sm\" name=\"nAmount\" id=\"nAmount"+lastRow+"\" value=\""+numcom(namount)+"\" style=\"text-align:right\"/></td>";

			}else{
				var u2 = "<td>"+refno+"<input type=\"hidden\" name=\"cRefRRNo\" id=\"cRefRRNo"+lastRow+"\" value=\""+refno+"\" /> </td>";
			
				var v = "<td>"+ddate+"<input type=\"hidden\" name=\"dApvDate\" id=\"dApvDate"+lastRow+"\" value=\""+ddate+"\" /></td>";

				var w = "<td align='right'>"+numcom(namount)+"<input type=\"hidden\" name=\"nAmount\" id=\"nAmount"+lastRow+"\" value=\""+namount+"\" /></td>";

				
			}
					
			var x = "<td align='right'>"+numcom(npayed)+"<input type=\"hidden\" name=\"cTotPayed\" id=\"cTotPayed"+lastRow+"\"  value=\""+npayed+"\" style=\"text-align:right\" readonly=\"readonly\">&nbsp;&nbsp;&nbsp;</td>";
			
			var y = "<td style=\"padding:2px\" align=\"right\">"+numcom(ntotowed)+"<input type=\"hidden\" name=\"cTotOwed\" id=\"cTotOwed"+lastRow+"\"  value=\""+ntotowed+"\">&nbsp;&nbsp;&nbsp;</td>";
				
			if ($('#isNoRef').find(":selected").val()==1) {
				var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied\" id=\"nApplied"+lastRow+"\" value=\""+napplied+"\" style=\"text-align:right\" readonly/></td>";
			}else{
				var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied\" id=\"nApplied"+lastRow+"\"  value=\""+napplied+"\" style=\"text-align:right\" /></td>";
			}

			var t = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"cacctdesc form-control input-sm\" name=\"cacctdesc\" id=\"cacctdesc"+lastRow+"\"  value=\""+cacctdesc+"\" "+ctyprefval+" placeholder=\"Account Title\"/></td>";	

			var t2 = "<td style=\"padding:2px\" align=\"center\" width=\"90px\" nowrap> <input type=\"text\" class=\"form-control input-sm\" name=\"cacctno\" id=\"cacctno"+lastRow+"\" value=\""+cacctno+"\" readonly placeholder=\"Account Code\"/></td>";	


			if ($('#isNoRef').find(":selected").val()==1) {
				//var t4 = "<td style=\"padding:2px\" align=\"center\" width=\"100px\" nowrap> <input type=\"text\" class=\"napvewt form-control input-sm\" name=\"napvewt\" id=\"napvewt"+lastRow+"\" value=\""+ewtcode+"\" placeholder=\"EWT Code\"/></td>";

				if(cacctno==$("#hdnewtpay").val()){  
					var xz = $("#hdnxtax").val();   
				}else if(cacctno==$("#hdnoutaxpay").val()){
					var xz = $("#hdntaxcodes").val();
				}else{
					var xz = "";
				}
				
				var ewtoptions = "";
				var ewtslctdval = "";
				if(xz!=""){
					$.each(jQuery.parseJSON(xz), function() { 
						if(ewtcode==this['ctaxcode']){
							isselctd = "selected";
							ewtslctdval = this['ctaxcode'];
						}else{
							isselctd = "";
						}

						ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
					});
				}

				var t4 = "<td style=\"padding:2px\" align=\"center\" width=\"100px\" nowrap> <select class='form-control input-sm' name=\"napvewt\" id=\"napvewt"+lastRow+"\" style=\"width: 100%\"><option value=\"\">&nbsp;</option> "+ewtoptions+"</select></td>";
				
			}else{
				var t4 = "<input type=\"hidden\" name=\"napvewt\" id=\"napvewt"+lastRow+"\" value=\""+ewtamt+"\" />";
			}

				var debtsel = "";
				var crdtsel = "";

				if(entrytyp=="Debit" || entrytyp==""){
					debtsel = "selected";
					crdtsel = "";
				}else if(entrytyp=="Credit"){
					debtsel = "";
					crdtsel = "selected";
				}

			var t5 = "<td style=\"padding:2px\" align=\"center\" width=\"80px\" nowrap> <select name=\"selentrytyp\" id=\"selentrytyp"+lastRow+"\" class=\" form-control input-sm\" onchange=\"GoToCompOthers();\"><option value=\"Debit\" "+debtsel+">Debit</option><option value=\"Credit\" "+crdtsel+">Credit</option></select></td>";	

			var t3 = "<td style=\"padding:2px\" align=\"center\" width=\"10px\" nowrap> <button class=\"btn btn-xs btn-danger\" name=\"delRow\" id=\"delRow"+lastRow+"\"><i class='fa fa-times'></i></button></td>";	

				var wittsel = 0;
				var xz = $("#costcenters").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if(costcent==this['nid']){
						isselected = "selected";
						wittsel++;
					}else{
						isselected = "";
					}
					taxoptions = taxoptions + "<option value='"+this['nid']+"' data-cdesc='"+this['cdesc']+"' "+isselected+">"+this['cdesc']+"</option>";
				});

					if(wittsel>=1){
						isselected = "";
					}else{
						isselected = "selected";
					}
			var costcntr = "<td  width=\"100px\" style=\"padding:1px\"><select class='form-control input-sm' name=\"selcostcentr\" id=\"selcostcentr"+lastRow+"\">  <option value='' data-cdesc='' "+isselected+">NONE</option> " + taxoptions + " </select> </td>"; 
			
			$('#MyTable > tbody:last-child').append('<tr>'+ u + u2 + v + w + x + y + z + t2 + t + t4 + t5 + costcntr + t3 + '</tr>');

			$("#delRow"+lastRow).on("click", function(){
				$(this).closest('tr').remove();
				Reindx();

				GoToCompAmt();
				GoToComp();
			});

			$("input.numeric").autoNumeric('init',{mDec:2});
			$("input.numeric").on("focus", function () {
				$(this).select();
			});
			
			$("input.numeric").on("keyup", function (e) {
				GoToComp();
				GoToCompAmt();
				setPosi($(this).attr('name'),e.keyCode);
			});

			$("#nAmount"+lastRow).autoNumeric('init',{mDec:2});
			$("#nAmount"+lastRow).on("focus", function () {
				$(this).select();
			});

			$("#nAmount"+lastRow).on("keyup", function (e) {
				$("#nApplied"+lastRow).val($(this).val());
				
				GoToCompOthers();

				setPosi($(this).attr('name'),e.keyCode);
			});

			if ($('#isNoRef').find(":selected").val()==1) {
				$("#napvewt"+lastRow).select2();
			}
	
			GoToCompAmt();
			GoToComp();
							
				
		}
		else{
			alert("Paid To Required!");
		}
	}

	function Reindx(){
		$("#MyTable > tbody > tr").each(function(index) {  
			tx = index + 1;

			$(this).find('input[type=hidden][name="cTranNo"]').attr("id","cTranNo"+tx);
			getapv = $(this).find('input[type=hidden][name="cTranNo"]').val();
			if(getapv==""){
				$(this).find('input[name="cRefRRNo"]').attr("id","cRefRRNo" + tx);
				$(this).find('input[name="dApvDate"]').attr("id","dApvDate" + tx);
				$(this).find('input[name="nAmount"]').attr("id","nAmount" + tx);
			}else{
				$(this).find('input[type=hidden][name="cRefRRNo"]').attr("id","cRefRRNo" + tx);
				$(this).find('input[type=hidden][name="dApvDate"]').attr("id","dApvDate" + tx);
				$(this).find('input[type=hidden][name="nAmount"]').attr("id","nAmount" + tx);
			}

			$(this).find('input[type=hidden][name="cTotPayed"]').attr("id","cTotPayed" + tx);
			$(this).find('input[type=hidden][name="cTotOwed"]').attr("id","cTotOwed" + tx);

			$(this).find('input[name="nApplied"]').attr("id","nApplied" + tx);
			$(this).find('input[name="cacctdesc"]').attr("id","cacctdesc" + tx);
			$(this).find('input[name="cacctno"]').attr("id","cacctno" + tx);

			if ($('#isNoRef').find(":selected").val()==1) {
				$(this).find('select[name="napvewt"]').attr("id","napvewt" + tx);				
			}else{
				$(this).find('input[type=hidden][name="napvewt"]').attr("id","napvewt" + tx); 
			}

			$(this).find('select[name="selentrytyp"]').attr("id","selentrytyp" + tx);
			$(this).find('select[name="selcostcentr"]').attr("id","selcostcentr" + tx);

			$(this).find('button[name="delRow"]').attr("id","delRow" + tx);


									$("#nAmount"+tx).autoNumeric('init',{mDec:2});
									$("#nAmount"+tx).on("focus", function () {
										$(this).select();
									});
									$("#nAmount"+tx).on("keyup", function (e) {
										$("#nApplied"+tx).val($(this).val());
										GoToCompAmt();
										GoToComp();

										setPosi($(this).attr('name'),e.keyCode);
									});

									

		});
	}

	function numcom(x) {
			var xcv = parseFloat(x).toFixed(2);
			return xcv.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function setPosi(nme,keyCode){
			var r = nme.replace(/\D/g,'');
			var namez = nme.replace(/[0-9]/g, '');
			
			
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			

			if(namez=="nApplied"){
				//alert(keyCode);
				if(keyCode==38 && r!=1){//Up
					var z = parseInt(r) - parseInt(1);
					document.getElementById("nApplied"+z).focus();
				}
				
				if((keyCode==40 || keyCode==13) && r!=lastRow){//Down or ENTER
					var z = parseInt(r) + parseInt(1);
					document.getElementById("nApplied"+z).focus();
				}
				
			}

	}

	function chkform(){
		var isOK = "True";
		//alert(isOK);
		
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;

			$("#MyTable > tbody > tr").each(function(index) {  
				$chkacdsc = $(this).find('input[name="cacctdesc"]').val();
				$chkacno = $(this).find('input[name="cacctno"]').val();
				$chkaval = $(this).find('input[name="nApplied"]').val();

				if($chkacdsc=="" || $chkacno=="" || $chkaval=="" || parseFloat($chkaval)==0){
					$xx = index + 1;
					$("#AlertMsg").html("Incomplete Details on line "+$xx);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');

					isOK="False";				
				}			
			});

			if(isOK=="False"){
				
				return false;
			}
			
			if(document.getElementById("txttotpaid").value == 0){
				$("#AlertMsg").html("<b>ERROR: </b>Enter total paid!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK="False";
				return false;
			}

			var npaid = document.getElementById("txttotpaid").value;
			var napplied = document.getElementById("txtnGross").value;
			
			var oob = parseFloat(npaid) - parseFloat(napplied);
			oob = oob.toFixed(4);
			
			if(parseFloat(oob)  > 1){
						
				$("#AlertMsg").html("<b>ERROR: </b>Unbalanced amount!<br>Out of Balance: "+ Math.abs(oob));
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK="False";
				return false;
			}
			
			
			if(isOK == "True"){
				document.getElementById("hdnrowcnt").value = lastRow;


				$("#MyTable > tbody > tr").each(function(index) {  
					tx = index + 1;

					$(this).find('input[type=hidden][name="cTranNo"]').attr("name","cTranNo"+tx);
					if ($('#isNoRef').find(":selected").val()==1) {
						$(this).find('input[name="cRefRRNo"]').attr("name","cRefRRNo" + tx);
						$(this).find('input[name="dApvDate"]').attr("name","dApvDate" + tx);
						$(this).find('input[name="nAmount"]').attr("name","nAmount" + tx);
					}else{
						$(this).find('input[type=hidden][name="cRefRRNo"]').attr("name","cRefRRNo" + tx);
						$(this).find('input[type=hidden][name="dApvDate"]').attr("name","dApvDate" + tx);
						$(this).find('input[type=hidden][name="nAmount"]').attr("name","nAmount" + tx);
					}

					$(this).find('input[type=hidden][name="cTotPayed"]').attr("name","cTotPayed" + tx);
					$(this).find('input[type=hidden][name="cTotOwed"]').attr("name","cTotOwed" + tx);

					$(this).find('input[name="nApplied"]').attr("name","nApplied" + tx);
					$(this).find('input[name="cacctdesc"]').attr("name","cacctdesc" + tx);
					$(this).find('input[name="cacctno"]').attr("name","cacctno" + tx);

					if ($('#isNoRef').find(":selected").val()==1) {
						$(this).find('select[name="napvewt"]').attr("name","napvewt" + tx);						
					}else{
						$(this).find('input[type=hidden][name="napvewt"]').attr("name","napvewt" + tx); 
					}

					$(this).find('select[name="selentrytyp"]').attr("name","selentrytyp" + tx);
					$(this).find('select[name="selcostcentr"]').attr("name","selcostcentr" + tx);

				});

				$("#frmpos").submit();

			return true;
			}

	}

	function GoToComp(){
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			var z;
			var gross = 0;
			
			//for (z=1; z<=lastRow; z++){
				//gross = parseFloat(gross) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
			//}

			var totndebit = 0;
			var totncredit = 0;

			for (z=1; z<=lastRow; z++){
				if($("#selentrytyp"+z).val()=="Debit"){
					totndebit = parseFloat(totndebit) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
				}else if($("#selentrytyp"+z).val()=="Credit"){
					totncredit = parseFloat(totncredit) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
				}
			}

			gross = parseFloat(totndebit) - parseFloat(totncredit);
			
			//document.getElementById("txtnGross").value = gross.toFixed(2);
			$("#txttotpaid").val(gross);
			$("#txttotpaid").autoNumeric('destroy');
			$("#txttotpaid").autoNumeric('init',{mDec:2});

	}

	function GoToCompAmt(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			var z;
			var gross = 0;
			
			var totndebit = 0;
			var totncredit = 0;

			for (z=1; z<=lastRow; z++){
				if($("#selentrytyp"+z).val()=="Debit"){
					if($("#cRefRRNo"+z).val()==""){ 
						totndebit = parseFloat(totndebit) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
					}else{
						totndebit = parseFloat(totndebit) + parseFloat($("#nAmount"+z).val().replace(/,/g,''));
					}
					
				}else if($("#selentrytyp"+z).val()=="Credit"){
					if($("#cRefRRNo"+z).val()==""){ 
						totncredit = parseFloat(totncredit) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
					}else{
						totncredit = parseFloat(totncredit) + parseFloat($("#nAmount"+z).val().replace(/,/g,''));
					}
				}
			}

			gross = parseFloat(totndebit) - parseFloat(totncredit);
			
			//document.getElementById("txtnGross").value = gross.toFixed(2);
			$("#txtnGross").val(gross);
			$("#txtnGross").autoNumeric('destroy');
			$("#txtnGross").autoNumeric('init',{mDec:2});
	}

	function GoToCompOthers(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			var z;
			var gross = 0;
			var totdebit = 0;
			var totcredit = 0;

			for (z=1; z<=lastRow; z++){
				getentrytp = $("#selentrytyp"+z).val();
				if(getentrytp=="Debit"){
					totdebit = totdebit + parseFloat($("#nAmount"+z).val().replace(/,/g,''));
				}

				if(getentrytp=="Credit"){
					totcredit = totcredit + parseFloat($("#nAmount"+z).val().replace(/,/g,''));
				}
			}

			gross = parseFloat(totdebit) - parseFloat(totcredit);
			$("#txtnGross").val(gross);
			$("#txtnGross").autoNumeric('destroy');
			$("#txtnGross").autoNumeric('init',{mDec:2});
	}

	function changeDet(){
		$('#MyTable tbody').empty(); 
	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "PayBill_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){

		$("#frmpos :input").attr("disabled", true);
		
		
		$("#txtctranno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);

		if(document.getElementById("hdnposted").value==1 && document.getElementById("hdnvoid").value==0){
			$("#btnentry").attr("disabled", false);
		}

		$("#btn-closemod").attr("disabled", false); 

		$(".kv-file-zoom").attr("disabled", false);

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
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{

			$("#frmpos :input").attr("disabled", false);
			
				
				$("#txtctranno").attr("readonly", true);
				$("#txtctranno").val($("#hdnorigNo").val());

				$("#btnentry").attr("disabled", true);
				
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);		  

				if($("#selpayment").val()=="cash"){
					$("#btnsearchbank").attr("disabled", true);	
				}
		}

	}

	function printchk(){

		if(document.getElementById("hdncancel").value==1){

			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";

		}else{
				
			//$("#frmvoucher").delay(300).submit();
    	//$("#frmchek").delay(300).submit();

			$ewtamt = 0;
			xintval = 0;
			$("#MyTable > tbody > tr").each(function(index) {	
				$xintval = index + 1;
				
				if ($("#napvewt"+$xintval).val()!="" && $("#napvewt"+$xintval).val()!=0) {
					$ewtamt++;
					//$ewtamt = ($("#napvewt"+$xintval).val()=="") ? 0 : $("#napvewt"+$xintval).val();
				}
				//else{
					//$ewtamt = $("#napvewt"+$xintval).val();
				//}
				
			});

			if($ewtamt != 0){
				$("#btn2307").click();
			}

			if($("#selpayment").val()=="cheque"){
				$("#btncheck").click();
			}

			$("#btnvoucher").click(); 

		}
	}

	function loadDets(){
		var xno = $("#txtctranno").val();
		$.ajax({
			url: 'th_PaybillDet.php',
			data: 'x='+xno,
			dataType: 'json',
			async:false,
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){

					//addrrdet(ctranno,ddate,namount,npayed,ntotowed,cacctno,cacctdesc,refno,ewtamt,ewtcode="",entrytyp=""){
					var xpayed = 0;
					if ($('#isNoRef').find(":selected").val()==0) {
						xpayed = item.npayed;
					}
					addrrdet(item.capvno,item.dapvdate,item.namount,xpayed,item.nowed,item.cacctno,item.napplied,item.cacctdesc,item.crefrr,item.newtamt,item.cewtcode,item.entrytyp,item.ncostcenter);
				});

				GoToCompAmt();
							
			}
		});
	}

</script>
