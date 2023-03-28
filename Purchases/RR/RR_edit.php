<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Receive_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$employeeid = $_SESSION['employeeid'];
$company = $_SESSION['companyid'];
if(isset($_REQUEST['txtctranno'])){
		$cpono = $_REQUEST['txtctranno'];
}
else{
		$cpono = $_REQUEST['txtcpono'];
	}

$sqlhead = mysqli_query($con,"select a.ctranno, a.ccode, a.cremarks, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dreceived,'%m/%d/%Y') as dneeded, a.ngross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, a.ccustacctcode, b.cname, a.crefsi from receive a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno = '$cpono'");


						 $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ALLOW_REF_RR'"); 
					
						 if (mysqli_num_rows($result)!=0) {
						 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
						 
							 $nCHKREFvalue = $all_course_data['cvalue']; 
							
						 }

						// 0 = Allow No Reference
						// 1 = W/ Reference Check Qty .. Qty must be less than or equal to reference
						// 2 = W/ Reference Open Qty .. allow qty even if more tha reference

/*
	function listcurrencies(){ //API for currency list
		$apikey = $_SESSION['currapikey'];
		
		//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");

		//if ( $json === false )
		//{
		  // return 1;
		//}else{

			$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
		   return $json;
		//}
		
	}
	*/

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
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

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
-->

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		$Gross = $row['ngross'];
		$CustSI = $row['crefsi'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
?>

<form action="RR_editsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Receiving</legend>	
        <input type="hidden" value="<?php echo $nCHKREFvalue;?>" name="hdnCHECKREFval" id="hdnCHECKREFval">
        	<table width="100%" border="0">
						<tr>
							<tH>RR No.:</tH>
							<td colspan="2" style="padding:2px"><div class="col-xs-3 nopadding"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
								
								
								<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cpono;?>">
								<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
								<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">

								&nbsp;&nbsp;
								<div id="statmsgz" style="display:inline"></div>
								</td>
								<td style="padding:2px" align="center"> &nbsp; </td>
						</tr>

						<tr>
							<tH width="100">Supplier:</tH>
							<td style="padding:2px">
								<div class="col-xs-12 nopadding">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="<?php echo $CustCode;?>" readonly>
									</div>

									<div class="col-xs-8 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="<?php echo $CustName;?>">
									</div> 
								</div>
							</td>
							<tH width="150">Supplier DR:</tH>
							<td width="250" style="padding:2px;">
								<div class="col-xs-8 nopadding">
									<input type='text' class="form-control input-sm" id="txtSuppSI" name="txtSuppSI" required value="<?php echo $CustSI; ?>"/>
								</div>
							</td>
						</tr>

						<tr>
							<tH width="100">Remarks:</tH>
							<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks; ?>"></div></td>
							<tH width="150" style="padding:2px">Date Received:</tH>
							<td style="padding:2px">
								<div class="col-xs-8 nopadding">
									<input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" value="<?php echo $DateNeeded; ?>" />
								</div>
							</td>
						</tr>
	
						<tr>
							<tH width="100"></tH>
							<td style="padding:2px" colspan="3">
								<div class="col-xs-12">
												<div class="col-xs-3 nopadding">
													<!--<select class="form-control input-sm" name="selbasecurr" id="selbasecurr">--> 						
														<?php
														/*
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
														*/
																	//	$objcurrs = listcurrencies();
																	//	$objrows = json_decode($objcurrs,true);
																			
																//foreach($objrows as $rows){
																//	if ($nvaluecurrbase==$rows['currencyCode']) {
																//		$nvaluecurrbasedesc = $rows['currencyName'];
																//	}
																	
														?>
																<!--	<option value="<?//=$rows['currencyCode']?>" <?//php if ($nvaluecurrbase==$rows['currencyCode']) { echo "selected='true'"; } ?>><?//=$rows['currencyCode']." - ".strtoupper($rows['currencyName'])?></option>-->
														<?php
																//}
														?>
													<!--</select>-->
													<!--	<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?//php echo $nvaluecurrbase; ?>"> 	-->
													<!--	<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?//php echo $nvaluecurrbasedesc; ?>"> -->
												</div>
												<div class="col-xs-1 nopadwleft"><!-- class="numeric required form-control input-sm text-right"
													<input type='hidden' id="basecurrval" name="basecurrval" value="1">	 -->
												</div>

												<div class="col-xs-5" id="statgetrate" style="padding: 4px !important"> 															
												</div>
								</div>
							</td>
							
						</tr>

						<tr>
							<td colspan="2">&nbsp;</td>
							<th style="padding:2px"><!--<span style="padding:2px">PURCHASE TYPE:</span>-->&nbsp;</th>
							<td>&nbsp;
								<!--
								<div class="col-xs-5">
										<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
											<option value="Grocery">Grocery</option>
											<option value="Cripples">Cripples</option>
										</select>
								</div>
								-->
							</td>
							</tr>
						<tr>
							<td colspan="2">
							<?php
								if($nCHKREFvalue==0) {
							?>
								<div class="col-xs-12 nopadwdown">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." width="25" tabindex="4"  autocomplete="off">
									</div>
									<div class="col-xs-8 nopadwleft">
										<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL+F) Search Product Name..." size="80" tabindex="5" autocomplete="off">
									</div>
								</div>
							<?php
								
								}else{
							?> 
								<input type="hidden" id="txtprodid" name="txtprodid">
								<input type="hidden" id="txtprodnme" name="txtprodnme">
							<?php
								}

							?> 

									<input type="hidden" name="hdnunit" id="hdnunit">
							</td>
							<td></td>
							<td></td>

						</tr>
					</table>

					<ul class="nav nav-tabs">
						<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Items List</a></li>
						<li id="liacct"><a href="#2Acct" data-toggle="tab">Items Inventory</a></li>
					</ul>

					<div class="tab-content nopadwtop2x">
						<div class="tab-pane active" id="1Det">

								<div class="alt2" dir="ltr" style="
									margin: 0px;
									padding: 3px;
									border: 1px solid #919b9c;
									width: 100%;
									height: 300px;
									text-align: left;
									overflow: auto">
					
										<table id="MyTable" class="MyTable" width="100%" cellpadding="3px">
												<thead>
													<tr>
														<th style="border-bottom:1px solid #999">&nbsp;</th>
														<th style="border-bottom:1px solid #999">Code</th>
														<th style="border-bottom:1px solid #999">Description</th>
														<th style="border-bottom:1px solid #999">UOM</th>
														<th style="border-bottom:1px solid #999">Factor</th>
														<th style="border-bottom:1px solid #999">Qty</th>
														<!--<th style="border-bottom:1px solid #999">Price</th>
														<th style="border-bottom:1px solid #999">Amount</th>
														<th style="border-bottom:1px solid #999">Total Amt in <?//php echo $nvaluecurrbase; ?></th>-->
																				<!--<th style="border-bottom:1px solid #999">Date Expired</th>-->
														<th style="border-bottom:1px solid #999">&nbsp;</th>
													</tr>
												</thead>
												<tbody class="tbody">
												</tbody>
														
										</table>
								</div>
							</div>

						<div class="tab-pane" id="2Acct">

										<div class="alt2" dir="ltr" style="
																margin: 0px;
																padding: 3px;
																border: 1px solid #919b9c;
																width: 100%;
																height: 250px;
																text-align: left;
																overflow: auto">
								
												<table id="MyTable2" class="table" width="100%" border="0">
													<thead>
																<tr>
																	
																		<th style="border-bottom:1px solid #999">Item Code</th>
																		<th style="border-bottom:1px solid #999">Serial No.</th>
																		<th style="border-bottom:1px solid #999">Barcode</th>
																		<th style="border-bottom:1px solid #999">UOM</th>
																		<th style="border-bottom:1px solid #999">Qty</th>
																		<th style="border-bottom:1px solid #999">Location</th>
																		<th style="border-bottom:1px solid #999">Expiration Date</th>
																		<th style="border-bottom:1px solid #999">&nbsp;</th>
																</tr>
													</thead>
													<tbody>
													</tbody>
																
												</table>
													<input type="hidden" name="hdnserialscnt" id="hdnserialscnt">
											</div>
						</div>
					</div>

					<br>

					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td>
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
					
					
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RR.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>
						
								<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='RR_new.php';" id="btnNew" name="btnNew">
									New<br>(F1)
								</button>


								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
									PO<br>(Insert)
								</button>

								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>

								<?php
									$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'Receive_print'");

									if(mysqli_num_rows($sql) == 1){
									
								?>

									<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cpono;?>');" id="btnPrint" name="btnPrint">
										Print<br>(CTRL+P)
									</button>

								<?php		
									}
								?>

								<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
									Edit<br>(CTRL+E)
								</button>
								
								<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform()" id="btnSave" name="btnSave">
									Save<br>(CTRL+S)
								</button>

								<!--<input type="hidden" id="txtnBaseGross" name="txtnBaseGross" value="0">
		 						<input type="hidden" id="txtnGross" name="txtnGross" value="0">
								-->
							
							</td>
						</tr>
					</table>

    </fieldset>
    
<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">PO List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-3 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight small">
                           <thead>
                            <tr>
                              <th>PO No</th>
                              <th>Date</th>
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
                              <th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
															<!--<th>Price</th>
															<th>Amount</th>
															<th>Cur</th>-->
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

</form>


<?php
}
else{
?>
<form action="RR_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Receiving</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">RR NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PO No. DID NOT EXIST!</b></font></tH>
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

<div class="modal fade" id="SerialMod" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="InvSerDetHdr">Inventory Detail</h4>
								<input type="hidden" class="form-control input-sm" name="serdisitmcode" id="serdisitmcode"> 
								<input type="hidden" class="form-control input-sm" name="serdisrefident" id="serdisrefident"> 
								<input type="hidden" class="form-control input-sm" name="serdisrefno" id="serdisrefno">
            </div>
            
            <div class="modal-body" style="height:20vh">

								<div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Serial No:</b></div>
										<div class="col-xs-7 nopadwtop"><input type="text" class="form-control input-sm" name="serdis" id="serdis"></div>
								</div>
                                <div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Barcode:</b></div>
										<div class="col-xs-7 nopadwtop"><input type="text" class="form-control input-sm" name="serdisbarc" id="serdisbarc"></div>
								</div>
								<div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;UOM</b></div>
										<div class="col-xs-2 nopadwtop"><input type="text" class="form-control input-sm" name="serdisuom" id="serdisuom" readonly></div>
										<div class="col-xs-1 nopadwtop"><b>&nbsp;&nbsp;&nbsp;QTY</b></div>
										<div class="col-xs-1 nopadwtop"><input type="text" class="form-control input-sm" name="serdisqty" id="serdisqty" value="1" ></div>
								</div>
								<div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Location:</b></div>
										<div class="col-xs-2 nopadwtop">
														<select class="form-control input-sm" name="selserloc" id="selserloc">
															<?php
																	$qrya = mysqli_query($con,"Select * From locations Order By cdesc");
																	while($row = mysqli_fetch_array($qrya, MYSQLI_ASSOC)){
																		echo "<option value=\"".$row['nid']."\" data-id=\"".$row['cdesc']."\">".$row['cdesc']."</option>";
																	}
															?>
														</select>
										</div>
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Expiration Date:</b></div>
										<div class="col-xs-2 nopadwtop"><input type="text" class="datepick form-control input-sm" name="dexpate" id="dexpate"></div>
								</div> 
								<div class="row nopadwtop2x">
										<div class="col-xs-12" id="TheSerialStat">
										</div>
								</div>
						</div>

						<div class="modal-footer">
								<button class="btn btn-success btn-sm" name="btnInsSer" id="btnInsSer">Insert (Enter)</button>
								<button class="btn btn-danger btn-sm" name="btnClsSer" id="btnClsSer" data-dismiss="modal" >Close (Ctrl+X)</button>
						</div>
				</div>
		</div>
</div>

<form method="post" name="frmedit" id="frmedit" action="RR_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>

<script type="text/javascript">

$(document).keydown(function(e) {	 
	
	if(e.keyCode == 112) { //F1
	 if($("#btnNew").is(":disabled")==false){
		 e.preventDefault();
		 window.location.href='RR_new.php';
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
 else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
	 if($("#btnPrint").is(":disabled")==false){
		 e.preventDefault();
		 printchk('<?php echo $cpono;?>');
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
 else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
	 e.preventDefault();
	 $('#txtprodnme').focus();
 }
 else if(e.keyCode == 45) { //Insert
	 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 openinv();
	 }
 }else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
	 if($('#SerialMod').hasClass('in')==true){
			$("#btnClsSer").click();
	 }

 }

});

	$(document).keypress(function(e) {
	  if ($("#SerialMod").hasClass('in') && (e.keycode == 13 || e.which == 13)) {
	    $("#btnInsSer").click();
	  }
	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
      format: 'MM/DD/YYYY',
    });	

		loaddetails();
		loadserials();

		$('#txtprodnme').attr("disabled", true);
		$('#txtprodid').attr("disabled", true);
		
		$("#txtcpono").focus();
		
		disabled();

});
	
$(function(){	
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
		}
	});
	
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

								
				$('#txtprodnme').val(item.cname).change(); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit);
				
				addItemName();	
				
			
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
			$('#hdnunit').val(data[2]);
		

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

	
				myFunctionadd("","","","","","","","");
				//ComputeGross();	
									
	    }
	    else{
			//alert("ITEM NOT IN THE MASTERLIST!");
			addqty();
		}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if ebter is clicked
		}
		
	});
	
	$('#SerialMod').on('shown.bs.modal', function () {
	    $('#serdis').focus();
	});

	$("#btnInsSer").on("click", function(){
			var itmcode = $("#serdisitmcode").val();
			var itmcoderefident = $("#serdisrefident").val();
			var serials = $("#serdis").val();
			var barcodes = $("#serdisbarc").val();
			var uoms = $("#serdisuom").val();
			var qtys = $("#serdisqty").val();
			var locas = $("#selserloc").val();
			var locasdesc = $("#selserloc").find(':selected').attr('data-id');
			var expz = $("#dexpate").val();      
			var refnox = $("#serdisrefno").val(); 
			InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,itmcoderefident,refnox,barcodes);
			//AddtoQtyTot(itmcode,qtys,itmcoderefident);

			//var existqty = document.getElementById("txtnqty"+itmcode+itmcoderefident).value;
			//var qtynow = parseFloat(existqty)+parseFloat(qtys);

			//document.getElementById("txtnqty"+itmcode+itmcoderefident).value = qtynow;
			
			//reset form
			$("#serdis").val("");
			$("#serdisbarc").val("");
			$("#serdisqty").val("1");
			
			$("#TheSerialStat").text(serials + " Inserted...");


			$("#serdis").focus();
  
	});

});

function InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refno,bcodes){

	$("<tr>").append(
		$("<td width=\"120px\" style=\"padding:1px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+nident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+refno+"' name=\"sertabrefno\" id=\"sertabrefno\">"+itmcode),
		$("<td>").html("<input type='hidden' value='"+serials+"' name=\"sertabserial\" id=\"sertabserial\">"+serials), 
		$("<td>").html("<input type='hidden' value='"+bcodes+"' name=\"sertabcodes\" id=\"sertabcodes\">"+bcodes), 
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
		$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
		$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+expz+"' name=\"sertabesp\" id=\"sertabesp\">"+expz),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input class='btn btn-danger btn-xs' type='button' id='delsrx" + itmcode + "' value='delete' />")
	).appendTo("#MyTable2 tbody");

									$("#delsrx"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});
}

function addItemName(){
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

			myFunctionadd("","","","","","","","");		
		//	ComputeGross();	
	 }
	 else{
		
		addqty();	
			
	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		
	 }

}

function myFunctionadd(nqty,nqtyOrig,nfactor,cmainunit,xref,nident){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	//var dneeded = document.getElementById("date_received").value;
	
	if(nqty=="" && nprice=="" && namount=="" && nfactor=="" && cmainunit=="" && xref=="" && nident==""){	
		//var itmprice = chkprice(itmcode,itmunit);
		//var itmamnt = itmprice;
		var itmqty = 1;
		var itmqtyorig = 0;
		var itmfactor = 1;
		var itmmainunit = itmunit;
		var itmxref = "";
		var itmident = "";

		//var curramtz = price;

	}
	else{
		//var itmprice = nprice;
		//var itmamnt = namount;
		var itmqty = nqty;
		var itmqtyorig = nqtyOrig;
		var itmfactor = nfactor;
		var itmmainunit = cmainunit;
		var itmxref = xref;
		var itmident = nident;

		//var curramtz = curramt;
	}

//	var baseprice = curramtz * parseFloat($("#basecurrval").val());

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;


	var uomoptions = "";
	
	if(xref == ""){							
		var xz = $("#hdnitmfactors").val();
		if(itmqtyunit==itmunit){
			isselctd = "selected";
		}else{
			isselctd = "";
		}
		var uomoptions = "<option value='"+itmmainunit+"' data-factor='1' "+isselctd+">"+itmmainunit+"</option>";

		$.each(jQuery.parseJSON(xz), function() { 
			if(itmcode==this['cpartno']){
				if(itmunit==this['cunit']){
					isselctd = "selected";
				}
				else{
					isselctd = "";
				}
				uomoptions = uomoptions + "<option value='"+this['cunit']+"' data-factor='"+this['nfactor']+"' "+isselctd+">"+this['cunit']+"</option>";

			}
		});	
		
		uomoptions = "<select class='xseluom"+lastRow+" form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\"  data-main='"+itmmainunit+"'>"+uomoptions+"</select>";
		
	}else{
		uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom\">"+itmunit;
	}


	tditmbtn = "<td width=\"50\">  <input class='btn btn-info btn-xs' type='button' id='ins" + itmcode + "' value='insert' /> </td>";
	
	tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"<input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"> </td>";
	
	tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"> " + itmdesc + "</td>";
	
	tditmunit = "<td width=\"80\"> " + uomoptions + "</td>";

	isfactoread = "";
	if(itmmainunit==itmunit){
		isfactoread = "readonly";
	}

	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+itmfactor+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";
	
	
	tditmqty = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> </td>"; 	 
	
	//tditmprice = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

	//tditmbaseamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+curramtz+"' class='form-control input-xs' style='text-align:right' name='txtntranamount' id='txtntranamount"+lastRow+"' readonly> </td>";
	
	//tditmamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+baseprice.toFixed(4)+"' class='form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";
	
	tditmdel = "<td width=\"80\" style=\"padding:1px\" align=\"center\"> <input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' /> </td>";

  //+ tditmprice + tditmbaseamount+ tditmamount 

	$('#MyTable > tbody:last-child').append('<tr style=\"padding-top:1px\">'+tditmbtn+tditmcode + tditmdesc + tditmunit + tditmfactor + tditmqty + tditmdel + '</tr>');


									$("#del"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
										// ComputeGross();
									});
									
									$("#ins"+itmcode).on('click', function() {
										 InsertDetSerial(itmcode,itmdesc,itmmainunit,itmident,itmxref);
									});
//alert("b");

									$("input.numeric").autoNumeric('init',{mDec:2});
									//$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
//alert("c");									
									$("input.numeric").on("keyup", function (e) {
										chkqty($(this).attr('id'));
									  // ComputeAmt($(this).attr('id'));
									 //  ComputeGross();
									   tblnav(e.keyCode,$(this).attr('id'));
									});
	//alert("d");								
									$("#seluom"+lastRow).on('change', function() {
									//	alert($(this).val());
									//	var xyz = chkprice(itmcode,$(this).val());
										
									//	$('#txtnprice'+lastRow).val(xyz.trim());
										
										//ComputeAmt($(this).attr('id'));
									//	ComputeGross();
										
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
										
									});
//alert("e");	

}

function chkqty(nme){
			var disnme = nme.replace(/[0-9]/g, ''); // string only
			var r = nme.replace( /^\D+/g, ''); // numeric only
			var nqty = 0;
			var chkValref = $("#hdnCHECKREFval").val();

					
					if(parseInt(chkValref)==1){
						nqty = $("#txtnqty"+r).val().replace(/,/g,'');
						nqty = parseFloat(nqty);

						nqtyorig = $("#txtnqtyORIG"+r).val();
						nqtyorig = parseFloat(nqtyorig);
						
						if(nqty > nqtyorig){
							
							$("#AlertMsg").html("");
							
							$("#AlertMsg").html("<b>ERROR: </b>Bigger qty is not allowed!<br><b>Original Qty: </b>" + nqtyorig);
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
							
							$("#txtnqty"+r).val(nqtyorig);
						}
						
					}
			
}

/*
		function ComputeAmt(nme){
			
			var disnme = nme.replace(/[0-9]/g, ''); // string only
			var r = nme.replace( /^\D+/g, ''); // numeric only
			var nnet = 0;
			var nqty = 0;
			var chkValref = $("#hdnCHECKREFval").val();
			//alert(disnme + ":" + $("#hdnCHECKREFval").val());
				if(disnme=="txtnqty"){ // If qty textbox check muna ung qty vs orig pag 1 or 2 ung CHEKREFval
					
					if(parseInt(chkValref)==1){
						nqty = $("#txtnqty"+r).val().replace(/,/g,'');
						nqty = parseFloat(nqty);

						nqtyorig = $("#txtnqtyORIG"+r).val();
						nqtyorig = parseFloat(nqtyorig);
						
						if(nqty > nqtyorig){
							
							$("#AlertMsg").html("");
							
							$("#AlertMsg").html("<b>ERROR: </b>Bigger qty is not allowed!<br><b>Original Qty: </b>" + nqtyorig);
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
							
							$("#txtnqty"+r).val(nqtyorig);
						}
						
					}
				}
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty);
			nprc = $("#txtnprice"+r).val();
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);
						
			$("#txtnamount"+r).val(namt);

		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtnamount"+i).val();
					gross = gross + parseFloat(amt);
				}
			}

			$("#txtnGross").val(gross.toFixed(4));
			
		}
*/
function InsertDetSerial(itmcode, itmname, itmunit, itemrrident,refrnce){
	$("#InvSerDetHdr").text("Inventory Details ("+itmname+")");
	$("#serdisuom").val(itmunit);
	$("#serdisitmcode").val(itmcode);
	$("#serdisrefident").val(itemrrident);
	$("#serdisrefno").val(refrnce);

	$("#TheSerialStat").text("");

	$("#SerialMod").modal("show");
}

function tblnav(xcode,txtinput){
	//alert(xcode);
				var inputCNT = txtinput.replace(/\D/g,'');
				var inputNME = txtinput.replace(/\d+/g, '');
				 
				switch(xcode){
					case 38: // <Up>  
					 	var idx =  parseInt(inputCNT) - 1;
               			$("#"+inputNME+idx).focus();
						break;
					case 13:
					case 40: // <Down>
					 	var idx =  parseInt(inputCNT) + 1;
               			$("#"+inputNME+idx).focus();
						break;
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
		//	var itmprice = $(this).find("input[name='txtnprice']").val();
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
		//	TotAmt = TotQty * parseFloat(itmprice);
		//	$(this).find("input[name='txtnamount']").val(TotAmt);
		}

	});
	
//	ComputeGross();

}

/*
function chkprice(itmcode,itmunit){
	var result;
	var ccode = document.getElementById("txtcustid").value;
	
	//alert("th_checkitmprice.php?itm="+itmcode+"&cust="+ccode+"&cunit="+itmunit)	;
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit},
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}
*/

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

function openinv(){
		if($('#txtcustid').val() == "" || $('#date_received').val() == ""){
			alert("Please pick a valid Supplier and Date Received!");
		}
		else{
			
			$("#txtcustid").attr("readonly", true);
			$("#txtcust").attr("readonly", true);

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#InvListHdr').html("PO List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			
			$.ajax({
                    url: 'th_qolist.php',
					data: 'x='+x,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					   $("#allbox").prop('checked', false);
					   
                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Purchase Order Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"'>").text(item.cpono),
							$("<td>").text(item.dneeded)
							).appendTo("#MyInvTbl tbody");
							
							
							$("#td"+item.cpono).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.cpono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIRef').modal('show');
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

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("PO List: " + $('#txtcust').val() + " | PO Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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

					//alert('th_sinumdet.php?x='+drno+"&y="+salesnos);
					$.ajax({
            url: 'th_qolistdet.php',
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
											$("<td>").html("<input type='checkbox' value='"+item.nident+"' name='chkSales[]' data-id=\""+drno+"\">"),
											$("<td>").text(item.citemno),
											$("<td>").text(item.cdesc),
											$("<td>").text(item.cunit),
											$("<td>").text(item.nqty),
										//	$("<td>").text(item.nprice),
										//	$("<td>").text(item.nbaseamount),
										//	$("<td>").text(item.ccurrencycode)
										).appendTo("#MyInvDetList tbody");
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

function InsertSI(){
	
	//check muna if pareparehas ng currency
	
	//get defsult curr if may laman na ang details
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var tblrowcnt = tbl.length;

	//var trannocurr = "";
	//var trannocurrcnt = 0;

	//	if(tblrowcnt>1){
	//		trannocurr = $("#selbasecurr").val();
	//		trannocurrcnt = 1;
	//	}

	//	$("input[name='chkSales[]']:checked").each( function () {

	//		if(trannocurr != $(this).data("curr")){
	//			trannocurr = $(this).data("curr");
	//			trannocurrcnt++;
	//		}
	//	});

	//if(trannocurrcnt>1){
	//	alert("Multi currency in one invoice is not allowed!");
	//}
//	else{

	
   		$("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $(this).data("id");
	   		var id = $(this).val();

	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   	console.log(data);
              $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.cdesc); 
							$('#txtprodid').val(item.citemno); 
							$("#hdnunit").val(item.cunit); 

							//if(index==0){
							//	$("#selbasecurr").val(item.ccurrencycode).change();
							//	$("#hidcurrvaldesc").val(item.ccurrencydesc);
							//	convertCurrency(item.ccurrencycode);
							//}

							//alert(item.cqtyunit + ":" + item.cunit);
							
								myFunctionadd(item.nqty,item.nqty,item.nfactor,item.cmainuom,item.xref,item.nident)
								
								$("#txtprodid").val("");
								$("#txtprodnme").val("");	
								$("#hdnunit").val("");	
			   
					   });
						
					//	ComputeGross();
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

		});
		//alert($("#hdnQuoteNo").val());
		
		$('#mySIModal').modal('hide');
		$('#mySIRef').modal('hide');

	//}

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
	
	if(document.getElementById("txtSuppSI").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Supplier DR is required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtSuppSI").focus();
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
		//	myprice = $(this).find('input[name="txtnprice"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}
			
		//	if(myprice == 0 || myprice == ""){
	//			msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
		//	}

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
		
		var myform = $("#frmpos").serialize();

		$.ajax ({
			url: "RR_editsave.php",
			//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, ccustsi:ccustsi },
			data: myform,
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING RR: </b> Please wait a moment...");
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
			
				var xcref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var crefidnt = $(this).find('input[type="hidden"][name="txtnrefident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
						if(cuom=="" || cuom==null){
							var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
						}
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nqtyOrig = $(this).find('input[type="hidden"][name="txtnqtyORIG"]').val();
			//	var nprice = $(this).find('input[type="hidden"][name="txtnprice"]').val();
			//	var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val();
				//var dneed = $(this).find('input[name="dexpired"]').val();
			
				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
				}
				
				//alert("trancode="+ trancode+ "&indx=" + index+ "&citmno=" + citmno+ "&cuom=" + cuom+ "&nqty=" + nqty+ "&nprice=" + nprice+ "&namt=" + namt+ "&mainunit=" + mainunit+ "&nfactor=" + nfactor+ "&nqtyorig=" + nqtyOrig+ "&xcref=" + xcref+ "&crefidnt=" + crefidnt);
				
				$.ajax ({
					url: "RR_newsavedet.php",
					data: { trancode: trancode, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, mainunit:mainunit, nfactor:nfactor, nqtyorig:nqtyOrig, xcref:xcref, crefidnt:crefidnt},
					async: false,
					success: function( data ) {
					//	alert(data);
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});

			
			$("#MyTable2 > tbody > tr").each(function(index) {	
			
				var xcref = $(this).find('input[type="hidden"][name="sertabrefno"]').val();   
				var crefidnt = $(this).find('input[type="hidden"][name="sertabident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="sertabitmcode"]').val();
				var cuom = $(this).find('input[type="hidden"][name="sertabuom"]').val();
				var nqty = $(this).find('input[type="hidden"][name="sertabqty"]').val();
				var dneed = $(this).find('input[type="hidden"][name="sertabesp"]').val();
				var clocas = $(this).find('input[type="hidden"][name="sertablocas"]').val();
				var seiraln = $(this).find('input[type="hidden"][name="sertabserial"]').val(); 
				var barcdln = $(this).find('input[type="hidden"][name="sertabcodes"]').val(); 

				$.ajax ({
					url: "RR_newsavedetserials.php",
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, seiraln:seiraln, barcdln:barcdln },
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

/*

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
			amt = $("#txtntranamount"+i).val();			
			recurr = parseFloat(newcurate) * parseFloat(amt);

			$("#txtnamount"+i).val(recurr.toFixed(4));
		}
	}

	ComputeGross();
}

*/


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "RR_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){
	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtcpono").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);
}

function enabled(){
	var msgsx = "";
	
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
			
				if(document.getElementById("hdnRRAmtAcc").value=="YES" && document.getElementById("hdnwRefAPC").value=="false"){
				
					var msgsx = "";
					
					$("#frmpos :input").attr("disabled", false);
				
					
					$("#txtctranno").attr("readonly", true);
					$("#txtctranno").val($("#hdnorigNo").val());
					
					$("#btnMain").attr("disabled", true);
					$("#btnNew").attr("disabled", true);
					$("#btnPrint").attr("disabled", true);
					$("#btnEdit").attr("disabled", true);	
					
					//readonly Amt if hdnRRAmt Acc No
					//if(document.getElementById("hdnRRAmtAcc").value=="NO"){
						//$("#MyTable > tbody > tr").each(function(index) {	
						
						//	var x = $(this).find('input[name="txtnamount"]');
							
						//	x.attr("readonly", true);
							
						//	var z = $(this).find('input[name="txtnprice"]');
							
						//	z.attr("readonly", true);
						
						//});
					//}

					//readonly Qty if hdnRRQty Acc No
				//	if(document.getElementById("hdnRRQtyAcc").value=="NO"){
					/*
						$("#MyTable > tbody > tr").each(function(index) {	
						
							var y = $(this).find('input[name="txtnqty"]');
							y.attr("readonly", true);

							var y2 = $(this).find('input[name="btninsitm"]');
							y2.attr("disabled", true);

							var y3 = $(this).find('input[name="btndelitm"]');
							y3.attr("disabled", true);													
						});
						*/
				//	}
					
				}

		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		
		if(msgsx != ""){
			document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			$("#txtcpono").val($("#hdntranno").val());
			$("#txtcpono").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
	
	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{
		  var url =  "RR_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');

	}
}

function loaddetails(){
	//alert("th_loaddetails.php?id="+$("#txtcpono").val());
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcpono").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.cdesc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 

				myFunctionadd(item.nqty,item.nqtyorig,item.nfactor,item.cmainuom,item.xref,item.nident);
			});

		}
	});


		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");

}

function loadserials(){	

	   		$.ajax({
					url : "th_serialslist.php?id=" + $("#txtcpono").val(),
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	
					   console.log(data);
             $.each(data,function(index,item){

								//InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident)
								InsertToSerials(item.citemno,item.cserial,item.cunit,item.nqty,item.nlocation,item.locadesc,item.dexpired,item.nrefidentity,item.crefno,item.cbarcode);
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						//alert(jqXHR.responseText);
					}
					
				});

}

function tblcheckk(){
	alert($("#MyTable > tbody tr").length);
}

</script>
