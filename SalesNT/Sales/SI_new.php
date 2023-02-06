<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//echo $_SESSION['chkitmbal']."<br>";
//echo $_SESSION['chkcompvat'];

//$ddeldate = date("m/d/Y");
//$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

//echo $ddeldate;

//where dcutdate = STR_TO_DATE('$ddeldate', '%m/%d/%Y')
 //where Month(dcutdate) <> Month(ddate) and Month(dcutdate) > Month(ddate);
 
	//$result1 = mysqli_query($con,"SELECT dcutdate FROM `sales` order By ddate desc Limit 1"); 

	//if (mysqli_num_rows($result1)!=0) {
	// $all_course_data1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
	 
		//$ndcutdate = $all_course_data1["dcutdate"];
	//}	
	//else{
		$ndcutdate = date("Y-m-d");
	//}
	
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
    
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
   <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    
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

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<input type="hidden" value='<?=json_encode(@$arrdisclist)?>' id="hdndiscs"> 
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

<form action="SI_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>New Sales Invoice</legend>	
        <table width="100%" border="0">
						<tr>
							<tH>SI Series No.</tH>
				      <td style="padding:2px;">
                <div class="col-xs-4 nopadding">
                  <input type='text' class="form-control input-sm" id="csiprintno" name="csiprintno" value="" autocomplete="off"/>
                </div>
              </td>
							<tH width="150">Invoice Date:</tH>
							<td style="padding:2px;">
								<div class="col-xs-11 nopadding">
									<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" onkeydown="event.preventDefault()" value="<?php echo date_format(date_create($ndcutdate),'m/d/Y'); ?>" />
								</div>
							</td>
						</tr>
			      <tr>
				      <tH>Customer:</tH>
				      <td style="padding:2px"><div class="col-xs-12 nopadding">
				        <div class="col-xs-3 nopadding">
				          <input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
				          <input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
				          <input type="hidden" id="hdnpricever" name="hdnpricever" value="">


			            </div>
				        <div class="col-xs-8 nopadwleft">
				          <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
			            </div>
			          </div>
							</td>

							<tH width="100"><b>Sales Type:</b></tH>
							<td style="padding:2px">
								<div class="col-xs-11 nopadding">
									<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
											<option value="Goods">Goods</option>
											<option value="Services">Services</option>
									</select>
								</div>
							</td>
	      		</tr>
						<tr>
							<tH width="100"><b>Currency:</b></tH>
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
														if ($nvaluecurrbase==$rows['id']) {
															$nvaluecurrbasedesc = $rows['currencyName'];
														}

														if($rows['countryCode']!=="Crypto" && $rows['currencyName']!==null){

															*/

															$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
															if (mysqli_num_rows($sqlhead)!=0) {
																while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
											?>
														<option value="<?=$rows['id']?>" <?php if ($nvaluecurrbase==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
											<?php

														}
													}
											?>
										</select>
											<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
											<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $nvaluecurrbasedesc; ?>"> 
									</div>
									<div class="col-xs-2 nopadwleft">
										<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
									</div>

									<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
												
									</div>
								</td>

							<tH width="100"><b>Payment Type:</b></tH>
							<td style="padding:2px">
								<div class="col-xs-11 nopadding">
									<select id="selpaytyp" name="selpaytyp" class="form-control input-sm selectpicker"  tabindex="1">
											<option value="Credit">Credit</option>
											<option value="Cash">Cash</option>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<tH width="100">Remarks:</tH>
							<td style="padding:2px"><div class="col-xs-11 nopadding">
								<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2">
								</div>
							</td>

								
								<td><b><div class="chklimit">Credit Limit:</div></b></td>
								<td style="padding:2px;" align="right">
									<div class="chklimit col-xs-11 nopadding" id="ncustlimit"></div>
									<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">
								</td>
						</tr>
						<tr>
								<tH width="100">Reference:</tH>
								<td style="padding:2px">
									<div class="col-xs-2 nopadding">
										<input type="text" class="form-control input-sm" id="txtrefmod" name="txtrefmod" readonly>
									</div>
									<div class="col-xs-9 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtrefmodnos" name="txtrefmodnos" readonly>
									</div>
								</td>
								<th><div class="chklimit">Balance:</div></th>
								<td style="padding:2px;"  align="right">				          
													<div class="chklimit col-xs-11 nopadding" id="ncustbalance"></div>
												<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
								</td>
						</tr>

						<tr>
								<td style="padding:2px" colspan="2"></td>
								
								<td>&nbsp;</td>
								<td style="padding:2px;"  align="right">
									<div class="chklimit col-xs-11 nopadding" id="ncustbalance2"></div>
								</td>
						</tr>


						<tr>
								<td colspan="2">
									<div class="col-xs-12 nopadding">
										<div class="chkitmsadd col-xs-3 nopadwdown">
											<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">
										</div>
										<div class="chkitmsadd col-xs-8 nopadwleft">
											<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="5">
										</div>
									</div>
									<input type="hidden" name="hdnqty" id="hdnqty">
									<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
									<input type="hidden" name="hdnunit" id="hdnunit"> 
									<input type="hidden" name="hdnctype" id="hdnctype"> 

								</td>
								<td>&nbsp;</td>
								<td style="padding:2px;"  align="right">&nbsp;</td>
						</tr>

				</table>

				<div class="alt2" dir="ltr" style="
						margin: 0px;
						padding: 3px;
						border: 1px solid #919b9c;
						width: 100%;
						height: 250px;
						text-align: left;
						overflow: auto">
		
						<table id="MyTable" class="MyTable table table-condensed" width="100%">
							<thead>
								<tr>
									<th style="border-bottom:1px solid #999">Code</th>
									<th style="border-bottom:1px solid #999">Description</th>
									<th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Price</th>
									<th style="border-bottom:1px solid #999">Discount</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
							</thead>
							<tbody class="tbody">
							</tbody>
												
						</table>

				</div>

		<br>

		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td valign="top">

					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SI.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

					<!--<button type="button" class="btn btn-info btn-sm" tabindex="6" id="btnIns" name="btnIns">DR<br>(Insert)</button>-->

					<div class="dropdown" style="display:inline-block !important;">
						<button type="button" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle">
							Reference <br>(Insert) <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="javascript:;" onClick="openinv('QO');">Billing</a></li>
							<li><a href="javascript:;" onClick="openinv('SO');">Sales Order</a></li>
							<li><a href="javascript:;" onClick="openinv('DR');">Delivery</a></li>
						</ul>
					</div>
					
					<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>
					<td align="right" valign="top">
					
					<table width="90%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td nowrap align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>Gross Amount in <?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
					</table>
				
				</td>
			</tr>
		</table>

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
							<tr>
							<th style="border-bottom:1px solid #999">Code</th>
							<th style="border-bottom:1px solid #999">Description</th>
													<th style="border-bottom:1px solid #999">Field Name</th>
							<th style="border-bottom:1px solid #999">Value</th>
													<th style="border-bottom:1px solid #999">&nbsp;</th>
						</tr>
						<tbody class="tbody">
											</tbody>
									</table>
			
				</div>
					</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- FULL LIST REFERENCES-->
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
														<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight small">
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
									<button type="button" id="btnInsDet" onClick="InsertSI('DR')" class="btn btn-primary">Insert</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

							</div>
					</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End LIST REFERENCES -->

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



<form method="post" name="frmedit" id="frmedit" action="SI_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">
	var xChkBal = "";
	var xChkLimit = "";
	var xChkLimitWarn = "";

	var slctqryvatc = "";
	var slctvatsoption = "";

	var xtoday = new Date();
	var xdd = xtoday.getDate();
	var xmm = xtoday.getMonth()+1; //January is 0!
	var xyyyy = xtoday.getFullYear();

	xtoday = xmm + '/' + xdd + '/' + xyyyy;

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
						   
					  });
					}
				});
		//if(xChkBal==1){
			//$("#tblAvailable").hide();
		//}
		//else{
			//$("#tblAvailable").show();
		//}


		if(xChkLimit==0){
			$(".chklimit").hide();
		}
		else{
			$(".chklimit").show();
		}
	

	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);
	  $(".chkitmsadd").hide();

  });

	$(document).keydown(function(e) {	
	
	 if(e.keyCode == 83 && e.ctrlKey) { //CTRL S
	  	  e.preventDefault();
		 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		  return chkform();
		 }
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 window.location.replace("SO.php");
	    }

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			openinv("DR");
		}
	  }

	
	});

	$(function(){
				$('#date_delivery').datetimepicker({
									format: 'MM/DD/YYYY',
					// onChangeDateTime:changelimits,
					//minDate: new Date(),
					});
			
			//$('#date_delivery').on('dp.change', function(e){ alert("changed"); });

			$("#allbox").click(function(e){
				var table= $(e.target).closest('table');
				$('td input:checkbox',table).not(this).prop('checked', this.checked);
			});

			$("#allboxqo").click(function(e){
				var table= $(e.target).closest('table');
				$('td input:checkbox',table).not(this).prop('checked', this.checked);
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
				if(value.trim()!=""){
					var data = value.split(":");
					$('#txtcust').val(data[0]);
					$('#hdnpricever').val(data[1]);
				//	$('#imgemp').attr("src",data[2]);
					
									
					$('#hdnvalid').val("YES");
					
					$('#txtremarks').focus();
					
					if(xChkLimit==1){
						
						limit = Number(data[3]).toLocaleString('en', { minimumFractionDigits: 2 });	

						$('#ncustbalance2').html("");
						$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
						$('#hdncustlimit').val(data[3]);
						
						checkcustlimit(dInput, data[3]);

					}
				}
				else{
					//
					//$('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
					
					$('#txtcustid').val("");
					$('#txtcust').val("");
				//	$('#imgemp').attr("src","../../images/blueX.png");
					$('#hdnpricever').val("");
					
					$('#hdnvalid').val("NO");
				}
			},
			error: function(){
				$('#txtcustid').val("");
				$('#txtcust').val("");
			//	$('#imgemp').attr("src","../../images/blueX.png");
				$('#hdnpricever').val("");
				
				$('#hdnvalid').val("NO");
			}
			});

			}
			
		});

		$('#txtcust, #txtcustid').on("blur", function(){
			//alert($('#hdnvalid').val());
			if($('#hdnvalid').val()=="NO"){
				$('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
				
				$('#txtprodnme').attr("disabled", true);
				$('#txtprodid').attr("disabled", true);
				
				if($('#txtcustid').val()!="" || $('#txtcust').val()!=""){
					alert("INVALID CUSTOMER");
					$('#txtcustid').val("");
					$('#txtcust').val("");
				}

				
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
				//$("#imgemp").attr("src",item.imgsrc); 
				$("#hdnpricever").val(item.cver);
				
				$('#hdnvalid').val("YES"); 
				
				$('#txtremarks').focus();
				
					if(xChkLimit==1){
						
						limit = Number(item.nlimit).toLocaleString('en', { minimumFractionDigits: 2 });	 

						$('#ncustbalance2').html("");
						$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
						$('#hdncustlimit').val(item.nlimit);

						checkcustlimit(item.id, item.nlimit);

					}
							
				
			}
		
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

				addItemName("","","","","","","","");
				
				
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

				myFunctionadd("","","","","","","","");
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

		$("#txtSearchBill").keypress(function(event){
			//event.preventDefault();

			$('#MyTable > tbody').empty();

			$iswpo = "False";

			if(event.keyCode == 13){
				//gethdr
				$.ajax({
					url : "th_getqohdr.php?id=" + $(this).val(),
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	
						console.log(data);
						$.each(data,function(index,item){  //  
							if(item.ccode!="NONE"){

								$("#txtcust").val(item.cname);
								$("#txtcustid").val(item.ccode);
								$("#hdnpricever").val(item.cpricever);

								if(xChkLimit==1){
								
									limit = Number(item.nlimit).toLocaleString('en', { minimumFractionDigits: 2 });	 

									$('#ncustbalance2').html("");
									$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
									$('#hdncustlimit').val(item.nlimit);

									checkcustlimit(item.ccode, item.nlimit);

								}

								$("#selsityp").val(item.csalestype).trigger('change');

								$iswpo = "True";
							}else{
								alert("Transaction cannot be found!");
							}
						});


						

					}
				});

				if($iswpo=="True"){
					//getdetails
					$.ajax({
						url : "th_qolistput.php?id=" + $(this).val() + "&itm=ALL&typ=QO",
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
										
								if(index==0){
									$("#selbasecurr").val(item.ccurrencycode).change();
									$("#hidcurrvaldesc").val(item.ccurrencydesc);
									convertCurrency(item.ccurrencycode);
								}


								addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.crefident,item.citmcls);
														
							});
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							alert(jqXHR.responseText);
						}
							
					});
				}

			}
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

			if(xChkLimitWarn==0) { //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
				$("#ncustbalance").html("<b><i><font color='red'>Max Limit Reached</font></i></b>");
			}
			else if(xChkLimitWarn==1) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='red' size='-1'>Delivery is blocked</font></i></b>");
			}
			else if(xChkLimitWarn==2) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>ORDERS BLOCKED</font></i></b>");
				$("#btnSave").attr("disabled", true);
				$("#btnIns").attr("disabled", true);
				//$('#txtprodnme').attr("disabled", true);
					//$('#txtprodid').attr("disabled", true);

			}
			
		}

	}

	function addItemName(qty,price,curramt,amt,factr,cref,nrefident,citmcls){

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

			//var isItem = "NO";
			//var disID = "";

			//	$("#MyTable > tbody > tr").each(function() {	
			//		disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
			//		disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					
			//		if($("#txtprodid").val()==disID && cref==disref){
						
			//			isItem = "YES";

			//		}
		//		});	

	//	if(isItem=="NO"){	
			myFunctionadd(qty,price,curramt,amt,factr,cref,nrefident,citmcls);
			
			ComputeGross();	

		//}
	//	else{

		//	addqty();	
				
		//}
			
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			$("#hdnqty").val("");
			$("#hdnqtyunit").val("");
			$("#hdnctype").val("");
			
		}

	}

	function myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident,citmcls){
		//alert("hello");
		var itmcode = $("#txtprodid").val();
		var itmdesc = $("#txtprodnme").val();
		var itmqtyunit = $("#hdnqtyunit").val();
		var itmqty = $("#hdnqty").val();
		var itmunit = $("#hdnunit").val();
		var itmccode = $("#hdnpricever").val(); 
		
		if(qty=="" && pricex=="" && amtx=="" && factr==""){
			var itmtotqty = 1;
			//alert(itmcode+","+itmunit+","+itmccode+","+$("#date_delivery").val());
			var price = chkprice(itmcode,itmunit,itmccode,$("#date_delivery").val());
			var curramtz = price;
			//var amtz = price;
			var itmctype= $("#hdnctype").val();
			var factz = 1;
		}
		else{
			var itmtotqty = qty
			var price = pricex;
			var curramtz = curramt;
			//var amtz = amtx;	
			var factz = factr;
			var itmctype= citmcls;
		}		

		var baseprice = curramtz * parseFloat($("#basecurrval").val());

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
			//qtystat = "readonly";
			qtystat = "";
		}

			
		var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode"+lastRow+"\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+nrefident+"' name=\"txtcrefident\" id=\"txtcrefident\"> <input type='hidden' value='"+itmctype+"' name=\"hdncitmtype\" id=\"hdncitmtype"+lastRow+"\"> </td>";
		var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";

		var tditmunit = "<td width=\"100\" nowrap>"+uomoptions+"</td>";
			
		
		var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

		var tditmprice = "asd<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' > </td>";
		
		var tditmdisc = "<td width=\"100\" nowrap> <input type='text' value='0' class='numeric form-control input-xs' style='text-align:right; cursor: pointer' name=\"txtndisc\" id='txtndisc"+lastRow+"'  readonly onclick=\"getdiscount('"+itmcode+"', "+lastRow+")\"> </td>";

		var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";

		var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> </td>";

		var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del"+ itmcode +"' value='delete' data-var='"+lastRow+"'/> &nbsp; </td>";

		//<input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/>

		$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmprice + tditmdisc + tditmbaseamount+ tditmamount + tditmdel + '</tr>');

										$("#del"+itmcode).on('click', function() { 
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

										$("input.numeric").autoNumeric('init',{mDec:2});
											


									//	$("input.numeric").numeric(
										//	{negative: false}
									//	);

									//	$("input.numericdec").numeric(
									//		{
									//			negative: false,
									//			decimalPlaces: 4
									//		}
									//	);

										$("input.numeric").on("click", function () {
											$(this).select();
										});
										
										$("input.numeric").on("keyup", function () {
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
			var nnet = 0;
			var vatz = 0;

			var nnetTot = 0;
			var vatzTot = 0;

			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {

					gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}
			}

			gross2 = gross * parseFloat($("#basecurrval").val().replace(/,/g,''));

			$("#txtnGross").val(gross2);
			$("#txtnBaseGross").val(gross);

		
			$("#txtnGross").autoNumeric('destroy');
			$("#txtnBaseGross").autoNumeric('destroy');


			$("#txtnGross").autoNumeric('init',{mDec:2});
			$("#txtnBaseGross").autoNumeric('init',{mDec:2});			
			
	}
		
	function ReIdentity(xy){

			
			var rowCount = $('#MyTable tr').length;
						
			if(rowCount>1){
				for (var i = xy+1; i <= rowCount; i++) {
					//alert(i);
					var ITMCode = document.getElementById('txtitemcode' + i);
					var SelUOM = document.getElementById('seluom' + i); 
					var ItmTyp = document.getElementById('hdncitmtype' + i); 
					var nQty = document.getElementById('txtnqty' + i);
					var MainUom = document.getElementById('hdnmainuom' + i);
					var nFactor = document.getElementById('hdnfactor' + i);
					var nPrice = document.getElementById('txtnprice' + i);
					var nDisc = document.getElementById('txtndisc' + i); 
					var nTranAmount = document.getElementById('txtntranamount' + i);
					var nAmount = document.getElementById('txtnamount' + i);
					var RowInfo = document.getElementById('row_' + i + '_info');					
					
					var za = i - 1;
					
					//alert(za);
					ITMCode.id = "txtitemcode" + za;
					SelUOM.id = "seluom" + za;
					ItmTyp.id = "hdncitmtype" + za;
					nQty.id = "txtnqty" + za;
					MainUom.id = "hdnmainuom" + za;
					nFactor.id = "hdnfactor" + za;
					nPrice.id = "txtnprice" + za;
					nDisc.id = "txtndisc" + za;
					nTranAmount.id = "txtntranamount" + za;
					nAmount.id = "txtnamount" + za;
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
				$(this).find("input[name='txtntranamount']").val(TotAmt);

				$("#txtntranamount"+r).autoNumeric('destroy');
				$("#txtntranamount"+r).autoNumeric('init',{mDec:2});


				namt2 = TotAmt * parseFloat($("#basecurrval").val());
				$(this).find("input[name='txtnamount']").val(namt2); 

				$("#txtnamount"+r).autoNumeric('destroy');
				$("#txtnamount"+r).autoNumeric('init',{mDec:2});

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
					//	$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
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
										//	alert($(this).prop('disabled'));
										//	if($(this).prop('disabled')!==false){
												$(this).closest('tr').remove();
										//	}
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
				}else if(typ=="QO"){
					$('#QOListHdr').html("Billing List: " + $('#txtcust').val());
					$("#btnQOInsDet").attr("onclick","InsertSI('QO')");
				}else if(typ=="SO"){
					$('#QOListHdr').html("Sales Order List: " + $('#txtcust').val());
					$("#btnQOInsDet").attr("onclick","InsertSI('SO')");
				}

				var xstat = "YES";
				
				//disable escape insert and save button muna
				//alert($("#selsityp").val());

				fltrsalestyp = "";
				if(typ=="QO"){
					fltrsalestyp = $("#selsityp").val();
				}
				
				//alert('x='+x+'&typ='+typ+'&styp='+fltrsalestyp);

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
									
									if(index==0){
										$("#selbasecurr").val(item.ccurrencycode).change();
										$("#hidcurrvaldesc").val(item.ccurrencydesc);
										//convertCurrency(item.ccurrencycode);
									}


									addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.crefident,item.citmcls)

									//get currentvalue of ref..
									$("#txtrefmod").val(typ);
									$("#txtrefmodnos").val(item.xref);
													
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

					$("#txtdiscsval"+ident+idnum).autoNumeric('init',{mDec:2});
					$("#txtdiscsamt"+ident+idnum).autoNumeric('init',{mDec:2});


					$("#txtdiscsval"+ident+idnum).on('keyup', function(event) {
						
						if($("#secdiscstyp"+ident+idnum).val()=="fix"){
							xamty = parseFloat($(this).val());
							$("#txtdiscsamt"+ident+idnum).val(xamty);

							$("#txtdiscsamt"+ident+idnum).autoNumeric('destroy');
							$("#txtdiscsamt"+ident+idnum).autoNumeric('init',{mDec:2});
						}else{
							//getprice
							xprice = $("#txtnprice"+idnum).val();

							xamty = parseFloat(xprice) * (parseFloat($("#txtdiscsval"+ident+idnum).val()) / 100);
							$("#txtdiscsamt"+ident+idnum).val(xamty);

							$("#txtdiscsamt"+ident+idnum).autoNumeric('destroy');
							$("#txtdiscsamt"+ident+idnum).autoNumeric('init',{mDec:2});
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

		$("#txtndisc"+idnum).val(vcvxg);

		$("#txtndisc"+idnum).autoNumeric('destroy');
		$("#txtndisc"+idnum).autoNumeric('init',{mDec:2});

		ComputeAmt(idnum); 
		ComputeGross();

		$('#MyDiscModal').modal('hide');
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
				
				//if(myprice == 0 || myprice == ""){
				//	msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
				//}

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

			//alert(ISOK);	
			if(msgz!=""){
				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				return false;
				ISOK = "NO";
			}
		}
		//alert(ISOK);
		if(ISOK == "YES"){
		var trancode = "";
		var isDone = "True";
		var VARHDRSTAT = "";
		var VARHDRERR = "";
		
			//Saving the header
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
			//alert(myform);

			$.ajax ({
				url: "SI_newsavehdr.php",
				//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, selreinv:selreinv, selsityp:selsitypz, siprintno:siprintno, nnetvat:nnetvat, nvat:nvat },
				data: myform,
				async: false,
				beforeSend: function(){
					$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW SI: </b> Please wait a moment...");
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

				//alert(trancode);
				//Save Details
				$("#MyTable > tbody > tr").each(function(index) {	
					//if(index>0){
						
						var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
						var crefident = $(this).find('input[type="hidden"][name="txtcrefident"]').val();
						var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
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
						
						if(nqty!==undefined){
							nqty = nqty.replace(/,/g,'');
							ndiscount = ndiscount.replace(/,/g,'');
							nprice = nprice.replace(/,/g,'');
							namt = namt.replace(/,/g,'');
							ntranamt = ntranamt.replace(/,/g,'');
						}

						//alert("SI_newsavedet.php?trancode="+trancode+"&crefno="+crefno+"&crefident="+crefident+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+ nprice+"&ndiscount="+ndiscount+"&ntranamt="+ntranamt+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&ccode="+ccode+"&vatcode="+vatcode+"&nrate="+nrate);

						$.ajax ({
							url: "SI_newsavedet.php",
							data: { trancode: trancode, crefno: crefno, crefident:crefident, indx: parseInt(index) + 1, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, ndiscount:ndiscount, ntranamt:ntranamt, namt:namt, mainunit:mainunit, nfactor:nfactor, ccode:ccode },
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
						data: { trancode: trancode, indx: parseInt(index) + 1, discnme: discnme, seldisctyp: seldisctyp, discval: discval, discamt: discamt, discacctno: discacctno, discitmno: discitmno, discitmnoident: discitmnoident},
						async: false,
						success: function( data ) {
							if(data.trim()=="False"){
								isDone = "False";
							}
						}
					});
					
				});
				
				if(isDone=="True"){
					$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
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
				$("#AlertMsg").html("Something went wrong<br>Status: "+VARHDRSTAT +"<br>Error: "+VARHDRERR);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			}


		}
			
			

	}

</script>