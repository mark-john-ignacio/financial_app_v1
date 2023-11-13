<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "OR_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$lallowNT = 0;
	$result=mysqli_query($con,"select * From company");								
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if($row['compcode'] == $company){
			$lallowNT =  $row['lallownontrade'];
		}
	}   


	$gettaxcd = mysqli_query($con,"SELECT * FROM `taxcode` where compcode='$company' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['ctaxcode'], 'ctaxdesc' => $row['ctaxdesc'], 'nrate' => $row['nrate']); 
		}
	}

	$getewtcd = mysqli_query($con,"SELECT * FROM `wtaxcodes` where compcode='$company' order By nident"); 
	if (mysqli_num_rows($getewtcd)!=0) {
		while($row = mysqli_fetch_array($getewtcd, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $row['ctaxcode'], 'cdesc' => $row['cdesc'], 'nrate' => $row['nrate'], 'cbase' => $row['cbase']); 
		}
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">
  <link href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../include/jquery-maskmoney.js" type="text/javascript"></script>
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

	<style>
		.tblnorm th, td{
			padding: 2px !important;
		}
	</style>

</head>

<body style="padding:5px; height:700px"> 
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">
<input type="hidden" value='<?=json_encode(@$arrewtlist)?>' id="hdnewtcodes"> 

<form action="OR_newsave2.php" name="frmOR" id="frmOR" method="post" enctype="multipart/form-data">
	<fieldset>
    <legend>Receive Payment</legend>	

				<ul class="nav nav-tabs">
					<li class="active"><a href="#items" data-toggle="tab">Receive Payment Details</a></li>
					<li><a href="#attc" data-toggle="tab">Attachments</a></li>
				</ul>

				<div class="tab-content">

					<div id="items" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

						<table width="100%" border="0">
							<tr>
								<tH width="150">Reference:</tH>
								<td style="padding:2px;">
									<div class="col-xs-12 nopadding">
											<div class="col-xs-5 nopadding">

												<select id="isNoRef" name="isNoRef" class="form-control input-sm selectpicker" onchange="changeDet();">
													<option value="0">With Sales Invoice</option>
													<option value="1">No Sales Invoice Reference</option>
												</select> 
											</div>
										</div>
								</td>
								<tH width="150">&nbsp;</tH>
								<td style="padding:2px;">&nbsp;</td>
							</tr>
							<tr>
								<tH width="150">Payor:</tH>
								<td valign="top" style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" readonly>
										</div>
										<div class="col-xs-7 nopadwleft">
											<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off">
										</div> 
									</div>        
								</td>
								<tH style="padding:2px">Receipt No.:</tH>
								<td style="padding:2px"><div class="col-xs-12 nopadding">
									<div class="col-xs-8 nopadding">
										<?php
										/*
											$ornostat = "";
											$sqlchk = mysqli_query($con,"select A.cornumber from (select cornumber from receipt where compcode='$company' UNION ALL Select cornumber from receipt_voids where compcode='$company') A Order By cornumber desc LIMIT 1");
											if (mysqli_num_rows($sqlchk)!=0) {
												while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
													$cORNOm = $row['cornumber'];
													$ornostat = "readonly";
													
													$cORNOm = $cORNOm + 1;
													
													if(strlen($cORNOm) <> strlen($row['cornumber'])){
														
														$varcnt = (int)strlen($row['cornumber']) - (int)strlen($cORNOm);
														
														for($zx=1; $zx<=$varcnt; $zx++){
															$cORNOm = "0".$cORNOm;
														}
													}
												}
											}else{
													$cORNOm = "";
													$ornostat = "";
											}
											*/ 
										?>
										<!-- value="<?//php echo $cORNOm;?>" <?//php echo $ornostat; ?> -->
										<input type="text" class="form-control input-sm" id="txtORNo" name="txtORNo" width="20px" required >
									</div>
									<!--<div class="col-xs-4 nopadwleft">
										<button type="button" class="btn btn-danger btn-sm" name="btnVoid" id="btnVoid">VOID OR</button>
									</div>-->
								</div></td>
							</tr>
							<tr>
								<tH width="150">Payment Method:</tH>
								<td valign="top" style="padding:2px">
																
									<div class="col-xs-12 nopadding">
										<div class="col-xs-6 nopadding">
											<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
													<option value="cash">Cash</option>
													<option value="cheque">Cheque</option>
													<option value="bank transfer">Bank Transfer</option>
													<option value="mobile payment">Mobile Payment</option>
													<option value="credit card">Credit Card</option>
													<option value="debit card">Debit Card</option>
												</select>
										</div>								
										<div class="col-xs-4 nopadwleft">
											<button type="button" class="btn btn-primary btn-sm" tabindex="6" style="width:100%" name="btnDet" id="btnDet">Details</button>
										</div>
									</div>
								
								</td>
								<tH><span style="padding:2px">Receipt Date:</span></tH>
								<td style="padding:2px;">
									<div class="col-xs-8 nopadding">
										<?php
											//get last date
											$ornostat = "";
													$sqlchk = mysqli_query($con,"select * from receipt where compcode='$company' Order By ddate desc LIMIT 1");
											if (mysqli_num_rows($sqlchk)!=0) {
												while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
													$dORLastDate = date("m/d/Y", strtotime($row['dcutdate']));
												}
											}else{
													$dORLastDate = date("m/d/Y");
											}
										?>
										<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dORLastDate; ?>"/>
									</div>   
								</td>
								
							</tr>
							<tr>
								<tH width="150">Currency:</tH>
								<td style="padding:2px;">
									<div class="row nopadding">
										<div class="col-xs-8 nopadding">
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
										<div class="col-xs-2" id="statgetrate" style="padding: 4px !important"> 																	
										</div>
									</div>
								</td>							
								<th style="padding:2px">Amount Received:</th>
								<td valign="top" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm text-right numeric" value="0.00" style="text-align:right;" autocomplete="off" required>
									</div>
								</td>
							<tr>
								<tH width="150">
									Deposit To Account (Dr):				
								</tH>
								<td style="padding:2px;" width="500">
									<?php
										$sqlchk = mysqli_query($con,"Select a.cacctno, b.cacctdesc, IFNULL(b.nbalance,0) as nbalance From accounts_default a left join accounts b on a.compcode=b.compcode and a.cacctno=b.cacctid where a.compcode='$company' and a.ccode='ORDEBCASH'");
										if (mysqli_num_rows($sqlchk)!=0) {
											while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
												$nDebitDef = $row['cacctno'];
												$nDebitDesc = $row['cacctdesc'];
												//$nBalance = $row['nbalance'];
											}
										}else{
											$nDebitDef = "";
											$nDebitDesc =  "";
											//$nBalance = 0.000;
										}
									?>
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcacctid" name="txtcacctid" class="form-control input-sm" readonly  value="<?php echo $nDebitDef;?>">
										</div>
										<div class="col-xs-7 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>"  autocomplete="off">
										</div> 
									</div>     
								</td>
								<th style="padding:2px">Amount Applied:</th>
								<td valign="top" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<input type="text" id="txtnApplied" name="txtnApplied" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off" readonly>
									</div>
								</td>
							</tr>													
							<tr>
								<tH rowspan='2' width="150">Memo:</tH>
								<td rowspan="3" valign="top" style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-10 nopadding">
											<textarea class="form-control" rows="1" id="txtremarks" name="txtremarks"></textarea>
										</div>
									</div>
								</td>
								<th style="padding:2px">Out of Balance:</th>
								<td valign="top" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<input type="text" id="txtnOutBal" name="txtnOutBal" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off" readonly>
									</div>
								</td>
							</tr>
							<tr>
								<th style="padding:2px">Receipt Type: </th>
								<td valign="top" style="padding:2px">
										<div class='col-xs-8 nopadding'>
											<select class='form-control input-sm' name="receipt" id="receipt">
												<option value="OR">Official Receipt</option>
												<option value="CR">Collection Receipt</option>
												<option value="AR">Acknowledgement Receipt</option>
											</select>
										</div>
								</td>
							</tr>
							<tr>
								<tH width="150">&nbsp;</tH>
								<th style="padding:2px">&nbsp;</th>
								<td valign="top" style="padding:2px">&nbsp;</td>
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
					<li class="active" id="liSales"><a href="#divSales">Sales Invoice</a></li>
					<li id="liOthers"><a href="#divOthers">Others</a></li>
				</ul>
			
    		<div class="tab-content">    

        	<div id="divSales" class="tab-pane fade in active" style="padding-top: 5px !important; padding-bottom: 5px">
								
							<div id="tableContainer" class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								border: 1px solid #919b9c;
								height: 400px;
								text-align: left; overflow: auto">

								<table id="MyTable" border="1" bordercolor="#CCCCCC" width="2350px" class="tblnorm">
									<thead>
										<tr>
											<th scope="col" width="100px" nowrap>Invoice No</th>
											<th scope="col" width="110px" class="text-center" nowrap>Date</th>
											<th scope="col" width="150px" class="text-center" nowrap>Amount</th>
											<th scope="col" width="150px" class="text-center" nowrap>DM</th>
											<th scope="col" width="150px" class="text-center" nowrap>CM</th>
											<th scope="col" width="150px" class="text-center" nowrap>Payments</th>
											<th scope="col" width="150px" class="text-center" nowrap>VAT Code</th>
											<th scope="col" width="150px" class="text-center" nowrap>VAT</th>
											<th scope="col" width="150px" class="text-center" nowrap>NetofVat</th>
											<th scope="col" width="250px" class="text-center" nowrap>EWTCode</th>
											<th scope="col" width="100px" class="text-center" nowrap>EWTAmt/Rate</th>                          
											<th scope="col" width="100px" class="text-center" nowrap>Total EWT</th>
											<th scope="col" width="150px" class="text-center" nowrap>Total Due</th>
											<th scope="col" width="150px" class="text-center" nowrap>Amt Applied</th>
											<th scope="col" width="80px" nowrap>&nbsp;Credit Acct Code</th>
											<th scope="col" width="250px" nowrap>&nbsp;Credit Acct Title</th>
											<th scope="col">&nbsp;</th>
										</tr>
									</thead>
									<tbody>
													
									</tbody>
								</table>
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
								<input type="hidden" name="hdnrowcntcmdm" id="hdnrowcntcmdm" value="0">
							</div>

					</div>
        
					<div id="divOthers" class="tab-pane fade">
						<div class="col-xs-12" style="padding-top: 5px !important; padding-bottom: 5px !important; padding-left: 0px !important;">
							<button type="button" class="btn btn-xs btn-info" id="btnaddOthers" onClick="addacct();" disabled>
								<i class="fa fa-plus"></i>&nbsp; Add New Line
							</button>
						</div>

            <div id="tblOtContainer" class="alt2" dir="ltr" style="
              margin: 0px;
              padding: 3px;
              border: 1px solid #919b9c;
              width: 100%;
              height: 400px;
              text-align: left;
              overflow: auto">

              <table width="100%" border="0" cellpadding="3" id="MyTblOthers">
                <thead>
                  <tr>
                    <th scope="col">Account No.</th>
                    <th scope="col">Account Title</th>
                    <th scope="col">Debit</th>
                    <th scope="col">Credit</th>
                    <th scope="col">&nbsp;</th>
                  </tr>
                </thead>
              </table>
              <input type="hidden" name="hdnOthcnt" id="hdnOthcnt" value="0">
            </div>
       	 	</div>
 				</div>

			<br>
			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td width="50%">					
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='OR.php';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>
						
						<?php
							if($lallowNT==1){
						?>
							<button type="button" onclick="getInvs();" id="btnaddSI" class="btn btn-info btn-sm">
								SI <br>(Insert) <span class="caret"></span>
							</button>
						<?php
							}else{
						?>
							<button type="button" class="btn btn-info btn-sm" id="btnaddSI" onclick="getInvs();">
								SI <br>(Insert)
							</button>
						<?php
							}
						?>
				
						<button type="button" class="btn btn-success btn-sm" tabindex="6" id="btnSave" onclick="chkform();">
							Save<br> (CTRL+S)
						</button>
					</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>

  </fieldset>

		<!--CASH DETAILS DENOMINATIONS -->
		<div class="modal fade" id="CashModal" role="dialog">
				<div class="modal-dialog">
						<div class="modal-content">
								<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h3 class="modal-title" id="cashheader">CASH DENOMINATION</h3>
								</div>
								<div class="modal-body" style="height:40vh">
								
									<div class="form-group">
												<div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">

											<table width="100%" border="0" class="table table-scroll table-condensed">
											<thead>
													<tr>
														<td align="center"><b>Denomination</b></td>
														<td align="center"><b>Pieces</b></td>
														<td align="center"><b>Amount</b></td>
													</tr>
											</thead>
											<tbody>
													<tr>
														<td align="center">1000</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1000' id='txtDenom1000' /></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1000' id='txtAmt1000' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">500</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom500' id='txtDenom500'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt500' id='txtAmt500' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">200</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom200' id='txtDenom200'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt200' id='txtAmt200' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">100</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom100' id='txtDenom100'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt100' id='txtAmt100' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">50</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom50' id='txtDenom50'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt50' id='txtAmt50' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">20</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom20' id='txtDenom20'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt20' id='txtAmt20' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">10</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom10' id='txtDenom10'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt10' id='txtAmt10' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">5</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom5' id='txtDenom5'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt5' id='txtAmt5' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">1</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1' id='txtDenom1'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1' id='txtAmt1' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">0.25</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom025' id='txtDenom025'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt025' id='txtAmt025' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">0.10</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom010' id='txtDenom010'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt010' id='txtAmt010' readonly/></div></td>
													</tr>
													<tr>
														<td align="center">0.05</td>
														<td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom005' id='txtDenom005'/></div></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt005' id='txtAmt005' readonly/></div></td>
													</tr>
												</tbody>
												</table>
										</div>
									</div>
								
								</div>
								<div class="modal-footer">
										
								</div>
						</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<!-- End Bootstrap modal -->

		<div class="modal fade" id="ChequeModal" role="dialog">
				<div class="modal-dialog">
						<div class="modal-content">
								<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h3 class="modal-title" id="chequeheader">CHEQUE DETAILS</h3>
								</div>
								<div class="modal-body">
								
											<table width="100%" border="0" class="table table-condensed">
													<tr>
														<td><b>Bank Name</b></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' placeholder="Input Bank Name"/></div></td>
													</tr>
													<tr>
														<td><b>Cheque Date</b></td>
														<td>
														<div class='col-sm-12'>
																<input type='text' class="form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate"/>

														</div>
														</td>
													</tr>
													<tr>
														<td><b>Cheque Number</b></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' placeholder="Input Cheque Number" /></div></td>
													</tr>
													<tr>
														<td><b>Cheque Amount</b></td>
														<td><div class='col-xs-12'><input type='text' class='numericchkamt form-control input-sm' name='txtCheckAmt' id='txtCheckAmt' placeholder="Input Cheque Amount" /></div></td>
													</tr>
											</table>
								
								</div>
								<div class="modal-footer">
										
								</div>
						</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


						<div class="modal fade" id="OthersModal" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h3 class="modal-title" id="chequeheader">TRANSACTION DETAILS</h3>
									</div>
									<div class="modal-body">
								
											<table width="100%" border="0" class="table table-condensed">
													<tr>
														<td><b>Payment Description</b></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtOTBankName' id='txtOTBankName' placeholder="Input Description"/></div></td>
													</tr>
													<tr>
														<td><b>Reference No.</b></td>
														<td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtOTRefNo' id='txtOTRefNo' placeholder="Input Reference No."/></div></td>
													</tr>
											</table>
								
									</div>
									<div class="modal-footer">
											
									</div>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div>

		<!-- add CM Module -->
					<div class="modal fade" id="MyAdjustmentModal" role="dialog">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close"  aria-label="Close"  onclick="chkCloseInfo();"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="invadjheader"> Additional AR Adjustment <button class="btn btn-sm btn-primary" name="btnaddcm" id="btnaddcm" type="button">Add</button></h4>           
								</div>
			
								<div class="modal-body">
									<input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2"> 
									<input type="hidden" name="txtdetsinoinfo" id="txtdetsinoinfo">  
									<input type="hidden" name="txthdnTYPAdj" id="txthdnTYPAdj"> 
									<input type="hidden" name="txthdnCMtxtbx" id="txthdnCMtxtbx"> 
					
									<table id="MyTableCMx" class="MyTable table table-sm" width="100%">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999" width="50px">Adj Type</th>
												<th style="border-bottom:1px solid #999">AP CM No.</th>
												<th style="border-bottom:1px solid #999">Date</th>
												<th style="border-bottom:1px solid #999">Amount</th>
												<th style="border-bottom:1px solid #999" width="200px">Remarks</th>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
											</tr>
										</thead>
										<tbody class="tbody">						
										</tbody>
									</table>
			
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
		<!-- /.modal -->

</form>

		<!-- Bootstrap modal INVOICES -->
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog modal-lg">
					<div class="modal-content">
							<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title" id="invheader">Invoice List</h3>
									<input name="invtyp" id="invtyp" type="hidden" value="" />
							</div>
							
							<div class="modal-body" style="height:40vh">
							
								<div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
										<table name='MyORTbl' id='MyORTbl' class="table table-scroll table-striped">
										<thead>
											<tr>
												<th style="text-align: center !important">
												<input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
												<th>Invoice No</th>
												<th>Sales Date</th>
												<th>Gross</th>
												<th>EWT</th>
												<th>VAT</th>
												<th>&nbsp;</th>
											</tr>
											</thead>
											<tbody>
											</tbody>
						</table>
									
							</div>
				</div>
				
							<div class="modal-footer">
									
									<button type="button" id="btnInsert" onclick="save();" class="btn btn-primary">Insert</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

							</div>
					</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<!-- End Bootstrap modal -->


<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  $("#btnSave").click();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("OR.php");

	  }
	});
	
	$(document).ready(function(){

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		$("#file-0").fileinput({
			theme: 'fa5',
			showUpload: false,
			showClose: false,
			allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
			overwriteInitial: false,
			maxFileSize:100000,
			maxFileCount: 5,
			browseOnZoneClick: true,
			fileActionSettings: { showUpload: false, showDrag: false,}
		});


		$("input.numericchkamt").autoNumeric('init',{mDec:2});
		$("input.numericint").autoNumeric('init',{mDec:0});
		
		$("input.numericchkamt, input.numericint").on("click focus", function () {
			$(this).select();
		});
		
		$('#datetimepicker4, #txtChekDate, #date_delivery').datetimepicker({
		  format: 'MM/DD/YYYY'
		});

  	$('#frmOR').on('keyup keypress', function(e) {
  	  var keyCode = e.keyCode || e.which;
  	  if (keyCode === 13) { 
  			e.preventDefault();
  			return false;
  		}
		});
										
		$('.numericint').keydown(function (e) {
		
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input').focus();
 
        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input').focus();
 
        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
 
        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        }
		});
	
		$('.numericint').keyup(function (e) {
		
			var str = $(this).attr('name');
			var res = str.substring(0, 8);
			var valz = str.substring(8);
			
			if(valz=="025"){
				var val2=0.25;
			}
			else if(valz=="010"){
				var val2=0.10;
			}
			else if(valz=="005"){
				var val2=0.05;
			}
			else{
				var val2 = valz;
			}
			
			var value = $(this).val();
			if(res=="txtDenom"){
				
				var x = parseFloat(val2) * parseFloat(value);	
				//alert("#txtAmt"+valz+" = "+x);	
				if(value!=""){		
					$("#txtAmt"+valz).val(x.toFixed(2));
				}
				else{
					$("#txtAmt"+valz).val("");
				}
				
			}

		});
		
		$("#txtCheckAmt").on('keyup', function() {
			//if($("#selpaytype").val() == "None"){
				$('#txtnGross').val($(this).val());
			//}
		});

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
					$('#txtacctbal').val(map[item].balance);
					return item;
			}
		
		});
			
		$('#txtcust').typeahead({
		
			items: 10,
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
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);

				$("#selbasecurr").val(item.cdefaultcurrency).change();
				$("#basecurrval").val($("#selbasecurr").find(':selected').data('val'));
				$("#hidcurrvaldesc").val($("#selbasecurr").find(':selected').data('desc'));
			}
		});

		document.getElementById('txtcust').focus();
		
		$("#txtcust").on('blur', function() {
			if($('#txtcustid').val() != "" && $('#txtcustid').val() != ""){
				$('#txtcust').attr('readonly', true);
			}
		}); 
		
		$("#selpayment").on("change", function(){
			$('#txtnGross').val('0.00');
			
			if ($(this).val() == "Cheque"){
				$('#txtnGross').attr('readonly', true);
			}
			else{
				$('#txtnGross').attr('readonly', false);
			}


			var valz = $(this).val();
			var codez = "";
			
			if(valz=="Cash"){
				codez = "ORDEBCASH";
			}
			else if(valz=="Cheque"){
				codez = "ORDEBCHK";
			}
			//alert(valz);
			
			$.ajax ({
				url: "../th_parameter.php",
				data: { id: codez },
				async: false,
				dataType: "json",
				success: function( data ) {
												
					console.log(data);
					$.each(data,function(index,item){
						$('#txtcacct').val(item.name);
						$('#txtcacctid').val(item.id);
						$('#txtacctbal').val(item.balance);
					});
							
												
				}
			});

		});	
		

		$("#btnDet").on('click', function() {
			if($('#selpayment').val() == "cash"){
				$('#CashModal').modal('show');
			}else if($('#selpayment').val() == "cheque"){
				$('#ChequeModal').modal('show');
			}else{
				$('#OthersModal').modal('show');
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

		$("#btnVoid").on("click", function(){
			var rems = prompt("Please enter your reason...", "");
			if (rems == null || rems == "") {
				alert("No remarks entered!\nCheque cannot be void!");
			}
			else{
				//alert( "id="+ $("#txtBankName").val()+"&chkno="+ $("#txtCheckNo").val()+"&rem="+ rems);
						$.ajax ({
						url: "OR_voidorno.php",
						data: { orno: $("#txtORNo").val(), rem: rems },
						async: false,
						success: function( data ) {
							if(data.trim()!="False"){
								$("#txtORNo").val(data.trim());
								$("#btnVoid").attr("disabled", false);
							}
						}
						});

			}
		});

		$("#btnaddcm").on("click", function(){			

			var xsino = $("#txtdetsinoinfo").val();
			var xadjtype = $("#txthdnTYPAdj").val();

			AddRefAdj(xadjtype,xsino,"","","","");
		
		});

		$("#isNoRef").change(function() {
			if($(this).find(":selected").val()==1) { 
				$("#btnaddSI").attr("disabled", true);  
				$("#btnaddOthers").attr("disabled", false); 

				$("#liSales").attr("class", "");
				$("#liOthers").attr("class", "active");
				
				$("#divSales").attr("class", "tab-pane fade");
				$("#divOthers").attr("class", "tab-pane fade in active");
			}else{
				$("#btnaddSI").attr("disabled", false);
				$("#btnaddOthers").attr("disabled", true); 

				$("#liSales").attr("class", "active");
				$("#liOthers").attr("class", "");
				
				$("#divSales").attr("class", "tab-pane fade in active");
				$("#divOthers").attr("class", "tab-pane fade");
			}
		});

		$('body').on('focus',".accountsname", function(){
			var $input = $(".accountsname");

			var id = $(document.activeElement).attr('id');
			var xname = $(document.activeElement).data('nme');	

			var numid = id.replace(xname,"");

			//alert(xname +" : "+ numid);

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

					$('#'+xname+numid).val(item.name).change(); 
					$("#"+xname+"ID"+numid).val(item.id);

				}
			});

		});
	
		$("#txtnGross").on("keyup", function(){
			if($("#isNoRef").find(":selected").val()==0) { 
				computeGross();
			}else{
				computeGrossOthers();
			}
		});

		$("#selbasecurr").on("change", function (){
	
			var dval = $(this).find(':selected').attr('data-val');
			var ddesc = $(this).find(':selected').attr('data-desc');
	
			$("#basecurrval").val(dval);
			$("#hidcurrvaldesc").val(ddesc);
			$("#statgetrate").html("");
				
		});

	});

	/*
	function deleteRow(r) {
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.rowIndex;

		document.getElementById('MyTable').deleteRow(i);
		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;
		
			for (z=i+1; z<=lastRow; z++){
				var tempsalesno = document.getElementById('txtcSalesNo' + z);
										
				var tempgross = document.getElementById('txtSIGross' + z);
				var tempdebit = document.getElementById('txtndebit' + z);
				var tempcredit = document.getElementById('txtncredit' + z);
				var temppaymnts = document.getElementById('txtnpayments' + z);

				var tempvcode = document.getElementById('txtnvatcode' + z);
				var tempvrate = document.getElementById('txtnvatrate' + z);
				var tempvamt = document.getElementById('txtvatamt' + z);
				var tempvnetamt = document.getElementById('txtnetvat' + z);
				var tempvcodeorig = document.getElementById('txtnvatcodeorig' + z);

				var tempewtcode= document.getElementById('txtnEWT' + z);
				var tempewtrate = document.getElementById('txtnEWTRate' + z);
				var tempewtamt = document.getElementById('txtnEWTAmt' + z);
				var tempewtcodeorig= document.getElementById('txtnEWTorig' + z);

				var tempdue= document.getElementById('txtDue' + z);
				var tempapplies = document.getElementById('txtApplied' + z);
				var tempsalesacctno = document.getElementById('txtcSalesAcctNo' + z); 

				var x = z-1;
				tempsalesno.id = "txtcSalesNo" + x;
				tempsalesno.name = "txtcSalesNo" + x;	

				tempgross.id = "txtSIGross" + x;
				tempgross.name = "txtSIGross" + x;

				tempdebit.id = "txtndebit" + x;
				tempdebit.name = "txtndebit" + x;
				tempcredit.id = "txtncredit" + x;
				tempcredit.name = "txtncredit" + x;
				temppaymnts.id = "txtnpayments" + x;
				temppaymnts.name = "txtnpayments" + x;

				tempvcode.id = "txtnvatcode" + x;
				tempvcode.name = "txtnvatcode" + x;
				tempvrate.id = "txtnvatrate" + x;
				tempvrate.name = "txtnvatrate" + x;
				tempvamt.id = "txtvatamt" + x;
				tempvamt.name = "txtvatamt" + x;
				tempvnetamt.id = "txtnetvat" + x;
				tempvnetamt.name = "txtnetvat" + x;
				tempvcodeorig.id = "txtnvatcodeorig" + x;
				tempvcodeorig.name = "txtnvatcodeorig" + x;

				tempewtcode.id = "txtnEWT" + x;
				tempewtcode.name = "txtnEWT" + x + "[]";
				tempewtrate.id = "txtnEWTRate" + x;
				tempewtrate.name = "txtnEWTRate" + x;
				tempewtamt.id = "txtnEWTAmt" + x;
				tempewtamt.name = "txtnEWTAmt" + x;
				tempewtcodeorig.id = "txtnEWTorig" + x;
				tempewtcodeorig.name = "txtnEWTorig" + x;

				tempdue.id = "txtDue" + x;
				tempdue.name = "txtDue" + x;
				tempapplies.id = "txtApplied" + x;
				tempapplies.name = "txtApplied" + x;
				tempsalesacctno.id = "txtcSalesAcctNo" + x;
				tempsalesacctno.name = "txtcSalesAcctNo" + x;
				
				//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

			}

		computeGross();
	}
	*/

	function getInvs(){
	
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			//clear table body if may laman
			$('#MyORTbl tbody').empty();
			$('#invtyp').val();
			
			//get salesno na selected na
			var y;
			var salesnos = "";
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			  if(y>1){
				  salesnos = salesnos + ",";
			  }
				salesnos = salesnos + $('#txtcSalesNo'+y).val();
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#invheader').html("Invoice List: " + $('#txtcust').val() + " (" + $('#selbasecurr').val() + ")")
			
			//alert("th_orlist.php?x="+x+"&y="+salesnos+"&typ="+typ+"&curr="+$('#selbasecurr').val());
			$.ajax({
        url: 'th_orlist.php',
				data: { x:x, y:salesnos, curr:$('#selbasecurr').val(), type: $('#receipt').val() },
        dataType: 'json',
        method: 'post',
        success: function (data) {
          // var classRoomsTable = $('#mytable tbody');
          console.log(data);
          $.each(data,function(index,item){

						var chkbox = "";
						if(item.ccurrencycode!=$('#selbasecurr').val()){
							chkbox = "";
						}else{
							chkbox = "<input type='checkbox' value='"+item.csalesno+"' name='chkSales[]' data-dm='"+item.cdm+"' data-cm='"+item.ccm+"' data-payment='"+item.npayment+"' data-vatcode='"+item.ctaxcode+"' data-vatrate='"+item.vatrate+"' data-vat='"+item.cvatamt+"' data-netvat='"+item.cnetamt+"' data-ewtcode='"+item.cewtcode+"' data-ewtrate='"+item.newtrate+"' data-amt='"+item.ngross+"' data-acctid='"+item.cacctno+"' data-acctdesc='"+item.ctitle+"' data-cutdate='"+item.dcutdate+"'>";
						}

            $("<tr>").append(
							$("<td align='center'>").html(chkbox),
              $("<td>").text(item.csalesno),
              $("<td>").text(item.dcutdate),
							$("<td>").text(item.ngross + " " + item.ccurrencycode),
							$("<td>").text(item.cewtcode),
							$("<td>").text(item.ctaxcode)
            ).appendTo("#MyORTbl tbody");

          });
        },
        error: function (textStatus)
				{
					// if(errorThrown!="Unexpected end of JSON input"){
					// }
					console.log(textStatus)
				}
      });
			
			$('#myModal').modal('show');
			
		}
	}

	function save(){

		var icount = 0;
		var rcnt = 0;
		
		$("input[name='chkSales[]']:checked").each( function () {
			icount++;
				var tbl = document.getElementById('MyTable').getElementsByTagName('tbody')[0];

				var tranno = $(this).val();
				var dcutdate = $(this).data("cutdate");
				var ngross = $(this).data("amt");
				var ndm = $(this).data("dm");
				var ncm = $(this).data("cm");
				var npayments = $(this).data("payment");
				var nvat = $(this).data("vat");
				var vatcode = $(this).data("vatcode"); 
				var vatrate = $(this).data("vatrate");
				var nnetvat = $(this).data("netvat");
				var newtcode = $(this).data("ewtcode");
				var newtrate = $(this).data("ewtrate");
				var newtamt = 0; 

				var acctcode = $(this).data("acctid");
				var acctdesc = $(this).data("acctdesc");
				var ewtdesc = "";
			
				var lastRow = tbl.rows.length + 1;							
				var z=tbl.insertRow(-1);

				var a=z.insertCell(-1);
				a.innerHTML ="<div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo' id='txtcSalesNo"+lastRow+"' value='"+tranno+"' />"+tranno+"</div>";
										
				var b=z.insertCell(-1);
				b.align = "center";
				b.innerHTML = dcutdate;
											
				var c=z.insertCell(-1);
				c.align = "right";
				c.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs text-right' name='txtSIGross' id='txtSIGross"+lastRow+"' value='"+ngross+"' readonly /></div>";

				var d=z.insertCell(-1); 
				d.align = "right";
				d.innerHTML = "<div class=\"input-group\"><input type='text' name='txtndebit' id='txtndebit"+lastRow+"' class=\"numeric form-control input-xs\" value=\""+ndm+"\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-xs\" name=\"btnadddm\" id=\"btnadddm"+lastRow+"\" type=\"button\" onclick=\"addCM('DM','"+tranno+"','txtndebit"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div>";
											
				var e=z.insertCell(-1);
				e.align = "right";
				e.innerHTML = " <div class=\"input-group\"><input type='text' name='txtncredit' id='txtncredit"+lastRow+"' class=\"numeric form-control input-xs\" value=\""+ncm+"\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-xs\" name=\"btnaddcm\" id=\"btnaddcm"+lastRow+"\" type=\"button\" onclick=\"addCM('CM','"+tranno+"','txtncredit"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div>";
											
				var f=z.insertCell(-1); 
				f.align = "right";
				f.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnpayments' id='txtnpayments"+lastRow+"' value='"+npayments+"' readonly=\"true\" />";

				/*
				var xz = $("#hdntaxcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 

					if(vatcode==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
				});

				var c1=z.insertCell(-1);
				c1.align = "right";
				c1.innerHTML = "<select class='form-control input-xs' name=\"txtnvatcode\" id=\"txtnvatcode"+lastRow+"\" readonly> " + taxoptions + " </select>";
				*/

				var c1=z.insertCell(-1);
				c1.align = "right";
				c1.innerHTML = "<input type='text' class='form-control input-xs text-right' name=\"txtnvatcode\" id=\"txtnvatcode"+lastRow+"\" value='"+vatcode+"' readonly /> <input type='hidden' name=\"txtnvatrate\" id=\"txtnvatrate"+lastRow+"\" value='"+vatrate+"' /> <input type='hidden' name=\"txtnvatcodeorig\" id=\"txtnvatcodeorig"+lastRow+"\" value='"+vatcode+"' />";

				var c2=z.insertCell(-1);
				c2.align = "right";
				c2.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtvatamt' id='txtvatamt"+lastRow+"' value='"+nvat+"' readonly />";
											
				var c3=z.insertCell(-1);
				c3.align = "right";
				c3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnetvat' id='txtnetvat"+lastRow+"' value='"+nnetvat+"' readonly />"; 
				
				$ifrdonly = "";
				$ifrdonlyint = 0;
				if(newtcode!=="none" && newtcode!==""){
					$ifrdonly = "readonly";
					$ifrdonlyint = 1;
				}

				
				//l.innerHTML = "<input type='text' class='ewtcode form-control input-xs' placeholder='EWT Code' name='txtnEWT"+lastRow+"' id='txtnEWT"+lastRow+"' autocomplete=\"off\" value='"+newtcode+"' "+$ifrdonly+"/>";

				var xz = $("#hdnewtcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 

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

					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' data-base='"+this['cbase']+"' "+isselctd+">"+this['ctaxcode']+ "("+this['nrate']+"%)" + "</option>";
				});
				
				var l=z.insertCell(-1); 
				l.innerHTML = "<select name='txtnEWT[]' id='txtnEWT"+lastRow+"' class='select2' multiple='multiple' style='width: 100%'> "+ taxoptions +" </select> <input type='hidden' name='hdnewtgiven' id='hdnewtgiven"+lastRow+"' value='"+$ifrdonlyint+"' /> <input type='hidden' name='txtnEWTorig' id='txtnEWTorig"+lastRow+"' value='"+newtcode+"' /> <input type='hidden' name='txtnEWTRate' value=\""+newtrate+"\" id='txtnEWTRate"+lastRow+"' />";

					newtrateStr = newtrate.toString();

						var splitString = newtrateStr.split(';');
						for (var i = 0; i < splitString.length; i++) {
							var stringPart = splitString[i];
							if (stringPart != 0 && stringPart != ""){
								if(i > 0){
									ewtdesc = ewtdesc + ";";
								}
								$jx = parseFloat(nnetvat)*(parseFloat(stringPart)/100);
								newtamt = newtamt + $jx;
								ewtdesc = ewtdesc + stringPart + "% - " + $jx.toFixed(2);
							}
						}


				var l2=z.insertCell(-1);
				l2.innerHTML = "<div id='txtnEWTPer"+lastRow+"' class='text-right'> "+ewtdesc.replace(";","<br>")+" </div>";
											
				var l3=z.insertCell(-1);
				l3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' placeholder='EWT Amt' name='txtnEWTAmt'  value=\""+newtamt+"\" id='txtnEWTAmt"+lastRow+"' readonly=\"true\" />";

					if(parseFloat(npayments)!==0){
						var ntotdue = (parseFloat(nnetvat) + parseFloat(nvat)) - parseFloat(ncm) - parseFloat(newtamt);
					}else{
						var ntotdue = parseFloat(ngross) - parseFloat(ncm) - parseFloat(npayments) - parseFloat(newtamt);
					}
												
				var g=z.insertCell(-1);
				g.align = "right";
				g.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtDue' id='txtDue"+lastRow+"' value='"+ntotdue+"' readonly=\"true\" />";
											
				var h=z.insertCell(-1);
				h.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtApplied' id='txtApplied"+lastRow+"' value='"+ntotdue+"' style='text-align:right' autocomplete=\"off\" />";
										
				var j=z.insertCell(-1);
				j.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='accountscode form-control input-xs' name='txtcSalesAcctTitleID' id='txtcSalesAcctTitleID"+lastRow+"' value='"+acctcode+"' readonly/></div>";

				var j2=z.insertCell(-1);
				j2.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='accountsname form-control input-xs' name='txtcSalesAcctTitle' id='txtcSalesAcctTitle"+lastRow+"' value='"+acctdesc+"' autocomplete=\"off\" data-nme=\"txtcSalesAcctTitle\" /></div>";
											
				var k=z.insertCell(-1);
				k.innerHTML = "<div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' name='row_delete' id='row_"+lastRow+"_delete' value='delete'/></div>";

				$("#row_"+lastRow+"_delete").on("click", function(){
					$(this).closest('tr').remove(); 
					ReIndexMyTable(tranno);
				});
				
										
				//var varnnet = item.nnet;
				//var varngrs = item.ngross;	
				$("#txtnEWT"+lastRow).select2();
				$("#txtnEWT"+lastRow).on("change", function(){
					computeDue(this);
					computeGross();
				});

				$("input.numeric").autoNumeric('init',{mDec:2});
				$("input.numeric").on("click focus", function () {
					$(this).select();
				});
												
				$("input.numeric").on("keyup", function (e) {
					setPosi($(this).attr('name'),e.keyCode,'MyTable');
					computeGross();
				});
										
				computeGross(); 

				if(parseFloat(ncm)!==0){
					getrefreturn(tranno);
				}
			
			
		});
		
		if(icount==0){
			alert("No Invoice is selected!")
		}
		
		$('#myModal').modal('hide');
		
	}

	function computeDue(selewt){

		lastRow = selewt.attributes["id"].value;
		lastRow = lastRow.replace("txtnEWT","");
		lastRow = lastRow.replace("[]","");

		///	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		//	var lastRow = tbl.length-1;
		//	if(lastRow!=0){
		//		var x = 0;
				
		//		for (z=1; z<=lastRow; z++){
		//			var varngrs = $("#txtvatamt"+lastRow).val().replace(/,/g,'');
		//			var varngrs = $("#txtSIGross"+lastRow).val().replace(/,/g,'');
					var varngrs = $("#txtSIGross"+lastRow).val().replace(/,/g,'');
		//		}

		//	}

		varnnet =  $("#txtnetvat"+lastRow).val().replace(/,/g,'');
		ndue = $("#txtDue"+lastRow).val().replace(/,/g,'');

		xcb = 0;
		var len = selewt.options.length;
		for (var i = 0; i < len; i++) {
			opt = selewt.options[i];

			if (opt.selected) {
				//alert(opt.value+ " : " + opt.dataset.rate + " : " + opt.dataset.base);

				if(opt.dataset.base=="NET"){
					xcb = xcb + parseFloat(varnnet)*(opt.dataset.rate/100);
				}else{
					xcb = xcb + parseFloat(varngrs)*(opt.dataset.rate/100);
				}

			}
		}

												
		$("#txtnEWTAmt"+lastRow).val(xcb);
		xcbdue = varngrs - xcb;
												
		$("#txtDue"+lastRow).val(xcbdue);
		$("#txtApplied"+lastRow).val(xcbdue);

		$("#txtnEWTAmt"+lastRow).autoNumeric('destroy');
		$("#txtnEWTAmt"+lastRow).autoNumeric('init',{mDec:2});

		$("#txtDue"+lastRow).autoNumeric('destroy');
		$("#txtDue"+lastRow).autoNumeric('init',{mDec:2});
												
		$("#txtApplied"+lastRow).autoNumeric('destroy');
		$("#txtApplied"+lastRow).autoNumeric('init',{mDec:2});

	}

	function ReIndexMyTable(tranno){
		$("#MyTable > tbody > tr").each(function(index) {   
			tx2 = index+1;
			//$(this).find('input[type=hidden][name="txtcSalesNo"]').attr("name","txtcSalesNo"+tx2);
			$(this).find('input[type=hidden][name="txtcSalesNo"]').attr("id","txtcSalesNo"+tx2);

			//$(this).find('input[name="txtSIGross"]').attr("name","txtSIGross"+tx2);
			$(this).find('input[name="txtSIGross"]').attr("id","txtSIGross"+tx2);

			//$(this).find('input[name="txtndebit"]').attr("name","txtndebit"+tx2);
			$(this).find('input[name="txtndebit"]').attr("id","txtndebit"+tx2);
			$(this).find('button[name="btnadddm"]').attr("id","btnadddm"+tx2);
			$(this).find('input[name="txtndebit"]').attr("onclick","addCM('DM','"+tranno+"','txtndebit"+tx2+"')");

			//$(this).find('input[name="txtncredit"]').attr("name","txtncredit"+tx2);
			$(this).find('input[name="txtncredit"]').attr("id","txtncredit"+tx2);
			$(this).find('button[name="btnaddcm"]').attr("id","btnaddcm"+tx2);
			$(this).find('input[name="txtncredit"]').attr("onclick","addCM('CM','"+tranno+"','txtncredit"+tx2+"')");

			//$(this).find('input[name="txtnpayments"]').attr("name","txtnpayments"+tx2);
			$(this).find('input[name="txtnpayments"]').attr("id","txtnpayments"+tx2);

			//$(this).find('input[name="txtnvatcode"]').attr("name","txtnvatcode"+tx2);
			$(this).find('input[name="txtnvatcode"]').attr("id","txtnvatcode"+tx2);

			//$(this).find('input[type=hidden][name="txtnvatrate"]').attr("name","txtnvatrate"+tx2);					
			$(this).find('input[type=hidden][name="txtnvatrate"]').attr("id","txtnvatrate"+tx2);

			//$(this).find('input[type=hidden][name="txtnvatcodeorig"]').attr("name","txtnvatcodeorig" + tx2);
			$(this).find('input[type=hidden][name="txtnvatcodeorig"]').attr("id","txtnvatcodeorig" + tx2);

			//$(this).find('input[name="txtvatamt"]').attr("name","txtvatamt" + tx2);
			$(this).find('input[name="txtvatamt"]').attr("id","txtvatamt" + tx2);

			//$(this).find('input[name="txtvatamt"]').attr("name","txtvatamt" + tx2);
			$(this).find('input[name="txtvatamt"]').attr("id","txtvatamt" + tx2);

			//$(this).find('input[name="txtnetvat"]').attr("name","txtnetvat" + tx2);
			$(this).find('input[name="txtnetvat"]').attr("id","txtnetvat" + tx2);

			//$(this).find('select[name="txtnetvat"]').attr("name","txtnetvat" + tx2);
			$(this).find('select[name="txtnetvat"]').attr("id","txtnetvat" + tx2);

			//$(this).find('select[name="txtnEWT"]').attr("name","txtnEWT" + tx2);
			$(this).find('select[name="txtnEWT[]"]').attr("id","txtnEWT" + tx2);

			//$(this).find('input[type=hidden][name="hdnewtgiven"]').attr("name","hdnewtgiven" + tx2);
			$(this).find('input[type=hidden][name="hdnewtgiven"]').attr("id","hdnewtgiven" + tx2);

			//$(this).find('input[type=hidden][name="txtnEWTorig"]').attr("name","txtnEWTorig" + tx2);
			$(this).find('input[type=hidden][name="txtnEWTorig"]').attr("id","txtnEWTorig" + tx2);

			//$(this).find('input[type=hidden][name="txtnEWTRate"]').attr("name","txtnEWTRate" + tx2);
			$(this).find('input[type=hidden][name="txtnEWTRate"]').attr("id","txtnEWTRate" + tx2);

			//$(this).find('input[name="txtnEWTAmt"]').attr("name","txtnEWTAmt" + tx2);
			$(this).find('input[name="txtnEWTAmt"]').attr("id","txtnEWTAmt" + tx2);

			//$(this).find('input[name="txtDue"]').attr("name","txtDue" + tx2);
			$(this).find('input[name="txtDue"]').attr("id","txtDue" + tx2);

			//$(this).find('input[name="txtApplied"]').attr("name","txtApplied" + tx2);
			$(this).find('input[name="txtApplied"]').attr("id","txtApplied" + tx2);

			//$(this).find('input[name="txtcSalesAcctTitle"]').attr("name","txtcSalesAcctTitle" + tx2);
			$(this).find('input[name="txtcSalesAcctTitle"]').attr("id","txtcSalesAcctTitle" + tx2);

			//$(this).find('input[type=hidden][name="txtcSalesAcctNo"]').attr("name","txtcSalesAcctNo" + tx2);
			$(this).find('input[type=hidden][name="txtcSalesAcctNo"]').attr("id","txtcSalesAcctNo" + tx2);

			//$(this).find('input[name="row_delete"]').attr("name","row_"+tx2+"_delete");
			$(this).find('input[name="row_delete"]').attr("id","row_"+tx2+"_delete");

		});
	}

	function computeGross(){
		//alert("Hello";)
		var tot = 0;
		var tot2 = 0;
		var tot3 = 0;
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		if(lastRow!=0){
			var x = 0;
			
			for (z=1; z<=lastRow; z++){
				x = $("#txtApplied" + z).val().replace(/,/g,'');
				
				x = x.replace(",","");
				if(x!=0 && x!=""){
					tot = parseFloat(x) + parseFloat(tot);	
				}

				// xEWT = document.getElementById('txtnEWT' + z).value;
					
				//  xEWT = xEWT.replace(",","");
				//  if(xEWT!=0 && xEWT!=""){
				//	totEWT = parseFloat(xEWT) + parseFloat(totEWT);  
				//  }
			}
		}
		
		
		//alert(parseFloat(tot2));
		/*
		var tbl3 = document.getElementById('MyTblOthers').getElementsByTagName('tr');
		var lastRow3 = tbl3.length-1;
		var totDR = 0;
		var totCR = 0;
		var tot3 = 0;
		
		if(lastRow3!=0){
			var x3DR = 0;
			var x3CR = 0;
			
			for (z3=1; z3<=lastRow3; z3++){
				x3DR = document.getElementById('txtnotDR' + z3).value;
				x3CR = document.getElementById('txtnotCR' + z3).value;
				
				x3DR = x3DR.replace(",","");
				if(x3DR!=0 && x3DR!=""){
					totDR = parseFloat(x3DR) + parseFloat(totDR);	
				}
				
				x3CR = x3CR.replace(",","");
				if(x3CR!=0 && x3CR!=""){
					totCR = parseFloat(x3CR) + parseFloat(totCR);	
				}
			}
			
			tot3 = parseFloat(totCR) - parseFloat(totDR);	
		}
		*/
		
		
		$("#txtnApplied").val(tot);
		$("#txtnApplied").autoNumeric('destroy');
		$("#txtnApplied").autoNumeric('init',{mDec:2});

		var outbalyy = parseFloat($("#txtnGross").val().replace(/,/g,'')) - parseFloat(tot);
		$("#txtnOutBal").val(outbalyy);

		$("#txtnOutBal").autoNumeric('destroy');
		$("#txtnOutBal").autoNumeric('init',{mDec:2, vMin:-99999999999999999.99});

	}

	function computeGrossOthers(){
		//alert("Hello";)
		var tbl3 = document.getElementById('MyTblOthers').getElementsByTagName('tr');
		var lastRow3 = tbl3.length-1;
		var totDR = 0;
		var totCR = 0;
		var tot = 0;
		
		if(lastRow3!=0){
			var x3DR = 0;
			var x3CR = 0;
			
			for (z3=1; z3<=lastRow3; z3++){
				x3DR = $('#txtnotDR' + z3).val().replace(/,/g,'');
				x3CR = $('#txtnotCR' + z3).val().replace(/,/g,'');
				
				x3DR = x3DR.replace(",","");
				if(x3DR!=0 && x3DR!=""){
					totDR = parseFloat(x3DR) + parseFloat(totDR);	
				}
				
				x3CR = x3CR.replace(",","");
				if(x3CR!=0 && x3CR!=""){
					totCR = parseFloat(x3CR) + parseFloat(totCR);	
				}
			}
			
			tot = parseFloat(totCR) - parseFloat(totDR);	
		}
		
		$("#txtnApplied").val(tot);
		$("#txtnApplied").autoNumeric('destroy');
		$("#txtnApplied").autoNumeric('init',{mDec:2});

		var outbalyy = parseFloat($("#txtnGross").val().replace(/,/g,'')) - parseFloat(tot);
		$("#txtnOutBal").val(outbalyy);

		$("#txtnOutBal").autoNumeric('destroy');
		$("#txtnOutBal").autoNumeric('init',{mDec:2, vMin:-99999999999999999.99});

	}

	function setPosi(nme,keyCode,tbl){
		var r = nme.replace(/\D/g,'');
		var namez = nme.replace(/[0-9]/g, '');
		
		//alert(nme+";"+keyCode);
		var tbl = document.getElementById(tbl).getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		//
		//if(namez=="txtApplied"){
			//alert(keyCode);
			if(keyCode==38 && r!=1){//Up
				var z = parseInt(r) - parseInt(1);
				document.getElementById(namez+z).focus();
			}
			
			if((keyCode==40 || keyCode==13) && r!=lastRow){//Down or ENTER
				var z = parseInt(r) + parseInt(1);
				document.getElementById(namez+z).focus();
			}
			
		//}

	}

	function addacct(){

		var tbl = document.getElementById('MyTblOthers').getElementsByTagName('tr');
		var lastRow = tbl.length;

		var a=document.getElementById('MyTblOthers').insertRow(-1);
		
		var v=a.insertCell(0);
			v.style.width = "150px";
			v.style.padding = "1px";
		var w=a.insertCell(1);
			w.style.padding = "1px";
		var xDR=a.insertCell(2);
			xDR.style.width = "100px";
			xDR.style.padding = "1px";
		var xCR=a.insertCell(3);
			xCR.style.width = "100px";
			xCR.style.padding = "1px";
		var y=a.insertCell(4);
			y.style.width = "50px";
			y.style.padding = "1px";

		v.innerHTML = "<input type='text' name=\"txtacctitleID"+lastRow+"\" id=\"txtacctitleID"+lastRow+"\" class=\"form-control input-xs\" placeholder=\"Enter Acct Code...\" readonly>";
		w.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"accountsname form-control input-xs\" placeholder=\"Search Acct Desc...\" autocomplete=\"off\" data-nme=\"txtacctitle\">";
		xDR.innerHTML = "<input type='text' name=\"txtnotDR"+lastRow+"\" id=\"txtnotDR"+lastRow+"\" class=\"numericNO form-control input-xs\" style=\"text-align:right\" value=\"0.00\" required autocomplete=\"off\">";
		xCR.innerHTML = "<input type='text' name=\"txtnotCR"+lastRow+"\" id=\"txtnotCR"+lastRow+"\" class=\"numericNO form-control input-xs\" style=\"text-align:right\" value=\"0.00\" required autocomplete=\"off\">";
		y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row3_"+lastRow+"_delete' value='delete' onClick=\"deleteRow3(this);\"/>";

		//alert(lastRow);
		$("#txtacctitle"+lastRow).focus();

		$("input.numericNO").autoNumeric('init',{mDec:2});
		$("input.numericNO").on("click focus", function () {
			$(this).select();
		});
												
		$("input.numericNO").on("keyup", function (e) {
			setPosi($(this).attr('name'),e.keyCode,'MyTblOthers');
			computeGrossOthers();
		});
									
		$("#txtacctitleID"+lastRow+", #txtacctitle"+lastRow).on("click focus", function(event) {
			$(this).select();
		});

	}

	function deleteRow3(r) {
		var tbl = document.getElementById('MyTblOthers').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.parentNode.rowIndex;
		document.getElementById('MyTblOthers').deleteRow(i);
		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;
		
		for (z=i+1; z<=lastRow; z++){
			var tempOacctno = document.getElementById('txtacctitleID' + z);
			var tempOctitle = document.getElementById('txtacctitle' + z);
			var tempODR = document.getElementById('txtnotDR' + z);
			var tempOCR = document.getElementById('txtnotCR' + z);
			var tempOdelbtn = document.getElementById('row3_'+z+'_delete');
				
			var x = z-1;
			tempOacctno.id = "txtacctitleID" + x;
			tempOacctno.name = "txtacctitleID" + x;
			tempOctitle.id = "txtacctitle" + x;
			tempOctitle.name = "txtacctitle" + x;
			tempODR.id = "txtnotDR" + x;
			tempODR.name = "txtnotDR" + x;
			tempOCR.id = "txtnotCR" + x;
			tempOCR.name = "txtnotCR" + x;
			tempOdelbtn.id = "row3_"+x+"_delete";
			tempOdelbtn.name = "row3_"+x+"_delete";
				
		}

		computeGross();
	}

	function addCM(xyadjtype,xytran,txtbx){
		var tbl = document.getElementById('MyTableCMx').getElementsByTagName('tr');
		var lastRow2 = tbl.length-1;

		if(lastRow2>=1){
			$("#MyTableCMx > tbody > tr").each(function() {	
			
				var citmno = $(this).find('input[type="hidden"][name="hdndetsino"]').val();
				var cadjyp = $(this).find('input[name="hdnctypeadj"]').val();
				//alert(citmno+"!="+itmcde);
				if(citmno!=xytran && cadjyp!==xyadjtype){

					$(this).find('input[name="txtapcmdm"]').attr("readonly", true);
					//$(this).find('input[name="txtcmamt"]').attr("readonly", true);
					$(this).find('input[name="txtremz"]').attr("readonly", true);   
				
					$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled",true);
					
				}
				else{
					var ctnnn = $(this).find('input[type="hidden"][name="hdnisgiven"]').val();

					if(ctnnn==1){
						$(this).find('input[name="txtapcmdm"]').attr("readonly", true);
					}else{
						$(this).find('input[name="txtapcmdm"]').attr("readonly", false);
					}
						
						//$(this).find('input[name="txtapamt"]').attr("readonly", false);
						$(this).find('input[name="txtremz"]').attr("readonly", false);
						$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs");
						$(this).find('input[type="button"][name="delinfo"]').prop("disabled",false);

				}
				
			});
		}			
			
		$('#txtdetsinoinfo').val(xytran); 
		$('#txthdnTYPAdj').val(xyadjtype);   
		$("#txthdnCMtxtbx").val(txtbx);
		$('#MyAdjustmentModal').modal('show');
	}

	function chkCloseInfo(){
		var isInfo = "TRUE";
		
		$("#MyTableCMx > tbody > tr").each(function(index) {	
				
			var citmfld1 = $(this).find('input[name="txtapcmdm"]').val();
			//var citmfld2 = $(this).find('input[name="txtremz"]').val();

			if(citmfld1==""){
				isInfo = "FALSE";
			}
					
		});

	
		if(isInfo == "TRUE"){
			//recompute details
			var tot = 0;
			var xinfo = $("#txthdnCMinfo").val();
			var dsc = $("#txthdnCMtxtbx").val();
			
			$("#MyTableCMx > tbody > tr").each(function(index) {	
				if(index>0){
				var x = $(this).find('input[name="txtapamt"]').val().replace(/,/g,'');
				var y = $(this).find('input[type="hidden"][name="txtcmrr"]').val();

					if(xinfo==y){
						tot = tot + parseFloat(x);
					}	
				}
				
			});
			
			if(parseFloat(tot)>0){
				$("#"+dsc).val(tot);

				$("#"+dsc).autoNumeric('destroy');
				$("#"+dsc).autoNumeric('init',{mDec:2});
			}
			

		//	recomlines();
		//	compgross1();
												
			$('#MyAdjustmentModal').modal('hide');	
		}
		else{
			alert("Incomplete info values!");
		}
	}

	function getrefreturn(cinvno){

			$.ajax({
        url: 'th_getreturn.php',
				data: { x:cinvno },
        dataType: 'json',
        method: 'post',
        success: function (data) {
          console.log(data);
          $.each(data,function(index,item){


						AddRefAdj(item.ctype,item.refsi,item.reftran,item.dte,item.grss,item.rmks);
					});
				}

			});
	}

	function AddRefAdj($adjtyp,$adjsi,$adjtran,$adjdte,$adjgrss,$rmks){

			var tbl = document.getElementById('MyTableCMx').getElementsByTagName('tr');
			var lastRow = tbl.length;

			if($adjtran==""){
				$did = "0";
			}else{
				$did = "1";
			}

			var tdaptypx = "<td><input type='text' class='form-control input-xs' name='hdnctypeadj' id='hdnctypeadj"+lastRow+"' value='"+$adjtyp+"' readonly></td>";

			var tdapcm = "<td><input type='hidden' name='hdndetsino' id='hdndetsino"+lastRow+"' value='"+$adjsi+"'><input type='hidden' name='hdnisgiven' id='hdnisgiven"+lastRow+"' value='"+$did+"'><input type='text' name='txtapcmdm' id='txtapcmdm"+lastRow+"' class='form-control input-xs' value='"+$adjtran+"'></td>";

			var tddate = "<td><input type='text' name='txtapdte' id='txtapdte"+lastRow+"' class='form-control input-xs' readonly value='"+$adjdte+"'></td>";

			var tdamt = "<td><input type='text' name='txtapamt' id='txtapamt"+lastRow+"' class='form-control input-xs text-right' readonly value='"+$adjgrss+"'></td>";

			var tdrem = "<td><input type='text' name='txtremz' id='txtremz"+lastRow+"' value='"+$rmks+"' class='form-control input-xs'></td>";

			if($adjtran==""){
				var tdels = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + $adjsi + lastRow + "' value='delete' /></td>";
			}else{
				var tdels = "<td>&nbsp;</td>";
			}

			$('#MyTableCMx > tbody:last-child').append('<tr>'+tdaptypx+tdapcm + tddate + tdamt + tdrem + tdels + '</tr>'); 

			if($adjtran==""){
				$("#delinfo"+$adjsi+lastRow).on('click', function() { 
					$(this).closest('tr').remove();
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
			}
	}

	function chkform(){

		var subz = "YES";

		if($('#txtcustid').val() == "" || $('#txtcustid').val() == ""){
			alert("You Need a Valid Customer.");
			subz = "NO";
		}


		if($('#txtnGross').val() == "" || $('#txtnGross').val() == 0){
			alert("Zero or Blank AMOUNT RECEIVED is not allowed!");
			subz = "NO";
		}


		if($('#txtORNo').val() == ""){
			alert("Please input your OR NUMBER!");
			subz = "NO";
		}
			
		if($('#selpayment').val() !== "cheque" && $('#selpayment').val() !== "cash"){
			if($('#txtOTRefNo').val() == ""){
				alert("Reference number required for this payment method!");
				subz = "NO";
			}
		}

		if($('#selpayment').val() == "cheque"){
			if($('#txtBankName').val() == "" || $('#txtChekDate').val() == "" || $('#txtCheckNo').val() == "" || $('#txtCheckAmt').val() == ""){
				alert("Please complete your cheque details!");
				subz = "NO";
			}
		}

			var lastRow1 = 0; 
			var lastRow2 = 0;
			
			var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
			lastRow1 = tbl1.length-1;
			$("#hdnrowcnt").val(lastRow1);

			var tbl2 = document.getElementById('MyTableCMx').getElementsByTagName('tr');
			lastRow2 = tbl2.length-1;
			$("#hdnrowcntcmdm").val(lastRow2); 

			var tbl3 = document.getElementById('MyTblOthers').getElementsByTagName('tr');
			lastRow3 = tbl3.length-1;
			$("#hdnOthcnt").val(lastRow3); 
				
		if(lastRow1==0 && $("#isNoRef").find(":selected").val()==0){
			alert("Details Required!");
			subz = "NO";
		}

		if(lastRow3==0 && $("#isNoRef").find(":selected").val()==1){
			alert("Details Required!");
			subz = "NO";
		}

		//if( parseFloat($('#txtnOutBal').val()) != parseFloat($('#txtnApplied').val()) ){
		if( parseFloat($('#txtnGross').val().replace(/,/g,'')) != parseFloat($('#txtnApplied').val().replace(/,/g,'')) ){
			alert("Unbalanced Transaction!");
			subz = "NO";
		}

		if(subz=="NO"){
			return false;
		}
		else{
			if($('#selpayment').val() == "Cheque"){
				//$('#txtCheckAmt').val($('#txtCheckAmt').maskMoney('unmasked')[0]);
			}


				var tx2 = 0;
				$("#MyTableCMx > tbody > tr").each(function(index) {   
					tx2 = index+1;
					$(this).find('input[name="hdnctypeadj"]').attr("name","hdnctypeadj"+tx2);
					$(this).find('input[type=hidden][name="hdndetsino"]').attr("name","hdndetsino"+tx2);					
					$(this).find('input[type=hidden][name="hdnisgiven"]').attr("name","hdnisgiven"+tx2);
					$(this).find('input[name="txtapcmdm"]').attr("name","txtapcmdm" + tx2);
					$(this).find('input[name="txtapdte"]').attr("name","txtapdte" + tx2);
					$(this).find('input[name="txtapamt"]').attr("name","txtapamt" + tx2);
					$(this).find('input[name="txtremz"]').attr("name","txtremz" + tx2);
				});

				tx2 = 0;
				$("#MyTable > tbody > tr").each(function(index) {   
					tx2 = index+1;

					$(this).find('input[type=hidden][name="txtcSalesNo"]').attr("name","txtcSalesNo"+tx2);
					$(this).find('input[type=hidden][name="txtcSalesNo"]').attr("id","txtcSalesNo"+tx2);

					$(this).find('input[name="txtSIGross"]').attr("name","txtSIGross"+tx2);
					$(this).find('input[name="txtSIGross"]').attr("id","txtSIGross"+tx2);

					$(this).find('input[name="txtndebit"]').attr("name","txtndebit"+tx2);
					$(this).find('input[name="txtndebit"]').attr("id","txtndebit"+tx2);

					$(this).find('input[name="txtncredit"]').attr("name","txtncredit"+tx2);
					$(this).find('input[name="txtncredit"]').attr("id","txtncredit"+tx2);

					$(this).find('input[name="txtnpayments"]').attr("name","txtnpayments"+tx2);
					$(this).find('input[name="txtnpayments"]').attr("id","txtnpayments"+tx2);

					$(this).find('input[name="txtnvatcode"]').attr("name","txtnvatcode"+tx2);
					$(this).find('input[name="txtnvatcode"]').attr("id","txtnvatcode"+tx2);

					$(this).find('input[type=hidden][name="txtnvatrate"]').attr("name","txtnvatrate"+tx2);					
					$(this).find('input[type=hidden][name="txtnvatrate"]').attr("id","txtnvatrate"+tx2);

					$(this).find('input[type=hidden][name="txtnvatcodeorig"]').attr("name","txtnvatcodeorig" + tx2);
					$(this).find('input[type=hidden][name="txtnvatcodeorig"]').attr("id","txtnvatcodeorig" + tx2);

					$(this).find('input[name="txtvatamt"]').attr("name","txtvatamt" + tx2);
					$(this).find('input[name="txtvatamt"]').attr("id","txtvatamt" + tx2);

					$(this).find('input[name="txtvatamt"]').attr("name","txtvatamt" + tx2);
					$(this).find('input[name="txtvatamt"]').attr("id","txtvatamt" + tx2);

					$(this).find('input[name="txtnetvat"]').attr("name","txtnetvat" + tx2);
					$(this).find('input[name="txtnetvat"]').attr("id","txtnetvat" + tx2);

					$(this).find('select[name="txtnetvat"]').attr("name","txtnetvat" + tx2);
					$(this).find('select[name="txtnetvat"]').attr("id","txtnetvat" + tx2);

					$(this).find('select[name="txtnEWT[]"]').attr("name","txtnEWT" + tx2 + "[]");
					$(this).find('select[name="txtnEWT"]').attr("id","txtnEWT" + tx2);

						//getrate of selected
						var xcb = "";
						var cnt = 0;
						$("#txtnEWT"+ tx2 + " > option:selected").each(function() {
							//	alert($(this).data("rate"));
							cnt++;
							if(cnt>1){
								xcb = xcb + ";" + $(this).data("rate");
							}else{
								xcb = xcb + $(this).data("rate");
							}
						});

					$(this).find('input[type=hidden][name="hdnewtgiven"]').attr("name","hdnewtgiven" + tx2);
					$(this).find('input[type=hidden][name="hdnewtgiven"]').attr("id","hdnewtgiven" + tx2);

					$(this).find('input[type=hidden][name="txtnEWTorig"]').attr("name","txtnEWTorig" + tx2);
					$(this).find('input[type=hidden][name="txtnEWTorig"]').attr("id","txtnEWTorig" + tx2);

					$(this).find('input[type=hidden][name="txtnEWTRate"]').val(xcb);
					$(this).find('input[type=hidden][name="txtnEWTRate"]').attr("name","txtnEWTRate" + tx2);
					$(this).find('input[type=hidden][name="txtnEWTRate"]').attr("id","txtnEWTRate" + tx2);

					$(this).find('input[name="txtnEWTAmt"]').attr("name","txtnEWTAmt" + tx2);
					$(this).find('input[name="txtnEWTAmt"]').attr("id","txtnEWTAmt" + tx2);

					$(this).find('input[name="txtDue"]').attr("name","txtDue" + tx2);
					$(this).find('input[name="txtDue"]').attr("id","txtDue" + tx2);

					$(this).find('input[name="txtApplied"]').attr("name","txtApplied" + tx2);
					$(this).find('input[name="txtApplied"]').attr("id","txtApplied" + tx2);

					$(this).find('input[name="txtcSalesAcctTitle"]').attr("name","txtcSalesAcctTitle" + tx2);
					$(this).find('input[name="txtcSalesAcctTitle"]').attr("id","txtcSalesAcctTitle" + tx2);

					$(this).find('input[type=hidden][name="txtcSalesAcctNo"]').attr("name","txtcSalesAcctNo" + tx2);
					$(this).find('input[type=hidden][name="txtcSalesAcctNo"]').attr("id","txtcSalesAcctNo" + tx2);

					$(this).find('input[name="row_delete"]').attr("name","row_"+tx2+"_delete");
					$(this).find('input[name="row_delete"]').attr("id","row_"+tx2+"_delete");

				});
			
			//return true;
			$("#frmOR").submit();
			
		}

	}

	function changeDet(){
		$('#MyTable tbody').empty(); 
	}
</script>


</body>
</html>
