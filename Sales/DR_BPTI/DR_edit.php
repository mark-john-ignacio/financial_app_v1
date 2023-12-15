<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "DR.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');
	require_once ('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DR_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	if(isset($_REQUEST['txtctranno'])){
		$txtctranno = $_REQUEST['txtctranno'];
	}
	else{
		$txtctranno = $_REQUEST['txtcsalesno'];
	}
		
	$company = $_SESSION['companyid'];

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit, c.cname as cdelname, d.cname as csalesmaname from dr a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid left join salesman d on a.compcode=d.compcode and a.csalesman=d.ccode where a.ctranno = '$txtctranno' and a.compcode='$company'");

	@$arrname = array();
	$directory = "../../Components/assets/DR/{$company}_{$txtctranno}";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
	} 

	//APCDR
	$sqlapcdr = mysqli_query($con,"select * from dr_apc_t where compcode='$company' and ctranno = '$txtctranno'");
	$rowapc = mysqli_fetch_row($sqlapcdr);

	@$arrempslist = array();
	$getempz = mysqli_query($con,"SELECT nid, cdesc, csign FROM `mrp_operators` where compcode='$company' and cstatus='ACTIVE' order By cdesc"); 
	if (mysqli_num_rows($getempz)!=0) {
		while($row = mysqli_fetch_array($getempz, MYSQLI_ASSOC)){
			@$arrempslist[] = array('nid' => $row['nid'], 'cdesc' => $row['cdesc'], 'csign' => $row['csign']); 
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>
	<link href="../../global/css/plugins.css" rel="stylesheet" type="text/css"/>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
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

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus(); ">
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

<?php


if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$Gross = $row['ngross'];
		$cDRPrintNo = $row['cdrprintno'];
		$cpricever = $row['cpricever'];
		$nlimit = $row['nlimit'];

		$cDRAPCOrdNo = $row['crefapcord'];
		$cDRAPCDRNo = $row['crefapcdr']; 

		$cSign1 = $row['csign1'];
		$cSign2 = $row['csign2']; 
		
		$salesman = $row['csalesman'];
		$salesmaname = $row['csalesmaname'];
		$delcode = $row['cdelcode'];
		$delname = $row['cdelname'];
		$delhousno = $row['cdeladdno'];
		$delcity = $row['cdeladdcity'];
		$delstate = $row['cdeladdstate'];
		$delcountry = $row['cdeladdcountry'];
		$delzip = $row['cdeladdzip'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];
	}
	
	
	//if(!file_exists("../../imgcust/".$CustCode .".jpg")){
	//	$imgsrc = "../../images/blueX.png";
	//}
	//else{
	//	$imgsrc = "../../imgcust/".$CustCode .".jpg";
	//}

?>
	<form action="DR_edit.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post">
		
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-shopping-cart"></i>Delivery Receipt Details
				</div>
				<div class="status">
					<?php
						if($lCancelled==1){
							echo "<font color='#FF0000'><b>CANCELLED</b></font>";
						}
						
						if($lPosted==1){
							if($lVoid==1){
								echo "<font color='#FF0000'><b>VOIDED</b></font>";
							}else{
								echo "<font color='#FF0000'><b>POSTED</b></font>";
							}
						}
					?>
				</div>
			</div>
			<div class="portlet-body">
		
					<ul class="nav nav-tabs">
						<li class="active"><a href="#home">Order Details</a></li>
						<li><a href="#menu1">Delivered To</a></li>
						<li><a href="#menu2">APC DR</a></li>
						<li><a href="#attc">Attachments</a></li>
					</ul>
			
					<div class="tab-content" style="margin-bottom: 10px">
					
						<div id="home" class="tab-pane fade in active" style="padding-left:5px;">			 
							<table width="100%" border="0">
								<tr>
									<th>&nbsp;TRANS NO.:</th>
									<td style="padding:2px">
										<div class="col-xs-3 nopadding">
											<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
										</div>
											<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
											<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
											<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
										&nbsp;&nbsp;
											<div id="statmsgz" style="display:inline"></div>
									</td>
									<tH style="padding:2px">&nbsp;</tH>
									<td style="padding:2px">&nbsp;</td>
								</tr>
								<tr>
									<th width="100">&nbsp;Customer:</th>
									<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $CustCode; ?>">
											<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
											<input type="hidden" id="hdnpricever" name="hdnpricever" value="<?php echo $cpricever;?>">
										</div>
										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" value="<?php echo $CustName; ?>">
										</div> 
										</div>
									</td>
									<th style="padding:2px">Order No.:</th>   
									<td style="padding:2px" align="center">
										<div class="col-xs-10 nopadding"> 
												<input type='text' class="form-control input-sm" id="cdrapcord" name="cdrapcord" value="<?php echo $cDRAPCOrdNo;?>" autocomplete="off" />
										</div>
									</td>
								</tr>
								<tr>
									<th width="100">&nbsp;Remarks:</th>
									<td style="padding:2px" rowspan="3">
										<div class="col-xs-11 nopadding">
											<textarea class="form-control input-sm" id="txtremarks" name="txtremarks" rows="4"><?php echo $Remarks; ?></textarea>

											<input type="hidden" id="txtsalesmanid" name="txtsalesmanid" value="<?php echo $salesman; ?>">
											<input type="hidden" id="txtsalesman" name="txtsalesman" value="<?php echo $salesmaname; ?>">
										</div>
									</td>
									<th style="padding:2px">DR Reference.:</th>
									<td style="padding:2px" align="center">
										<div class="col-xs-10 nopadding"> 
												<input type='text' class="form-control input-sm" id="cdrapcdr" name="cdrapcdr" value="<?php echo $cDRAPCDRNo;?>" autocomplete="off" />
										</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<th style="padding:2px">DR Series No.:</th>
									<td style="padding:2px" align="center">
										<div class="col-xs-10 nopadding"> 
												<input type='text' class="form-control input-sm" id="cdrprintno" name="cdrprintno" value="<?php echo $cDRPrintNo;?>" autocomplete="off" />
										</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<th width="150" style="padding:2px">Delivery Date:</th>
									<td style="padding:2px;">
									<div class="col-xs-10 nopadding">
										<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($Date),'m/d/Y'); ?>" />
									</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>
										<div class="col-xs-4 nopadwleft">
											<select class='xsel2 form-control input-sm' id="selSign1" name="selSign1">
												<option value="" <?=($cSign1=="") ? "selected" : ""?>></option>
												<?php
													foreach(@$arrempslist as $rsx){
														$slcted = ($cSign1==$rsx['nid']) ? "selected" : "";
														echo "<option value='".$rsx['nid']."' ".$slcted."> ".$rsx['cdesc']." </option>";
													}
												?>
											</select>
										</div>
										<div class="col-xs-4 nopadwleft"> 
											<select class='xsel2 form-control input-sm' id="selSign2" name="selSign2">
												<option value="" <?=($cSign2=="") ? "selected" : ""?>></option>
												<?php
													foreach(@$arrempslist as $rsx){
														$slcted = ($cSign2==$rsx['nid']) ? "selected" : "";
														echo "<option value='".$rsx['nid']."' ".$slcted."> ".$rsx['cdesc']." </option>";
													}
												?>
											</select>
										</div>
										<div class="col-xs-3 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtsoref" name="txtsoref" width="20px" tabindex="6" placeholder="Reference SO">
										</div>		 
									</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
							</table>
							
						</div>
			
						<div id="menu1" class="tab-pane fade" style="padding-left:5px">
							<table width="100%" border="0">
								<tr>
									<td width="150"><b>Customer</b></td>
									<td width="310" colspan="2" style="padding:2px">
										<div class="col-xs-8 nopadding">
											<div class="col-xs-3 nopadding">
												<input type="text" id="txtdelcustid" name="txtdelcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $delcode; ?>">
											</div>

											<div class="col-xs-9 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off" value="<?php echo $delname; ?>">
											</div> 
										</div>

									</td>
								</tr>
								<tr>
								<td><button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnNewAdd" name="btnNewAdd">
								Select Address</button></td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  readonly="true" value="<?php echo $delhousno; ?>" /></div></td>
								</tr>

								<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
													<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off"  readonly="true"  value="<?php echo $delcity; ?>"/>
												</div>

												<div class="col-xs-6 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off"   readonly="true"  value="<?php echo $delstate; ?>"/>
												</div></div></td>
								</tr>

								<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-9 nopadding">
													<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" readonly="true" value="<?php echo $delcountry; ?>"/>
												</div>

												<div class="col-xs-3 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off"  readonly="true" value="<?php echo $delzip; ?>"/>
												</div></div></td>
								</tr>
							</table>
						</div>
						<!--
						-- APC DR Fields
						-->
						<div id="menu2" class="tab-pane fade" style="padding-left:10px; padding-top:10px; padding-right:10px;">

							<table width="100%" border="0">

								<tr>
									<th width="80px"> Pull Req # </th>
									<td colspan="5"> 
										<div class="row nopadding">
											<div class="col-xs-3 nopadding">										
												<input type="text" maxlength="50" id="txtpullrqs" name="txtpullrqs" class="form-control input-sm" placeholder="As Per Advice..." tabindex="1" value="<?=$rowapc[2]?>">
											</div>
											<div class="col-xs-9 nopadwleft">
												<input type="text" maxlength="100" id="txtpullrmrks" name="txtpullrmrks" class="form-control input-sm" placeholder="Remarks" tabindex="1" value="<?=$rowapc[3]?>">										
											</div>
										</div>
									</td>
									<th width="80px"> &nbsp;REV #</th>
									<td> <input type="text" maxlength="50" id="txtRevNo" name="txtRevNo" class="form-control input-sm" placeholder="REV #..." tabindex="1" value="<?=$rowapc[4]?>"> </td>
								</tr>

								<tr>
									<th style="padding-top: 5px" width="80px"> Sales Rep </th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="txtSalesRep" name="txtSalesRep" class="form-control input-sm" placeholder="Sales Rep..." tabindex="1" value="<?=$rowapc[5]?>"> </td>
									<th style="padding-top: 5px" width="80px">&nbsp;Truck No. </th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="txtTruckNo" name="txtTruckNo" class="form-control input-sm" placeholder="Truck No..." tabindex="1" value="<?=$rowapc[6]?>"> </td>
									<th style="padding-top: 5px" width="80px"> &nbsp;Del Sched</th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="txtDelSch" name="txtDelSch" class="form-control input-sm" placeholder="Delivery Sched..." tabindex="1" value="<?=$rowapc[7]?>"> </td>
									<th style="padding-top: 5px" width="80px"> &nbsp;Others</th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="txtRevOthers" name="txtRevOthers" class="form-control input-sm" placeholder="Others..." tabindex="1" value="<?=$rowapc[8]?>"> </td>
								</tr>

								<tr>
									<th style="padding-top: 5px" width="80px"> Certified </th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="DRfootCert" name="DRfootCert" class="form-control input-sm" placeholder="Certified By (QA)..." tabindex="1" value="<?=$rowapc[9]?>"> </td>
									<th style="padding-top: 5px" width="80px">&nbsp;Issued </th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="DRfootIssu" name="DRfootIssu" class="form-control input-sm" placeholder="Issued By..." tabindex="1" value="<?=$rowapc[10]?>"> </td>
									<th style="padding-top: 5px" width="80px"> &nbsp;Checked</th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="DRfootChec" name="DRfootChec" class="form-control input-sm" placeholder="Checked By..." tabindex="1" value="<?=$rowapc[11]?>"> </td>
									<th style="padding-top: 5px" width="80px"> &nbsp;Approved</th>
									<td style="padding-top: 5px"> <input type="text" maxlength="50" id="DRfootAppr" name="DRfootAppr" class="form-control input-sm" placeholder="Approved By..." tabindex="1" value="<?=$rowapc[12]?>"> </td>
								</tr>
							</table>

							<br><br><br><br><br>
						</div>
						
						<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px">
							
							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />

						</div>

					</div>


					<div class="portlet light bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-cogs"></i>Details
							</div>						
						</div>
						<div class="portlet-body" style="overflow: auto">
							<div style="min-height: 30vh;">
							
								<ul class="nav nav-tabs">
									<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Items List</a></li>
									<li id="liacct"><a href="#2Acct" data-toggle="tab">Items Inventory</a></li>
								</ul>

								<div class="tab-content nopadwtop2x">
									<div class="tab-pane active" id="1Det">

										<input type="hidden" name="hdnqty" id="hdnqty">
										<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
										<input type="hidden" name="hdnunit" id="hdnunit">
										<input type="hidden" id="txtprodid" name="txtprodid">
										<input type="hidden" id="txtprodnme" name="txtprodnme">
										
										<table id="MyTable" class="MyTable table table-condensed" width="100%">
											<thead>
												<tr>
													<th style="border-bottom:1px solid #999">&nbsp;</th>
													<th style="border-bottom:1px solid #999">APC Item No.</th>
													<th style="border-bottom:1px solid #999">PO No.</th>
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

									<div class="tab-pane" id="2Acct">
											
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
						</div>

					</div>


					<?php
						if($poststat == "True"){
					?>
						<br>
						<table width="100%" border="0" cellpadding="3">
							<tr>
								<td>
									<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
			
									<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='DR.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
										Back to Main<br>(ESC)
									</button>   
									<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='DR_new.php';" id="btnNew" name="btnNew">
										New<br>(F1)
									</button>
									<button type="button" class="btn purple btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
										SO<br>(Insert)
									</button>
									<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
										Undo Edit<br>(CTRL+Z)
									</button>

									<?php
										$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'DR_print'");

										if(mysqli_num_rows($sql) == 1){
										
									?>

										<div class="dropdown" style="display:inline-block !important;">
											<button type="button" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle"  id="btnPrint" name="btnPrint">
												Print<br>(CTRL+P) <span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												<li><a href="javascript:;" onClick="printchk('<?php echo $txtctranno;?>','REGDR');">Regular DR</a></li>
												<li><a href="javascript:;" onClick="printchk('<?php echo $txtctranno;?>','APCDR');">APC DR</a></li>
											</ul>
										</div>

									<?php		
										}
									?>
											
									<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
										Edit<br>(CTRL+E)
									</button>
				
									<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
										Save<br>(CTRL+S)
									</button>
					
								</td>
								<td align="right">
									<!--<b>TOTAL AMOUNT : <input type="text" id="txtnGross" name="txtnGross" readonly value="<?php //echo $Gross; ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></b>-->
									<input type="hidden" id="txtnGross" name="txtnGross" value="<?php echo $Gross; ?>">
								</td>
							</tr>
						</table>

						<br><br><br><br><br><br><br>
					<?php
						}
					?>

			</div>
		</div>
    
   
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
										<th nowrap>SO No</th>
										<th nowrap>Control No</th>
										<th nowrap>Del Date</th>
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

<?php
}
else{
?>
<form action="DR_edit.php" name="frmpos2" id="frmpos2">
  <fieldset>
   	<legend>Delivery Receipt</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">TRANS NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>DR No. DID NOT EXIST!</b></font></tH>
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

<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
              	 <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:98%; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

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

var file_name = <?= json_encode(@$arrname) ?>;
/**
 * Checking of list files
 */
if(file_name.length != 0){
	file_name.map(({name, ext}) => {
		//console.log("Name: " + name + " ext: " + ext)
	})

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var list_file = [];
	var file_config = [];
	var extender;
	/**
	 * setting up an list of file and config of a file
	 */
	file_name.map(({name, ext}, i) => {
		list_file.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/DR/<?=$company."_".$txtctranno?>/" + name)
		//console.log(ext);

		if(jQuery.inArray(ext, arroffice) !== -1){
			extender = "office";
		} else if (jQuery.inArray(ext, arrimg) !== -1){
			extender = "image";
		} else if (ext == "txt"){
			extender = "text";
		} else {
			extender =  ext;
		}

		//console.log(extender)
		file_config.push({
			type : extender, 
			caption : name,
			width : "120px",
			url: "th_filedelete.php?id="+name+"&code=<?=$txtctranno?>", 
			key: i + 1
		});
	})
}



	<?php
		if($poststat == "True"){
	?>
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='DR_new.php';
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
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $txtctranno;?>','REGDR');
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
			window.location.href='DR.php';
		}
	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $("#btnIns").is(":disabled")==false){
			openinv();
			}
	  }
		else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
			if($('#SerialMod').hasClass('in')==true){
		 		$("#btnClsSer").click();
			}
	  } 
	});
	<?php
		}
	?>

	$(document).keypress(function(e) {
	  if ($("#SerialMod").hasClass('in') && (e.keycode == 13 || e.which == 13)) {
	    $("#btnInsSer").click();
	  }
	});
	
	$(document).ready(function(e) {
			$(".nav-tabs a").click(function(){
    			$(this).tab('show');
			});

			$("#allbox").click(function(){
				$('input:checkbox').not(this).prop('checked', this.checked);
			});

			if(file_name.length > 0){
				$('#file-0').fileinput({
					showUpload: false,
					showClose: false,
					allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
					overwriteInitial: false,
					maxFileSize:100000,
					maxFileCount: 5,
					browseOnZoneClick: true,
					fileActionSettings: { showUpload: false, showDrag: false, },
					initialPreview: list_file,
					initialPreviewAsData: true,
					initialPreviewFileType: 'image',
					initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/RFP_Files/<?=$company."_".$txtctranno?>/{filename}',
					initialPreviewConfig: file_config
				});
			} else {
				$("#file-0").fileinput({
					showUpload: false,
					showClose: false,
					allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
					overwriteInitial: false,
					maxFileSize:100000,
					maxFileCount: 5,
					browseOnZoneClick: true,
					fileActionSettings: { showUpload: false, showDrag: false, }
				});
			}
		
	   			$.ajax({
					url : "../../include/th_xtrasessions.php",
					type: "Post",
					async:false,
					dataType: "json",
					success: function(data)
					{	
					   //console.log(data);
                       $.each(data,function(index,item){
						   xChkBal = item.chkinv; //0 = Check ; 1 = Dont Check
						  // xChkLimit = item.chkcustlmt; //0 = Disable ; 1 = Enable
						  // xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
						   
						   xChkLimit = 0;
						   xChkLimitWarn = 0;
						   
					   });
					}
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

		$("#selSign1").select2({
			placeholder: "Prepared By...",
			allowClear: true
		});

		$("#selSign2").select2({
			placeholder: "Checked By...",
			allowClear: true
		});

		loaddetails();
		loaddetinfo();
		loadserials();

	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);
	  
	disabled();
		
    });


$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
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

				if(xChkLimit==1){

					var limit = data[1];
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}
					
					limit = Number(limit).toLocaleString('en');
					
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(data[1]);
					
					checkcustlimit($(this).val(), data[1]);
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
			
			addItemName("","","","","","","","","","");
			
			
		}
	
	});


	$("#txtprodid").keypress(function(event){
		if(event.keyCode == 13){

		$.ajax({
        url:'../get_productid.php',
        data: 'c_id='+ $(this).val() + "&itmbal=" + xChkBal+"&styp=Goods",                 
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

			myFunctionadd("","","","","","");
			ComputeGross();	
			
	    //}
	    //else{
			
			//addqty();
	//}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if ebter is clicked
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
							InsertToSerials(zxitmcode,zxserial,zxuom,zxqty,zxloca,zxlocadesc,zxexpd,zxnident,zxreference,zxmainident,'');			
						}

					});
			}
		
			//close modal
			$("#SerialMod").modal("hide");
	});
	
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
					//console.log(data);
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
					url : "th_qolistputall.php?id=" + $(this).val() + "&itmbal=" + xChkBal,
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	
					  // console.log(data);
					   $.each(data,function(index,item){

						$('#txtprodnme').val(item.desc); 
						$('#txtprodid').val(item.id); 
						$("#hdnunit").val(item.cunit); 
						$("#hdnqty").val(item.nqty);
						$("#hdnqtyunit").val(item.cqtyunit);
						//alert(item.cqtyunit + ":" + item.cunit);
						addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident,item.xcskucode,item.xcpono)

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
					//console.log(data);
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
											
				//console.log(data);
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

function addItemName(qty,qtyorig,price,curramt,amt,factr,cref,crefident,itmsku,itmpono){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				if($("#txtprodid").val()==disID){
					
					isItem = "YES";

				}
			});	

	// if(isItem=="NO"){
	 	myFunctionadd(qty,qtyorig,price,curramt,amt,factr,cref,crefident,itmsku,itmpono);
		
		ComputeGross();	

	// }
	// else{
//
	//	addqty();	
			
	// }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		
	 }

}

function myFunctionadd(qty,nqtyorig,pricex,curramt,amtx,factr,cref,crefident,itmsku,itmpono){
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
		var price = chkprice(itmcode,itmunit,itmccode,xtoday);
		var amtz = price;
		var factz = 1;
	}
	else{
		
		var itmtotqty = qty
		var itmorgqty = nqtyorig;
		var price = pricex;
		var amtz = amtx;	
		var factz = factr;	
	}
	
	//alert(itmcode+","+itmunit+","+itmccode+","+xtoday);
		
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

	if(cref==null){
		cref = ""
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var insbtn = "<td width=\"50\"> <input class='btn btn-info btn-xs' type='button' name='ins' id='ins" + lastRow + "' value='insert' /></td>";	

	var tdapcitmno = "<td width=\"130\" nowrap> <input type='text' value='"+itmsku+"' class='form-control input-xs' name='txtapcitmno' id='txtapcitmno"+lastRow+"'> </td>"; 

	var tditmpono = "<td width=\"130\" nowrap> <input type='text' value='"+itmpono+"' class='form-control input-xs' name='txtapono' id='txtapono"+lastRow+"'> </td>"; 

	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode" + lastRow + "\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference" + lastRow + "\"><input type='hidden' value='"+crefident+"' name=\"txtcrefident\" id=\"txtcrefident" + lastRow + "\"></td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;
	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select> </td>";

	isfactoread = "";
	if(itmqtyunit==itmunit){
		isfactoread = "readonly";
	}

	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+factz+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";

	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmorgqty+"' name='hdnqtyorig' id='hdnqtyorig"+lastRow+"'> <input type='hidden' value='"+price+"' name=\"txtnprice\" id='txtnprice"+lastRow+"' readonly \"> <input type='hidden' value='"+amtz+"' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> <input type='hidden' value='"+curramt+"' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' \> </td>";
			
	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' name='del' id='del" + lastRow + "' value='delete' onClick=\"deleteRow(this);\"/></td>";

	// / &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> 

	$('#MyTable > tbody:last-child').append('<tr>'+insbtn+ tdapcitmno + tditmpono + tditmcode + tditmdesc + tditmavail + tditmunit + tditmfactor + tditmqty + tditmdel + '</tr>');

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
					   //console.log(data);

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

function InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refe,mainident,cremarks){ 

	$("<tr>").append(
		$("<td width=\"120px\" style=\"padding:1px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+mainident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+nident+"' name=\"sertabreferid\" id=\"sertabreferid\"><input type='hidden' value='"+refe+"' name=\"sertabrefer\" id=\"sertabrefer\">"+itmcode),
		$("<td>").html("<input type='hidden' value='"+serials+"' name=\"sertabserial\" id=\"sertabserial\">"+serials), 
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
		$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
		$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+expz+"' name=\"sertabesp\" id=\"sertabesp\">"+expz),
		$("<td width=\"300px\" style=\"padding:1px\">").html("<input type='text' value='"+cremarks+"' name=\"sertabremx\" id=\"sertabremx\" class='form-control input-sm' autocomplete='off'>"),
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
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val();
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;
						
			$("#txtnamount"+r).val(namt.toFixed(4));

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
			$(this).find("input[name='txtnamount']").val(TotAmt.toFixed(4));
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
			
	addinfo(itmcde,itmnme,"","");
	
	$('#MyDetModal').modal('show');
}

function addinfo(itmcde,itmnme,fldnme,cvlaz){
	//alert(itmcde+","+itmnme);
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	
	var tdinfocode = "<td><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
	var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
	var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs' value=\""+fldnme+"\"></td>";
	var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs' value=\""+cvlaz+"\"></td>";
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
					   
                    //console.log(data);
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
								$("<td>").text(item.ccontrolno),
								$("<td>").text(item.dcutdate)
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
					//console.log('Something went wrong', status, err);
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
							  
					salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				}
				
			});

					//alert('th_sinumdet.php?x='+drno+"&y="+salesnos);
					$.ajax({
                    url: 'th_qolistdet.php',
					data: 'x='+drno+"&y="+salesnos+"&itmbal="+xChkBal,
                    dataType: 'json',
                    method: 'post',
					beforeSend: function(data){
						//console.log(data)
					},
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false); 
					   
                      //console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							  alert("NO more items to add!")
						  }
						  else{
						  
							if (item.nqty>=1){
								if(item.navail>=1){
									var xxmsg = "<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\">";
								}
								else{
									var xxmsg = "<font color='red'><b>X</b></font>";
								}
								
								$("<tr>").append(
								$("<td align='center'>").html(xxmsg),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
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
	
   $("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $(this).data("id");
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   //console.log(data);
                       $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.desc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 
							$("#hdnqty").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							//alert(item.cqtyunit);
							addItemName(item.totqty,item.nqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident,item.xcskucode,item.xcpono)
											   
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


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "DR_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	$("#txtcsalesno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
				if(document.getElementById("hdnvoid").value==1){
					var msgsx = "VOIDED";
				}else{
					var msgsx = "POSTED";
				}
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			
			$("#txtcsalesno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
		ComputeGross();
		
		checkcustlimit($("#txtcustid").val(), $("#ncustlimit").text());
		

	}
}

function printchk(x,typ){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		if(typ=="REGDR"){
			var url = "DR_confirmprint.php?x="+x;		  
		}else if(typ=="APCDR"){
			var url = "DR_confirmprintapc.php?x="+x;		 	
		}

		$("#myprintframe").attr('src',url);
		$("#PrintModal").modal('show');
		 
	}
}


function loaddetails(){
	//alert(xChkBal);
	
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcsalesno").val(), itmbal: xChkBal },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			//console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.desc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);

				addItemName(item.totqty,item.nqtyorig,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident,item.xcskucode,item.xcpono)
			});

		}
	});

}

function loaddetinfo(){
	$.ajax ({
		url: "th_loaddetinfo.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			//console.log(data);
			$.each(data,function(index,item){

				addinfo(item.id,item.desc,item.fldnme,item.cvalue);

			});

		}
	});

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
	
	//alert(ISOK);


	// Check pag meron wla Qty na Order vs available inventory
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
		//		msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
		//	}else{
		//		var myqtytots = parseFloat(myqty) * parseFloat(myfacx);
				
		//		if(parseFloat(myav) < parseFloat(myqtytots)){
		//			msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
		//		}
		//	}
			
			if(xChkBal==0){
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
	var isDone = "True";
	
		//Saving the header
		var trancode = $("#txtcsalesno").val();
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
		//alert("Quote_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		var input_data = [
			{	key: 'id', input: $("#txtcsalesno").val()	},
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
			{	key: 'delzip', input: $("#txtcZip").val()	},
			{	key: 'cdrapcord', input: $("#cdrapcord").val()	},
			{	key: 'cdrapcdr', input: $("#cdrapcdr").val()	},
			{	key: 'txtpullrqs', input: $("#txtpullrqs").val() },
			{	key: 'txtpullrmrks', input: $("#txtpullrmrks").val() },
			{	key: 'txtRevNo', input: $("#txtRevNo").val() },
			{	key: 'txtSalesRep', input: $("#txtSalesRep").val() },
			{	key: 'txtTruckNo', input: $("#txtTruckNo").val() },
			{	key: 'txtDelSch', input: $("#txtDelSch").val() },
			{	key: 'txtRevOthers', input: $("#txtRevOthers").val() },
			{	key: 'DRfootCert', input: $("#DRfootCert").val() },
			{	key: 'DRfootIssu', input: $("#DRfootIssu").val() },
			{	key: 'DRfootChec', input: $("#DRfootChec").val() },
			{	key: 'DRfootAppr', input: $("#DRfootAppr").val() },
			{	key: 'selSign1', input: $("#selSign1").val() },
			{	key: 'selSign2', input: $("#selSign2").val() }
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
			//console.log(par)
		}
		$.ajax ({
			url: "DR_updatehdr.php",
			data: formdata,
			cache: false,
			contentType: false,
			processData: false,
			type: 'post',
			method: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING DELIVERY RECEIPT: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					//alert(data.trim());
					trancode = data.trim();
				}
				else{
					$("#AlertMsg").html(trancode);
				}
			}
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
			//alert(index);

				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var crefnoident = $(this).find('input[type="hidden"][name="txtcrefident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[type="hidden"][name="txtnprice"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var ntransamt = $(this).find('input[type="hidden"][name="txtntranamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val();
				var norigqty = $(this).find('input[type="hidden"][name="hdnqtyorig"]').val();

				var nitemsysno = $(this).find('input[name="txtapcitmno"]').val();
				var nitemposno = $(this).find('input[name="txtapono"]').val(); 
				
				//alert("DR_newsavedet.php?trancode="+trancode+"&crefno="+crefno+"&crefnoident="+crefnoident+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&norigqty="+norigqty);		

				$.ajax ({
					url: "DR_newsavedet.php",
					data: { trancode: trancode, crefno: crefno, crefnoident:crefnoident, indx:index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, norigqty:norigqty, ntransamt:ntransamt, nitemsysno:nitemsysno, nitemposno:nitemposno },
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
					url: "DR_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
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
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, seiraln:seiraln },
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
			
						//$("#txtcsalesno").val(trancode);
						$("#frmpos").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}
			
		}
		else{
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem updating your transaction...");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}


	}

}

function loadserials(){	

	   		$.ajax({
					url : "th_serialslist.php?id=" + $("#txtcsalesno").val(),
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	
					   //console.log(data);
             			$.each(data,function(index,item){

								//InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refe,mainident){);
								InsertToSerials(item.citemno,item.cserial,item.cunit,item.nqty,item.nlocation,item.locadesc,item.dexpired,item.nrefidentity,item.crefno,0,item.cremarks);
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

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