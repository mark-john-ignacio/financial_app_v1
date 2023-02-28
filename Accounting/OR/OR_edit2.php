<?php
session_start();
$_SESSION['pageid'] = "OR_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
$corno = $_REQUEST['txtctranno'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
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

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtctranno').focus(); disabled();">

<?php

    	$sqlchk = mysqli_query($con,"Select a.cacctcode, a.ccode, a.namount, a.cpaymethod, a.cpaytype, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.napplied, a.lapproved, a.lcancelled, a.lprintposted, a.cornumber, a.cremarks, b.cname, d.cname as csuppname, c.cacctdesc, c.nbalance From receipt a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join accounts c on a.compcode=c.compcode and a.cacctcode=c.cacctid left join suppliers d on a.compcode=d.compcode and a.ccode=d.ccode where a.compcode='$company' and a.ctranno='$corno'");
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctcode'];
			$nDebitDesc = $row['cacctdesc'];
			$nBalance = $row['nbalance'];
			
			
			$cCode = $row['ccode'];
			$cName = $row['cname'];
			 if($cName==""){
				 $cName = $row['csuppname'];
			 }
			 
			$cPaytype = $row['cpaytype'];
			$cPayMeth = $row['cpaymethod'];
			$cORNo = $row['cornumber'];
			$dDate = $row['dcutdate'];
			$nAmount = $row['namount'];
			$nApplied = $row['napplied'];
			
			$cRemarks = $row['cremarks'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

?>
	<form action="OR_editsave2.php" name="frmOR" id="frmOR" method="post" onSubmit="return chkform();">
		<fieldset>
    	<legend>
        <div class="col-xs-6 nopadding"> Receive Payment </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
					<?php
						if($lCancelled==1){
							echo "<font color='#FF0000'><b>CANCELLED</b></font>";
						}
						
						if($lPosted==1){
							echo "<font color='#FF0000'><b>POSTED</b></font>";
						}
					?>
   			</div>
  		</legend>	

      <table width="100%" border="0">
				<tr>
					<tH>Trans. No.:</tH>
					<td colspan="3" style="padding:2px;">
						<div class="col-xs-12 nopadding">
							<div class="col-xs-2 nopadding"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmOR');"></div>
						
							<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $corno;?>">
						
							<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
							<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
							<input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
								&nbsp;&nbsp;
							<div id="statmsgz" style="display:inline"></div>
							</div>						
					</td>
				</tr>
				<tr>
					<tH width="210">
						Deposit To Account    
					</tH>
					<td style="padding:2px;" width="500">
						<div class="col-xs-12 nopadding">
							<div class="col-xs-6 nopadding">
								<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
							</div> 
							<div class="col-xs-6 nopadding">
								<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
							</div>
						</div>     
					</td>
					<tH width="150">Date:</tH>
					<td style="padding:2px;">
						<div class="col-xs-8 nopadding">
						<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($dDate),'m/d/Y'); ?>" />
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
					<tH width="210" valign="top">Payor:</tH>
					<td valign="top" style="padding:2px">
					<div class="col-xs-12 nopadding">
							<div class="col-xs-6 nopadding">
								<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off" value="<?php echo $cName ;?>"  />
					</div> 
					<div class="col-xs-3 nopadwleft">
								<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode ;?>">
							</div>
					</div>        
					</td>
					<th valign="top" style="padding:2px">Receipt No.:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-12 nopadding">
						<div class="col-xs-8 nopadding">
						<input type="text" class="form-control input-sm" id="txtORNo" name="txtORNo" width="20px" required value="<?php echo $cORNo;?>" readonly>
					</div>
				</tr>
				<tr>
					<tH width="210" valign="top">Payment Method:</tH>
					<td valign="top" style="padding:2px">
					<div class="col-xs-12 nopadding">
					<div class="col-xs-6 nopadding">
						<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
								<option value="cash" <?php if($cPayMeth=="cash") { echo "selected"; } ?>>Cash</option>
								<option value="cheque" <?php if($cPayMeth=="cheque") { echo "selected"; } ?>>Cheque</option>
								<option value="bank transfer" <?php if($cPayMeth=="bank transfer") { echo "selected"; } ?>>Bank Transfer</option>
								<option value="mobile payment" <?php if($cPayMeth=="mobile payment") { echo "selected"; } ?>>Mobile Payment</option>
								<option value="credit card" <?php if($cPayMeth=="credit card") { echo "selected"; } ?>>Credit Card</option>
								<option value="debit card" <?php if($cPayMeth=="debit card") { echo "selected"; } ?>>Debit Card</option>
							</select>
					</div>
					
					<div class="col-xs-4 nopadwleft">
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" style="width:100%" name="btnDet" id="btnDet">Details</button>
					</div>
					</div>
					
					
					</td>
					<tH style="padding:2px">Amount Received:</tH>
					<td valign="top" style="padding:2px">
						<?php 
							if($cPayMeth=="Cheque") 
							{ 
								$vargrossstat = "readonly"; 
							} else{
								$vargrossstat = "";
							}
						?>
						<div class="col-xs-8 nopadding">
							<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control text-right" value="<?php echo $nAmount;?>" <?php echo $vargrossstat; ?> autocomplete="off" onKeyUp="computeGross();" required>
						</div></td>
				</tr>
				<tr>
					<tH width="210" rowspan="2" valign="top">Memo:</tH>

					<td rowspan="2" valign="top" style="padding:2px">
					<div class="col-xs-12 nopadding">
						<div class="col-xs-10 nopadding">
							<textarea class="form-control" rows="1" id="txtremarks" name="txtremarks"><?php echo $cRemarks;?></textarea>
						</div>
					</div>
					</td>
					<th valign="top" style="padding:2px">Amount Applied:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
						<input type="text" id="txtnApplied" name="txtnApplied" class="numericchkamt form-control" value="<?php echo $nApplied;?>" style="text-align:right" readonly>
					</div></td>
				</tr>
				<tr>
					<th valign="top" style="padding:2px">Out of Balance:</th>
					<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
						<input type="text" id="txtnOutBal" name="txtnOutBal" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off" readonly>
					</div></td>
				</tr>
      </table>
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
											<th scope="col" width="150px" class="text-center" nowrap>VAT</th>
											<th scope="col" width="150px" class="text-center" nowrap>NetofVat</th>
											<th scope="col" class="text-center" nowrap>EWTCode</th>                            
											<th scope="col" class="text-center" nowrap>EWTRate(%)</th>
											<th scope="col" class="text-center" nowrap>EWTAmt</th>
											<th scope="col" width="150px" class="text-center" nowrap>DM</th>
											<th scope="col" width="150px" class="text-center" nowrap>CM</th>
											<th scope="col" width="150px" class="text-center" nowrap>Payments</th>
											<th scope="col" width="150px" class="text-center" nowrap>Total Due</th>
											<th scope="col" width="150px" class="text-center" nowrap>Amt Applied&nbsp;</th>
															
											<th scope="col" nowrap>&nbsp;Acct No</th>
											<th scope="col" width="500px" nowrap>&nbsp;Acct Desc</th>
											<th scope="col">&nbsp;</th>
										</tr>
									</thead>
                  <tbody>           
                			<?php

                        $sqlbody = mysqli_query($con,"select a.*,b.dcutdate, c.cacctdesc from receipt_sales_t a left join sales b on a.csalesno=b.ctranno and a.compcode=b.compcode left join accounts c on a.cacctno=c.cacctid and a.compcode=c.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
            
                        if (mysqli_num_rows($sqlbody)!=0) {
                          $cntr = 0;
                          while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                            $cntr = $cntr + 1;
                			?>
                          <tr>
                            <td><div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo<?php echo $cntr;?>' id='txtcSalesNo<?php echo $cntr;?>' value='<?php echo $rowbody['csalesno'];?>'  /><?php echo $rowbody['csalesno'];?></div></td>
                            <td align='center'><?php echo $rowbody['dcutdate'];?></td>
                            
                            <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtSIGross<?php echo $cntr;?>' id='txtSIGross<?php echo $cntr;?>' value='<?php echo $rowbody['namount'];?>' readonly="true" /></div></td>
                            
                            <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtvatamt<?php echo $cntr;?>' id='txtvatamt<?php echo $cntr;?>' value='<?php echo $rowbody['nvat'];?>' readonly="true" /></td>

                            <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtnetvat<?php echo $cntr;?>' id='txtnetvat<?php echo $cntr;?>' value='<?php echo $rowbody['nnet'];?>' readonly="true" /></td>
                            
                             <td width="150px"><input type='text' class='form-control input-xs' placeholder='EWT Code' name='txtnEWT<?php echo $cntr;?>' id='txtnEWT<?php echo $cntr;?>' autocomplete=\"off\" value="<?php echo $rowbody['cewtcode'];?>" /></td>

                             <td width="150px"><input type='text' class='form-control input-xs text-right' placeholder='EWT Rate' name='txtnEWTRate<?php echo $cntr;?>' value="<?php echo $rowbody['newtrate'];?>" id='txtnEWTRate<?php echo $cntr;?>' readonly="true" /></td>

                             <td width="180px"><input type='text' class='numericchkamt form-control input-xs text-right' placeholder='EWT Amt' name='txtnEWTAmt<?php echo $cntr;?>'  value="<?php echo $rowbody['newtamt'];?>" id='txtnEWTAmt<?php echo $cntr;?>' readonly="true" /></td>
             
                             <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtndebit<?php echo $cntr;?>' id='txtndebit<?php echo $cntr;?>' value='<?php echo $rowbody['ndm'];?>' readonly="true" /></td>
            
                            <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtncredit<?php echo $cntr;?>' id='txtncredit<?php echo $cntr;?>' value='<?php echo $rowbody['ncm'];?>' readonly="true" /></td>
            
                            <td align='right'><input type='text' class='numericchkamt form-control input-xs text-right' name='txtnpayments<?php echo $cntr;?>' id='txtnpayments<?php echo $cntr;?>' value='<?php echo $rowbody['npayment'];?>' readonly="true" /></td>
            
                            <td align='right'><input type='text' name='txtDue<?php echo $cntr;?>' id='txtDue<?php echo $cntr;?>' value='<?php echo $rowbody['ndue'];?>' class='numericchkamt form-control input-xs text-right' readonly="true" /></div></td>
                            
                            <td><input type='text' class='numericchkamt form-control input-xs' name='txtApplied<?php echo $cntr;?>' id='txtApplied<?php echo $cntr;?>' value="<?php echo $rowbody['napplied'];?>" style="text-align:right" autocomplete="off" /></div></td>
                            
                             <td><div class='col-xs-12 nopadding'><input type='text' name='txtcSalesAcctNo<?php echo $cntr;?>' id='txtcSalesAcctNo<?php echo $cntr;?>' value='<?php echo $rowbody['cacctno'];?>' class='form-control input-xs' autocomplete="off" /></td>
                             
                            <td><div class='col-xs-12 nopadding'><input type='text' name='txtcSalesAcctTitle<?php echo $cntr;?>' id='txtcSalesAcctTitle<?php echo $cntr;?>' value='<?php echo $rowbody['cacctdesc'];?>' class='form-control input-xs' /></td>

                            <td><div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow(this);' autocomplete="off" /></div></td>
                          </tr>
                          
                          <script>
														$("#txtnEWT<?php echo $cntr;?>").typeahead({
															items: 10,
															source: function(request, response) {
																$.ajax({
																	url: "../th_ewtcodes.php",
																	dataType: "json",
																	data: {
																		query: $("#txtnEWT<?php echo $cntr;?>").val()
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
																$("#txtnEWT<?php echo $cntr;?>").val(item.ctaxcode).change(); 
																$("#txtnEWTRate<?php echo $cntr;?>").val(item.nrate);
																
																var xcb = 0;
																var xcbdue = 0;
																
																varnnet =  $("#txtnetvat<?php echo $cntr;?>").val().replace(/,/g,'');
																varngrs = $("#txtSIGross<?php echo $cntr;?>").val().replace(/,/g,'');
																ndue = $("#txtDue<?php echo $cntr;?>").val().replace(/,/g,'');

																if(item.cbase=="NET"){
																	xcb = parseFloat(varnnet)*(item.nrate/100);
																}else{
																	xcb = parseFloat(varngrs)*(item.nrate/100);
																}
																
																$("#txtnEWTAmt<?php echo $cntr;?>").val(xcb);
																//recompute due
																xcbdue = varngrs - xcb;
																
																$("#txtDue<?php echo $cntr;?>").val(xcbdue); 
																
																$("#txtnEWTAmt<?php echo $cntr;?>").autoNumeric('destroy');
																$("#txtnEWTAmt<?php echo $cntr;?>").autoNumeric('init',{mDec:2});

																$("#txtDue<?php echo $cntr;?>").autoNumeric('destroy');
																$("#txtDue<?php echo $cntr;?>").autoNumeric('init',{mDec:2});
																
																$("#txtApplied<?php echo $cntr;?>").autoNumeric('destroy');
																$("#txtApplied<?php echo $cntr;?>").autoNumeric('init',{mDec:2});
																
																computeGross();
																
																//setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
																
															}
														});
													
														$("#txtcSalesAcctNo<?php echo $cntr;?>, #txtcSalesAcctTitle<?php echo $cntr;?>").on("click focus", function(event) {
															$(this).select();
														});
									
														$("#txtcSalesAcctNo<?php echo $cntr;?>").on("keyup", function(event) {
															if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
															
																if(event.keyCode==13 ){	
																var dInput = this.value;
														
																	$.ajax({
																		type:'post',
																		url:'../getaccountid.php',
																		data: 'c_id='+ $(this).val(),                 
																		success: function(value){
																			if(value.trim()!=""){
																				$("#txtcSalesAcctTitle<?php echo $cntr;?>").val(value.trim());
																			}
																		}
																	});
																}
																
																setPosi("txtcSalesAcctNo<?php echo $cntr;?>",event.keyCode,'MyTable');
																
															}
															
														});
									
														$("#txtcSalesAcctTitle<?php echo $cntr;?>").typeahead({
													
															items: 10,
															source: function(request, response) {
																$.ajax({
																	url: "../th_accounts.php",
																	dataType: "json",
																	data: {
																		query: $("#txtcSalesAcctTitle<?php echo $cntr;?>").val()
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
																$("#txtcSalesAcctTitle<?php echo $cntr;?>").val(item.name).change(); 
																$("#txtcSalesAcctNo<?php echo $cntr;?>").val(item.id);
																
																setPosi("txtcSalesAcctTitle<?php echo $cntr;?>",13,'MyTable');
																
															}
														});
						 						 </script>
                
												<?php
													}
												}
												?>
           				</tbody>
            		</table>
            		<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
							</div>
						</div>
<!--
		</div>

	</div>
</div>
																	-->

																	<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
			<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='OR.php';" id="btnMain" name="btnMain">
			Back to Main<br>(ESC)</button>
				
					<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='OR_new2.php';" id="btnNew" name="btnNew">
			New<br>(F1)</button>

						<div class="dropdown" style="display:inline-block !important;">
							<button type="button" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle">
								SI <br>(Insert) <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="javascript:;" onClick="getInvs('Trade');">Trade</a></li>
								<li><a href="javascript:;" onClick="getInvs('Non-Trade');">Non-Trade</a></li>
							</ul>
						</div>

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmOR');" id="btnUndo" name="btnUndo">
						Undo Edit<br>(CTRL+Z)
					</button>

					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $corno;?>');" id="btnPrint" name="btnPrint">
			Print<br>(CTRL+P)
					</button>
					
					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
			Edit<br>(CTRL+E)    </button>
					
					<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
			Save<br>(CTRL+S)    </button>

		</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>



<!-- Bootstrap modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">Invoice List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
            	<div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
							<input name="invtyp" id="invtyp" type="hidden" value="" />
                  <table name='MyORTbl' id='MyORTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Invoice No</th>
                      <th>Sales Date</th>
                      <th>Gross</th>
                      <th>DM</th>
                      <th>CM</th>
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



<!--CASH DETAILS DENOMINATIONS -->
<div class="modal fade" id="CashModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CASH DENOMINATION</h3>
            </div>
            <div class="modal-body">
            
                  <table width="100%" border="0" class="table table-scroll table-condensed">
                  <thead>
                      <tr>
                        <td align="center"><b>Denomination</b></td>
                        <td align="center"><b>Pieces</b></td>
                        <td align="center"><b>Amount</b></td>
                      </tr>
                  </thead>
                  	<?php
											$cntr = 0;
											$Pcs1000 = 0;
											$Pcs500 = 0;
											$Pcs200 = 0;
											$Pcs100 = 0;
											$Pcs50 = 0;
											$Pcs20 = 0;
											$Pcs10 = 0;
											$Pcs5 = 0;
											$Pcs1 = 0;
											$Pcs025 = 0;
											$Pcs010 = 0;
											$Pcs005 = 0;
											$Amt1000 = 0;
											$Amt500 = 0;
											$Amt200 = 0;
											$Amt100 = 0;
											$Amt50 = 0;
											$Amt20 = 0;
											$Amt10 = 0;
											$Amt5 = 0;
											$Amt1 = 0;
											$Amt025 = 0;
											$Amt010 = 0;
											$Amt005 = 0;


						if($cPayMeth=="Cash"){
							
							$sqlbody = mysqli_query($con,"select a.* from receipt_cash_t a where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
				
										if (mysqli_num_rows($sqlbody)!=0) {
											while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
												if($rowbody['ndenomination']==1000){
													$Pcs1000 = $rowbody['npieces'];
													$Amt1000 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==500){
													$Pcs500 = $rowbody['npieces'];
													$Amt500 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==200){
													$Pcs200 = $rowbody['npieces'];
													$Amt200 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==100){
													$Pcs100 = $rowbody['npieces'];
													$Amt100 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==50){
													$Pcs50 = $rowbody['npieces'];
													$Amt50 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==20){
													$Pcs20 = $rowbody['npieces'];
													$Amt20 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==10){
													$Pcs10 = $rowbody['npieces'];
													$Amt10 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==5){
													$Pcs5 = $rowbody['npieces'];
													$Amt5 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==1){
													$Pcs1 = $rowbody['npieces'];
													$Amt1 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.25){
													$Pcs025 = $rowbody['npieces'];
													$Amt025 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.10){
													$Pcs010 = $rowbody['npieces'];
													$Amt010 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.05){
													$Pcs005 = $rowbody['npieces'];
													$Amt005 = $rowbody['namount'];
												}
											}
										}
						}
					?>

                  <tbody>
                      <tr>
                        <td align="center">1000</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1000' id='txtDenom1000' value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>" /></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1000' id='txtAmt1000' readonly value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">500</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom500' id='txtDenom500' value="<?php if($Pcs500<>0){ echo $Pcs500; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt500' id='txtAmt500' readonly value="<?php if($Amt500<>0){ echo $Amt500; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">200</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom200' id='txtDenom200' value="<?php if($Pcs200<>0){ echo $Pcs200; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt200' id='txtAmt200' readonly value="<?php if($Amt200<>0){ echo $Amt200; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">100</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom100' id='txtDenom100' value="<?php if($Pcs100<>0){ echo $Pcs100; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt100' id='txtAmt100' readonly value="<?php if($Amt100<>0){ echo $Amt100; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">50</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom50' id='txtDenom50' value="<?php if($Pcs50<>0){ echo $Pcs50; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt50' id='txtAmt50' readonly value="<?php if($Amt50<>0){ echo $Amt50; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">20</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom20' id='txtDenom20' value="<?php if($Pcs20<>0){ echo $Pcs20; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt20' id='txtAmt20' readonly value="<?php if($Amt20<>0){ echo $Amt20; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">10</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom10' id='txtDenom10' value="<?php if($Pcs10<>0){ echo $Pcs10; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt10' id='txtAmt10' readonly value="<?php if($Amt10<>0){ echo $Amt10; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">5</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom5' id='txtDenom5' value="<?php if($Pcs5<>0){ echo $Pcs5; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt5' id='txtAmt5' readonly value="<?php if($Amt5<>0){ echo $Amt5; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">1</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1' id='txtDenom1' value="<?php if($Pcs1<>0){ echo $Pcs1; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1' id='txtAmt1' readonly value="<?php if($Amt1<>0){ echo $Amt1; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.25</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom025' id='txtDenom025' value="<?php if($Pcs025<>0){ echo $Pcs025; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt025' id='txtAmt025' readonly value="<?php if($Amt025<>0){ echo $Amt025; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.10</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom010' id='txtDenom010' value="<?php if($Pcs010<>0){ echo $Pcs010; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt010' id='txtAmt010' readonly value="<?php if($Amt010<>0){ echo $Amt010; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.05</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom005' id='txtDenom005' value="<?php if($Pcs005<>0){ echo $Pcs005; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt005' id='txtAmt005' readonly value="<?php if($Amt005<>0){ echo $Amt005; } ?>"/></div></td>
                      </tr>
                    </tbody>
                    </table>
            
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
                <h3 class="modal-title" id="invheader">CHEQUE DETAILS</h3>
            </div>
            <div class="modal-body">
            	<?php
											$cBank = "";
											$cCheckNo = "";
											$dDateCheck = "";
											$nCheckAmt = "";
											
					if($cPayMeth=="Cheque"){
						
						$sqlbody = mysqli_query($con,"select a.* from receipt_check_t a where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
			
									if (mysqli_num_rows($sqlbody)!=0) {
										$cntr = 0;
										while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
											$cBank = $rowbody['cbank'];
											$cCheckNo = $rowbody['ccheckno'];
											$dDateCheck = $rowbody['ddate'];
											$nCheckAmt = $rowbody['nchkamt'];
										}
									}
					}
				?>

                  <table width="100%" border="0" class="table table-condensed">
                      <tr>
                        <td><b>Bank Name</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' placeholder="Input Bank Name" value="<?php echo $cBank; ?>"/></div></td>
                      </tr>
                      <tr>
                        <td><b>Cheque Date</b></td>
                        <td>
                        <div class='col-sm-12'>
                            <input type='text' class="form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate"  value="<?php echo date_format(date_create($dDateCheck),'m/d/Y'); ?>"/>

                        </div>
                        </td>
                      </tr>
                      <tr>
                        <td><b>Cheque Number</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' placeholder="Input Cheque Number"  value="<?php echo $cCheckNo; ?>"/></div></td>
                      </tr>
                       <tr>
                        <td><b>Cheque Amount</b></td>
                        <td><div class='col-xs-12'><input type='text' class='numericchkamt form-control input-sm' name='txtCheckAmt' id='txtCheckAmt' placeholder="Input Cheque Amount"  value="<?php echo $nCheckAmt; ?>" /></div></td>
                      </tr>
                    </table>
            
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


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

</form>

<?php
}
else{
?>

<form action="OR_edit2.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Receive Payment</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">OR No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>OR No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

</body>
</html>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='OR_new2.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){ 
			e.preventDefault();
			$("#btnSave").click();
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
			printchk('<?php echo $corno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmOR');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			$("#btnMain").click();
		}
	  }
	});
	
	$(document).ready(function(){

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		$("input.numericchkamt").autoNumeric('init',{mDec:2});
		$("input.numericchkamt").on("click focus", function () {
      $(this).select();
    });

		$("input.numericint").autoNumeric('init',{mDec:0});
 		$("input.numericint").on("click focus", function () {
      $(this).select();
    });
		
		$("input.numeric").autoNumeric('init',{mDec:2});
    $("input.numeric").on("click focus", function () {
      $(this).select();
    });

		
		$("input.numeric").on("keyup", function (e) {
			var nme = $(this).attr('name');
			var x = nme.replace(/[0-9]/g, '');
			
			if(x=="txtApplied"){
				var tblnme = "MyTable";
			}else if(x=="txtLoApplied"){
				var tblnme = "MyTbl";
			}else if(x=="txtnotDR" || x=="txtnotCR"){
				var tblnme = "MyTblOthers";
			}
			
			setPosi($(this).attr('name'),e.keyCode,tblnme);
			computeGross();
		});
		
		

	$('#frmOR').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
		e.preventDefault();
		return false;
	  }
	});           
           // Bootstrap DateTimePicker v4
           $('#datetimepicker4, #txtChekDate, #date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
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
		if($("#selpaytype").val() == "None"){
			$('#txtnGross').val($(this).val());
		}
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
			 return '<div style="border-top:1px solid gray; width: 300px"><span>'+ item.id + '</span><br><small>' + item.value + "</small></div>";
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
			
		if($('#selpayment').val() == "Cheque"){
			if($('#txtBankName').val() == "" || $('#txtChekDate').val() == "" || $('#txtCheckNo').val() == "" || $('#txtCheckAmt').val() == ""){
				alert("Please complete your cheque details!");
				subz = "NO";
			}
		}
		
			var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow1 = tbl1.length-1;
			if(lastRow1!=0){
				$("#hdnrowcnt").val(lastRow1);				
			}
	
		if(lastRow1==0){
				alert("Details Required!");
				subz = "NO";
		}


		if( parseFloat($('#txtnGross').val()) != parseFloat($('#txtnApplied').val()) ){
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
			var tempamt = document.getElementById('txtAmt' + z);
			var tempdue= document.getElementById('txtDue' + z);
			var tempapplies = document.getElementById('txtApplied' + z);
			
			var x = z-1;
			tempsalesno.id = "txtcSalesNo" + x;
			tempsalesno.name = "txtcSalesNo" + x;
			tempamt.id = "txtAmt" + x;
			tempamt.name = "txtAmt" + x;
			tempdue.id = "txtDue" + x;
			tempdue.name = "txtDue" + x;
			tempapplies.id = "txtApplied" + x;
			tempapplies.name = "txtApplied" + x;
			
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
						$("<td>").html("<input type='checkbox' value='"+item.csalesno+"' name='chkSales[]'>"),
						$("<td>").text(item.csalesno),
						$("<td>").text(item.dcutdate),
						$("<td>").text(item.ngross),
						$("<td>").text(item.ndebit),
						$("<td>").text(item.ncredit)
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

//var rowCount = $('#MyTable tr').length-1;
//var rcnt = rowCount - 1;	
 $("input[name='chkSales[]']:checked").each( function () {
	 i += 1;
	// rcnt += 1;
	var tbl = document.getElementById('MyTable').getElementsByTagName('tbody')[0];
 // alert(tbl.rows.length);
			
				 var id = $(this).val();
				 $.ajax({
				url : "th_getsalesdetails.php?id=" + id + "&typ=" + $('#invtyp').val(),
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{				
				
					console.log(data);
					$.each(data,function(index,item){
						 
						var ngross = item.ngross;
						var ndebit = item.ndebit;
						var ncredit = item.ncredit;
						var npayment = item.npayment;
						var ndue = 0;
						 
						ndue = ((parseFloat(ngross) + parseFloat(ndebit)) - parseFloat(ncredit)) - parseFloat(npayment);
						
						if(parseFloat(npayment)==0){
							npayment = "0.00"
						}
						
						var lastRow = tbl.rows.length + 1;							
						var z=tbl.insertRow(-1);

						var a=z.insertCell(-1);
							a.innerHTML ="<div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo"+lastRow+"' id='txtcSalesNo"+lastRow+"' value='"+item.csalesno+"' />"+item.csalesno+"</div>";
						
						var b=z.insertCell(-1);
							b.align = "center";
							b.innerHTML = item.dcutdate;
							
						var c=z.insertCell(-1);
							c.align = "right";
							c.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs text-right' name='txtSIGross"+lastRow+"' id='txtSIGross"+lastRow+"' value='"+item.ngross+"' readonly /></div>";
							
						var c2=z.insertCell(-1);
							c2.align = "right";
							c2.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtvatamt"+lastRow+"' id='txtvatamt"+lastRow+"' value='"+item.nvat+"' readonly />";
							
						var c3=z.insertCell(-1);
							c3.align = "right";
							c3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnetvat"+lastRow+"' id='txtnetvat"+lastRow+"' value='"+item.nnet+"' readonly />"; 
						
						var l=z.insertCell(-1);
							l.innerHTML = "<input type='text' class='form-control input-xs' placeholder='EWT Code' name='txtnEWT"+lastRow+"' id='txtnEWT"+lastRow+"' autocomplete=\"off\" />";
						
						var l2=z.insertCell(-1);
							l2.innerHTML = "<input type='text' class='form-control input-xs text-right' placeholder='EWT Rate' name='txtnEWTRate"+lastRow+"' value=\"0\" id='txtnEWTRate"+lastRow+"' readonly=\"true\" />";
							
						var l3=z.insertCell(-1);
							l3.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' placeholder='EWT Amt' name='txtnEWTAmt"+lastRow+"'  value=\"0.00\" id='txtnEWTAmt"+lastRow+"' readonly=\"true\" />";
								
						var d=z.insertCell(-1);
							d.align = "right";
							d.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtndebit"+lastRow+"' id='txtndebit"+lastRow+"' value='"+item.ndebit+"' readonly=\"true\" />";
							
						var e=z.insertCell(-1);
							e.align = "right";
							e.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtncredit"+lastRow+"' id='txtncredit"+lastRow+"' value='"+item.ncredit+"' readonly=\"true\" />";
							
						var f=z.insertCell(-1);
							f.align = "right";
							f.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtnpayments"+lastRow+"' id='txtnpayments"+lastRow+"' value='"+item.npayment+"' readonly=\"true\" />";
							
						var g=z.insertCell(-1);
							g.align = "right";
							g.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtDue"+lastRow+"' id='txtDue"+lastRow+"' value='"+ndue+"' readonly=\"true\" />";
							
						var h=z.insertCell(-1);
							h.innerHTML = "<input type='text' class='numeric form-control input-xs text-right' name='txtApplied"+lastRow+"' id='txtApplied"+lastRow+"' value='"+ndue+"' style='text-align:right' autocomplete=\"off\" />";

						var i=z.insertCell(-1);
							i.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='form-control input-xs' name='txtcSalesAcctNo"+lastRow+"' id='txtcSalesAcctNo"+lastRow+"' value='"+item.cacctno+"' autocomplete=\"off\" /></div>";
							
						var j=z.insertCell(-1);
							j.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='form-control input-xs' name='txtcSalesAcctTitle"+lastRow+"' id='txtcSalesAcctTitle"+lastRow+"' value='"+item.ctitle+"' autocomplete=\"off\" /></div>";
							
						var k=z.insertCell(-1);
							k.innerHTML = "<div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' value='delete' onClick='deleteRow(this);' /></div>";
						
						var varnnet = item.nnet;
						var varngrs = item.ngross;	
										 
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
										
										var xcb = 0;
										var xcbdue = 0;

										varnnet =  $("#txtnetvat"+lastRow).val().replace(/,/g,'');
										varngrs = $("#txtSIGross"+lastRow).val().replace(/,/g,'');
										ndue = $("#txtDue"+lastRow).val().replace(/,/g,'');
										
										if(item.cbase=="NET"){
											xcb = parseFloat(varnnet)*(item.nrate/100);
										}else{
											xcb = parseFloat(varngrs)*(item.nrate/100);
										}
										
										$("#txtnEWTAmt"+lastRow).val(xcb);
										//recompute due
										xcbdue = ndue - xcb;
										
										$("#txtDue"+lastRow).val(xcbdue);

										$("#txtApplied"+lastRow).val(xcbdue);

										$("#txtnEWTAmt"+lastRow).autoNumeric('destroy');
										$("#txtnEWTAmt"+lastRow).autoNumeric('init',{mDec:2});

										$("#txtDue"+lastRow).autoNumeric('destroy');
										$("#txtDue"+lastRow).autoNumeric('init',{mDec:2});
										
										$("#txtApplied"+lastRow).autoNumeric('destroy');
										$("#txtApplied"+lastRow).autoNumeric('init',{mDec:2});
										
										computeGross();
										
										//setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
										
									}
								});
								
								
								$("#txtcSalesAcctNo"+lastRow+", #txtcSalesAcctTitle"+lastRow).on("click focus", function(event) {
									$(this).select();
								});
								
								$("#txtcSalesAcctNo"+lastRow).on("keyup", function(event) {
									if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
									
										if(event.keyCode==13 ){	
										var dInput = this.value;
								
											$.ajax({
												type:'post',
												url:'../getaccountid.php',
												data: 'c_id='+ $(this).val(),                 
												success: function(value){
													if(value.trim()!=""){
														$("#txtcSalesAcctTitle"+lastRow).val(value.trim());
													}
												}
											});
										}
										
										setPosi("txtcSalesAcctNo"+lastRow,event.keyCode,'MyTable');
										
									}
									
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
												 
					});

					computeGross();
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}
				
			});

	 
	 
	 
 });
 
 if(i==0){
	 alert("No Invoice is selected!")
 }
 
 $('#myModal').modal('hide');
 
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


function disabled(){

	$("#frmOR :input").attr("disabled", true);
	
	
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
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		if($("#selpayment").val()=="Cheque" || $("#selpaytype").val()=="Sales"){
			$("#txtnGross").attr("readonly", true);
		}
		
		$("#frmOR :input").attr("disabled", false);
		
		$("#txtctranno").attr("readonly", true);
		$("#btnMain").attr("disabled", true);
		$("#btnNew").attr("disabled", true);
		$("#btnPrint").attr("disabled", true);
		$("#btnEdit").attr("disabled", true);
	
	}

}

function chkSIEnter(keyCode,frm){

	if(keyCode==13){			
		document.getElementById(frm).action = "OR_edit2.php";
		document.getElementById(frm).submit();
	}
}

</script>
