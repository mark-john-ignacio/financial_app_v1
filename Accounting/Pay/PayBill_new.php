<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PayBill_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	$company = $_SESSION['companyid'];

	$ddeldate = date("m/d/Y");
	$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

	$arrnoslist = array();
	$sqlempsec = mysqli_query($con,"select ifnull(ccheckno,'') as ccheckno, ifnull(cpayrefno,'') as cpayrefno,ctranno from paybill where compcode='$company' and lcancelled=0");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){

		if($row0['ccheckno']!==""){
			$arrnoslist[] = array('noid' => $row0['ccheckno'], 'ctranno' => $row0['ctranno']);
		}

		if($row0['cpayrefno']!==""){
			$arrnoslist[] = array('noid' => $row0['cpayrefno'], 'ctranno' => $row0['ctranno']);
		}
		
	}

	$nvalue = "";
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_APV'"); 										
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
		$nvalue = $all_course_data['cvalue']; 												
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

	//get locations of cost center
	@$clocs = array();
	$gettaxcd = mysqli_query($con,"SELECT nid, cdesc FROM `locations` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$clocs[] = $row; 
		}
	}

	$_SESSION['myxtoken'] = gen_token();		
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<!--
	--
	-- FileType Bootstrap Scripts and Link
	--
	-->	
	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
	<input type="hidden" value='<?=json_encode(@$arrwtxlist)?>' id="hdnxtax"> 
	<input type="hidden" id="existingnos" value='<?=json_encode($arrnoslist)?>'>
	<input type="hidden" id="costcenters" value='<?=json_encode($clocs)?>'>

	<input type="hidden" value='<?=@$ewtpaydef?>' id="hdnewtpay">
	<input type="hidden" value='<?=@$OTpaydef ?>' id="hdnoutaxpay">

	<form action="PayBill_newsave.php" name="frmpos" id="frmpos" method="post" onsubmit="return chkform();" enctype="multipart/form-data">
		<fieldset>
			<legend>Bills Payment</legend>
				
			<ul class="nav nav-tabs">
				<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Bills Payment Details</a></li>
				<li><a href="#attc" data-toggle="tab">Attachments</a></li>
			</ul>

			<div class="tab-content nopadwtop2x">
				<div class="tab-pane active" id="1Det">

					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php
							if($nvalue==0){
						?>
							
						<tr>
							<td><span style="padding:2px"><b>Reference:</b></span></td>
							<td colspan="3"> 
								<div class="col-xs-12"  style="padding-left:2px">
									<div class="col-xs-3 nopadding">

										<select id="isNoRef" name="isNoRef" class="form-control input-sm selectpicker" onchange="changeDet();">
											<option value="0">With AP Voucher</option>
											<option value="1">No AP Voucher Reference</option>
										</select> 
									</div>
								</div>
							</td>
						</tr>
						<?php
							}
						?>
						<tr>
							<td><span style="padding:2px"><b>Paid To:</b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-left:2px">
								<div class="col-xs-2 nopadding">
										<input type="text" class="form-control input-sm"  id="txtcustid" name="txtcustid" readonly>
										<input type="hidden" value=""  id="hdncustewt">
								</div>
								<div class="col-xs-10 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
								</div>
							</div>
							</td>
							<td><span style="padding:2px"><b>Payee:</b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-bottom:2px">
									<div class='col-xs-12 nopadding'>
											<input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" tabindex="5">
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
											<option value="cheque">Cheque</option>
											<option value="cash">Cash</option>
											<option value="bank transfer">Bank Transfer</option>
											<option value="mobile payment">Mobile Payment</option>
											<option value="credit card">Credit Card</option>
											<option value="debit card">Debit Card</option>
										</select>
									</div>
									<div class="col-xs-9 nopadwleft">

											<div class="col-xs-7 nopadding" id="paymntrefrdet">

												<div class="col-xs-7 nopadding">
													<input type='text' class='noref form-control input-sm' name='txtCheckNo' id='txtCheckNo' value="" readonly required placeholder="Check No."/>
													<input type='hidden' name='txtChkBkNo' id='txtChkBkNo' value="" />
												</div>	
												<div class="col-xs-5 nopadwleft">
													<button type="button" class="btn btn-danger btn-sm disabled" name="btnVoid" id="btnVoid" data-toggle="popover" data-content="Void Check" data-trigger="hover" data-placement="top" disabled><i class="fa fa-ban" aria-hidden="true"></i></button> 
													
													<button type="button" class="btn btn-warning btn-sm disabled" name="btnreserve" id="btnreserve" data-toggle="popover" data-content="Reserve Check" data-trigger="hover" data-placement="top" disabled><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></button> 	
												</div>
											</div>

											<div class="col-xs-7 nopadding" style="display: none" id="payrefothrsdet">
												<input type="text" id="txtPayRefrnce" class="noref form-control input-sm" name="txtPayRefrnce" value="" placeholder="Reference No.">
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
									<div class="col-xs-7 nopadding">
										<input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" tabindex="3"  />
									</div>
								</div>
							</td>
						</tr>
						<tr>

						<td width="150"><span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span></td>
							<td>
								<div class="row nopadwleft" id="paymntdescdet">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtBank" class="form-control input-sm" name="txtBank" value="" placeholder="Bank Code" readonly required>
									</div>
									<div class="col-xs-1 nopadwleft">
										<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchbank" id="btnsearchbank"><i class="fa fa-search"></i></button>
									</div>
									<div class="col-xs-8 nopadwleft" style="padding-right:15px !important">
										<input type="text" class="form-control input-sm" id="txtBankName" name="txtBankName" width="20px" tabindex="1" placeholder="Bank Name..." required value="" autocomplete="off" readonly>
									</div>
									
								</div>

							</td>	
							<td><span style="padding:2px" id="chkdate"><b>Check Date:</b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-bottom:2px">
									<div class='col-xs-7 nopadding'>
											<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo date("m/d/Y"); ?>" />
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
												<option value="<?=$rows['id']?>" <?php if ($nvaluecurrbase==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>" data-desc="<?=$rows['currencyName']?>"><?=$rows['currencyName']?></option>
											<?php
													}
												}
											?>
										</select>
										<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?=$nvaluecurrbase; ?>"> 	
										<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?=$nvaluecurrbasedesc; ?>"> 
									</div>
									<div class="col-xs-2 nopadwleft">
										<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
									</div>
									<div class="col-xs-3" id="statgetrate" style="padding: 4px !important"> 																	
									</div>
								</div>											
							</td>
							<td><span style="padding:2px" id="chkdate"><b>Amount Paid:</b></span></td>
							<td>
								<div class='col-xs-12' style="padding-bottom:2px">
									<div class="col-xs-7 nopadding"> 
										<input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="0.00" style="font-size:16px; font-weight:bold; text-align:right" > 

										
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td><span style="padding:2px"><b>Payment Acct (Cr): </b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px">
									<div class="col-xs-3 nopadding">                              
										<input type="text" id="txtcacctid" class="form-control input-sm" name="txtcacctid" value="" placeholder="Account Code" required>
									</div>
									<div class="col-xs-9 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="" autocomplete="off">
									</div>
									
								</div>
							</td>
							<td><span style="padding:2px" id="chkdate"><b>Amount Payable:</b></span></td>
							<td>
								<div class='col-xs-12' style="padding-bottom:2px">
									<div class="col-xs-7 nopadding">  
										<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" value="0.00" style="font-size:16px; font-weight:bold; text-align:right" readonly> 
									</div>
								</div>
							</td>
						</tr>

						<tr>
							<td><span style="padding:2px"><b>Particulars:</b></span></td>
							<td> 
								<div class="col-xs-12"  style="padding-left:2px">
									<div class='col-xs-12 nopadding'>
										<textarea class="form-control" rows="2" id="txtparticulars" name="txtparticulars"></textarea>
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
				overflow: scroll">
				<table width="150%" border="0" cellpadding="0" id="MyTable">
					<thead>
						<tr>
							<th scope="col" id="hdnRefTitle" nowrap>APV No&nbsp;&nbsp;&nbsp;</th>
							<th scope="col">Ref No</th>
							<th scope="col" width="100px">Date</th>
							<th scope="col" class="text-right" width="120px">Amount</th>
							<th scope="col" class="text-right" width="120px">Payed&nbsp;&nbsp;&nbsp;</th>
							<th scope="col" width="120px" class="text-right">Total Owed&nbsp;&nbsp;&nbsp;</th>
							<th scope="col" width="120px" class="text-center">Amount Applied</th>
							<th scope="col">Account Code</th>
							<th scope="col">Account Title</th>											
							<th scope="col" id="tblewt" style="display: none">EWT Code</th>
							<th scope="col">Type</th>
							<th scope="col">Cost Center</th>
							<th scope="col">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>											
			<br>

			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
											
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>

						<button type="button" class="btn btn-info btn-sm" tabindex="6" id="btnAPVIns" name="btnAPVIns">
							APV<br>(Insert)
						</button>
																			
										
						<button type="submit" class="btn btn-success btn-sm" tabindex="6">Save<br> (CTRL+S)</button>
									
					</td>
					<td align="right">
					&nbsp;

					</td>
				</tr>
				<tr>
					<td align="right">
						&nbsp;
					</td>
				</tr>
			</table>

		</fieldset>

	</form>

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

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("PayBill.php");

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#myChkModal').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			var custid = $("#txtcustid").val();
			showapvmod(custid)
		}
	  }
	});

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
			var tx = id.replace("napvewt","");

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

		$("#file-0").fileinput({
			theme: 'fa5',
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

		$('[data-toggle="popover"]').popover();
		
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
						}
					);
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
				return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);
				$("#txtpayee").val(item.value); 
				$("#hdncustewt").val(item.cewtcode);
				
				$("#selbasecurr").val(item.cdefaultcurrency).change();
				$("#basecurrval").val($("#selbasecurr").find(':selected').data('val'));
				$("#hidcurrvaldesc").val($("#selbasecurr").find(':selected').data('desc'));

				if($('#isNoRef').find(":selected").val() == 0){
					showapvmod(item.id);
				}
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
				$("#btnAPVIns").html("APV<br>(Insert)"); text
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
				//$("#paymntdesc").html("<b>Bank Name</b>");	
				//$("#paymntrefr").html("<b>Check No.</b>");

				$("#paymntdescdet").show();
				$("#paymntrefrdet").show();

				$("#paymntothrsdet").hide();
				$("#payrefothrsdet").hide();
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

			$.each(jQuery.parseJSON(xz), function() { 
				
				if(disval==this['noid']){
					$("#chknochek").text("With Reference: " + this['ctranno']);
					return false; // breaks
				}else{
					$("#chknochek").text("");
				}

			});
		});

		$('#btnaddline').on('click', function(e) {
				
			addrrdet("","",0,0,0,"","","",0);

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

		$('#APListHeader').html("AP List: "+$('#txtcust').val()+" ("+$('#selbasecurr').val()+")");

		$('#MyAPVList tbody').empty(); /* , typ: $("#selpaytype").val()  */

		$.ajax({
			url: 'th_APVlist.php',
			data: { code: custid},
			dataType: 'json',
			async:false,
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){
							
					if(item.ctranno=="NO"){
						alert("No Available Reference.");
										
						$('#txtcust').val("").change(); 
						$("#txtcustid").val("");
						$("#txtpayee").val("");

					}
					else{
				
						var chkbox = "";
						if(item.ccurrencycode!=$('#selbasecurr').val()){
							chkbox = "";
						}else{
							chkbox = "<input type='checkbox' value='"+index+"' name='chkSales[]'>";
						}

						$("<tr id=\"APV"+index+"\">").append(
							$("<td>").html(chkbox), 
							$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'> <input type='hidden' id='hdnAPVewt"+index+"' name='hdnAPVewt"+index+"' value='"+item.newtamt+"'>"),
							$("<td>").html(item.crefno+"<input type='hidden' id='APVrrno"+index+"' name='APVrrno"+index+"' value='"+item.crefno+"'>"),
							$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
							$("<td>").html(item.cacctno+"<input type='hidden' id='APVacctno"+index+"' name='APVacctno"+index+"' value='"+item.cacctno+"'>"),
							$("<td>").html(item.cacctdesc+"<input type='hidden' id='APVacctdesc"+index+"' name='APVacctdesc"+index+"' value='"+item.cacctdesc+"'>"),
							$("<td>").html(item.namount+" " +item.ccurrencycode+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'> <input type='hidden' id='APVpayed"+index+"' name='APVpayed"+index+"' value='"+item.napplied+"'>")
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

			addrrdet(a,b,d,e,owed,c,f,a2,g);
			
			totGross = parseFloat(totGross) + parseFloat(owed) ;

		});


		$('#myAPModal').modal('hide');
		$('#myAPModal').on('hidden.bs.modal', function (e) {

				$("#txtnGross").val(totGross);
				$("#txtnGross").autoNumeric('destroy');
				$("#txtnGross").autoNumeric('init',{mDec:2});
		
		});
		

	}

	function addrrdet(ctranno,ddate,namount,npayed,ntotowed,cacctno,cacctdesc,refno,ewtamt){

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
				var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied\" id=\"nApplied"+lastRow+"\" value=\""+ntotowed+"\" style=\"text-align:right\" readonly/></td>";
			}else{
				var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied\" id=\"nApplied"+lastRow+"\"  value=\""+ntotowed+"\" style=\"text-align:right\" /></td>";
			}

			var t = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"cacctdesc form-control input-sm\" name=\"cacctdesc\" id=\"cacctdesc"+lastRow+"\"  value=\""+cacctdesc+"\" "+ctyprefval+" placeholder=\"Account Title\"/></td>";	

			var t2 = "<td style=\"padding:2px\" align=\"center\" width=\"90px\" nowrap> <input type=\"text\" class=\"form-control input-sm\" name=\"cacctno\" id=\"cacctno"+lastRow+"\" value=\""+cacctno+"\" readonly placeholder=\"Account Code\"/></td>";	

			if ($('#isNoRef').find(":selected").val()==1) {

				//var t4 = "<td style=\"padding:2px\" align=\"center\" width=\"100px\" nowrap> <input type=\"text\" class=\"napvewt form-control input-sm\" name=\"napvewt\" id=\"napvewt"+lastRow+"\" value=\""+cacctno+"\" placeholder=\"EWT Code\"/></td>";

				var t4 = "<td style=\"padding:2px\" align=\"center\" width=\"100px\" nowrap> <select class='form-control input-sm' name=\"napvewt\" id=\"napvewt"+lastRow+"\" style=\"width: 100%\"><option value=\"\">&nbsp;</option> </select> </td>";
					
			}else{
				var t4 = "<input type=\"hidden\" name=\"napvewt\" id=\"napvewt"+lastRow+"\" value=\""+ewtamt+"\" />";

			}

			var t5 = "<td style=\"padding:2px\" align=\"center\" width=\"80px\" nowrap> <select name=\"selentrytyp\" id=\"selentrytyp"+lastRow+"\" class=\" form-control input-sm\" onchange=\"GoToCompOthers();\"><option value=\"Debit\">Debit</option><option value=\"Credit\">Credit</option></select></td>";

			var t3 = "<td style=\"padding:2px\" align=\"center\" width=\"10px\" nowrap> <button class=\"btn btn-xs btn-danger\" name=\"delRow\" id=\"delRow"+lastRow+"\"><i class='fa fa-times'></i></button></td>";	
			
			var xz = $("#costcenters").val();
			taxoptions = "";
			$.each(jQuery.parseJSON(xz), function() { 
				taxoptions = taxoptions + "<option value='"+this['nid']+"' data-cdesc='"+this['cdesc']+"'>"+this['cdesc']+"</option>";
			});

			var costcntr = "<td  width=\"100px\" style=\"padding:1px\"><select class='form-control input-sm' name=\"selcostcentr\" id=\"selcostcentr"+lastRow+"\">  <option value='' data-cdesc=''>NONE</option> " + taxoptions + " </select> </td>"; 
			
			$('#MyTable > tbody:last-child').append('<tr>'+ u + u2 + v + w + x + y + z + t2 + t + t4 + t5 + costcntr + t3 + '</tr>');

				$("#delRow"+lastRow).on("click", function(){
					$(this).closest('tr').remove();
					Reindx();

					GoToCompAmt();
					GoToComp();
				});

				if ($('#isNoRef').find(":selected").val()==1) {
					$("#napvewt"+lastRow).select2();
				}
			
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

			if(namez=="nAmount"){
				if(keyCode==38 && r!=1){//Up
					var z = parseInt(r) - parseInt(1);
					document.getElementById("nAmount"+z).focus();
				}
				
				if((keyCode==40 || keyCode==13) && r!=lastRow){//Down or ENTER
					var z = parseInt(r) + parseInt(1);
					document.getElementById("nAmount"+z).focus();
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
			
			//for (z=1; z<=lastRow; z++){
				//gross = parseFloat(gross) + parseFloat($("#nAmount"+z).val().replace(/,/g,''));
		//	}

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

</script>
