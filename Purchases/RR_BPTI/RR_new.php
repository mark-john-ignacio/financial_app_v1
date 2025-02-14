<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Receive_new";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_RR'"); 
					
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
    
	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.css?t=<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	-->

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>

	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
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

<body style="padding:5px">
	<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

	<form action="RR_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
		<input type="hidden" value="<?php echo $nCHKREFvalue;?>" name="hdnCHECKREFval" id="hdnCHECKREFval">
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-download"></i>New Receiving
				</div>
			</div>
			<div class="portlet-body">
		
				<ul class="nav nav-tabs">
					<li class="active"><a href="#home">RR Details</a></li>
					<li><a href="#attc">Attachments</a></li>
				</ul>

				<div class="tab-content">  
					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px">

						<table width="100%" border="0">
							<tr>
								<tH width="100">Supplier:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="" readonly>
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="">
										</div> 
									</div>
								</td>
								<tH width="150">Supplier DR:</tH>
								<td width="250" style="padding:2px;">
									<div class="col-xs-8 nopadding">
										<input type='text' class="form-control input-sm" id="txtSuppSI" name="txtSuppSI" required/>
									</div>
								</td>
							</tr>

							<tr>
								<tH width="100">Remarks:</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
								<tH width="150" style="padding:2px">Date Received:</tH>
								<td style="padding:2px">
									<div class="col-xs-8 nopadding">
										<input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" />
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
														<!--</select>
															<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?//php echo $nvaluecurrbase; ?>"> 	
															<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?//php echo $nvaluecurrbasedesc; ?>"> -->
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
							--></td>
							</tr>

						</table>
					
					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px">
					
						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />

					</div>
				</div>

				<div class="portlet light bordered" style="margin-top: 20px">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details
						</div>
						<div class="inputs">
							<div class="portlet-input input-inline">							
								<?php
									if($nCHKREFvalue==0) {
								?>
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." width="25" tabindex="4"  autocomplete="off">
											<input type="hidden" id="txtcskuid" name="txtcskuid">
										</div> 
										<div class="col-xs-9 nopadwleft">
											<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm" placeholder="(CTRL+F) Search Product Name..." size="80" tabindex="5" autocomplete="off">
										</div>
									</div>
								<?php
									}
									else{
								?> 
									<input type="hidden" id="txtprodid" name="txtprodid">
									<input type="hidden" id="txtprodnme" name="txtprodnme">
									<input type="hidden" id="txtcskuid" name="txtcskuid">
								<?php
									}

								?> 

								<input type="hidden" name="hdnunit" id="hdnunit">
							</div>
						</div>
					</div>
					<div class="portlet-body" style="overflow: auto">

						<ul class="nav nav-tabs">
							<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Items List</a></li>
							<li id="liacct"><a href="#2Acct" data-toggle="tab">Items Inventory</a></li>
						</ul>

						<div class="tab-content nopadwtop2x">
							<div class="tab-pane active" id="1Det" style="padding-left:5px; padding-top:10px">

								<div style="min-height: 30vh; min-width: 1500px;">
						
									<table id="MyTable" class="MyTable" width="100%" cellpadding="3px">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
												<th style="border-bottom:1px solid #999">Code</th>
												<th style="border-bottom:1px solid #999">Name</th>
												<th style="border-bottom:1px solid #999">Size Spec</th>
												<th style="border-bottom:1px solid #999">UOM</th>
												<th style="border-bottom:1px solid #999">Factor</th>
												<th style="border-bottom:1px solid #999">Qty</th>
												<th style="border-bottom:1px solid #999">&nbsp;&nbsp;&nbsp;Ref PO</th>
												<th style="border-bottom:1px solid #999">&nbsp;&nbsp;&nbsp;Cost Center</th>
												<!--<th style="border-bottom:1px solid #999">Price</th>
												<th style="border-bottom:1px solid #999">Amount</th>
												<th style="border-bottom:1px solid #999">Total Amt in <?//php echo $nvaluecurrbase; ?></th>-->
												<!--<th style="border-bottom:1px solid #999">Date Expired</th>-->
												<th style="border-bottom:1px solid #999">Remarks</th>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
											</tr>
										</thead>
										<tbody class="tbody">
										</tbody>
										
									</table>
								</div>
							</div>

							<div class="tab-pane fade in" id="2Acct" style="padding-left:5px; padding-top:10px">

								<div style="min-height: 30vh; width: 1100px;">
							
									<table id="MyTable2" class="MyTable" width="100%" cellpadding="3px">
										<thead>
											<tr>                   	
												<th style="border-bottom:1px solid #999">Item Code</th>
												<th style="border-bottom:1px solid #999">Description</th>
												<th style="border-bottom:1px solid #999">Lot No.</th>
												<th style="border-bottom:1px solid #999">Packing List</th>
												<th style="border-bottom:1px solid #999">Location</th>
												<th style="border-bottom:1px solid #999">UOM</th>
												<th style="border-bottom:1px solid #999">Qty</th>
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
					</div>
				</div>

				<div class="row nopadwtop2x">
					<div class="col-xs-6">
						<div class="portlet">
							<div class="portlet-body">

								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RR.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>

								<button type="button" class="btn purple btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
									PO<br>(Insert)
								</button>
		
								<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">
									Save<br> (CTRL+S)
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
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

	<div class="modal fade" id="SerialMod" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="row nopadwtop">
						<div class="col-xs-6 nopadding">
							<h4 class="modal-title" id="InvSerDetHdr">Inventory Detail</h4>
							<input type="hidden" class="form-control input-sm" name="serdisitmcode" id="serdisitmcode">
							<input type="hidden" class="form-control input-sm" name="serdisitmdesc" id="serdisitmdesc">
							<input type="hidden" class="form-control input-sm" name="serdisrefident" id="serdisrefident"> 
							<input type="hidden" class="form-control input-sm" name="serdisrefno" id="serdisrefno">
						</div>
						<div class="col-xs-6 nopadding">
							<div class="row">
								<div class="col-xs-1 nopadwtop text-right"> </div>
								<div class="col-xs-3 nopadwtop text-right" style="font-size: 1.25em">
									Total Inserted
								</div>
								<div class="col-xs-2 nopadwleft text-right">
									<input type="text" class="form-control input-sm text-right" name="ToInserted" id="ToInserted" value="0" readonly >
								</div>
								<div class="col-xs-3 nopadwtop text-right" style="font-size: 1.25em"> 
									Total Qty
								</div>
								<div class="col-xs-2 nopadwleft text-right">
									<input type="text" class="form-control input-sm text-right" name="TonnedIns" id="TonnedIns" value="0" readonly>
								</div>	
								<div class="col-xs-1 nopadwtop text-right"> </div>					
							</div>
						</div>
					</div>
				</div>
				
				<div class="modal-body" style="height:20vh">

					<div class="row nopadwtop">
						<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Quantity</b></div>
						<div class="col-xs-2 nopadding"><input type="text" class="form-control input-sm" name="serdisqty" id="serdisqty" value="1" ></div>
						<div class="col-xs-1 nopadwleft"><input type="text" class="form-control input-sm" name="serdisuom" id="serdisuom" readonly></div>

						<div class="col-xs-7 nopadwleft" id="TheSerialStat"></div>

						
					</div>
					<div class="row nopadwtop">
						<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Location:</b></div>
						<div class="col-xs-3 nopadding">
							<select class="form-control input-sm" name="selserloc" id="selserloc">
								<?php
										$qrya = mysqli_query($con,"Select * From mrp_locations Order By cdesc");
										while($row = mysqli_fetch_array($qrya, MYSQLI_ASSOC)){
											echo "<option value=\"".$row['nid']."\" data-id=\"".$row['cdesc']."\">".$row['cdesc']."</option>";
										}
								?>
							</select>
						</div>
					</div> 
					<div class="row nopadwtop">
						<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Lot #</b></div>
						<div class="col-xs-9 nopadding"><input type="text" class="form-control input-sm" name="clotno" id="clotno" value="" ></div>
					</div>
					<div class="row nopadwtop">
						<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Packing List</b></div>  
						<div class="col-xs-9 nopadding"><input type="text" class="form-control input-sm" name="cpackno" id="cpackno" value="" ></div>
					</div>
				</div>

				<div class="modal-footer">
					<button class="btn btn-success btn-sm" name="btnInsSer" id="btnInsSer">Insert (Enter)</button>
					<button class="btn btn-danger btn-sm" name="btnClsSer" id="btnClsSer" data-dismiss="modal" >Close (Ctrl+X)</button>
				</div>
			</div>
		</div>
	</div>

	<!-- FULL PO LIST REFERENCES-->
	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12 nopadding">
						<div class="col-xs-8">							
							<h4 class="modal-title" id="InvListHdr">PO List</h4>
						</div>
						<div class="col-xs-4">
							
							<input type="text" class="form-control input-xs" id="txtSrchByDesc" name="txtSrchByDesc" placeholder="Search All PO by Item Description..." autocomplete="off" />
												
						</div>
					</div>
				</div>
				
				<div class="modal-body"  style="height:45vh">
				
					<div class="col-xs-12 nopadding">

						<div class="form-group">
							<div class="col-xs-4 pre-scrollable" style="height:42vh; border-right: 2px solid #ccc">
								<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
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

							<div class="col-xs-8 pre-scrollable" style="height:42vh; border-right: 2px solid #ccc">
								<table name='MyInvDetList' id='MyInvDetList' class="table table-small">
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

	<form method="post" name="frmedit" id="frmedit" action="RR_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" value="">
	</form>

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
		if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
			e.preventDefault();
			return chkform();
		}
		else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
			e.preventDefault();
			$('#txtprodnme').focus();
		}
		else if(e.keyCode == 27){ //ESC
			e.preventDefault();
			window.location.replace("RR.php");

		}
		else if(e.keyCode == 45) { //Insert
			if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $('#SerialMod').hasClass('in')==false){
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
		$(".nav-tabs a").click(function(){
    		$(this).tab('show');
		});
		$("#selserloc").select2({
			dropdownParent: $('#SerialMod .modal-content'),
			width: '100%'
		});
		
		$('.datepick').datetimepicker({
			format: 'MM/DD/YYYY',
			defaultDate: moment(),
		});	

		$("#file-0").fileinput({
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

		document.getElementById('txtcust').focus();
		
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
			$("#txtcskuid").val(item.cskucode);

			if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
			
				myFunctionadd("","","","","","","","");
				//ComputeGross();	
									
			}
				
					
				
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
						$('#txtcskuid').val(data[3]);
				

						if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){								
			
							myFunctionadd("","","","","","","","");
							//ComputeGross();	
												
						}
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
						$("#txtcskuid").val("");
		
					//closing for success: function(value){
					}
				}); 
			
			}//if ebter is clicked		
		});
		
		$('#SerialMod').on('shown.bs.modal', function () {
			$('#serdis').focus();
		});

		$("#btnInsSer").on("click", function(){
			var itmcode = $("#serdisitmcode").val();
			var itmcoderefident = $("#serdisrefident").val();   
			var lotno = $("#clotno").val();
			var packlist = $("#cpackno").val();
			var locas = $("#selserloc").val();
			var locasdesc = $("#selserloc").find(':selected').attr('data-id');
			var uoms = $("#serdisuom").val();
			var qtys = $("#serdisqty").val();			
			var itmdesc = $("#serdisitmdesc").val();      
			var refnox = $("#serdisrefno").val(); 

			//checkQty if not over total
			xval1 = parseFloat(qtys) + parseFloat($("#ToInserted").val().replace(/,/g,''));
			xval2 = parseFloat($("#TonnedIns").val().replace(/,/g,''));

			if(xval1<=xval2){
				InsertToSerials(itmcode,itmdesc,lotno,packlist,uoms,qtys,locas,locasdesc,itmcoderefident,refnox);
				
				//reset form  
				$("#clotno").val("");
				$("#cpackno").val("");
				$("#serdisqty").val("1");

				$("#TheSerialStat").text("Inserted...");

				$("#serdisqty").focus();
			}else{
				$("#TheSerialStat").text("Over Quantity...");
			}
		
		});

		$('#txtSrchByDesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_product.php",
					dataType: "json",
					data: { query: $("#txtSrchByDesc").val() },
					success: function (data) {
						response(data);
					}

				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cname+'</span</div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#MyInvTbl').DataTable().destroy();
				$('#MyInvTbl tbody').empty(); 
				$('#MyInvDetList tbody').empty();

				$.ajax({
					url: 'th_qolist_items.php',
					data: 'x='+$('#txtcustid').val()+'&itm='+item.cname,
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

						$('#MyInvTbl').DataTable({
							"bPaginate": false,
							"bLengthChange": false,
							"bFilter": true,
							"bInfo": false,
							"bAutoWidth": false,
							"dom": '<"pull-left"f><"pull-right"l>tip',
							language: {
								search: "",
								searchPlaceholder: "Search SO "
							}
						});

						$('.dataTables_filter input').addClass('form-control input-sm');
						$('.dataTables_filter input').css(
							{'width':'150%','display':'inline-block'}
						);
						

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

				$('#txtSrchByDesc').val("").change(); 
				
			}
		
		});
	});

	//InsertToSerials(itmcode,itmdesc,lotno,packlist,uoms,qtys,locas,locasdesc,itmcoderefident,refnox);
	function InsertToSerials(itmcode,itmdesc,lotno,packlist,uoms,qtys,locas,locasdesc,nident,refno){

		$("<tr>").append(
			$("<td width=\"120px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+nident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+refno+"' name=\"sertabrefno\" id=\"sertabrefno\">"+itmcode),
			$("<td>").html(itmdesc), 
			$("<td width=\"200px\">").html("<input type='text' class='form-control input-xs' value='"+lotno+"' name=\"sertablots\" id=\"sertablots\">"), 
			$("<td width=\"200px\" style=\"padding-left: 1px\">").html("<input type='hidden' value='"+packlist+"' name=\"sertabpacks\" id=\"sertabpacks\">"+packlist), 
			$("<td width=\"150x\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
			$("<td width=\"80px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
			$("<td width=\"80px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
			$("<td width=\"50px\">").html("<input class='btn btn-danger btn-xs' type='button' id='delsrx" + itmcode + "' value='delete' />")
		).appendTo("#MyTable2 tbody");

		$("#delsrx"+itmcode).on('click', function() {
			$(this).closest('tr').remove();
		});

		var xnqty = 0;
		$("#MyTable2 > tbody > tr").each(function(index) {	

			xitmrefx =$(this).find('input[type="hidden"][name="sertabitmcode"]').val();
			xitmidnt =$(this).find('input[type="hidden"][name="sertabident"]').val();

			console.log(xitmrefx +"-"+ xitmidnt);
			if(xitmrefx==itmcode && xitmidnt==nident){
				xnqty = xnqty + parseFloat($(this).find('input[type="hidden"][name="sertabqty"]').val());
			}

		});

		$("#ToInserted").val(xnqty);
	
	}

	function myFunctionadd(nqty,nfactor,cmainunit,xref,nident,costid,costdesc,cremarks=""){

		var itmcode = document.getElementById("txtprodid").value;
		var itmcsku = document.getElementById("txtcskuid").value;
		var itmdesc = document.getElementById("txtprodnme").value;
		var itmunit = document.getElementById("hdnunit").value;
		//var dneeded = document.getElementById("date_received").value;
		
		if(nqty=="" && nfactor=="" && cmainunit=="" && xref=="" && nident==""){	
		//	var itmprice = chkprice(itmcode,itmunit);
		//	var itmamnt = itmprice;
			var itmqty = 1;
			var itmqtyorig = 0;
			var itmfactor = 1;
			var itmmainunit = itmunit;
			var itmxref = "";
			var itmident = "";

		//	var curramtz = price;

		}
		else{
		//	var itmprice = nprice;
		//	var itmamnt = namount;
			var itmqty = nqty;
			var itmqtyorig = nqty;
			var itmfactor = nfactor;
			var itmmainunit = cmainunit;
			var itmxref = xref;
			var itmident = nident;

		//	var curramtz = curramt;
		}

		//var baseprice = curramtz * parseFloat($("#basecurrval").val());


		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;


		var uomoptions = "";
		
		if(xref == ""){				
			
			var xz = $("#hdnitmfactors").val();
			//if(itmqtyunit==itmunit){
			//	isselctd = "selected";
			//}else{
				isselctd = "";
			//}
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
			
			uomoptions = "<select class='xseluom"+lastRow+" form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmmainunit+"'>"+uomoptions+"</select>";
			
		}else{
			uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom\">"+itmunit;
		}
			

		tditmbtn = "<td width=\"50\" style=\"padding:1px\">  <input class='btn btn-info btn-xs' type='button' id='ins" + lastRow + "' value='insert' /> </td>";
		
		tditmcode = "<td width=\"120\" style=\"padding:1px\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" </td>";

		//if(itmcsku!==""){
		tdskucode = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:250px; padding:1px\">"+itmcsku+"<input type='hidden' value='"+itmcsku+"' name=\"txtcskuode\" id=\"txtcskuode\"> </td>";
		//}else{
		//	tdskucode = "<td width=\"250\" style=\"padding:1px\"><input type='text' value='"+itmcsku+"' class='form-control input-xs' name=\"txtcskuode\" id=\"txtcskuode\"> </td>";
		//}
		
		
		tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:250px; padding:1px\"> " + itmdesc + "<input type='hidden' value='"+itmdesc+"' name=\"txtcitmdesc\" id=\"txtcitmdesc\"></td>";
		
		tditmunit = "<td width=\"80\" style=\"padding:1px\"> " + uomoptions + "</td>";
		
		isfactoread = "";
		if(itmmainunit==itmunit){
			isfactoread = "readonly";
		}

		var tditmfactor = "<td width=\"100\" nowrap style=\"padding:1px\"> <input type='text' value='"+itmfactor+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";
		
		tditmqty = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> </td>";
		
		//tditmprice = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

		//tditmbaseamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+curramtz+"' class='form-control input-xs' style='text-align:right' name='txtntranamount' id='txtntranamount"+lastRow+"' readonly> </td>";
		
		//tditmamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+baseprice.toFixed(4)+"' class='form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";

		tditmporef = "<td width=\"90\" style=\"padding:1px\"> <input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"> &nbsp;&nbsp;&nbsp;"+itmxref+"</td>";

		tditmcostc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:120px; padding:1px\"> <input type='hidden' value='"+costid+"' name=\"txtncostid\" id=\"txtncostid\"> <input type='hidden' value='"+costdesc+"' name=\"txtncostdesc\" id=\"txtncostdesc\"> &nbsp;&nbsp;&nbsp;"+costdesc+"</td>"; 

		tditmrmks = "<td width=\"200\" style=\"padding:1px\" align=\"center\"> <input type='text' class='form-control input-xs' name=\"txtcremarks\" id=\"txtcremarks\" value=\""+cremarks+"\"/> </td>";
		
		tditmdel = "<td width=\"80\" style=\"padding:1px\" align=\"center\"> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /> </td>";

		//+ tditmprice + tditmbaseamount+ tditmamount 

		$('#MyTable > tbody:last-child').append('<tr style=\"padding-top:1px\">'+tditmbtn+tditmcode + tdskucode + tditmdesc + tditmunit + tditmfactor + tditmqty + tditmporef + tditmcostc + tditmrmks + tditmdel + '</tr>');


			$("#del"+lastRow).on('click', function() {
				$(this).closest('tr').remove();
				// ComputeGross();
			});
			
			$("#ins"+lastRow).on('click', function() {
				InsertDetSerial(itmcode,itmdesc,itmmainunit,itmident,itmxref,$(this).attr("id"));
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
				// ComputeGross();
				tblnav(e.keyCode,$(this).attr('id'));
			});
			//alert("d");								
			$("#seluom"+lastRow).on('change', function() {
				//alert($(this).val());
				//var xyz = chkprice(itmcode,$(this).val());
				
				//$('#txtnprice'+lastRow).val(xyz.trim());
				
				//ComputeAmt($(this).attr('id'));
				//ComputeGross();
				
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

	function InsertDetSerial(itmcode, itmname, itmunit, itemrrident, refrnce, disid){
		$("#InvSerDetHdr").text("Inventory Details ("+itmname+")");
		$("#serdisuom").val(itmunit);
		$("#serdisitmcode").val(itmcode);
		$("#serdisitmdesc").val(itmname);
		$("#serdisrefident").val(itemrrident);
		$("#serdisrefno").val(refrnce);

		$("#serdisqty").val(1);
		$("#clotno").val("");
		$("#cpackno").val("");

		$x = disid.replace("ins","");
		$("#TonnedIns").val($("#txtnqty"+$x).val());
		

		$("#TheSerialStat").text("");

		var xnqty = 0;
		$("#MyTable2 > tbody > tr").each(function(index) {	
			xitmrefx =$(this).find('input[type="hidden"][name="sertabitmcode"]').val();
			xitmidnt =$(this).find('input[type="hidden"][name="sertabident"]').val();

			console.log(xitmrefx +"-"+ xitmidnt);
			if(xitmrefx==itmcode && xitmidnt==itemrrident){
				xnqty = xnqty + parseFloat($(this).find('input[type="hidden"][name="sertabqty"]').val());
			}

		});

		$("#ToInserted").val(xnqty);

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
			$('#MyInvTbl').DataTable().destroy();
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

					});
					
					$('#MyInvTbl').DataTable({
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": true,
						"bInfo": false,
						"bAutoWidth": false,
						"dom": '<"pull-left"f><"pull-right"l>tip',
						language: {
							search: "",
							searchPlaceholder: "Search SO "
						}
					});

					$('.dataTables_filter input').addClass('form-control input-sm');
					$('.dataTables_filter input').css(
						{'width':'150%','display':'inline-block'}
					);

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
		
		$('#MyInvDetList').DataTable().destroy();

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
							
				salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtnrefident"]').val();
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

				$('#MyInvDetList').DataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": true,
					"bInfo": false,
					"bAutoWidth": false,
					"dom": '<"pull-left"f><"pull-right"l>tip',
					language: {
						search: "",
						searchPlaceholder: "Search Item "
					}
				});

				$('.dataTables_filter input').addClass('form-control input-sm');
				$('.dataTables_filter input').css(
					{'width':'150%','display':'inline-block'}
				);
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
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var tblrowcnt = tbl.length;
		
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
						$("#txtcskuid").val(item.cskucode);
						
						myFunctionadd(item.nqty,item.nfactor,item.cmainuom,item.xref,item.nident,item.nlocation_id,item.ncostcenter,"");
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");	
						$("#hdnunit").val("");	
						$("#txtcskuid").val("");

			
					});
					
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
				//myprice = $(this).find('input[name="txtnprice"]').val();
				
				if(myqty == 0 || myqty == ""){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
				}
				
			//	if(myprice == 0 || myprice == ""){
			//		msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
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


			//Saving the header

			/*
			var ccode = $("#txtcustid").val();
			var crem = $("#txtremarks").val();
			var ddate = $("#date_received").val();
			var ngross = $("#txtnGross").val(); 
			var ccustsi = $("#txtSuppSI").val();
			*/
			var myform = $("#frmpos").serialize();
			var formdata = new FormData($('#frmpos')[0]);
			formdata.delete('upload[]')
			jQuery.each($('#file-0')[0].files, function(i, file){
				formdata.append('file-'+i, file);
			});
			
			$.ajax ({
				url: "RR_newsave.php",
				//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, ccustsi:ccustsi },
				data: formdata,
				cache: false,
				processData: false,
				contentType: false,
				method: 'post',
				type: 'post',
				async: false,
				beforeSend: function(){
					$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW RR: </b> Please wait a moment...");
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
				
					var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
					var cskuno = $(this).find('input[type="hidden"][name="txtcskuode"]').val();
					var cskudesc = $(this).find('input[type="hidden"][name="txtcitmdesc"]').val();

					var cuom = $(this).find('select[name="seluom"]').val();
							if(cuom=="" || cuom==null){
								var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
							}
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nqtyOrig = $(this).find('input[type="hidden"][name="txtnqtyORIG"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[name="hdnfactor"]').val();

					var xcref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					var crefidnt = $(this).find('input[type="hidden"][name="txtnrefident"]').val();
					var ncostid = $(this).find('input[type="hidden"][name="txtncostid"]').val();  
					var ncostdesc = $(this).find('input[type="hidden"][name="txtncostdesc"]').val(); 
					var crmkss = $(this).find('input[name="txtcremarks"]').val();

					//alert("trancode="+ trancode+ "&indx=" + index+ "&citmno=" + citmno+ "&cuom=" + cuom+ "&nqty=" + nqty+ "&mainunit=" + mainunit+ "&nfactor=" + nfactor+ "&nqtyorig=" + nqtyOrig+ "&xcref=" + xcref+ "&crefidnt=" + crefidnt);

					if(nqty!==undefined){
						nqty = nqty.replace(/,/g,'');
					}
					
					$.ajax ({
						url: "RR_newsavedet.php",
						data: { trancode: trancode, indx: index, citmno: citmno, cskuno:cskuno, cskudesc:cskudesc, cuom: cuom, nqty:nqty, mainunit:mainunit, nfactor:nfactor, nqtyorig:nqtyOrig, xcref:xcref, crefidnt:crefidnt, ncostid:ncostid, ncostdesc:ncostdesc, crmkss:crmkss},
						async: false,
						success: function( data ) {
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
					var clotsx = $(this).find('input[name="sertablots"]').val();				
					var cpackl = $(this).find('input[type="hidden"][name="sertabpacks"]').val(); 
					var clocas = $(this).find('input[type="hidden"][name="sertablocas"]').val();

					$.ajax ({
						url: "RR_newsavedetserials.php",
						data: { trancode: trancode, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, clotsx:clotsx, cpackl:cpackl },
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
				
						}, 2000); // milliseconds = 3seconds

					
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
</script>
