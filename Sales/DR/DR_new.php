<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "DR_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	//echo $_SESSION['chkitmbal']."<br>";
	//echo $_SESSION['chkcompvat'];

	$ddeldate = date("m/d/Y");
	$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

	//echo $ddeldate;

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
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

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

<body style="padding:5px">
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

	<form action="SO_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;" enctype="multipart/form-data">
		<fieldset>
    	<legend>New Delivery Receipt</legend>
		
				<div class="col-xs-12 nopadwdown"><b>Delivery Information</b></div>
				<!-- 
				-- Navigators
				-->
				<ul class="nav nav-tabs">
						<li class="active"><a href="#home">Order Details</a></li>
						<li><a href="#menu1">Delivered To</a></li>
					<li><a href="#attach_pane">Attachments</a></li>
				</ul>
	
				<div class="tab-content">

         	<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px">
			 
						<table width="100%" border="0">
							<tr>
								<tH>&nbsp;Customer:</tH>
								<td style="padding:2px"><div class="col-xs-12 nopadding">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
									<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
									<input type="hidden" id="hdnpricever" name="hdnpricever" value="">

								</div>
								<div class="col-xs-8 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
								</div>
								</div></td>
								<tH>DR Series No.:</tH>
								<td style="padding:2px;"><div class="col-xs-10 nopadding">
								<input type='text' class="form-control input-sm" id="cdrprintno" name="cdrprintno" value="" autocomplete="off" tabindex="2" />
								</div></td>
							</tr>
							<tr>
								<tH width="100">&nbsp;Salesman:</tH>
							<td style="padding:2px"><div class="col-xs-12 nopadding">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtsalesmanid" name="txtsalesmanid" class="form-control input-sm" placeholder="Customer Code..." tabindex="3">
								</div>
								<div class="col-xs-8 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtsalesman" name="txtsalesman" width="20px" tabindex="3" placeholder="Search Salesman Name..."  size="60" autocomplete="off">
								</div>
								</div></td>
							<tH width="150">Delivery Date:</tH>
							<td style="padding:2px;">
							<div class="col-xs-10 nopadding">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $ddeldate; ?>" tabindex="4"  />
							</div>
							</td>
							</tr>
								<tr>
									<tH width="100">&nbsp;Remarks:</tH>
							<td style="padding:2px"><div class="col-xs-11 nopadding">
								<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="5">
								</div></td>
							<tH width="150" style="padding:2px">
							<!--<div class="chklimit"><b>Credit Limit:</b></div>-->
							</tH>
							<td style="padding:2px" align="right">
								<!-- <div class="chklimit col-xs-10 nopadding" id="ncustlimit"></div>
								<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">-->
							</td>
							</tr>
								<tr>
									<td>&nbsp;</td>
								<td>
								<div class="col-xs-8 nopadding">
								</div>
								<div class="col-xs-3 nopadwright">
										<input type="text" class="form-control input-sm" id="txtsoref" name="txtsoref" width="20px" tabindex="6" placeholder="Reference SO">
									</div>
							</td>
							<td style="padding:2px">
							<!--<div class="chklimit"><b>Balance:</b></div>-->
							</td>
							<td style="padding:2px"  align="right">
								<!--
								<div class="chklimit col-xs-10 nopadding" id="ncustbalance"></div>
								<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
								<div class="chklimit col-xs-10 nopadding" id="ncustbalance2"></div>
								-->
							</td>
							</tr>
							<!--
							<tr>
								<td colspan="4">
										<div class="col-xs-12 nopadwtop2x">
											<div class="col-xs-3 nopadwdown">
											<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">
											</div>
											<div class="col-xs-5 nopadwleft">
											<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5">
											</div>
										</div>

									<input type="hidden" name="hdnqty" id="hdnqty">
									<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
									<input type="hidden" name="hdnunit" id="hdnunit">

								</td>
								<td align="right" style="vertical-align:top">
								<div class="chklimit col-xs-10 nopadding" id="ncustbalance2"></div>
								</td>
								</tr>-->
						</table>
						
					</div>

					<!--
					-- Deliver To Panel
					-->
					<div id="menu1" class="tab-pane fade" style="padding-left:5px; padding-top:10px">
						<table width="100%" border="0">
							<tr>
							<td width="150"><b>Customer</b></td>
							<td width="310" colspan="2" style="padding:2px">
							<div class="col-xs-8 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtdelcustid" name="txtdelcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
										</div>

										<div class="col-xs-9 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
										</div> 
										</div>

							</td>
							</tr>
							<tr>
							<td><button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnNewAdd" name="btnNewAdd">
							Select Address</button></td>
							<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  readonly="true" /></div></td>
							</tr>

							<tr>
							<td>&nbsp;</td>
							<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
												<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off"  readonly="true" />
											</div>

											<div class="col-xs-6 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off"   readonly="true" />
											</div></div></td>
							</tr>

							<tr>
							<td>&nbsp;</td>
							<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-9 nopadding">
												<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" readonly="true" />
											</div>

											<div class="col-xs-3 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off"  readonly="true" />
											</div></div></td>
							</tr>
						</table>
					</div>

					<div id="attach_pane" class="tab-pane fade" style="padding-left:5px; padding-top:10px">
						<!--
						--
						-- Import Files Modal
						--
						-->
						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />
						
					</div>

				</div>

				<hr>
				<div class="col-xs-12 nopadwdown"><b>Details</b></div>
						
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
								height: 250px;
								text-align: left;
								overflow: auto">
									<input type="hidden" name="hdnqty" id="hdnqty">
										<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
										<input type="hidden" name="hdnunit" id="hdnunit">
												<input type="hidden" id="txtprodid" name="txtprodid">
												<input type="hidden" id="txtprodnme" name="txtprodnme">
						
									<table id="MyTable" class="MyTable table table-condensed" width="100%">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
												<th style="border-bottom:1px solid #999">Code</th>
												<th style="border-bottom:1px solid #999">Description</th>
												<th style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
												<th style="border-bottom:1px solid #999">UOM</th>
												<th style="border-bottom:1px solid #999">Factor</th>
												<th style="border-bottom:1px solid #999">Qty</th>
												<!--<th style="border-bottom:1px solid #999">Price</th>
												<th style="border-bottom:1px solid #999">Amount</th>-->
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
							
											<table id="MyTableInvSer" cellpadding="3px" width="100%" border="0">
												<thead>
															<tr>
																
																	<th style="border-bottom:1px solid #999">Item Code</th>
																	<th style="border-bottom:1px solid #999">Serial No.</th>
																	<th style="border-bottom:1px solid #999">UOM</th>
																	<th style="border-bottom:1px solid #999">Qty</th>
																	<th style="border-bottom:1px solid #999">Location</th>
																	<th style="border-bottom:1px solid #999">Expiration Date</th>
										<th style="border-bottom:1px solid #999">Remarks</th>
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
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='DR.php';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>

						<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
							SO<br>(Insert)
						</button>

						
						<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
						<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>
						<td align="right" valign="top">
					<!-- <b>TOTAL AMOUNT </b>-->
						&nbsp;&nbsp;
						<input type="hidden" id="txtnGross" name="txtnGross" value="0">
							</td>
						</tr>
				</table>

    </fieldset>
    
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
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999">Code</th>
										<th style="border-bottom:1px solid #999">Description</th>
										<th style="border-bottom:1px solid #999">Field Name</th>
										<th style="border-bottom:1px solid #999">Value</th>
										<th style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>
								<tbody class="tbody">
                </tbody>
              </table>
    
						</div>
        	</div><!-- /.modal-content -->
    		</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->


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
													<div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
																<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
																<thead>
																	<tr>
																		<th>SO No</th>
																		<th>Amount</th>
																	</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
													</div>

													<div class="col-xs-8 nopadwleft pre-scrollable" style="height:37vh">
																<table name='MyInvDetList' id='MyInvDetList' class="table table-small">
																<thead>
																	<tr>
																		<th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
																		<th>Item No</th>
																		<th>Description</th>
																		<th>UOM</th>
																		<th>Qty</th>
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
            </div>
            
            <div class="modal-body" style="height:20vh">
							
								<div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Required Qty:</b></div>
										<div class="col-xs-1 nopadwtop" id="htmlserqtyneed"><input type="hidden" name="hdnserqtyneed" id="hdnserqtyneed"></div>
										<div class="col-xs-1 nopadwtop" id="htmlserqtyuom"><input type="hidden" name="hdnserqtyuom" id="hdnserqtyuom"></div>
								</div>
								
								<div class="row nopadwtop2x"><div class="col-xs-12">
										<table id="MyTableSerials" cellpadding="3px" width="100%" border="0">
		    							<thead>
		                        <tr>
		                            <th style="border-bottom:1px solid #999">Serial No.</th>	                            
		                            <th style="border-bottom:1px solid #999">Location</th>
		                            <th style="border-bottom:1px solid #999">Exp. Date</th>
		                            <th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">UOM</th>	
									<th style="border-bottom:1px solid #999">Qty Picked</th>
									
		                        </tr>
		                   </thead>
                   		 <tbody>
                   		 </tbody>
                        
                </table>
								</div></div>

						</div>

						<div class="modal-footer">
								<button class="btn btn-success btn-sm" name="btnInsSer" id="btnInsSer">Insert (Enter)</button>
								<button class="btn btn-danger btn-sm" name="btnClsSer" id="btnClsSer" data-dismiss="modal" >Close (Ctrl+X)</button>
						</div>
				</div>
		</div>
</div>
	
	<!-- Address List -->
<div class="modal fade" id="MyAddModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader"> Address Lists </h3>           
			</div>
    
            <div class="modal-body">
                <table id="MyAddTble" class="table table-condensed" width="100%">
                	<thead>
    				<tr>
                    	<th style="border-bottom:1px solid #999">&nbsp;</th>
						<th style="border-bottom:1px solid #999">House No.</th>
						<th style="border-bottom:1px solid #999">City</th>
                        <th style="border-bottom:1px solid #999">State</th>
						<th style="border-bottom:1px solid #999">Country</th>
                        <th style="border-bottom:1px solid #999">Zip</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
                    </thead>
					<tbody class="tbody">
                    </tbody>
                </table>
    
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<form method="post" name="frmedit" id="frmedit" action="DR_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">
var xChkBal = "";
var xChkLimit = "";
var xChkLimitWarn = "";

var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;

	$(document).ready(function(e) {
			$(".nav-tabs a").click(function(){
    			$(this).tab('show');
			});
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
						  // xChkLimit = item.chkcustlmt; //0 = Disable ; 1 = Enable
						  xChkLimit = 0;
						  // xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
						  xChkLimitWarn = 0;
						   
					   });
					}
				});
			/*
			*
			* Bootstrap JQueries Fields
			*
			*/

			$("#file-0").fileinput({
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false,}
			});
	
		if(xChkBal==1){
			$("#tblAvailable").hide();
		}
		else{
			$("#tblAvailable").show();
		}


		if(xChkLimit==0){
			$(".chklimit").hide();
		}
		else{
			$(".chklimit").show();
		}
		

	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);

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
			openinv();
		}
	  }
		else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
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

$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				// minDate: new Date(),
        });

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
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
			if(value!=""){
				var data = value.split(":");
				$('#txtcust').val(data[0]);
				//$('#imgemp').attr("src",data[2]);
				$('#hdnpricever').val(data[1]);
				//deliveredto   
				$('#txtdelcustid').val(dInput);
				$('#txtdelcust').val(data[0]); 
				 
				$('#txtsalesmanid').val(data[10]);
				$('#txtsalesman').val(data[11]);
				
				$('#txtchouseno').val(data[5]);
				$('#txtcCity').val(data[6]);
				$('#txtcState').val(data[7]);
				$('#txtcCountry').val(data[8]);
				$('#txtcZip').val(data[9]);
								
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
				
				if(xChkLimit==1){

					var limit = data[1];
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}
					
					limit = Number(limit).toLocaleString('en');
					
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(data[1]);
					//alert(dInput);
					checkcustlimit(dInput, data[1]);
				}
				
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				//$('#imgemp').attr("src","../../images/blueX.png");
				$('#hdnpricever').val("");
				
				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");
				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			//$('#imgemp').attr("src","../../images/blueX.png");
			$('#hdnpricever').val("");

				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");
			
			$('#hdnvalid').val("NO");
		}
		});

		}
		
	});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
		  
		  $('#txtprodnme').attr("disabled", true);
		  $('#txtprodid').attr("disabled", true);
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

				$('#txtdelcustid').val(item.id);
				$('#txtdelcust').val(item.value); 
				 
				$('#txtsalesmanid').val(item.csman);
				$('#txtsalesman').val(item.smaname);
				
				$('#txtchouseno').val(item.chouseno);
				$('#txtcCity').val(item.ccity);
				$('#txtcState').val(item.cstate);
				$('#txtcCountry').val(item.ccountry);
				$('#txtcZip').val(item.czip);
			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();
			
				if(xChkLimit==1){
					
					var limit = item.nlimit;
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}

					limit = Number(limit).toLocaleString('en');					
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(item.nlimit);
					
					checkcustlimit(item.id, item.nlimit);

				}
			
			
		}
	
	});

	document.getElementById('txtcust').focus();
	
	$("#txtsalesmanid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_salesmanid.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
					if(value!=""){				 
						$('#txtsalesman').val(value);
					}
				}
			});
		}
	});
	
	$('#txtsalesman').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_salesman.php",
				dataType: "json",
				data: {
					query: $("#txtsalesman").val()
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
						
			$('#txtsalesman').val(item.value).change(); 
			$("#txtsalesmanid").val(item.id);
			
			
		}
	
	});
	
	$("#txtdelcustid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_custchildid.php',
				data: 'c_id='+ $(this).val() + 'm_id='+ $("#txtcustid").val(),                 
				success: function(value){
					if(value!=""){				 
						var data = value.split(":");

						$('#txtdelcust').val(data[1]); 
						
						$('#txtchouseno').val(data[2]);
						$('#txtcCity').val(data[3]);
						$('#txtcState').val(data[4]);
						$('#txtcCountry').val(data[5]);
						$('#txtcZip').val(data[6]);
					}
				}
			});
		}
	});

	//Search Cust name
	$('#txtdelcust').typeahead({
		items: "all",
		autoSelect: true,
		fitToElement: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_customerdel.php",
				dataType: "json",
				data: {
					query: request, cmain: $("#txtcustid").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			//if(item.cname != item.value){
			//	return '<div style="border-top:1px solid gray;"><span>' + item.id + '</span><br><small>' + item.value + " / " + item.cname + "</small></div>";
			//}else{
				return '<div style="border-top:1px solid gray;"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		//	}
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtdelcust').val(item.value).change(); 
			$("#txtdelcustid").val(item.id);
			
			$('#txtchouseno').val(item.cadd);
			$('#txtcCity').val(item.ccity);
			$('#txtcState').val(item.cstate);
			$('#txtcCountry').val(item.ccountry);
			$('#txtcZip').val(item.czip);
							
			$('#hdnvalid').val("YES");
			
		}
	
	});
	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_product.php",
				dataType: "json",
				data: { query: $("#txtprodnme").val(), itmbal: xChkBal, styp: "Goods" },
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
			$("#hdnqty").val(item.nqty);
			$("#hdnqtyunit").val(item.cqtyunit);
			
			addItemName("","","","","","","");
			
		}
	
	});


	$("#txtprodid").keypress(function(event){
		if(event.keyCode == 13){

		$.ajax({
        url:'../get_productid.php',
        data: 'c_id='+ $(this).val() + "&itmbal="+xChkBal+"&styp=Goods",                 
        success: function(value){
            var data = value.split(",");
            $('#txtprodid').val(data[0]);
            $('#txtprodnme').val(data[1]);
			$('#hdnunit').val(data[2]);
			$("#hdnqty").val(data[3]);
			$("#hdnqtyunit").val(data[4]);


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
		 }
		 
		//if(isItem=="NO"){		

			myFunctionadd("","","","","","","");
			ComputeGross();	
			
	   // }
	   // else{
			
		//	addqty();
		//}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if enter is clicked
		}
		
	});
	
	$("#btnInsSer").on("click", function(){
	
			var tbl = document.getElementById('MyTableSerials').getElementsByTagName('tr');
			var lastRow = tbl.length;
	
			if(lastRow>1){
					$("#MyTableSerials > tbody > tr").each(function(index) {
						var zxitmcode = $(this).find('input[type="hidden"][name="lagyitmcode"]').val();
						var zxserial = $(this).find('input[type="hidden"][name="lagyserial"]').val();
						var zxuom = $(this).find('input[type="hidden"][name="lagycuom"]').val();	
						var zxqty = $(this).find('input[name="lagyqtyput"]').val();		
						var zxloca = $(this).find('input[type="hidden"][name="lagylocas"]').val();	
						var zxlocadesc = $(this).find('input[type="hidden"][name="lagylocadesc"]').val();
						var zxexpd = $(this).find('input[type="hidden"][name="lagyexpd"]').val();
						var zxnident = $(this).find('input[type="hidden"][name="lagyrefident"]').val();
						var zxreference = $(this).find('input[type="hidden"][name="lagyrefno"]').val();
						var zxmainident = $("#serdisrefident").val();

						if(parseFloat(zxqty) > 0){
							InsertToSerials(zxitmcode,zxserial,zxuom,zxqty,zxloca,zxlocadesc,zxexpd,zxnident,zxreference,zxmainident);			
						}

					});
			}
		
			//close modal
			$("#SerialMod").modal("hide");
	});
	
	$("#btnNewAdd").on("click", function(){
		if($("#txtdelcustid").val()=="" || $("#txtdelcust").val()==""){
			alert("Select Delivery To Customer!");
		}else{
			$('#MyAddTble tbody').empty();
			//get addressses...
			$.ajax({
				url : "../SO/th_addresslist.php?id=" + $("#txtdelcustid").val() ,
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{	
					console.log(data);
                    $.each(data,function(index,item){
						
						$("<tr>").append(
						$("<td>").html("<a onclick=\"trclickable('"+item.chouseno+"','"+item.ccity+"','"+item.cstate+"','"+item.ccountry+"','"+item.czip+"')\" style=\"cursor: pointer;\">Select</a>"),
						$("<td>").html(item.chouseno),
						$("<td>").html(item.ccity),
						$("<td>").html(item.cstate),
						$("<td>").html(item.ccountry),
						$("<td>").html(item.czip)
						).appendTo("#MyAddTble tbody");
											   
					});
						
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}					
			});
			
		
			 $("#MyAddModal").modal("show");// 
		}
	});
	
	$("#txtsoref").keydown(function(event){
		
		var issokso = "YES";
		var msgs = "";
		
		if(event.keyCode == 13){

			$('#MyTable tbody').empty();

			//SO Header
			$.ajax({
				url : "th_getso.php?id=" + $(this).val() ,
				type: "GET",
				dataType: "JSON",
				async: false,
				success: function(data)
				{	
					console.log(data);
                    $.each(data,function(index,item){

						if(item.lapproved==0 && item.lcancelled==0){
						   msgs = "Transaction is still pending";
						   issokso = "NO";
						}
						
						if(item.lapproved==0 && item.lcancelled==1){
						   msgs = "Transaction is already cancelled";
						   issokso = "NO";
						}
					
					if(issokso=="YES"){
						$('#txtcust').val(item.cname); 
						$("#txtcustid").val(item.ccode);

						$("#hdnpricever").val(item.cver);

						$('#txtdelcustid').val(item.cdelcode);
						$('#txtdelcust').val(item.cdelname); 

						$('#txtsalesmanid').val(item.csalesman);
						$('#txtsalesman').val(item.csmaname);

						$('#txtchouseno').val(item.chouseno);
						$('#txtcCity').val(item.ccity);
						$('#txtcState').val(item.cstate);
						$('#txtcCountry').val(item.ccountry);
						$('#txtcZip').val(item.czip);

						$('#date_delivery').val(item.dcutdate);
					}
						
					});
						
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}					
			});
			
			if(issokso=="YES"){
			//add details
			//alert("th_qolistputall.php?id=" + $(this).val() + "&itmbal=" + xChkBal);
				$.ajax({
					url : "th_qolistputall.php?id=" + $(this).val() + "&itmbal=" + xChkBal + "&ddate=" + $("#date_delivery").val(),
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
						//alert(item.cqtyunit + ":" + item.cunit);
						addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident)

					 });

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}

				});
			}
			
			if(issokso=="NO"){
				alert(msgs);
			}
		}
	});

});

function checkcustlimit(id,xcred){
	//Check Credit Limit BALNCE here
	//alert(xChkLimitWarn);
	var xBalance = 0;
	var xinvs = 0;
	var xors = 0;
	
		$.ajax ({
			url: "../th_creditlimit.php",
			data: { id: id },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					//if(item.invs!=null){
						//alert(item.invs +":"+ item.ors);
						xinvs = item.invs;
					//}
					
					//if(item.ors!=null){
						xors = item.ors;
					//}
					
				});
			},
			error: function (req, status, err) {

				alert("Something went wrong\n" + status +"\n"+ err);
				//$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
				//$("#alertbtnOK").show();
				//$("#AlertModal").modal('show');
			}
		});
	
	//alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
		
	xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
	$("#hdncustbalance").val(xBalance);
	
	
	
	if(xBalance > 0){
		xBalance = Number(xBalance).toLocaleString('en');
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
			$('#txtprodnme').attr("disabled", true);
	  		$('#txtprodid').attr("disabled", true);

		}
	}

}

function addItemName(qty,price,curramt,amt,factr,cref,crefident){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				
				if($("#txtprodid").val()==disID && cref==disref){
					
					isItem = "YES";

				}
			});	

	// if(isItem=="NO"){	
	 	myFunctionadd(qty,price,curramt,amt,factr,cref,crefident);
		
		ComputeGross();	

//	 }
	// else{

//		addqty();	
			
//	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		
	 }

}

function myFunctionadd(qty,pricex,curramt,amtx,factr,cref,crefident){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var itmorgqty = 0;
		var price = chkprice(itmcode,itmunit,itmccode,$("#date_delivery").val());
		var amtz = price;
		var factz = 1;
	}
	else{
		var itmtotqty = qty
		var itmorgqty = qty;
		var price = pricex;
		var amtz = amtx;	
		var factz = factr;	
	}

	
		if(xChkBal==1){
			var avail = "";
		}
		else{
			if(parseFloat(itmqty)>0){
				var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='"+itmqty+"'> " + itmqty + " " + itmqtyunit +" </td>";
				var qtystat = "";
			}
			else{
				var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> Unavailable </td>";
				var qtystat = "readonly";
				itmtotqty = 0;
			}
		}
		

		var xz = $("#hdnitmfactors").val();
		if(itmqtyunit==itmunit){
			isselctd = "selected";
		}else{
			isselctd = "";
		}
		var uomoptions = "<option value='"+itmqtyunit+"' data-factor='1' "+isselctd+">"+itmqtyunit+"</option>";

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
								
		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	if(cref==null){
		cref = ""
	}
	
	var insbtn = "<td width=\"50\"> <input class='btn btn-info btn-xs' name='ins' type='button' id='ins" + lastRow + "' value='insert' /></td>";
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode" + lastRow + "\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference" + lastRow + "\"><input type='hidden' value='"+crefident+"' name=\"txtcrefident\" id=\"txtcrefident" + lastRow + "\"></td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;
	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmqtyunit+"'>"+uomoptions+"</select> </td>";

	isfactoread = "";
	if(itmqtyunit==itmunit){
		isfactoread = "readonly";
	}

	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+factz+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";


	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmorgqty+"' name='hdnqtyorig' id='hdnqtyorig"+lastRow+"'> <input type='hidden' value='"+price+"' name=\"txtnprice\" id='txtnprice"+lastRow+"' \> <input type='hidden' value='"+amtz+"' name=\"txtnamount\" id='txtnamount"+lastRow+"' \> <input type='hidden' value='"+curramt+"' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' \> </td>";
		
	//var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' readonly=\"true\" \"> </td>";
			
	//var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+amtz+"' class='form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly=\"true\" \> </td>";

	// &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> 
	
	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' name='del' id='del" + lastRow + "' value='delete'/></td>";

//tditmprice + tditmamount +
	$('#MyTable > tbody:last-child').append('<tr>'+insbtn+tditmcode + tditmdesc + tditmavail + tditmunit + tditmfactor + tditmqty +  tditmdel + '</tr>');

									$("#del"+lastRow).on('click', function() {
										$(this).closest('tr').remove();
										Reindex();
									});

									$("#ins"+lastRow).on('click', function() {
										 var xcsd = $(this).closest("tr").find("input[name=txtnqty]").val();
										 InsertDetSerial(itmcode, itmdesc, itmunit, crefident, xcsd, factz, itmqtyunit, cref)
									});

									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});

									$("#seluom"+lastRow).on('change', function() {

										var xyz = chkprice(itmcode,$(this).val(),itmccode,xtoday);
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

										$('#txtnprice'+lastRow).val(xyz.trim());
										//alert($(this).attr('id'));
										ComputeAmt($(this).attr('id'));
										ComputeGross();

									});
									
									ComputeGross();
									
									
}

function Reindex(){
			$("#MyTable > tbody > tr").each(function(index) {	
				tx = index + 1;
	
				$(this).find('input[name="ins"]').attr("id","ins"+tx);
				$(this).find('input[name="txtitemcode"]').attr("id","txtitemcode"+tx);
				$(this).find('input[type="hidden"][name="txtcreference"]').attr("id","txtcreference"+tx);
				$(this).find('input[type="hidden"][name="txtcrefident"]').attr("id","txtcrefident"+tx);
				$(this).find('select[name="seluom"]').attr("id","seluom"+tx);
				$(this).find('input[name="hdnfactor"]').attr("id","hdnfactor"+tx); 
				$(this).find('input[name="txtnqty"]').attr("id","txtnqty"+tx);
				$(this).find('input[type="hidden"][name="hdnmainuom"]').attr("id","hdnmainuom"+tx);
				$(this).find('input[type="hidden"][name="hdnqtyorig"]').attr("id","hdnqtyorig"+tx);
				$(this).find('input[type="hidden"][name="txtnprice"]').attr("id","txtnprice"+tx);
				$(this).find('input[type="hidden"][name="txtnamount"]').attr("id","txtnamount"+tx);
				$(this).find('input[type="hidden"][name="txtntranamount"]').attr("id","txtntranamount"+tx);
				$(this).find('input[name="del"]').attr("id","del"+tx);

			});
}

function InsertDetSerial(itmcode, itmname, itmunit, itemrrident, itemqty, itmfctr, itemcunit, itmxref){
	$("#InvSerDetHdr").text("Inventory Details ("+itmname+")");
	$("#hdnserqtyneed").val(itemqty); 
	$("#htmlserqtyneed").text(itemqty); 
	$("#hdnserqtyuom").val(itemcunit); 
	$("#htmlserqtyuom").text(itemcunit);
	//alert("th_serialslist-manual.php?itm="+itmcode+"&cuom="+itmunit+"&qty="+itemqty+"&factr="+itmfctr+"&mainuom="+itemcunit);

	$('#MyTableSerials tbody').empty();

			$.ajax({
					url : "th_serialslist-manual.php",
					data: { itm: itmcode, cuom: itmunit, qty: itemqty, factr: itmfctr, mainuom: itemcunit, itmxref: itmxref },
					type: "POST",
					async: false,
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);

             $.each(data,function(index,item){

								$("<tr>").append(
									$("<td>").html("<input type='hidden' value='"+itmcode+"' name=\"lagyitmcode\" id=\"lagyitmcode\"><input type='hidden' value='"+item.cserial+"' name=\"lagyserial\" id=\"lagyserial\"><input type='hidden' value='"+item.nrefidentity+"' name=\"lagyrefident\" id=\"lagyrefident\"><input type='hidden' value='"+item.ctranno+"' name=\"lagyrefno\" id=\"lagyrefno\">"+item.cserial), 
									$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.nlocation+"' name=\"lagylocas\" id=\"lagylocas\"><input type='hidden' value='"+item.locadesc+"' name=\"lagylocadesc\" id=\"lagylocadesc\">"+item.locadesc),
									$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.dexpired+"' name=\"lagyexpd\" id=\"lagyexpd\">"+item.dexpired),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.nqty+"' name=\"lagynqty\" id=\"lagynqty\">"+item.nqty),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.cunit+"' name=\"lagycuom\" id=\"lagycuom\">"+item.cunit),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='text' class='numeric form-control input-sm text-right' value='0' name=\"lagyqtyput\" id=\"lagyqtyput\">")
								).appendTo("#MyTableSerials tbody");

									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									$("input.numeric").on("keyup", function() {
									   if(parseFloat($(this).val()) > parseFloat(itemqty)){
												alert("Quantity must be less than available qty.");
												$(this).val(item.nqty);
										 }
									});
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});
		//MyTableSerials

	$("#SerialMod").modal("show");
}

function InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refe,mainident){ 

	$("<tr>").append(
		$("<td width=\"120px\" style=\"padding:1px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+mainident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+nident+"' name=\"sertabreferid\" id=\"sertabreferid\"><input type='hidden' value='"+refe+"' name=\"sertabrefer\" id=\"sertabrefer\">"+itmcode),
		$("<td>").html("<input type='hidden' value='"+serials+"' name=\"sertabserial\" id=\"sertabserial\">"+serials), 
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
		$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
		$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+expz+"' name=\"sertabesp\" id=\"sertabesp\">"+expz),
		$("<td width=\"300px\" style=\"padding:1px\">").html("<input type='text' value='' name=\"sertabremx\" id=\"sertabremx\" class='form-control input-sm' autocomplete='off'>"),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input class='btn btn-danger btn-xs' type='button' id='delsrx" + itmcode + "' value='delete' />")
	).appendTo("#MyTableInvSer tbody");

									$("#delsrx"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});
}
			
		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val();
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;

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

			$("#txtnGross").val(gross);
			
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
			var itmprice = $(this).find("input[name='txtnprice']").val();
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
			TotAmt = TotQty * parseFloat(itmprice);
			$(this).find("input[name='txtnamount']").val(TotAmt);
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
				alert(citmno+"!="+itmcde);
				if(citmno!=itmcde){
					
					$(this).find('input[name="txtinfofld"]').attr("disabled", true);
					$(this).find('input[name="txtinfoval"]').attr("disabled", true);
					$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					
				}
				else{
					$(this).find('input[name="txtinfofld"]').attr("disabled", false);
					$(this).find('input[name="txtinfoval"]').attr("disabled", false);
					$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
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
										$(this).closest('tr').remove();
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
	//alert("th_checkitmprice.php?itm="+itmcode+"&cust="+ccode+"&cunit="+itmunit+"&dte="+datez);		
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


function openinv(){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
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
			$('#InvListHdr').html("SO List: " + $('#txtcust').val())

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
						$("#AlertMsg").html("No Sales Order Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"'>").text(item.cpono),
							$("<td>").text(item.ngross)
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

	$('#InvListHdr').html("SO List: " + $('#txtcust').val() + " | SO Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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
					data: 'x='+drno+"&y="+salesnos+"&itmbal="+xChkBal,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false); 
					   
                      console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							  alert("NO more items to add!")
						  }
						  else{
						  
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\">"),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
								).appendTo("#MyInvDetList tbody");
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

function InsertSI(){	
	
   $("input[name='chkSales[]']:checked").each( function () {

				var tranno = $(this).data("id");
	   			var id = $(this).val();
				//alert("th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal);
	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal + "&ddate=" + $("#date_delivery").val(),
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.desc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 
							$("#hdnqty").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							//alert(item.cqtyunit + ":" + item.cunit);
							addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident)
											   
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
			myfacx = $(this).find('input[name="hdnfactor"]').val();
			
			myprice = $(this).find('input[name="txtnamount"]').val();
			
			//if(myqty == 0 || myqty == ""){
				//msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
		//	}else{
				//var myqtytots = parseFloat(myqty) * parseFloat(myfacx);

				//if(parseFloat(myav) < parseFloat(myqtytots)){
				//	msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
			//	}
		//	}
			if(xChkBal==0){
				//alert(myqty + ">" + myav);
				if(parseFloat(myqty)>parseFloat(myav)){
					$xcb = index+1;
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Qty is greater than the available qty: row " + $xcb;
				}
			}
			
			//if(myprice == 0 || myprice == ""){
				//msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
			//}

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

	// Check if Credit Limit activated (kung sobra)
	if(xChkLimit==1){
		if(parseFloat($("#txtnGross").val())>parseFloat($("#hdncustbalance").val())){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Available Credit Limit is not enough!");
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
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val(); 
		var cdrprintno = $("#cdrprintno").val();
		
		var salesman = $("#txtsalesmanid").val();
		var delcodes = $("#txtdelcustid").val();
		var delhousno = $("#txtchouseno").val();
		var delcity = $("#txtcCity").val();
		var delstate = $("#txtcState").val();
		var delcountry = $("#txtcCountry").val();
		var delzip = $("#txtcZip").val();

		var input_data = [
			{	key: 'ccode', input: $("#txtcustid").val()	},
			{	key: 'crem', input: $("#txtremarks").val()	},
			{	key: 'ddate', input: $("#date_delivery").val()	},
			{	key: 'ngross', input: $("#txtnGross").val()	},
			{	key: 'cdrprintno', input: $("#cdrprintno").val()	},

			{	key: 'salesman', input: $("#txtsalesmanid").val()	},
			{	key: 'delcodes', input: $("#txtdelcustid").val()	},
			{	key: 'delhousno', input: $("#txtchouseno").val()	},
			{	key: 'delcity', input: $("#txtcCity").val()		},
			{	key: 'delstate', input: $("#txtcState").val()	},
			{	key: 'delcountry', input: $("#txtcCountry").val()	},
			{	key: 'delzip', input: $("#txtcZip").val()	}
		]
		//alert("DR_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross+"&cdrprintno="+cdrprintno+"&salesman="+salesman+"&delcodes="+delcodes+"&delhousno="+delhousno+"&delcity="+delcity+"&delstate="+delstate+"&delcountry="+delcountry+"&delzip="+delzip);
		var formdata = new FormData();
		jQuery.each(input_data, function(i, { key, input }){
			formdata.append(key, input)
		})
		jQuery.each($('#file-0')[0].files, function(i, file) {
			formdata.append('file-'+i, file)
		})

		
		for(var par of formdata.entries()){
			console.log(par)
		}
		$.ajax ({
			url: "DR_newsavehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW DR: </b> Please wait a moment...");
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
			
				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val(); 
				var crefnoident = $(this).find('input[type="hidden"][name="txtcrefident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[type="hidden"][name="txtnprice"]').val();
				var ntransamt = $(this).find('input[type="hidden"][name="txtntranamount"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val();
				var norigqty = $(this).find('input[type="hidden"][name="hdnqtyorig"]').val(); 

				$.ajax ({
					url: "DR_newsavedet.php",
					data: { trancode: trancode, crefno: crefno, crefnoident:crefnoident, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, norigqty:norigqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, ntransamt:ntransamt },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  if(index>0){
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "DR_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
			  }
			});
			
			$("#MyTableInvSer > tbody > tr").each(function(index) {	


				var xcref = $(this).find('input[type="hidden"][name="sertabrefer"]').val(); 
				var crefidnt = $(this).find('input[type="hidden"][name="sertabident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="sertabitmcode"]').val();
				var cuom = $(this).find('input[type="hidden"][name="sertabuom"]').val();
				var nqty = $(this).find('input[type="hidden"][name="sertabqty"]').val();
				var dneed = $(this).find('input[type="hidden"][name="sertabesp"]').val();
				var clocas = $(this).find('input[type="hidden"][name="sertablocas"]').val();
				var seiraln = $(this).find('input[type="hidden"][name="sertabserial"]').val(); 
				var sertabremx = $(this).find('input[name="sertabremx"]').val();
				
				$.ajax ({
					url: "DR_newsavedetserials.php",
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, seiraln:seiraln, sertabremx:sertabremx },
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
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...<br><br>" + trancode);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}


	}

}
	
function trclickable(hsno,ccty,stt,ctry,zip){
	$('#txtchouseno').val(hsno);
	$('#txtcCity').val(ccty);
	$('#txtcState').val(stt);
	$('#txtcCountry').val(ctry);
	$('#txtcZip').val(zip);
	
	$("#MyAddModal").modal("hide");
}

</script>