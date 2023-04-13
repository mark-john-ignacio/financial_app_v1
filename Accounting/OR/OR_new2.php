<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "OR_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	$gettaxcd = mysqli_query($con,"SELECT * FROM `taxcode` where compcode='$company' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['ctaxcode'], 'ctaxdesc' => $row['ctaxdesc'], 'nrate' => $row['nrate']); 
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
  <link href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../include/jquery-maskmoney.js" type="text/javascript"></script>
-->

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtcust').focus();">
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  

<form action="OR_newsave2.php" name="frmOR" id="frmOR" method="post"  onSubmit="return chkform();">
	<fieldset>
    <legend>Receive Payment</legend>	
      <table width="100%" border="0">
				<tr>
					<tH width="210">
						Deposit To Account					
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
							<div class="col-xs-6 nopadding">
								<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>"  autocomplete="off">
							</div> 
							<div class="col-xs-6 nopadwleft">
								<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
							</div>
						</div>     
					</td>
    			<tH width="150"><span style="padding:2px">Date:</span></tH>
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
					<tH>&nbsp;</tH>
					<td style="padding:2px;">&nbsp;</td>
					<tH>&nbsp;</tH>
					<td style="padding:2px;">&nbsp;</td>
				</tr>
				<tr>
					<tH width="100" valign="top">Payor:</tH>
					<td valign="top" style="padding:2px">
					<div class="col-xs-12 nopadding">
							<div class="col-xs-6 nopadding">
								<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off">
					</div> 
					<div class="col-xs-3 nopadwleft">
								<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
							</div>
					</div>        
					</td>
					<tH width="150" style="padding:2px">Receipt No.:</tH>
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
					<tH width="100" valign="top">Payment Method:</tH>
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
					<th valign="top" style="padding:2px">Amount Received:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
						<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm  text-right numeric" value="0.00" style="text-align:right;" autocomplete="off" required onKeyUp="computeGross();">
					</div></td>
				</tr>
				<tr>
					<tH width="100" rowspan="2" valign="top">Memo:</tH>
					<td rowspan="2" valign="top" style="padding:2px">
					<div class="col-xs-12 nopadding">
						<div class="col-xs-10 nopadding">
							<textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
						</div>
					</div>
					</td>
					<th valign="top" style="padding:2px">Amount Applied:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
						<input type="text" id="txtnApplied" name="txtnApplied" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off" readonly>
					</div></td>
				</tr>
				<tr>
					<th valign="top" style="padding:2px">Out of Balance:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
						<input type="text" id="txtnOutBal" name="txtnOutBal" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off" readonly>
					</div></td>
				</tr>
      </table>

			<!--
			<ul class="nav nav-tabs">
				<li class="active"><a href="#divSales">Sales Invoice</a></li>
				<li><a href="#divLoans">Loans</a></li>
				<li><a href="#divOthers">Others</a></li>
			</ul>
			
			<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 40vh;text-align: left;overflow: auto">
    		<div class="tab-content">    
        	<div id="divSales" class="tab-pane fade in active">
							
						<div class="col-xs-12 nopadwdown">
							<button type="button" class="btn btn-xs btn-info" onClick="getInvs();">
								<i class="fa fa-search"></i>&nbsp; Find Invoice
							</button>
        		</div>
			-->
						<br>
						<div style="border: 1px solid #919b9c; height: 40vh; overflow: auto">
							<div id="tableContainer" class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								width: 2500px;
								height: 300px;
								text-align: left;">

								<table id="MyTable" border="1" bordercolor="#CCCCCC" class="table table-sm table-bordered">
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
											<th scope="col" class="text-center" nowrap>EWTCode</th>                            
											<th scope="col" class="text-center" nowrap>EWTRate(%)</th>
											<th scope="col" class="text-center" nowrap>EWTAmt</th>
											<th scope="col" width="150px" class="text-center" nowrap>Total Due</th>
											<th scope="col" width="150px" class="text-center" nowrap>Amt Applied</th>
											<th scope="col" width="500px" nowrap>&nbsp;Credit Acct</th>
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

					<!--
					</div>
        
					<div id="divOthers" class="tab-pane fade">
						<div class="col-xs-12 nopadwdown">
							<button type="button" class="btn btn-xs btn-info" onClick="addacct();">
								<i class="fa fa-plus"></i>&nbsp; Add New Line
							</button>
						</div>

            <div id="tblOtContainer" class="alt2" dir="ltr" style="
              margin: 0px;
              padding: 3px;
              border: 1px solid #919b9c;
              width: 100%;
              height: 200px;
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
			</div>
			-->
			
			<br>
			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td width="50%">					
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='OR.php';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>

						<div class="dropdown" style="display:inline-block !important;">
							<button type="button" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle">
								SI <br>(Insert) <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="javascript:;" onClick="getInvs('Trade');">Trade</a></li>
								<li><a href="javascript:;" onClick="getInvs('Non-Trade');">Non-Trade</a></li>
							</ul>
						</div>
				
						<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave">
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
	<!-- End Bootstrap modal -->

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
												<th align="center">
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
		}
	});
	
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
	

});

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

			var tbl2 = document.getElementById('MyTableCMx').getElementsByTagName('tr');
			lastRow2 = tbl2.length-1;
			$("#hdnrowcntcmdm").val(lastRow2);
					
		if(lastRow1!=0){
			$("#hdnrowcnt").val(lastRow1);				
		}
				
	
		if(lastRow1==0){
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
			
			return true;
			$("#frmOR").submit();
			
		}

}

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
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
			tempewtcode.name = "txtnEWT" + x;
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

function getInvs(typ){
	
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			//clear table body if may laman
			$('#MyORTbl tbody').empty();
			$('#invtyp').val(typ);
			
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
			$('#invheader').html("Invoice List: " + $('#txtcust').val())
			
			//alert("th_orlist.php?x="+x+"&y="+salesnos+"&typ="+typ);
			$.ajax({
        url: 'th_orlist.php',
				data: { x:x, y:salesnos, typ:typ },
        dataType: 'json',
        method: 'post',
        success: function (data) {
          // var classRoomsTable = $('#mytable tbody');
          console.log(data);
          $.each(data,function(index,item){
            $("<tr>").append(
							$("<td>").html("<input type='checkbox' value='"+item.csalesno+"' name='chkSales[]' data-dm='"+item.cdm+"' data-cm='"+item.ccm+"' data-payment='"+item.npayment+"' data-vatcode='"+item.ctaxcode+"' data-vatrate='"+item.vatrate+"' data-vat='"+item.cvatamt+"' data-netvat='"+item.cnetamt+"' data-ewtcode='"+item.cewtcode+"' data-ewtrate='"+item.newtrate+"' data-ewtamt='"+item.cewtamt+"' data-amt='"+item.ngross+"' data-acctid='"+item.cacctno+"' data-acctdesc='"+item.ctitle+"' data-cutdate='"+item.dcutdate+"'>"),
              $("<td>").text(item.csalesno),
              $("<td>").text(item.dcutdate),
							$("<td>").text(item.ngross),
							$("<td>").text(item.cewtcode),
							$("<td>").text(item.ctaxcode)
            ).appendTo("#MyORTbl tbody");

          });
        },
        error: function (jqXHR, textStatus, errorThrown)
				{
					if(errorThrown!="Unexpected end of JSON input"){
					}
				}
      });
			
			$('#myModal').modal('show');
			
		}


}

function save(){

	var i = 0;
	var rcnt = 0;
	
  $("input[name='chkSales[]']:checked").each( function () {
		i++;
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
			var newtamt = $(this).data("ewtamt");

			var acctcode = $(this).data("acctid");
			var acctdesc = $(this).data("acctdesc");

			if(parseFloat(npayments)!==0){
				var ntotdue = (parseFloat(nnetvat) + parseFloat(nvat)) - parseFloat(ncm) - parseFloat(newtamt);
			}else{
				var ntotdue = parseFloat(ngross) - parseFloat(ncm) - parseFloat(npayments) - parseFloat(newtamt);
			}
		
			var lastRow = tbl.rows.length + 1;							
			var z=tbl.insertRow(-1);

			var a=z.insertCell(-1);
			a.innerHTML ="<div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo"+lastRow+"' id='txtcSalesNo"+lastRow+"' value='"+tranno+"' />"+tranno+"</div>";
									
			var b=z.insertCell(-1);
			b.align = "center";
			b.innerHTML = dcutdate;
										
			var c=z.insertCell(-1);
			c.align = "right";
			c.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs text-right' name='txtSIGross"+lastRow+"' id='txtSIGross"+lastRow+"' value='"+ngross+"' readonly /></div>";

			var d=z.insertCell(-1);
			d.align = "right";
			d.innerHTML = "<div class=\"input-group\"><input type='text' name='txtndebit"+lastRow+"' id='txtndebit"+lastRow+"' class=\"numeric form-control input-xs\" value=\""+ndm+"\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-xs\" name=\"btnadddm\" id=\"btnadddm"+lastRow+"\" type=\"button\" onclick=\"addCM('DM','"+tranno+"','txtncm"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div>";
										
			var e=z.insertCell(-1);
			e.align = "right";
			e.innerHTML = " <div class=\"input-group\"><input type='text' name='txtncredit"+lastRow+"' id='txtncredit"+lastRow+"' class=\"numeric form-control input-xs\" value=\""+ncm+"\" style=\"text-align:right\" readonly><span class=\"input-group-btn\"><button class=\"btn btn-primary btn-xs\" name=\"btnaddcm\" id=\"btnaddcm"+lastRow+"\" type=\"button\" onclick=\"addCM('CM','"+tranno+"','txtncm"+lastRow+"')\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></span></div>";
										
			var f=z.insertCell(-1);
			f.align = "right";
			f.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnpayments"+lastRow+"' id='txtnpayments"+lastRow+"' value='"+npayments+"' readonly=\"true\" />";

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
			c1.innerHTML = "<input type='text' class='form-control input-xs text-right' name=\"txtnvatcode"+lastRow+"\" id=\"txtnvatcode"+lastRow+"\" value='"+vatcode+"' readonly /> <input type='hidden' name=\"txtnvatrate"+lastRow+"\" id=\"txtnvatrate"+lastRow+"\" value='"+vatrate+"' /> <input type='hidden' name=\"txtnvatcodeorig"+lastRow+"\" id=\"txtnvatcodeorig"+lastRow+"\" value='"+vatcode+"' />";

			var c2=z.insertCell(-1);
			c2.align = "right";
			c2.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtvatamt"+lastRow+"' id='txtvatamt"+lastRow+"' value='"+nvat+"' readonly />";
										
			var c3=z.insertCell(-1);
			c3.align = "right";
			c3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnetvat"+lastRow+"' id='txtnetvat"+lastRow+"' value='"+nnetvat+"' readonly />"; 
			
			$ifrdonly = "";
			$ifrdonlyint = 0;
			if(newtcode!=="none" && newtcode!==""){
				$ifrdonly = "readonly";
				$ifrdonlyint = 1;
			}

			var l=z.insertCell(-1);
			l.innerHTML = "<input type='text' class='ewtcode form-control input-xs' placeholder='EWT Code' name='txtnEWT"+lastRow+"' id='txtnEWT"+lastRow+"' autocomplete=\"off\" value='"+newtcode+"' "+$ifrdonly+"/> <input type='hidden' name='hdnewtgiven"+lastRow+"' id='hdnewtgiven"+lastRow+"' value='"+$ifrdonlyint+"' /> <input type='hidden' name='txtnEWTorig"+lastRow+"' id='txtnEWTorig"+lastRow+"' value='"+newtcode+"' />";

			var l2=z.insertCell(-1);
			l2.innerHTML = "<input type='text' class='form-control input-xs text-right' placeholder='EWT Rate' name='txtnEWTRate"+lastRow+"' value=\""+newtrate+"\" id='txtnEWTRate"+lastRow+"' readonly=\"true\" />";
										
			var l3=z.insertCell(-1);
			l3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' placeholder='EWT Amt' name='txtnEWTAmt"+lastRow+"'  value=\""+newtamt+"\" id='txtnEWTAmt"+lastRow+"' readonly=\"true\" />";
										
			var g=z.insertCell(-1);
			g.align = "right";
			g.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtDue"+lastRow+"' id='txtDue"+lastRow+"' value='"+ntotdue+"' readonly=\"true\" />";
										
			var h=z.insertCell(-1);
			h.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtApplied"+lastRow+"' id='txtApplied"+lastRow+"' value='"+ntotdue+"' style='text-align:right' autocomplete=\"off\" />";
									
			var j=z.insertCell(-1);
			j.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='form-control input-xs' name='txtcSalesAcctTitle"+lastRow+"' id='txtcSalesAcctTitle"+lastRow+"' value='"+acctdesc+"' autocomplete=\"off\" /> <input type='hidden' name='txtcSalesAcctNo"+lastRow+"' id='txtcSalesAcctNo"+lastRow+"' value='"+acctcode+"' /></div>";
										
			var k=z.insertCell(-1);
			k.innerHTML = "<div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' value='delete' onClick='deleteRow(this);' /></div>";
									
			//var varnnet = item.nnet;
			//var varngrs = item.ngross;	
													
			$("input.numeric").autoNumeric('init',{mDec:2});
			$("input.numeric").on("click focus", function () {
				$(this).select();
			});
											
			$("input.numeric").on("keyup", function (e) {
				setPosi($(this).attr('name'),e.keyCode,'MyTable');
				computeGross();
			});
									
									$("#txtnEWT"+lastRow).typeahead({
										items: 10,
										source: function(request, response) {
											$.ajax({
												url: "../th_ewtcodes.php",
												dataType: "json",
												data: {
													query: $("#txtnEWT"+lastRow).val()
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
											$("#txtnEWT"+lastRow).val(item.ctaxcode).change(); 
											$("#txtnEWTRate"+lastRow).val(item.nrate);
											
											
											computeDue(item.cbase,item.nrate);
											computeGross();
											
											//setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
											
										}
									});
									
									
									$("#txtcSalesAcctTitle"+lastRow).on("click focus", function(event) {
										$(this).select();
									});
									
									$("#txtcSalesAcctTitle"+lastRow).typeahead({
								
										items: 10,
										source: function(request, response) {
											$.ajax({
												url: "../th_accounts.php",
												dataType: "json",
												data: {
													query: $("#txtcSalesAcctTitle"+lastRow).val()
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
										afterSelect: function(item, event) { 
											$("#txtcSalesAcctTitle"+lastRow).val(item.name).change(); 
											$("#txtcSalesAcctNo"+lastRow).val(item.id);
											
											setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
											
										}
									});
											   	


						computeGross(); 

						if(parseFloat(ncm)!==0){
							getrefreturn(tranno);
						}
	   
	   
   });
   
   if(i==0){
	   alert("No Invoice is selected!")
   }
   
   $('#myModal').modal('hide');
   
}

function computeDue(cbase,nrate){

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	if(lastRow!=0){
		var x = 0;
		
		for (z=1; z<=lastRow; z++){
			var varngrs = $("#txtvatamt"+lastRow).val().replace(/,/g,'');
			var varngrs = $("#txtSIGross"+lastRow).val().replace(/,/g,'');
			var varngrs = $("#txtSIGross"+lastRow).val().replace(/,/g,'');
		}

	}

	varnnet =  $("#txtnetvat"+lastRow).val().replace(/,/g,'');
	ndue = $("#txtDue"+lastRow).val().replace(/,/g,'');
											
	if(cbase=="NET"){
		xcb = parseFloat(varnnet)*(nrate/100);
	}else{
		xcb = parseFloat(varngrs)*(nrate/100);
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

/*
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

	v.innerHTML = "<input type='text' name=\"txtacctno"+lastRow+"\" id=\"txtacctno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Enter Acct Code...\" style=\"text-transform:uppercase\" autocomplete=\"off\">";
	w.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Acct Desc...\" style=\"text-transform:uppercase\" autocomplete=\"off\">";
	xDR.innerHTML = "<input type='text' name=\"txtnotDR"+lastRow+"\" id=\"txtnotDR"+lastRow+"\" class=\"numeric form-control input-sm\" style=\"text-align:right\" value=\"0.0000\" required autocomplete=\"off\">";
	xCR.innerHTML = "<input type='text' name=\"txtnotCR"+lastRow+"\" id=\"txtnotCR"+lastRow+"\" class=\"numeric form-control input-sm\" style=\"text-align:right\" value=\"0.0000\" required autocomplete=\"off\">";
	y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row3_"+lastRow+"_delete' value='delete' onClick=\"deleteRow3(this);\"/>";

	//alert(lastRow);
		$("#txtacctitle"+lastRow).focus();

									$("input.numeric").numeric({decimalPlaces: 4});
									$("input.numeric").on("click focus", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function (e) {
										setPosi($(this).attr('name'),e.keyCode,'MyTbl');
										computeGross();
									});
								
									$("#txtacctno"+lastRow+", #txtacctitle"+lastRow).on("click focus", function(event) {
										$(this).select();
									});

									$("#txtacctno"+lastRow).on("keyup", function(event) {
										
										if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
										
											if(event.keyCode==13 ){	
												var dInput = this.value;
										
												$.ajax({
													type:'post',
													url:'../getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														//alert(value);
														if(value.trim()!=""){
															$("#txtacctitle"+lastRow).val(value.trim());
														}
													}
												});
											}
											
											setPosi("txtacctno"+lastRow,event.keyCode,'MyTblOthers');
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
																						
											setPosi("txtacctitle"+lastRow,13,'MyTblOthers');
										}
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
			var tempOacctno = document.getElementById('txtacctno' + z);
			var tempOctitle = document.getElementById('txtacctitle' + z);
			var tempODR = document.getElementById('txtnotDR' + z);
			var tempOCR = document.getElementById('txtnotCR' + z);
			var tempOdelbtn = document.getElementById('row3_'+z+'_delete');
			
			var x = z-1;
			tempOacctno.id = "txtacctno" + x;
			tempOacctno.name = "txtacctno" + x;
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
*/

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


</script>


</body>
</html>
