<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "JobOrders";

	include('../../Connection/connection_string.php');
	//include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$tranno = $_REQUEST['txtctranno'];

	$_SESSION['myxtoken'] = gen_token();

	//EDIT ACCESS
	$editstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'JobOrders_edit'");
	if(mysqli_num_rows($sql) == 0){
		$editstat = "False";
	}

	//New ACCESS
	$newstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'JobOrders_new'");
	if(mysqli_num_rows($sql) == 0){
		$newstat = "False";
	}


	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);				
	}


	$arrmrpjo = array();
	$sql = "select X.*, A.citemdesc, C.cname from mrp_jo X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno left join customers C on X.compcode=C.compcode and X.ccode=C.cempid  where X.compcode='$company' and X.ctranno = '$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo[] = $row2;				
	}


	$arrmrpjo_pt = array();
	$sql = "select X.nid, X.ctranno, X.mrp_process_id, X.mrp_process_desc, X.nmachineid, IFNULL(X.ddatestart,'') as ddatestart, IFNULL(ddateend,'') as ddateend, X.nactualoutput, X.operator_id, X.nrejectqty, X.nscrapqty, IFNULL(Y.cdesc,'') as cmachinedesc, IFNULL(Z.cdesc,'') as operator_name, X.lpause from mrp_jo_process_t X left join mrp_machines Y on X.compcode=Y.compcode and X.nmachineid=Y.nid left join mrp_operators Z on X.compcode=Z.compcode and X.operator_id=Z.nid where X.compcode='$company' and X.ctranno in (Select ctranno from mrp_jo_process where compcode='$company' and mrp_jo_ctranno  = '$tranno')";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_pt[] = $row2;				
	}

	@$arrname = array();
	$directory = "../../Components/assets/JOR/{$company}_{$tranno}/";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
	}

	$arrmrpjo = array();
	$sql = "select X.*, A.citemdesc, C.cname from mrp_jo X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno left join customers C on X.compcode=C.compcode and X.ccode=C.cempid  where X.compcode='$company' and X.ctranno = '$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo[] = $row2;				
	}

	$arrinvv = array();
	$sql = "select citemno, sum(nqtyin) - sum(nqtyout) as nbal From tblinventory where compcode='$company' group by citemno";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrinvv[$row2['citemno']] = $row2['nbal'];				
	}

	$prntnme = array();
	$sqltempname = mysqli_query($con,"select * from nav_menu_prints where compcode='$company'");
	$rowdettempname= $sqltempname->fetch_all(MYSQLI_ASSOC);
	foreach($rowdettempname as $row0){
		$prntnme[$row0['code']] = $row0['filename'];
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?php echo time();?>">
  	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

	<style>
		.selectedJO {
			background-color: LightGoldenRodYellow;
		}

	</style>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">

	<form action="JO_updatesave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">

		<input type="hidden" name="hdnposted" id="hdnposted" value="<?=$arrmrpjo[0]['lapproved'];?>">
		<input type="hidden" name="hdncancel" id="hdncancel" value="<?=$arrmrpjo[0]['lcancelled'];?>">
		<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?=$arrmrpjo[0]['lvoid'];?>">

		<fieldset>
				<legend>			
					<div class="col-xs-6 nopadding"> Job Order Details </div>  
					
					<div class= "col-xs-6 text-right nopadding" id="salesstat">
						<?php
							if($arrmrpjo[0]['lcancelled']==1){
								echo "<font color='#FF0000'><b>CANCELLED</b></font>";
							}
									
							if($arrmrpjo[0]['lapproved']==1){
								if($arrmrpjo[0]['lvoid']==1){
									echo "<font color='#FF0000'><b>VOIDED</b></font>";
								}else{
									echo "<font color='#FF0000'><b>POSTED</b></font>";
								}
							}
						?>
					</div>

				</legend>

					<ul class="nav nav-tabs">
						<li class="active"><a href="#apv">JO Details</a></li>
						<li><a href="#attc">Attachments</a></li>
						<li><a href="#subjo">Sub-Job Orders</a></li>
						<li><a href="#mats">Materials Request</a></li>
					</ul>

					<div class="tab-content" style="overflow: inherit !important">  

						<div id="apv" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px; padding-right:5px; overflow: inherit !important">

							<table width="100%" border="0" cellspacing="0" cellpadding="2"  style="margin-bottom: 25px">
								<tr>
									<td><span style="padding:2px"><b>Job Order No.:</b></span></td>
									<td> 

										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-4 nopadding ">
													<input type="text" id="hdnctranno" name="hdnctranno" class="form-control input-sm required" required readonly value="<?=$tranno?>">
											</div>
										</div>

									</td>

									<td colspan="2" style="padding:2px" align="right">
										<div id="statmsgz" class="small" style="display:inline"></div>
									</td>

								</tr>
							
								<tr>
									<td><span style="padding:2px"><b>Customer:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-4 nopadding ">
													<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly value="<?=$arrmrpjo[0]['ccode']?>">
											</div>
											<div class="col-xs-8 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4" value="<?=$arrmrpjo[0]['cname']?>">
											</div>
										</div>
									</td>
									<td><span style="padding:2px" id="chkdate"><b>Target Date:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class='col-xs-8 nopadding'>
													<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtTargetDate" id="txtTargetDate" value="<?=date_format(date_create($arrmrpjo[0]['dtargetdate']),"m/d/Y")?>" />
											</div>
										</div>
									</td>
								</tr>
							
								<tr>
									<td width="150"><span style="padding:2px" id="paymntdesc"><b>Reference SO</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-3 nopadding">
												<input type="text" class="form-control input-sm required" id="crefSO" name="crefSO" value="<?=$arrmrpjo[0]['crefSO']?>" placeholder="Reference SO No." readonly required>
											</div>
											<div class="col-xs-1 nopadwleft">
												<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchSO" id="btnsearchSO"><i class="fa fa-search"></i></button>
											</div>		
											
											<div class="col-xs-8 nopadwright">
												<div class="form-check" style="padding-top: 3px; padding-left: 10px">
													<input class="form-check-input" type="checkbox" value="1" id="isWRef" name="isWRef" <?=($arrmrpjo[0]['lnoref']==1) ? "checked" : "" ?>/>
													<label class="form-check-label" for="isWRef">No Reference</label>
												</div>
											</div>
										</div>

									</td>

									<td width="150"><span style="padding:2px"><b>Priority</b></span></td>
									<td>
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-8 nopadding">
												<select id="selpriority" name="selpriority" class="form-control input-sm selectpicker">
													<option value="Low" <?=($arrmrpjo[0]['cpriority']=="Low") ? "selected" : "" ?>>Low</option>
													<option value="Normal" <?=($arrmrpjo[0]['cpriority']=="Normal") ? "selected" : ""?>>Normal</option>
													<option value="High" <?=($arrmrpjo[0]['cpriority']=="High") ? "selected" : ""?>>High</option>
												</select>
											</div>
									</td>		
														
								</tr>

								<tr>									
									<td valign="top" style="padding-top:8px;"><span style="padding:2px;"><b>Remarks</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<textarea class="form-control input-sm" id="txtcremarks" name="txtcremarks" rows="3"><?=$arrmrpjo[0]['cremarks']?></textarea>
										</div>
									</td>
									<td valign="top" style="padding-top:8px;"><span style="padding:2px"><b>Department:</b></span></td>
									<td valign="top">
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-8 nopadding">
												<select id="seldept" name="seldept" class="form-control input-sm selectpicker">
													<?php
														foreach($arrallsec as $localocs){
													?>
														<option value="<?php echo $localocs['nid'];?>" <?=($arrmrpjo[0]['location_id']==$localocs['nid']) ? "selected" : "" ?>><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
												</select>
											</div>
									</td>											
								</tr>

							</table>

							<hr>
							<div class="col-xs-12 nopadwdown"><b>Item Details</b></div>

							<div class="col-xs-12 nopadwtop">
								<div class="col-xs-6 nopadwleft"><b>Item</b></div>
								<div class="col-xs-1 nopadwleft"><b>UOM</b></div>
								<div class="col-xs-1 nopadwleft"><b>JO Qty</b></div>
								<div class="col-xs-1 nopadwleft"><b>Working Hours</b></div>
								<div class="col-xs-1 nopadwleft"><b>Setup Time</b></div>
								<div class="col-xs-1 nopadwleft"><b>Cycle Time</b></div>
								<div class="col-xs-1 nopadwleft"><b>Total Time</b></div>
							</div>

							<div class="col-xs-12  nopadwtop">
								<div class="col-xs-6 nopadwleft"><input type="text" id="citemdesc" name="citemdesc" class="form-control input-sm required" required placeholder="Item Description..." readonly value="<?=$arrmrpjo[0]['citemdesc']?>"> <input type="hidden" id="citemno" name="citemno" value="<?=$arrmrpjo[0]['citemno']?>"> <input type="hidden" id="nrefident" name="nrefident" value="<?=$arrmrpjo[0]['nrefident']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtcunit" name="txtcunit" class="form-control input-sm required" required placeholder="UOM..." readonly  value="<?=$arrmrpjo[0]['cunit']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtjoqty" name="txtjoqty" class="form-control input-sm required text-right numeric" required placeholder="0.00"  value="<?=$arrmrpjo[0]['nqty']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtworkinghrs" name="txtworkinghrs" class="form-control input-sm required text-right numeric" required placeholder="0.00"  value="<?=$arrmrpjo[0]['nworkhrs']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtsetuptime" name="txtsetuptime" class="form-control input-sm required text-right numeric" required placeholder="0.00"  value="<?=$arrmrpjo[0]['nsetuptime']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtcycletime" name="txtcycletime" class="form-control input-sm required text-right numeric" required placeholder="0.00"  value="<?=$arrmrpjo[0]['ncycletime']?>"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtntotal" name="txtntotal" class="form-control input-sm required text-right numeric" required placeholder="0.00" readonly  value="<?=$arrmrpjo[0]['ntottime']?>"></div>
							</div>
						
							
						</div>	

						<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />

						</div>

						<div id="subjo" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">
							<table id="TblJO" class="MyTable table table-condensed" width="100%">
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999">Sub JO No.</th>
										<th style="border-bottom:1px solid #999">Item</th>
										<th style="border-bottom:1px solid #999">UOM</th>
										<th style="border-bottom:1px solid #999; text-align: right">Qty</th>
										<th style="border-bottom:1px solid #999; text-align: right">Working Hours</th>
										<th style="border-bottom:1px solid #999; text-align: right">Setup Time</th>
										<th style="border-bottom:1px solid #999; text-align: right">Cycle Time</th>
										<th style="border-bottom:1px solid #999; text-align: right">Total Time</th>
									</tr>
								</thead>
								<tbody class="tbody">
									<?php
										$sql = "select X.*, A.citemdesc from mrp_jo_process X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' and X.mrp_jo_ctranno = '$tranno' Order By X.nid";
										$resultmain = mysqli_query ($con, $sql); 
										while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
									?>
										<tr id="tr<?=$row2['nid']?>">
											<td><a href="javascript:;" onclick="getprocess('<?=$row2['ctranno']?>','<?=$row2['citemdesc']?>','tr<?=$row2['nid']?>')"><?=$row2['ctranno']?></a></td>
											<td><?=$row2['citemdesc']?></td>
											<td><?=$row2['cunit']?></td>
											<td style="text-align: right"><?=number_format($row2['nqty'])?></td>
											<td style="text-align: right"><?=number_format($row2['nworkhrs'],2)?></td>
											<td style="text-align: right"><?=number_format($row2['nsetuptime'],2)?></td>
											<td style="text-align: right"><?=number_format($row2['ncycletime'],2)?></td>
											<td style="text-align: right"><?=number_format($row2['ntottime'],2)?></td>
										</tr>
									<?php
										}
									?>
								</tbody>                    
							</table>			
							
							<hr>
							<div class="col-xs-12 nopadwdown" id="subjodets"><h5>Sub-JO Details</h5></div>

							<table id="MyJOSubs" class="MyTable table table-condensed" width="100%">
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999">Action</th>
										<th style="border-bottom:1px solid #999">Machine</th>
										<th style="border-bottom:1px solid #999">Process</th>
										<th style="border-bottom:1px solid #999">Date Started</th>
										<th style="border-bottom:1px solid #999">Date Ended</th>
										<th style="border-bottom:1px solid #999">Actual Output</th>
										<th style="border-bottom:1px solid #999">Operator</th>
										<th style="border-bottom:1px solid #999; text-align: right">Reject Qty</th>
										<th style="border-bottom:1px solid #999; text-align: right">Scrap Qty</th>
										<th style="border-bottom:1px solid #999">QC</th>
										<th style="border-bottom:1px solid #999">Remarks</th>
									</tr>
								</thead>
								<tbody class="tbody">

								</tbody>
							</table>


						</div>

						<div id="mats" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

							<table id="MyMaterials" class="MyTable table table-condensed" width="100%">
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999">Material Code</th>
										<th style="border-bottom:1px solid #999">Material Description</th>
										<th style="border-bottom:1px solid #999">UOM</th>
										<th style="border-bottom:1px solid #999; text-align: right">Qty Needed</th>
										<th style="border-bottom:1px solid #999; text-align: right">Available Inv.</th>
										<th style="border-bottom:1px solid #999; text-align: right">Buildable Qty</th>
									</tr>
								</thead>
								<tbody class="tbody">
									<?php
										$sql = "select X.*, A.citemdesc from mrp_jo_process_m X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' and X.mrp_jo_ctranno = '$tranno' Order By X.nid";
										$resultmain = mysqli_query ($con, $sql); 
										while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
									?>
										<tr>
											<td><?=$row2['citemno']?></td>
											<td><?=$row2['citemdesc']?></td>
											<td><?=$row2['cunit']?></td>
											<td style="text-align: right"><?=number_format($row2['nqty'])?></td>
											<td style="text-align: right">
												<?php
													if(isset($arrinvv[$row2['citemno']])){
														echo number_format($arrinvv[$row2['citemno']],2);
													}else{
														echo 0;
													}
												?>
											</td>
											<td style="text-align: right">
												<?php
													if(isset($arrinvv[$row2['citemno']])){
														$dtotal = $arrmrpjo[0]['nqty'];
														$qtyperpc = floatval($row2['nqty']) / floatval($dtotal);

														$qtubuild = floatval($arrinvv[$row2['citemno']]) / floatval($qtyperpc);
														echo number_format($qtubuild,2);
													}else{
														echo 0;
													}
												?>
											</td>
										</tr>
									<?php
										}
									?>
								</tbody>                    
							</table>

						</div>

					</div>
						
						
					<br><br><br><br><br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">																
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='JO.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>		
								<?php
									if($newstat == "True"){
								?>
									<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='JO_new.php';" id="btnNew" name="btnNew">
										New<br>(F1)
									</button>	
								<?php
									}
								?>
								<?php
									if($editstat=="True"){
								?>									
								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>
								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$tranno;?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
								</button>					
								<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="genMRS('<?=$tranno?>');" id="btnGen" name="btnGen">
									Generate <br> MRS
								</button>
								<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
									Edit<br>(CTRL+E)    
								</button>																										
								<button type="submit" class="btn btn-success btn-sm" tabindex="6">
									Update JO<br> (CTRL+S)
								</button>		
								<?php
									}
								?>												
							</td>
						</tr>									
					</table>


			</fieldset>

	</form>

	<!-- PRINT OUT MODAL-->
	<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-contnorad">   
					<div class="modal-body" style="height: 12in !important">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
				
							<iframe id="myprintframe" name="myprintframe" scrolling="yes" style="width:100%; height: 11.5in; display:block; margin:0px; padding:0px; border:0px; overflow: scroll;"></iframe>    
						
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	<!-- End Bootstrap modal -->

	<!-- DETAILS ONLY -->
	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">SO List</h3>
				</div>
							
				<div class="modal-body" style="height:40vh">

					<div class="col-xs-12 nopadding">
						<div class="form-group">
							<div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
								<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
									<thead>
										<tr>
											<th>SO No</th>
											<th>Delivery Date</th>
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
							<p><center>
								<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
							</center></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!-- End Alert modal -->

	<form action="" method="post" name="frmQPrint" id="frmQprint" target="_blank">
		<input type="hidden" name="hdntransid" id="hdntransid" value="<?php echo $tranno; ?>">
	</form>

	<form action="JO_edit.php" method="post" name="frmUndo" id="frmUndo">
		<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $tranno; ?>">
	</form>

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../../global/plugins/bootbox/bootbox.min.js"></script>

<script type="text/javascript">

	var file_name = <?= json_encode(@$arrname) ?>;
	/**
	 * Checking of list files
	 */
	file_name.map(({name, ext}) => {
			console.log("Name: " + name + " ext: " + ext)
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
		list_file.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/JOR/<?=$company."_".$tranno?>/" + name)
		console.log(ext);

		if(jQuery.inArray(ext, arroffice) !== -1){
			extender = "office";
		} else if (jQuery.inArray(ext, arrimg) !== -1){
			extender = "image";
		} else if (ext == "txt"){
			extender = "text";
		} else {
			extender =  ext;
		}

		console.log(extender)
		file_config.push({
			type : extender, 
			caption : name,
			width : "120px",
			url: "th_filedelete.php?id="+name+"&code=<?=$tranno?>", 
			key: i + 1
		});
	})

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("JO.php");
	  }
	});

	$(document).ready(function() {

		$('#txtTargetDate').datetimepicker({
      format: 'MM/DD/YYYY',
    });

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
		});

		$("input.numeric").autoNumeric('init',{mDec:2}); 
		$("input.numeric").on("click", function () {
			$(this).select();
		});
									
		$("input.numeric").on("keyup", function () {
			computeTot();
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
					initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/JOR/<?=$company."_".$tranno?>/{filename}',
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

		//Search Cust name
		$('#txtcust').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../../Sales/th_customer.php",
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
			
			}
		
		});

		$('#btnsearchSO').on("click", function(){
			getSO();
		});

		$('#isWRef').change(function() {

			if(this.checked) {
				$('#btnsearchSO').attr("disabled", true); 
				$('#citemdesc').attr("readonly", false);
			}else{
				$('#btnsearchSO').attr("disabled", false);
				$('#citemdesc').attr("readonly", true);
			}

		});

		$('#citemdesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: $("#citemdesc").val() },
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
							
				$('#citemdesc').val(item.desc).change(); 
				
				$("#citemdesc").val(item.desc); 
				$("#citemno").val(item.id);
				$("#txtcunit").val(item.cunit);
				$("#nrefident").val(item.nident);
				$("#txtjoqty").val(item.nqty);
				$("#txtworkinghrs").val(item.nworkhrs);
				$("#txtsetuptime").val(item.nsetuptime);
				$("#txtcycletime").val(item.ncycletime);

				computeTot();
				
				
			}
		
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

		disabled();

	});

	$(document).on('click', 'button.nbtnpaused', function(e) {
		let yx = $(this).data("val");

		$xmsg = "";
		$mstat = "";		

		bootbox.prompt({
			title: 'Enter reason for pause.',
			inputType: 'text',
			centerVertical: true,
			callback: function (result) {
				if(result==""){		
					bootbox.alert({
						message: "Reason for pause is required!",
						size: "small",
						className: "bootalert"
					});
				}else if(result!="" && result !=null){
					$.ajax ({
						url: "th_setpause.php",
						data: { processid: yx, pausemsg: result },
						async: false,
						dataType: "json",
						success: function( data ) {
							console.log(data);
							
							$.each(data, function(index, element) {
								$xmsg = data.msg;
								$mstat = data.stat;					
							});
							
							if($mstat!=""){
								if($mstat=="True"){
									$("#txpause"+yx).html("ON PAUSE");
									$("#txpause"+yx).addClass("text-danger");

									$("#tspause"+yx).html("<button type=\"button\" class=\"nbtnresume btn btn-success btn-xs btn-block\" id=\"btnUpResume"+yx+"\" data-val=\""+yx+"\">Resume</button>");
								}
								
								bootbox.alert({
									message: $xmsg,
									size: "small",
									className: "bootalert"
								});
							}
						}			
					});
				}

			}
		});
	});

	$(document).on('click', 'button.nbtnresume', function(e) {
		let yx = $(this).data("val");

		$xmsg = "";
		$mstat = "";		

		bootbox.prompt({
			title: 'Enter reason to resume.',
			inputType: 'text',
			centerVertical: true,
			callback: function (result) {
				if(result==""){		
					bootbox.alert({
						message: "Reason to resume is required!",
						size: "small",
						className: "bootalert"
					});
				}else if(result!="" && result !=null){
					$.ajax ({
						url: "th_setresume.php",
						data: { processid: yx, pausemsg: result },
						async: false,
						dataType: "json",
						success: function( data ) {
							console.log(data);
							
							$.each(data, function(index, element) {
								$xmsg = data.msg;
								$mstat = data.stat;					
							});
							
							if($mstat!=""){
								if($mstat=="True"){
									$("#txpause"+yx).html("");
									$("#txpause"+yx).removeClass("text-danger");

									$("#tspause"+yx).html("<button type=\"button\" class=\"nbtnpaused btn btn-warning btn-xs btn-block\" id=\"btnUpActual"+yx+"\" data-val=\""+yx+"\">Pause</button>");
								}
								
								bootbox.alert({
									message: $xmsg,
									size: "small",
									className: "bootalert"
								});
							}
						}			
					});
				}

			}
		});
	});

	function getSO(){
		$xcus = $('#txtcustid').val();

		if($xcus == ""){
			alert("Please pick a valid customer!");
		}
		else{

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();

			xstat = "YES";

			$.ajax({
				url: 'th_solist.php',
				data: 'x='+$xcus,
				dataType: 'json',
				method: 'post',
				success: function (data) {
							
					console.log(data);
					$.each(data,function(index,item){
									
						if(item.cpono=="NONE"){
							$("#AlertMsg").html("No Sales Order Available");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

							xstat = "NO";

						}
						else{
							$("<tr>").append(
								$("<td id='td"+item.csono+"'>").text(item.csono),
								$("<td>").text(item.dcutdate)
							).appendTo("#MyInvTbl tbody");
														
							$("#td"+item.csono).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.csono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
						}

					});
						
					if(xstat=="YES"){
						$("#mySIRef").modal("show");
					}

				},
				error: function (req, status, err) {
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
			
		$('#loadimg').show();
		
		var salesnos = "";
		var cnt = 0;

		$.ajax({
        	url: 'th_solistdet.php',
			data: 'x='+drno,
			dataType: 'json',
			method: 'post',
			success: function (data) {
					   
          		console.log(data);
				$.each(data,function(index,item){
											
					if (item.nqty>=1){
						$("<tr>").append(
							$("<td>").html("<a href='javascript:;' onclick='savedet(this);' data-itemno='"+item.citemno+"' data-desc='"+item.cdesc+"' data-ident='"+item.nident+"' data-unit='"+item.cunit+"' data-qty='"+item.nqty+"' data-workhrs='"+item.nworkhrs+"' data-setup='"+item.nsetuptime+"' data-cycle='"+item.ncycletime+"' data-csono='"+drno+"'>"+item.citemno+"</a>"),
							$("<td>").text(item.cdesc),
							$("<td>").text(item.cunit),
							$("<td>").text(item.nqty)
						).appendTo("#MyInvDetList tbody");
					}
				});
       		},
			complete: function(){
				$('#loadimg').hide();
			},
        	error: function (req, status, err) {
				console.log('Something went wrong', status, err);
				$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
         	}
      	});
	}

	function savedet(xz){

		$("#crefSO").val(xz.dataset.csono);
		$("#citemdesc").val(xz.dataset.desc); 
		$("#citemno").val(xz.dataset.itemno);
		$("#txtcunit").val(xz.dataset.unit);
		$("#nrefident").val(xz.dataset.ident);
		$("#txtjoqty").val(xz.dataset.qty);
		$("#txtworkinghrs").val(xz.dataset.workhrs);
		$("#txtsetuptime").val(xz.dataset.setup);
		$("#txtcycletime").val(xz.dataset.cycle);

		computeTot();

		$("#mySIRef").modal("hide");

	}	

	function computeTot(){

		$nqty = parseFloat($("#txtjoqty").val().replace(/,/g,''));
		$ncycle = parseFloat($("#txtcycletime").val().replace(/,/g,''));
		$nsetup = parseFloat($("#txtsetuptime").val().replace(/,/g,''));

		$xtot = ($nqty*$ncycle) + $nsetup;
		$("#txtntotal").val($xtot);

		//$("#txtjoqty").autoNumeric('destroy');
		//$("#txtjoqty").autoNumeric('init',{mDec:2});
		//$("#txtworkinghrs").autoNumeric('destroy');
		//$("#txtworkinghrs").autoNumeric('init',{mDec:2});
		//$("#txtsetuptime").autoNumeric('destroy');
		//$("#txtsetuptime").autoNumeric('init',{mDec:2});
		//$("#txtcycletime").autoNumeric('destroy');
		//$("#txtcycletime").autoNumeric('init',{mDec:2});
		$("#txtntotal").autoNumeric('destroy');
		$("#txtntotal").autoNumeric('init',{mDec:2});

	}

	function getprocess($xtran,$xitms,$trid){
		var file_name = '<?= json_encode($arrmrpjo_pt) ?>';

		$('tr').removeClass("selectedJO");

		$("#"+$trid).addClass("selectedJO");

		$("#subjodets").html("<h5>"+$xtran+": "+$xitms+"<h5>");
		$("#MyJOSubs tbody").empty(); 

		var obj = jQuery.parseJSON(file_name);
		$.each(obj, function(key,value) {
			if(value.ctranno == $xtran){
				//alert(value.mrp_process_desc);

				if(value.lpause == 0){					
					var tdpause = "<td id=\"tspause"+value.nid+"\"><button type=\"button\" class=\"nbtnpaused btn btn-warning btn-xs btn-block\" id=\"btnUpActual"+value.nid+"\" data-val=\""+value.nid+"\">Pause</button></td>";
				}else{
					var tdpause = "<td id=\"tspause"+value.nid+"\"><button type=\"button\" class=\"nbtnresume btn btn-success btn-xs btn-block\" id=\"btnUpResume"+value.nid+"\" data-val=\""+value.nid+"\">Resume</button></td>";
				}

				var tdmachine = "<td>"+value.cmachinedesc+"</td>";
				var tdprocess = "<td>"+value.mrp_process_desc+"</td>";
				var tddatest = "<td>"+value.ddatestart+"</td>";				

				if(value.lpause == 0){					
					var tddateen = "<td id=\"txpause"+value.nid+"\">"+value.ddateend+"</td>";
				}else{
					var tddateen = "<td id=\"txpause"+value.nid+"\" class=\"text-danger\">ON PAUSE</td>";
				}


				var tdactual = "<td>"+value.nactualoutput+"</td>";
				var tdoperator = "<td>"+value.operator_name+"</td>";
				var tdreject = "<td>&nbsp;</td>";
				var tdscrap = "<td>&nbsp;</td>";
				var tdqc = "<td>&nbsp;</td>";
				var tdrems = "<td>&nbsp;</td>";

				//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
				
				$('#MyJOSubs > tbody:last-child').append('<tr>'+tdpause+tdmachine + tdprocess + tddatest + tddateen + tdactual + tdoperator + tdreject + tdscrap + tdqc + tdrems + '</tr>');

			}
		}); 
	}

	function disabled(){
		$("#frmpos :input").attr("disabled", true);
		
		
		$("#hdnctranno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);
		$("#btnGen").attr("disabled", false);
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
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{
			
			$("#frmpos :input").attr("disabled", false);
			
				$("#hdnctranno").attr("readonly", true);
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);	
				$("#btnGen").attr("disabled", true);		
		
		}
	}

	function printchk(x,typx){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{

			if(typx=="Print"){
				$("#hdntransid").val(x);
				$("#frmQprint").attr("action","JOPrint.php");
			}
			
			$("#frmQprint").submit();			

		}
	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById("frmUndo").submit();
		}
	}

	function genMRS($x){

		$.ajax ({
			url: "generate_mrs.php",
			data: { id: "<?=$tranno?>", itm: "<?=$arrmrpjo[0]['citemdesc']?>" },
			type: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>Generating MRS: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()=="False"){
					$("#AlertMsg").html("<b>Error: </b> There's a problem generating your MRS!");
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}else if(data.trim()==0){
					$("#AlertMsg").html("<b>Error: </b> There's no materials to generate MRS!");
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}else{

					$("#AlertMsg").html("");
					$("#AlertModal").modal('hide');

					$("#myprintframe").attr("src","../../Inventory/Transfers/<?=$prntnme['INVTRANS_REQUEST']?>?id="+data.trim()+"&n=1");
					$("#PrintModal").modal('show');

					
				}
			}
		});

		
	}

</script>
