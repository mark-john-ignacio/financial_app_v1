<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SO_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//echo $_SESSION['chkitmbal']."<br>";
//echo $_SESSION['chkcompvat'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

//echo $ddeldate;

/*
function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
	//$obj = json_decode($json, true);
  
	return $json;
}

*/

	$gettaxcd = mysqli_query($con,"SELECT * FROM `taxcode` where compcode='$company' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['ctaxcode'], 'ctaxdesc' => $row['ctaxdesc'], 'nrate' => $row['nrate']); 
		}
	}

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	$setSman = "True";
	$getSmans = mysqli_query($con,"SELECT * FROM `salesman` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($getSmans)==0) {
		$setSman = "False";
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>
    
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>
	<!--<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>-->

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
	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
	<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">


	<form action="SO_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;" enctype="multipart/form-data">
		
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-shopping-cart"></i>New Sales Order
				</div>
			</div>
			<div class="portlet-body">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#home">Order Details</a></li>
					<li><a href="#menu1">Delivered To</a></li>
					<li><a href="#attc">Attachments</a></li>	
				</ul>
 
				<div class="tab-content">  
					<!--Home Panel-->
						<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top: 10px">
									
							<table width="100%" border="0">
								<tr>
									<tH width="150">&nbsp;Customer:</tH>
									<td style="padding:2px">
										<div class="col-xs-12 nopadding">
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
									<tH width="150">Control No.:</tH>
									<td style="padding:2px;">
									<div class="col-xs-11 nopadding">
										<input type='text' class="form-control input-sm" id="txtcPONo" name="txtcPONo" value="" autocomplete="off" />
									</div>
									</td>
								</tr>
								<tr>
									<tH width="150">&nbsp;Currency:</tH>
									<td style="padding:2px">
										<div class="col-xs-6 nopadding">
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
															if ($nvaluecurrbase==$rows['currencyCode']) {
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

									<tH width="150">PO Date:</tH>
									<td style="padding:2px;">
										<div class="col-xs-11 nopadding">
											<input type='text' class="form-control input-sm" id="date_PO" name="date_PO" value="<?php echo $ddeldate; ?>" />
										</div>
									</td>
								</tr>
								<tr>
									<tH width="150">&nbsp;Remarks:</tH>
									<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div>
									</td>
									<tH width="150">Delivery Date:</tH>
									<td style="padding:2px;">
										<div class="col-xs-11 nopadding">
											<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $ddeldate; ?>" />
										</div>
									</td>	
										
								</tr>
								<tr>
									<tH rowspan="3" width="150">&nbsp;<!--Special Instructions:--></tH>
									<td rowspan="3" style="padding:2px">
										<div class="col-xs-11 nopadding">
											<!--<textarea rows="3"  class="form-control input-sm" name="txtSpecIns"  id="txtSpecIns"></textarea>-->
											<input type='hidden' name="txtSpecIns" value="" />
										</div>
									</td>
									<tH width="150">Sales Type:</th>
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
									<tH width="150"><?=($setSman=="True") ? " Salesman:" : ""?></tH>
									<td style="padding:2px">
										<?php if($setSman=="True"){ ?>
											<div class="col-xs-12 nopadding">
												<div class="col-xs-3 nopadding">
													<input type="text" id="txtsalesmanid" name="txtsalesmanid" class="form-control input-sm" placeholder="Salesman Code..." tabindex="1">
												</div>

												<div class="col-xs-8 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtsalesman" name="txtsalesman" width="20px" tabindex="1" placeholder="Search Salesman Name..."  size="60" autocomplete="off">
												</div> 
											</div>
										<?php
											}
										?>
									</td>
									<td>&nbsp;</td>
									<td style="padding:2px"  align="right" colspan="2">&nbsp;</td>		
								</tr>
							</table>		
							
						</div>
					
					<!--Delivery To Panel-->
						<div id="menu1" class="tab-pane fade" style="padding-left:5px; padding-left: 10px;">
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
											<td><button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnNewAdd" name="btnNewAdd">Select Address</button></td>
											<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  readonly="true" /></div></td>
										</tr>					
										<tr>
											<td>&nbsp;</td>
											<td colspan="2" style="padding:2px">
												<div class="col-xs-8 nopadding">
													<div class="col-xs-6 nopadding">
														<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off"  readonly="true" />
													</div>														
													<div class="col-xs-6 nopadwleft">
														<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off"   readonly="true" />
													</div>
												</div>
											</td>
										</tr> 
										<tr>
											<td>&nbsp;</td>
											<td colspan="2" style="padding:2px">
												<div class="col-xs-8 nopadding">
													<div class="col-xs-9 nopadding">
														<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" readonly="true" />
													</div>														
													<div class="col-xs-3 nopadwleft">
														<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off"  readonly="true" />
													</div>
												</div>
											</td>
										</tr> 
									</table>
						</div>
					
					<!-- Attachment Panel -->
						<div id="attc" class="tab-pane fade" style="padding-left:5px; padding-left: 10px;">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />
							
						</div>

				</div><!--tab-content-->


				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details
						</div>
						<div class="inputs">
							<div class="portlet-input input-inline">
								<div class="col-xs-12 nopadding">
									<input type="hidden" name="hdnqty" id="hdnqty">
									<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
									<input type="hidden" name="hdnunit" id="hdnunit">
									<input type="hidden" name="hdnvat" id="hdnvat">
									<input type="hidden" name="hdnmakebuy" id="hdnmakebuy">
											
									<div class="col-xs-4 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4"></div>
									<div class="col-xs-8 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>
								</div> 
							</div>	  
						</div>
					</div>
					<div class="portlet-body" style="overflow: auto">
						<div style="min-height: 30vh; width: 1500px;">
							<table id="MyTable" class="MyTable table-sm table-bordered" border="1">
								<thead>
									<tr>
										<th width="100px" style="border-bottom:1px solid #999">Code</th>
										<th width="300px" style="border-bottom:1px solid #999">Description</th>
										<th width="100px" style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
										<th width="150px" style="border-bottom:1px solid #999" class="chkVATClass">VAT</th>
										<th width="80px" style="border-bottom:1px solid #999">UOM</th>
										<th width="60px" style="border-bottom:1px solid #999">Factor</th>
										<th width="80px" style="border-bottom:1px solid #999">Qty</th>
										<th width="200px" style="border-bottom:1px solid #999">Price</th>
										<th width="200px" style="border-bottom:1px solid #999">Amount</th>
										<th width="200px" style="border-bottom:1px solid #999">PO NO.</th>
										<th width="120px" style="border-bottom:1px solid #999">Date Needed</th>
										<th width="200px" style="border-bottom:1px solid #999">Remarks</th>
										<!--<th style="border-bottom:1px solid #999">Total Amt in <?//php echo $nvaluecurrbase; ?></th>-->
										<th style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>	
								</thead>														
								<tbody class="tbody">
								</tbody>															
							</table>
						</div>
					</div>
				</div>

				<div class="row nopadwtop2x">
					<div class="col-xs-6">
						<?php
							$xc = check_credit_limit($company);
							if($xc==1){
						?>
						<div class="portlet blue-hoki box" id="creditport">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i>Credit Info
								</div>
								<div class="status" id="ncustbalance2">
									
								</div>
							</div>
							<div class="portlet-body">
								<div class="row static-info">
									<div class="col-md-3 name">
										 Credit Limit:
									</div>
									<div class="col-md-9 value">
										<div class="chklimit col-xs-10 nopadding" id="ncustlimit"></div>
										<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">
									</div>
								</div>
								<div class="row static-info">
									<div class="col-md-3 name">
										Balance:
									</div>
									<div class="col-md-9 value">
										<div class="chklimit col-xs-10 nopadding" id="ncustbalance"></div>
										<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
									</div>
								</div>
													
							</div>
						</div>
						<?php
							}
						?>
						<div class="portlet">
							<div class="portlet-body">
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SO.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>

								<button type="button" class="btn purple btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
									Quote<br>(Insert)
								</button>	
			
								<button type="submit" class="btn green btn-sm" tabindex="6"  id="btnSave" onClick="return chkform();" name="btnSave">
									SAVE<br> (CTRL+S)
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="well">							
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total NET Sales:
									<input type="hidden" id="txtnNetVAT" name="txtnNetVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnNetVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Add VAT:
									<input type="hidden" id="txtnVAT" name="txtnVAT" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnVAT">
									0.00
								</div>
							</div>
							<div class="row static-info align-reverse">
								<div class="col-xs-7 name">
									Total Amount:
									<input type="hidden" id="txtnGross" name="txtnGross" value="0">
									<input type="hidden" id="txtnBaseGross" name="txtnBaseGross" value="0">
								</div>
								<div class="col-xs-4 value" id="divtxtnGross">
									0.00
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
   
		<!-- Add Info -->
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
    		<div class="modal-dialog modal-full">
        		<div class="modal-content">
            		<div class="modal-header">
                		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               			<h3 class="modal-title" id="InvListHdr">PO List</h3>
            		</div>
            
            		<div class="modal-body" style="height:45vh">
            
       					<div class="col-xs-12 nopadding">
                			<div class="form-group">
								<div class="col-xs-4 pre-scrollable" style="height:42vh; border-right: 2px solid #ccc">
									<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
										<thead>
											<tr>
												<th nowrap>Quote No</th>
												<th nowrap>Quote Date</th>
												<th nowrap style='text-align: right'>Amount</th>
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
										<th style="text-align: center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
										<th>Item No</th>
										<th>Description</th>
										<th>UOM</th>
										<th>Qty</th>
										<th style='text-align: right'>Price</th>
										<th style='text-align: right'>Amount</th>
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

						<input type="hidden" name="hdncurr" id="hdncurr">
						<input type="hidden" name="hdncurrate" id="hdncurrate">

            		</div>
        		</div><!-- /.modal-content -->
    		</div><!-- /.modal-dialog -->
		</div>
		<!-- End FULL INVOICE LIST -->

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

	</form>


	<!-- Alert Modal -->
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



	<form method="post" name="frmedit" id="frmedit" action="SO_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" value="">
	</form>


</body>
</html>

<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">
	var xChkBal = "";
	var xChkLimit = "";
	var xChkLimitWarn = "";

	var xtoday = new Date();
	var xdd = xtoday.getDate();
	var xmm = xtoday.getMonth()+1; //January is 0!
	var xyyyy = xtoday.getFullYear();

	xtoday = xmm + '/' + xdd + '/' + xyyyy;

	$(document).keydown(function(e) {	
	
		if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
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
		else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
			if($('#hdnvalid').val()!="NO"){
				e.preventDefault();
				if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
					$('#txtprodnme').focus();
				}
			}
		}
	
	});

	$(document).ready(function(e) {

		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

		$(".nav-tabs a").click(function(){
				$(this).tab('show');
		});

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

		//$("#txtnBaseGross").autoNumeric('init',{mDec:2});
		//$("#txtnGross").autoNumeric('init',{mDec:2});

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
					xChkVatableStatus = item.chkcompvat;
				});
			}
		});

		if(xChkVatableStatus==1){
			$(".chkVATClass").show();	
		}
		else{
			$(".chkVATClass").hide();
		}

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

		$('#date_delivery, #date_PO').datetimepicker({
				format: 'MM/DD/YYYY',
				//minDate: new Date(),
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

						$("#selbasecurr").val(data[13]).change(); //val
						$("#basecurrvalmain").val($("#selbasecurr").data("val"));
									
						$('#hdnvalid').val("YES");
						
						$('#txtremarks').focus();
						
						if(xChkLimit==1){

							var limit = data[3];
							if(limit % 1 == 0){
								limit = parseInt(limit);
							}
							//alert(limit)
							limit = Number(limit).toLocaleString('en', { minimumFractionDigits: 4 });
							$('#ncustbalance2').html("");
							$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
							$('#hdncustlimit').val(data[3]);
							
							checkcustlimit($(this).val(), data[3]);
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

					$("#selbasecurr").val(item.cdefaultcurrency).change(); //val
					$("#basecurrvalmain").val($("#selbasecurr").data("val"));
								
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
				
					if(xChkLimit==1){
						
						var limit = item.nlimit;
						if(limit % 1 == 0){
							limit = parseInt(limit);
						}

						limit = Number(limit).toLocaleString('en', { minimumFractionDigits: 4 });
						$('#ncustbalance2').html("");				
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
					url:'../get_customerid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
						if(value!=""){				 
							var data = value.split(":");

							$('#txtdelcust').val(data[0]); 
							
							$('#txtchouseno').val(data[5]);
							$('#txtcCity').val(data[6]);
							$('#txtcState').val(data[7]);
							$('#txtcCountry').val(data[8]);
							$('#txtcZip').val(data[9]);
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
					url: "../th_customer.php",
					dataType: "json",
					data: {
						query: request
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
					
					$('#txtchouseno').val(item.chouseno);
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
					data: { query: $("#txtprodnme").val(), itmbal: xChkBal, styp: $("#selsityp").val() },
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.id+"<br>"+item.desc+'</span</div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#txtprodnme').val(item.desc).change(); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);
				$("#hdnvat").val(item.ctaxcode);
				$("#hdnmakebuy").val(item.makebuy);
				
				addItemName("","","","","","","");
				
				
			}
		
		});


		$("#txtprodid").keyup(function(event){

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
						$("#hdnvat").val(data[6]);
						$("#hdnmakebuy").val(data[10]);


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

				addItemName("","","","","","","");
				ComputeGross();	
				
			//   }
			//  else{
				
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
		});
		
		$("#btnNewAdd").on("click", function(){
			if($("#txtdelcustid").val()=="" || $("#txtdelcust").val()==""){
				alert("Select Delivery To Customer!");
			}else{
				$('#MyAddTble tbody').empty();
				//get addressses...
				$.ajax({
					url : "th_addresslist.php?id=" + $("#txtdelcustid").val() ,
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
		

	});

function checkcustlimit(id,xcred){
	//Check Credit Limit BALNCE here
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
		xBalance = Number(xBalance).toLocaleString('en', { minimumFractionDigits: 4 });
		$("#ncustbalance").html("<b><font size='+1'>"+xBalance+"</font></b>");
	}
	else{
		if(parseFloat(xcred) > 0){
		
			if(xChkLimitWarn==0) { //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
				$("#ncustbalance").html("<b><i><font color='red'>Max Limit Reached</font></i></b>");
			}
			else if(xChkLimitWarn==1) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='white' size='+1'>Delivery is blocked</font></i></b>");
			}
			else if(xChkLimitWarn==2) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='white' size='+1'>ORDERS BLOCKED</font></i></b>");
				$("#btnSave").attr("disabled", true);
				$("#btnIns").attr("disabled", true);
				$('#txtprodnme').attr("disabled", true);
				$('#txtprodid').attr("disabled", true);

			}

		}else{
			$("#ncustbalance").html("<b><i><font color='red'>Unlimited Credit Limit</font></i></b>");
		}
	}

}

function addItemName(qty,price,curramt,amt,factr,cref,nrefident){

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

//	 if(isItem=="NO"){	
	 	myFunctionadd(qty,price,curramt,amt,factr,cref,nrefident);
		
		ComputeGross();	

//	 }
//	 else{

//		addqty();	
			
	// }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		
	 }

}

function myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	var itmakebuy = $("#hdnmakebuy").val(); 
	
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = chkprice(itmcode,itmunit,itmccode,xtoday);
		var curramtz = price;
		//var amtz = price;
		var factz = 1;
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var curramtz = curramt;
		//var amtz = amtx;	
		var factz = factr;	
	}

	var baseprice = curramtz * parseFloat($("#basecurrval").val());

	
		if(xChkBal==1){
			var avail = "";
		}
		else{

			if($("#selsityp").val()=="Goods"){
				if(parseFloat(itmqty)>0){
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='"+itmqty+"'> " + itmqty + " " + itmqtyunit +" </td>";
					var qtystat = "";
				}else{
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> Unavailable </td>";
					var qtystat = "readonly";
					//itmtotqty = 0;
				}
			}else{
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> NA </td>";
					var qtystat = "";
					//itmtotqty = 0;
			}


		}
		
		/*
		var uomoptions = "";
								
		 $.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: itmcode },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.id==itmunit){
						isselctd = "selected";
					}
					else{
						isselctd = "";
					}
					
					uomoptions = uomoptions + '<option value='+item.id+' '+isselctd+'>'+item.name+'</option>';
				});
						
											 
			}
		});
		*/

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
	
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+nrefident+"' name=\"hdnrefident\" id=\"hdnrefident\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"></td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;

	var tditmvats = "";
		if(xChkVatableStatus==1){ 
			
				var xz = $("#hdntaxcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if($("#hdnvat").val()==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
				});

			tditmvats = "<td width=\"100\" nowrap> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";

		}

	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmqtyunit+"'>"+uomoptions+"</select> </td>";

	isfactoread = "";
	if(itmqtyunit==itmunit){
		isfactoread = "readonly";
	}

	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+factz+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";

	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+" data-v-min=\"1\"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> </td>";
		
	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric2 form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' \"  "+qtystat+" > </td>";

	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> <input type='hidden' value='"+baseprice.toFixed(4)+"' name=\"txtnamount\" id='txtnamount"+lastRow+"'> </td>";
			
	//var tditmamount = "<td width=\"100\" nowrap>  </td>"; tditmamount
	
	// &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> 
	var tditmrempo = "<td><input type='text' value='' class='form-control input-xs' name=\"txtcitmrempo\" id='txtcitmrempo"+lastRow+"'></td>";

	var tddneed = "<td width=\"100\" style=\"padding: 1px; position:relative;\" nowrap><input type='text' value='' class='form-control input-xs' name=\"txtcitmdneed\" id='txtcitmdneed"+lastRow+"'></td>";
	var tditmremx = "<td><input type='text' value='"+itmakebuy+"' class='form-control input-xs' name=\"txtcitmremx\" id='txtcitmremx"+lastRow+"'></td>";


	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /></td>";


	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmavail + tditmvats + tditmunit + tditmfactor + tditmqty + tditmprice + tditmbaseamount + tditmrempo + tddneed + tditmremx + tditmdel + '</tr>');

									$("#del"+lastRow).on('click', function() {
										$(this).closest('tr').remove();

										Reindex();
										ComputeGross();
									});

									$("input.numeric2").autoNumeric('init',{mDec:4});
									$("input.numeric").autoNumeric('init',{mDec:2});

									$("#selitmvatyp"+lastRow).on("change", function() {
										ComputeGross();
									});

									//$("input.numeric").numeric(
									//	{negative: false}
									//);

								//	$("input.numericdec").numeric(
									//	{
								//			negative: false,
								//			decimalPlaces: 4
								//		}
								//	);

									$("input.numeric, input.numeric2").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric, input.numeric2").on("keyup", function () {
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
									
									$('#txtcitmdneed'+lastRow).datetimepicker({
										format: 'MM/DD/YYYY',
										useCurrent: false,
										//minDate: moment().format('L'),
										defaultDate: moment().format('L'),
										widgetPositioning: {
												horizontal: 'right',
												vertical: 'bottom'
										}
									});
									
																		
}

	function Reindex(){
		$("#MyTable > tbody > tr").each(function(index) {	
			tx = index + 1;

			$(this).find('select[name="seluom"]').attr("id","seluom"+tx);
			$(this).find('input[name="txtnqty"]').attr("id","txtnqty"+tx);
			$(this).find('input[name="txtnprice"]').attr("id","txtnprice"+tx);
			$(this).find('input[type="hidden"][name="txtnamount"]').attr("id","txtnamount"+tx);
			$(this).find('input[name="txtntranamount"]').attr("id","txtntranamount"+tx);
			$(this).find('input[type="hidden"][name="hdnmainuom"]').attr("id","hdnmainuom"+tx);
			$(this).find('input[name="hdnfactor"]').attr("id","hdnfactor"+tx); 

			if(xChkVatableStatus==1){ 
				$(this).find('select[name="selitmvatyp"]').attr("id","selitmvatyp"+tx); 
			}

			$(this).find('input[name="txtcitmrempo"]').attr("id","txtcitmrempo"+tx);
			$(this).find('input[name="txtcitmdneed"]').attr("id","txtcitmdneed"+tx);
			$(this).find('input[name="txtcitmremx"]').attr("id","txtcitmremx"+tx);

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
		
		namt = nqty * nprc;
		namt = namt.toFixed(4);

		namt2 = namt * parseFloat($("#basecurrval").val());
		namt2 = namt2.toFixed(4);

		
		$("#txtntranamount"+r).val(namt);		

		$("#txtnamount"+r).val(namt2);

		$("#txtntranamount"+r).autoNumeric('destroy');
		//$("#txtnamount"+r).autoNumeric('destroy');

		$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
		//$("#txtnamount"+r).autoNumeric('init',{mDec:2});


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
		
				if(xChkVatableStatus==1){  
					var slctdval = $("#selitmvatyp"+i+" option:selected").data('id');

					if(slctdval!=0){
						if(parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) > 0 ){

							nnet = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
							vatz = nnet * (parseInt(slctdval)/100);

							nnetTot = nnetTot + nnet;
							vatzTot = vatzTot + vatz;
						}
					}else{
						nnetTot = nnetTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
					}
				}else{

					nnetTot = nnetTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));

				}

				gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
			}
		}

		gross2 = gross * parseFloat($("#basecurrval").val().replace(/,/g,''));

		$("#txtnNetVAT").val(nnetTot);
		$("#txtnVAT").val(vatzTot);
		$("#txtnGross").val(gross2);
		$("#txtnBaseGross").val(gross);		

		$("#divtxtnNetVAT").text(nnetTot.toFixed(2));
		$("#divtxtnVAT").text(vatzTot.toFixed(2));
		$("#divtxtnGross").text(gross.toFixed(2));

		$("#divtxtnNetVAT").formatNumber();
		$("#divtxtnVAT").formatNumber();
		$("#divtxtnGross").formatNumber();
		
	}



/*function addqty(){

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
			$(this).find("input[name='txtntranamount']").val(TotAmt.toFixed(4)); 

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});


			namt2 = TotAmt * parseFloat($("#basecurrval").val());
			$(this).find("input[name='txtnamount']").val(namt2.toFixed(4)); 

			$("#txtnamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});
		}

	});
	
	ComputeGross();

}*/


/*function viewhidden(itmcde,itmnme){
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
}*/


function chkprice(itmcode,itmunit,ccode,datez){
	var result;
		// alert("?itm="+itmcode+"&cust="+ccode+"&cunit="+itmunit+"&dte="+datez)	
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

			$('#MyInvTbl').DataTable().destroy();
			
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
			$('#InvListHdr').html("Quote List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			$.ajax({
          		url: 'th_qolist.php',
				data: 'x='+x+ "&selsi=" + $("#selsityp").val(),
				dataType: 'json',
				method: 'post',
				success: function (data) {
            		// var classRoomsTable = $('#mytable tbody');
					$("#allbox").prop('checked', false);
					   
           			console.log(data);
          			$.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
							$("#AlertMsg").html("No Quotations Available");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

							xstat = "NO";
								
							$("#txtcustid").attr("readonly", false);
							$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
								$("<td id='td"+item.cpono+"' data-curr='"+item.ccurrencycode+"' data-rate='"+item.nexchangerate+"'>").text(item.cpono),
								$("<td>").text(item.dcutdate),
								$("<td align='right'>").text(item.ngross)
							).appendTo("#MyInvTbl tbody");
								
								
							$("#td"+item.cpono).on("click", function(){
								checkcurrency($(this).text(),$(this).data("curr"),$(this).data("rate"));
								//	opengetdet($(this).text());
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
							searchPlaceholder: "Search Quotation "
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

function checkcurrency(tranno,currcode,currrate){
	var cnttbl = $('#MyTable tr').length - 1;

	if(cnttbl>0){
		//check if same ng currency
		if(currcode!=$("#selbasecurr").val()){
			var xyz = confirm("Currency of the selected reference is different from the previous reference selected.\nIf you continue, total amount in "+$("#basecurrvalmain").val()+" will be computed base from the current currency selected.");

			if(xyz==true){
				//$("#selbasecurr").val(currcode).change();
				//$("#basecurrval").val(currrate);
				opengetdet(tranno);
			}
		}else{
			opengetdet(tranno);
		}
	}else{
		$("#hdncurr").val(currcode);
		$("#hdncurrate").val(currrate);
		opengetdet(tranno);
	}
}

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("Quote List: " + $('#txtcust').val() + " | Quote Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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
							  
			salesnos = salesnos +  $(this).find('input[type="hidden"][name="hdnrefident"]').val();
		}
				
	});

	//alert('th_sinumdet.php?x='+drno+"&y="+salesnos+"&itmbal="+xChkBal);
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
								$("<td align='center'>").html("<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\">"),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty),
								$("<td align='right'>").text(item.nprice),
								$("<td align='right'>").text(item.namount)
							).appendTo("#MyInvDetList tbody");
						}
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

	//checkcurrency muna
	//$("#selbasecurr").val(currcode).change();
	//$("#basecurrval").val(currrate);
	if($("#hdncurr").val()!=""){
		$("#selbasecurr").val($("#hdncurr").val()).change();
		$("#basecurrval").val($("#hdncurrate").val());
	}

	
   	$("input[name='chkSales[]']:checked").each( function () {	   
				
		var tranno = $(this).data("id");
	   	var id = $(this).val();

	   	$.ajax({
			url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal ,
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
					$("#hdnvat").val(item.ctaxcode);
					$("#hdnmakebuy").val(item.makebuy);

					//alert(item.cqtyunit + ":" + item.cunit);
					//myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident)
					addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.nident)
											   
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
	
	if((document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value=="") || (document.getElementById("txtdelcust").value=="" && document.getElementById("txtdelcustid").value=="")){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Customer Required/Delivered To Customer!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	// ACTIVATE MUNA LAHAT NG INFO	
	/*$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});*/

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
			
			myprice = $(this).find('input[type="hidden"][name="txtnamount"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}else{
				var myqtytots = parseFloat(myqty) * parseFloat(myfacx);

				if($("#selsityp").val()=="Goods"){
					if(parseFloat(myav) < parseFloat(myqtytots)){
						msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
					}
				}
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

	// Check if Credit Limit activated (kung sobra)
	//alert(xChkLimit +" : "+ parseFloat($('#hdncustlimit').val()));
	if(xChkLimit==1 && parseFloat($('#hdncustlimit').val()) > 0){
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
		var dpodate = $("#date_PO").val();
		var ngross = $("#txtnGross").val();
		var csitype = $("#selsityp").val(); 
		var custpono = $("#txtcPONo").val();

		var ncurrcode = $("#selbasecurr").val();
		var ncurrdesc = $("#selbasecurr option:selected").text();
		var ncurrrate = $("#basecurrval").val();
		var nbasegross = $("#txtnBaseGross").val();

		$("#hidcurrvaldesc").val($("#selbasecurr option:selected").text());

		var specins = $("#txtSpecIns").val();
		var salesman = $("#txtsalesmanid").val();
		var delcodes = $("#txtdelcustid").val();
		var delhousno = $("#txtchouseno").val();
		var delcity = $("#txtcCity").val();
		var delstate = $("#txtcState").val();
		var delcountry = $("#txtcCountry").val();
		var delzip = $("#txtcZip").val();
		
		//alert("SO_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, selsityp: csitype, custpono:custpono, salesman:salesman, delcodes:delcodes, delhousno:delhousno, delcity:delcity, delstate:delstate, delcountry:delcountry, delzip:delzip, specins:specins, ncurrcode:ncurrcode, ncurrdesc:ncurrdesc, ncurrrate:ncurrrate, nbasegross:nbasegross },  frmpos

		//var myform = $("#frmpos").serialize();
		var formdata = new FormData($("#frmpos")[0]);
		/**
		 * @property JQuery formulate every file to compose to formdata 
		 * @property formdata.delete('#upload') delete an upload key without values
		 */
		formdata.delete('upload[]');
		jQuery.each(jQuery('#file-0')[0].files, function(i, file) {
			formdata.append('file-'+i, file);
		});

		console.log(formdata);

		$.ajax ({
			url: "SO_newsavehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW ORDER: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				console.log(data);
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			},
			error: function (request, error) {
        console.log(arguments);
        alert(" Can't do because: " + error);
    	}
		});
		
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	

				var nrefident = $(this).find('input[type="hidden"][name="hdnrefident"]').val();
				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val();

				if(xChkVatableStatus==1){ 
					var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
					var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id');
				}else{
					var vatcode = "";
					var nrate = 0;
				}

				var citmrempo = $(this).find('input[name="txtcitmrempo"]').val();
				var citmremdneed = $(this).find('input[name="txtcitmdneed"]').val();
				var citmremx = $(this).find('input[name="txtcitmremx"]').val();
				
				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					nbaseamt = nbaseamt.replace(/,/g,'');
				}

				//alert("SO_newsavedet.php?nrefident="+nrefident+"&trancode="+trancode+"&crefno="+crefno+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&namt="+namt+"&nbaseamt="+nbaseamt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&vatcode="+vatcode+"&nrate="+nrate+"&citmrempo="+citmrempo+"&citmremdneed="+citmremdneed+"&citmremx="+citmremx);
			
				$.ajax ({
					url: "SO_newsavedet.php",
					data: { nrefident: nrefident, trancode: trancode, crefno: crefno, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, nbaseamt:nbaseamt, mainunit:mainunit, nfactor:nfactor, vatcode:vatcode, nrate:nrate, citmrempo:citmrempo, citmremdneed:citmremdneed, citmremx:citmremx },
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
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "SO_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
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

function convertCurrency(fromCurrency) {
  
  toCurrency = $("#basecurrvalmain").val(); //statgetrate

   $.ajax ({
	 url: "../th_convertcurr.php",
	 data: { tocurr: fromCurrency, fromcurr: toCurrency },
	 async: false,
	 beforeSend: function () {
		 $("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
	 },
	 success: function( data ) {

		 $("#basecurrval").val(data);
		 
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

</script>