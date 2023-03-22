<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "PayBill_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$ccvno = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];
		
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

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
-->

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus();">
<?php
    	$sqlchk = mysqli_query($con,"Select a.cacctno, c.cacctdesc, a.ccode, a.cpaymethod, a.cbankcode, a.ccheckno, a.cpaydesc, a.cpayrefno, e.cname as cbankname, a.cpayee, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dcheckdate,'%m/%d/%Y') as dcheckdate, a.ngross, a.npaid, a.lapproved, a.lcancelled, a.lprintposted, b.cname, d.cname as custname, c.cacctdesc, a.cparticulars, a.cpaytype
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

			$cpartic = $row['cparticulars'];

			$cBank = $row['cbankcode'];
			$cBankName = $row['cbankname'];
			$cCheckNo = $row['ccheckno'];

			$cPayDesc = $row['cpaydesc'];
			$cPayRefr = $row['cpayrefno'];

			$cAcctID = $row['cacctno'];
			$cAcctDesc = $row['cacctdesc'];
			
			$cPayee = $row['cpayee'];
			$dDate = $row['ddate'];
			$dCheckDate = $row['dcheckdate'];
			$nAmount = $row['ngross'];
			$nPaid = $row['npaid'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

?>

<input type="hidden" id="existingnos" value='<?=json_encode($arrnoslist)?>'>

<form action="PayBill_editsave.php" name="frmpos" id="frmpos" method="post" onsubmit="return chkform();">
	<fieldset>
   	  <legend>Bills Payment Details</legend>
   	  
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<tH>TRAN No.:</tH>
						<td colspan="3" style="padding:2px;">
						<div class="col-xs-2 nopadding"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
							
							<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
							
							<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
							<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
							<input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
							&nbsp;&nbsp;
							<div id="statmsgz" style="display:inline"></div>
						</td>
					</tr>
					<tr>
						<td><span style="padding:2px"><b>Paid To:</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-left:2px">
							<div class="col-xs-6 nopadding">
									<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4" value="<?php echo $cName;?>">
							</div>
							<div class="col-xs-6 nopadwleft">
									<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly  value="<?php echo $cCode;?>">
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
											<div class="col-xs-12" style="padding-left:2px">
												<div class="col-xs-4 nopadding">
													<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
														<option value="cheque" <?=($cpaymeth=="cheque") ? "selected" : ""?>>Cheque</option>
														<option value="cash" <?=($cpaymeth=="cash") ? "selected" : ""?>>Cash</option>
														<option value="bank transfer" <?=($cpaymeth=="bank transfer") ? "selected" : ""?>>Bank Transfer</option>
														<option value="mobile payment" <?=($cpaymeth=="mobile payment") ? "selected" : ""?>>Mobile Payment</option>
														<option value="credit card" <?=($cpaymeth=="credit card") ? "selected" : ""?>>Credit Card</option>
														<option value="debit card" <?=($cpaymeth=="debit card") ? "selected" : ""?>>Debit Card</option>
													</select>
												</div>
												<div class="col-xs-3" style="padding:2px !important">
													&nbsp;&nbsp;&nbsp;<b>Payment Type</b>
												</div>
												<div class="col-xs-4 nopadding">
													<select id="selpaytype" name="selpaytype" class="form-control input-sm selectpicker">
														<option value="apv" <?=($cpaytype=="apv") ? "selected" : ""?>>AP Voucher</option>
														<option value="po" <?=($cpaytype=="po") ? "selected" : ""?>>PO Pre-Payment</option>
													</select>
												</div>
										</td>
										<td><span style="padding:2px"><b>Particulars:</b></span></td>
											<td rowspan="2">
											<div class="col-xs-12"  style="padding-bottom:2px">
													<div class='col-xs-12 nopadding'>
														<textarea class="form-control" rows="2" id="txtparticulars" name="txtparticulars"><?=$cpartic?></textarea>
													</div>
											</div>
										</td>
					</tr>
					<tr>

					<td width="150">
							<span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span> 
						</td>
						<td>
							<div class="col-xs-12"  style="padding-left:2px <?=($cpaymeth=="cash") ? "; display: none" : ""?>" id="paymntdescdet">
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
						<td>
							<span style="padding:2px" id="paymntrefr">
								<?php
									if($cpaymeth=="cash"){
										echo "";
									}elseif($cpaymeth=="cheque"){
										echo "<b>Check No.</b>";
									}else{
										echo "<b>Reference No.</b>";
									}
								?>
							</span>
						</td>
						<td>

							<div class="col-xs-12"  style="padding-left:2px">

								<div class="col-xs-7 nopadding" style="<?=($cpaymeth!=="cheque") ? "; display: none" : ""?>" id="paymntrefrdet">
									<input type='text' class='noref form-control input-sm' name='txtCheckNo' id='txtCheckNo' value="<?php echo $cCheckNo; ?>" placeholder="Check No."/>
								</div>

								<div class="col-xs-7 nopadding" style="<?=($cpaymeth=="cheque" || $cpaymeth=="cash") ? "; display: none" : ""?>" id="payrefothrsdet">
									<input type="text" id="txtPayRefrnce" class="noref form-control input-sm" name="txtPayRefrnce" value="<?php echo $cPayRefr; ?>" placeholder="Reference No.">
								</div>

								<div class="col-xs-5 nopadding">
									<div class="form-control input-sm no-border" style="color: red" id="chknochek">
									
									</div>
								</div>

								<!--
								<div class="col-xs-6 nopadwleft">
									<button type="button" class="btn btn-danger btn-sm" name="btnVoid" id="btnVoid">VOID CHECK NO. </button>
								</div>
								-->
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
				</table>

				<br>

	  				<div id="tableContainer" class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 250px;
                        text-align: left;
                        overflow: auto">
                        <table width="100%" border="0" cellpadding="0" id="MyTable">
                         <thead>
                          <tr>
														<th scope="col" id="hdnRefTitle">APV No</th>
                            <th scope="col" width="150px">Date</th>
                            <th scope="col" class="text-right" width="150px">Amount</th>
                            <th scope="col" class="text-right" width="150px">Payed&nbsp;&nbsp;&nbsp;</th>
                            <th scope="col" width="150px" class="text-right">Total Owed&nbsp;&nbsp;&nbsp;</th>
                            <th scope="col" width="150px" class="text-center">Amount Applied</th>
														<th scope="col" >Dr Acct</th>
                          </tr>
                         </thead>
                         <tbody>
                         </tbody>
                        </table>
					</div>
                    
                    <br>
						<table width="100%" border="0" cellpadding="3">
							<tr>
								<td width="60%" rowspan="2">
										<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
											<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
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
								<td align="right">
									<div class="col-xs-12">
										<div class="col-xs-5 text-right"> <b>Total Amount : </span> </div>
										<div class="col-xs-7"> <input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" value="<?php echo number_format($nAmount,2); ?>" style="font-size:16px; font-weight:bold; text-align:right" readonly> </div>
									</div>
								</td>
							</tr>
							<tr>
								<td align="right">
									<div class="col-xs-12" style="padding-top: 3px !important">
										<div class="col-xs-5 text-right"> <b>Total Applied : </span> </div>
										<div class="col-xs-7"> <input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="<?php echo number_format($nPaid,2); ?>" style="font-size:16px; font-weight:bold; text-align:right" readonly> </div>
									</div>
								</td>
							</tr>
						</table>

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
<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="DRListHeader">Bank List</h3>
            </div>
            
            <div class="modal-body pre-scrollable">
            
                          <table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
                           <thead>
                            <tr>
                              <th>Bank Code</th>
                              <th>Bank Name</th>
                              <th>Bank Acct No</th>
                              <th>Checkbook No.</th>
                              <th>Check No.</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
            </div>         	
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

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


<form action="print_voucher.php" name="frmvoucher" id="frmvoucher" method="post" target="_blank">
	<input type="hidden" name="id" id="id" value="<?php echo $ccvno;?>">
	<input type="submit" style="display: none" id="btnvoucher">
</form>

<form action="print_check.php" name="frmchek" id="frmchek" method="post" target="_blank"> 
	<input type="hidden" name="id" id="id" value="<?php echo $ccvno;?>">
	<input type="submit" style="display: none" id="btncheck"> 
</form>

</body>
</html>

<script type="text/javascript">

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


	$(document).ready(function() {
			$('.datepick').datetimepicker({
					format: 'MM/DD/YYYY',
			});
		
		loadDets();
		
		disabled();
		
	});


	$(function(){ 

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

		$('#txtcust').typeahead({
		
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../th_csall.php",
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
				return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.typ + ": </b>"+ item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);
				$("#txtpayee").val(item.value);
				
				showapvmod(item.id);

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
						url: "PayBill_voidchkno.php",
						data: { id: $("#txtBank").val(), chkno: $("#txtCheckNo").val(), rem: rems },
						async: false,
						success: function( data ) {
							if(data.trim()!="False"){
								$("#txtCheckNo").val(data.trim());
								$("#btnVoid").attr("disabled", false);
							}
						}
						});

			}
		});


		$("#btnsearchbank").on("click", function() {
			
			$('#MyDRDetList tbody').empty();
			
				$.ajax({
					url: 'th_banklist.php',
					dataType: 'json',
					async:false,
					method: 'post',
					success: function (data) {
												// var classRoomsTable = $('#mytable tbody');
						console.log(data);
						$.each(data,function(index,item){

							$("<tr id=\"bank"+index+"\">").append(
								$("<td>").text(item.ccode),
								$("<td>").text(item.cname),
								$("<td>").text(item.cbankacctno),
								$("<td>").text(item.ccheckno),
								$("<td>").text(item.ccurrentcheck)
							).appendTo("#MyDRDetList tbody");
										
							$("#bank"+index).on("click", function() {
								$("#txtBank").val(item.ccode);
								$("#txtBankName").val(item.cname);
								$("#txtCheckNo").val(item.ccurrentcheck);
								$("#txtcacctid").val(item.cacctno);
								$("#txtcacct").val(item.cacctdesc);

								if($("#selpayment").val()=="cheque"){
									$("#txtCheckNo").val(item.ccurrentcheck);
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

		$("#selpayment").on("change", function(){  
			if($(this).val()=="cash"){       //paymntdesc paymntdescdet
				$("#paymntdesc").html(" ");
				$("#paymntrefr").html(" ");		
				
				$("#paymntdescdet").hide();
				$("#paymntrefrdet").hide();
				$("#payrefothrsdet").hide(); 

				$("#chkdate").html("<b>Check Date</b>");
				$("#txtChekDate").attr("disabled", true);     

				$("#txtBank").prop("required", false);
				$("#txtBankName").prop("required", false); 
				$("#txtCheckNo").prop("required", false); 
				$("#txtPayRefrnce").prop("required", false); 

			}else if($(this).val()=="cheque"){	
				$("#paymntdesc").html("<b>Bank Name</b>");	
				$("#paymntrefr").html("<b>Check No.</b>");

				$("#paymntdescdet").show();
				$("#paymntrefrdet").show();

				$("#paymntothrsdet").hide();
				$("#payrefothrsdet").hide();

				$("#chkdate").html("<b>Check Date</b>"); 
				$("#txtChekDate").attr("disabled", false);

				$("#txtBank").prop("required", true);
				$("#txtBankName").prop("required", true); 
				$("#txtCheckNo").prop("required", true); 
				$("#txtPayRefrnce").prop("required", false);

			}else if($(this).val()=="bank transfer"){
				$("#paymntdesc").html("<b>Bank Name</b>");
				$("#paymntrefr").html("<b>Reference No.</b>");

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
				$("#paymntdesc").html("<b>Bank Name</b>");
				$("#paymntrefr").html("<b>Reference No.</b>");

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

	});

	function showapvmod(custid){
						$('#MyAPVList tbody').empty();
			
						$.ajax({
											url: 'th_APVlist.php',
						data: 'code='+custid,
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
								$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'>"),
								$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
								$("<td>").html(item.cacctno+"<input type='hidden' id='APVacctno"+index+"' name='APVacctno"+index+"' value='"+item.cacctno+"'>"),
								$("<td>").text(item.cacctdesc+"<input type='hidden' id='APVacctdesc"+index+"' name='APVacctdesc"+index+"' value='"+item.cacctdesc+"'>"),
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
				var b = $("#APVdte"+xyz).val();
				var c = $("#APVacctno"+xyz).val();
				var d = $("#APVamt"+xyz).val().replace(/,/g,'');
				var e = $("#APVpayed"+xyz).val();
				var f = $("#APVacctdesc"+xyz).val();
			
			var owed = parseFloat(d) - parseFloat(e);

			addrrdet(a,b,d,e,owed,c,0,f);
			
			totGross = parseFloat(totGross) + parseFloat(owed) ;

		});


		$('#myAPModal').modal('hide');
		$('#myAPModal').on('hidden.bs.modal', function (e) {

				$("#txtnGross").val(totGross);
				$("#txtnGross").autoNumeric('destroy');
				$("#txtnGross").autoNumeric('init',{mDec:2});
		
		});
		

	}

	function addrrdet(ctranno,ddate,namount,npayed,ntotowed,cacctno,napplied,cacctdesc){

		var ctypref = $("#selpaytype").val();
		ctyprefval = "";
		if(ctypref=="apv"){
			ctyprefval = "readonly";
		}
		
		if(document.getElementById("txtcustid").value!=""){
			
		$('#txtcust').attr('readonly', true);
			
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
		
		var u = "<td>"+ctranno+"<input type=\"hidden\" name=\"cTranNo"+lastRow+"\" id=\"cTranNo"+lastRow+"\" value=\""+ctranno+"\" /> <input type=\"hidden\" name=\"cacctno"+lastRow+"\" id=\"cacctno"+lastRow+"\" value=\""+cacctno+"\" /> </td>";
		
		var v = "<td>"+ddate+"<input type=\"hidden\" name=\"dApvDate"+lastRow+"\" id=\"dApvDate"+lastRow+"\" value=\""+ddate+"\" /></td>";
		
		var w = "<td align='right'>"+numcom(namount)+"<input type=\"hidden\" name=\"nAmount"+lastRow+"\" id=\"nAmount"+lastRow+"\" value=\""+namount+"\" /></td>";
		
		var x = "<td align='right'>"+numcom(npayed)+"<input type=\"hidden\" name=\"cTotPayed"+lastRow+"\" id=\"cTotPayed"+lastRow+"\"  value=\""+npayed+"\" style=\"text-align:right\" readonly=\"readonly\">&nbsp;&nbsp;&nbsp;</td>";
		
		var y = "<td style=\"padding:2px\" align=\"right\">"+numcom(ntotowed)+"<input type=\"hidden\" name=\"cTotOwed"+lastRow+"\" id=\"cTotOwed"+lastRow+"\"  value=\""+ntotowed+"\">&nbsp;&nbsp;&nbsp;</td>";
			
		var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied"+lastRow+"\" id=\"nApplied"+lastRow+"\"  value=\""+napplied+"\" style=\"text-align:right\" /></td>";

		var t = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"form-control input-sm\" name=\"cacctdesc"+lastRow+"\" id=\"cacctdesc"+lastRow+"\"  value=\""+cacctdesc+"\" "+ctyprefval+"/> <input type=\"hidden\" name=\"cacctno"+lastRow+"\" id=\"cacctno"+lastRow+"\" value=\""+cacctno+"\" /></td>";
		
		//alert('<tr>'+u + v + w + x + y + '</tr>');		
		
		$('#MyTable > tbody:last-child').append('<tr>'+u + v + w + x + y + z + t + '</tr>');
		
			
									//$("input.numeric").numeric({decimalPlaces: 4});
									$("input.numeric").autoNumeric('init',{mDec:2});

									$("input.numeric").on("focus", function () {
										$(this).select();
									});
									
									$("input.numeric").on("keyup", function (e) {
											setPosi($(this).attr('name'),e.keyCode);
											GoToComp();
									});

									$("#cacctdesc"+lastRow).typeahead({
										items: 10,
										source: function(request, response) {
											$.ajax({
												url: "../th_accounts.php",
												dataType: "json",
												data: {
													query: $("#cacctdesc"+lastRow).val()
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
											$("#cacctdesc"+lastRow).val(item.name).change(); 
											$("#cacctno"+lastRow).val(item.id);
										}
									});


									GoToComp();
									
						
		}
		else{
			alert("Paid To Required!");
		}
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
			
			if(parseFloat(oob) > 1){
				
				
				$("#AlertMsg").html("<b>ERROR: </b>Unbalanced amount!<br>Out of Balance: "+ Math.abs(oob));
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK="False";
				return false;
			}
			
			
			if(isOK == "True"){
				document.getElementById("hdnrowcnt").value = lastRow;
				//$("#frmpos").submit();

				return true;
			}

	}

	function GoToComp(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		var z;
		var gross = 0;
		
		for (z=1; z<=lastRow; z++){
			gross = parseFloat(gross) + parseFloat($("#nApplied"+z).val().replace(/,/g,''));
		}
		
		//document.getElementById("txtnGross").value = gross.toFixed(2);
		$("#txttotpaid").val(gross);
		$("#txttotpaid").autoNumeric('destroy');
		$("#txttotpaid").autoNumeric('init',{mDec:2});

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
			
				
				$("#txtctranno").attr("readonly", true);
				$("#txtctranno").val($("#hdnorigNo").val());
				
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);		
		}

	}


	function printchk(){

		if(document.getElementById("hdncancel").value==1){

			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";

		}else{
				
			//$("#frmvoucher").delay(300).submit();
    	//$("#frmchek").delay(300).submit();

			$("#btnvoucher").click(); 
			$("#btncheck").click();

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
												// var classRoomsTable = $('#mytable tbody');
												console.log(data);
												$.each(data,function(index,item){
								addrrdet(item.capvno,item.dapvdate,item.namount,item.npayed,item.nowed,item.cacctno,item.napplied,item.cacctdesc);
							});
							
						}
					});

	}

</script>
