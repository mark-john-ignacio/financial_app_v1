<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PR_edit.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	if(isset($_REQUEST['txtctranno'])){
		$cprno = $_REQUEST['txtctranno'];
	}
	else{
		$cprno = $_REQUEST['txtcprno'];
	}

	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'PR_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'PR_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$arrseclist = array();
	$sqlempsec = mysqli_query($con,"select A.section_nid as nid, B.cdesc from users_sections A left join locations B on A.section_nid=B.nid where A.UserID='$employeeid' and B.cstatus='ACTIVE' Order By B.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrseclist[] = $row0['nid'];
	}

	if(isset($_REQUEST['cwh'])){
		$arrseclist = array();
		$arrseclist[0] = $_REQUEST['cwh'];
	}else{
		if(count($arrseclist)==0){
			$arrseclist[] = 0;
		}
	}

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'PR_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	// UOM LIST //
		$arruomlist = array();
		$resultmain = mysqli_query ($con, "SELECT A.cpartno, A.cunit, B.cDesc FROM items A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company'"); 

		while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

			$arruomlist[] = array('cunit' => $row2['cunit'], 'cDesc' => $row2['cDesc'], 'citemno' => $row2['cpartno']);

		}
		
		$result = mysqli_query ($con, "SELECT A.cpartno, A.cunit, B.cDesc FROM items_factor A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' AND A.cstatus='ACTIVE'"); 

		if(mysqli_num_rows($result) >  1)
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$arruomlist[] = array('cunit' => $row['cunit'], 'cDesc' => $row['cDesc'], 'citemno' => $row['cpartno']);

		}

	// END UOM LIST

	$sqlhead = mysqli_query($con,"Select A.*, B.cdesc from purchrequest A left join mrp_operators B on A.crequestedby=B.nid Where A.compcode='$company' and A.ctranno='$cprno'");

	@$arrname = array();
	if (file_exists('../../Components/assets/PReq/'.$company.'_'.$cprno.'/')) {
		$allfiles = scandir('../../Components/assets/PReq/'.$company.'_'.$cprno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}

	$chkapprovals = "";
	$sqlappx = mysqli_query($con,"Select * from purchrequest_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 and cprno='$cprno' Order by nlevel ASC LIMIT 1");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			$chkapprovals = $rows['userid']; 
		}
	}

	//get locations of cost center
	@$clocs = array();
	$gettaxcd = mysqli_query($con,"SELECT nid, cdesc FROM `locations` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$clocs[] = $row; 
		}
	}

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

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcprno').focus();">
<input type="hidden" id="costcenters" value='<?=json_encode($clocs)?>'>
<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$cpreparedBy = $row['crequestedby'];

		$cSecID = $row['locations_id'];
		$cRemarks = $row['cremarks'];
		$dDueDate = date_format(date_create($row['dneeded']), "m/d/Y");

		$clastapprvby = $row['capprovedby'];
		$clastchkdby = $row['ccheckedby'];

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];
		$lSent = $row['lsent'];
	}
?>
	<form action="PR_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post"  enctype="multipart/form-data">

		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
				<i class="fa fa-cart-plus"></i>Purchase Request Details
				</div>
				<div class="status">
					<?php
						$xm = 0;
						if($lCancelled==1){
							echo "<font color='#FF0000'><b>CANCELLED</b></font>";
							$xm = 1;
						}
						
						if($lPosted==1){
							if($lVoid==1){
								echo "<font color='#FF0000'><b>VOIDED</b></font>";
							}else{
								echo "<font color='#FF0000'><b>POSTED</b></font>";
							}
							$xm = 1;
						}
					?>
				</div>
			</div>
			<div class="portlet-body">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#home">PR Details</a></li>
					<li><a href="#attc">Attachments</a></li>
				</ul>

				<div class="tab-content">  

					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top: 10px;">

						<table width="100%" border="0">
							<tr>
								<tH>PR No.:</tH>
								<td style="padding:2px">
									<div class="col-xs-3 nopadding">
										<input type="text" class="form-control input-sm" id="txtcprno" name="txtcprno" width="20px" tabindex="1" value="<?php echo $cprno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
									</div>     
									<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cprno;?>">
									<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
									<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
									<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
									&nbsp;&nbsp;
									
								</td>
								<td colspan="2"  style="padding:2px" align="right">
									<div id="statmsgz" class="small" style="display:inline"></div>
								</td>
							</tr>

							<tr>
								<tH width="150">Requested By:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-5 nopadding">
											<select class='xsel2 form-control input-sm' id="txtcustid" name="txtcustid">`
												<option value="">&nbsp;</option>
												<?php
													foreach(@$arrempslist as $rsx){
														$slcted = ($cpreparedBy==$rsx['nid']) ? "selected" : "";
														echo "<option value='".$rsx['nid']."' ".$slcted."> ".$rsx['cdesc']." </option>";
													}
												?>
											</select>
										</div>
										<div class="col-xs-5 nopadwleft">
											<select class="form-control input-sm" name="selwhfrom" id="selwhfrom"> 
												<?php
													foreach($rowdetloc as $localocs){									
												?>
														<option value="<?php echo $localocs['nid'];?>" <?=($cSecID==$localocs['nid']) ? "selected" : "";?>><?php echo $localocs['cdesc'];?></option>										
												<?php	
													}						
												?>
											</select>
										</div>
									</div>
								</td>
								<tH width="150" style="padding:2px">Date Needed:</tH>
								<td style="padding:2px" width="200">
									<div class="col-xs-8 nopadding">
										<input type='text' class="form-control input-sm" id="date_needed" name="date_needed" value="<?=$dDueDate; ?>"/>
									</div>
								</td>
							</tr>
							<tr>
							<tH>Checked/Approved:</tH>
								<td style="padding:2px">
									<div class="col-xs-5 nopadding">
										<input type='text' class="form-control input-sm" id="chkdby" name="chkdby" placeholder="Enter Checked By..." value="<?=$clastchkdby?>">
									</div>
									<div class="col-xs-5 nopadwleft">
										<input type='text' class="form-control input-sm" id="apprby" name="apprby" placeholder="Enter Approved By..." value="<?=$clastapprvby?>">
									</div>
								</td>
								<tH width="150">&nbsp;</tH>
								<td style="padding:2px;">
									<input type='hidden' id="txtremarks" name="txtremarks" value='<?=$cRemarks?>'>
								</td>
							</tr>
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
						</table>

					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px;">
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

				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details
						</div>
						<div class="inputs">
							<div class="portlet-input input-inline">
								<div class="col-xs-12 nopadding">

									<input type="hidden" name="hdnunit" id="hdnunit">
											
									<div class="col-xs-4 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." tabindex="4"></div>
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
										<th width="200px" style="border-bottom:1px solid #999">Part No.</th>
										<th width="300px" style="border-bottom:1px solid #999">Description</th>
										<th width="100px" style="border-bottom:1px solid #999">&nbsp;&nbsp;Item Code</td>
										<th width="80px" style="border-bottom:1px solid #999">UOM</th>
										<th width="120px" style="border-bottom:1px solid #999">Qty</th>
										<th width="250px" style="border-bottom:1px solid #999">Remarks</th>
										<th width="150px" style="border-bottom:1px solid #999">Cost Center</th>
										<th width="50px" style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>
								<tbody class="tbody">
									<?php 
										$sqlbody = mysqli_query($con,"select a.* from purchrequest_t a left join items b on A.compcode=B.compcode and A.citemno=b.cpartno where a.compcode = '$company' and a.ctranno = '$cprno' Order By a.nident");

										if (mysqli_num_rows($sqlbody)!=0) {
											$cntr = 0;
											while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
											$cntr = $cntr + 1;
									?>
									<tr>
										<td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;"><input type='hidden' value='<?=$rowbody['cpartdesc']?>' name="txtcpartdesc" id="txtcpartdesc"><?=$rowbody['cpartdesc']?></td>
										<td width='350px'><input type='text' class='form-control input-xs' id='txtcitemdesc' name='txtcitemdesc' placeholder='Enter remarks...' value='<?=$rowbody['citemdesc']?>' /></td>
										<td width='100px'><input type='hidden' value='<?=$rowbody['citemno']?>' name="txtitemcode" id="txtitemcode">&nbsp;&nbsp;<?=$rowbody['citemno']?></td>
										<td width='80px' style='padding:1px'>
											<select class='xseluom form-control input-xs' name="seluom" id="seluom<?=$cntr?>">
												<?php
													foreach($arruomlist as $rs2){
														if($rs2['citemno']==$rowbody['citemno']){
															if($rs2['cunit']==$rowbody['cunit']){
																echo "<option value='".$rs2['cunit']."' selected>".$rs2['cDesc']."</option>";
															}else{
																echo "<option value='".$rs2['cunit']."'>".$rs2['cDesc']."</option>";
															}
														}
													}
												?>
											</select>
										</td>
										<td width='80px' style='padding:1px'>
											<input type='text' value='<?=$rowbody['nqty']?>' class='numeric form-control input-xs' style='text-align:right' name="txtnqty" id="txtnqty<?=$cntr?>" autocomplete='off' onFocus='this.select();' /> 
											<input type='hidden' value='<?=$rowbody['cmainunit']?>' name='hdnmainuom' id='hdnmainuom<?=$cntr?>'> 
											<input type='hidden' value='<?=$rowbody['nfactor']?>' name='hdnfactor' id='hdnfactor<?=$cntr?>'>
										</td>
										<td width='250px' style='padding:1px'><input type='text' class='form-control input-xs' id='dremarks<?=$cntr?>' name='dremarks' placeholder='Enter remarks...'value="<?=$rowbody['cremarks']?>" /></td>
										<td width='150px'>
											<select class='form-control input-xs' name='txtnSub' id='txtnSub<?=$cntr?>'>  
												<option value='0' data-cdesc=''>NONE</option>
												<?php
													foreach($clocs as $rs2){
														if($rs2['nid']==$rowbody['location_id']){
																echo "<option value='".$rs2['nid']."' data-cdesc='".$rs2['cdesc']."' selected>".$rs2['cdesc']."</option>";
														}else{
															echo "<option value='".$rs2['nid']."' data-cdesc='".$rs2['cdesc']."'>".$rs2['cdesc']."</option>";
														}
													}
												?>
											</select>
										</td>
										<td width='50px' style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del<?=$cntr?>' value='delete' /></td>
									</tr>

									<script>
										$("#del<?=$cntr?>").on('click', function() { 
											$(this).closest('tr').remove();
										});
									</script>
									<?php
											}
										}
									?>

								</tbody>				                    
							</table>

						</div>
					</div>
				</div>

				<?php
					if($poststat=="True"){
				?>

				<div class="row nopadwtop2x">
					<div class="col-xs-12">
						<div class="portlet">
							<div class="portlet-body">
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PR.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>&loc=<?=isset($_REQUEST['hdnsrchsec']) ? $_REQUEST['hdnsrchsec'] : ""?>&st=<?=isset($_REQUEST['hdnsrchsta']) ? $_REQUEST['hdnsrchsta'] : ""?>';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>
							
								<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='PR_new.php';" id="btnNew" name="btnNew">
									New<br>(F1)
								</button>

								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>

								<?php
									$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'PR_print'");

									if(mysqli_num_rows($sql) == 1){
									
								?>

									<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cprno;?>','Print');" id="btnPrint" name="btnPrint">
										Print<br>(CTRL+P)
									</button>

								<?php		
										}
								?>

								<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
									Edit<br>(CTRL+E)    
								</button>
								
								<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
									Save<br>(CTRL+S)
								</button>

								<?php
								if($xm==0 && $lSent==1){ 
									$chkrejstat1 = "";
									$chkrejstat2 = "";
									if($chkapprovals==$employeeid){
										$chkrejstat1 = (($poststat!="True") ? " disabled" : "");
										$chkrejstat2 = (($cancstat!="True") ? " disabled" : "");
									}else{
										$chkrejstat1 = " disabled";
										$chkrejstat2 = " disabled";
									}
		
									if($chkrejstat1==""){
										echo "<button id=\"btnPosting\" type=\"button\" onClick=\"trans('POST','".$cprno."')\" class=\"btn btn-default btn-sm".$chkrejstat1."\">Post<br><i class=\"fa fa-thumbs-up\" style=\"font-size:18px;color:Green\" title=\"Approve transaction\"></i></button>";
									}
		
									if($chkrejstat2==""){
										echo " <button id=\"btnCanceling\" type=\"button\" onClick=\"trans('CANCEL','".$cprno."')\" class=\"btn btn-default btn-sm".$chkrejstat2."\">Cancel<br><i class=\"fa fa-thumbs-down\" style=\"font-size:18px;color:Red\" title=\"Cancel transaction\"></i></button>";
									}
																
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
					}
				?>
			</div>
		</div>

	</form>
<?php
}else{
?>
	<form action="PR_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Purchase Request</legend>	

			<table width="100%" border="0">
				<tr>
					<tH width="100">PR NO.:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcprno" name="txtcprno" width="20px" tabindex="1" value="<?php echo $cprno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
				</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PR No. DID NOT EXIST!</b></font></tH>
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
								<button type="button" class="btn btn-primary btn-sm" id="OK" onclick="trans_send('OK')">Ok</button>
								<button type="button" class="btn btn-danger btn-sm" id="Cancel" onclick="trans_send('Cancel')">Cancel</button>
								
								
								<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
								
								<input type="hidden" id="typ" name="typ" value = "">
								<input type="hidden" id="modzx" name="modzx" value = "">
						</center>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="post" name="frmedit" id="frmedit" action="PR_edit.php"> 
		<input type="hidden" name="txtctranno" id="txtctranno" value="">
	</form>

	<form method="post" name="frmprint" id="frmprint" action="PrintPR_PDF.php" target="_blank">
		<input type="hidden" name="printid" id="printid" value="">
	</form>

</body>
</html>

<script type="text/javascript">

	var fileslist = [];
	/*
	var xz = $("#hdnfiles").val();
	$.each(jQuery.parseJSON(xz), function() { 
		fileslist.push(xz);
	});
	*/

	console.log(fileslist);
	var filesconfigs = [];
	var xzconfig = JSON.parse($("#hdnfileconfig").val());

	//alert(xzconfig.length);

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var xtc = "";
	for (var i = 0; i < xzconfig.length; i++) {
    var object = xzconfig[i];
		//alert(object.ext + " : " + object.name);

		$vrx = encodeURIComponent(object.name);
		fileslist.push("<?=$AttachUrlBase?>PReq/<?=$company."_".$cprno?>/" + $vrx)

		if(jQuery.inArray(object.ext, arroffice) !== -1){
			xtc = "office";
		}else if(jQuery.inArray(object.ext, arrimg) !== -1){
			xtc = "image";
		}else if(object.ext=="txt"){
			xtc = "text";
		}else if(object.ext=="csv"){
			xtc = "gdocs";
		}else{
			xtc = object.ext;
		}

		filesconfigs.push({
			type : xtc, 
			caption : object.name,
			width : "120px",
			url: "th_filedelete.php?id="+object.name+"&code=<?=$cprno?>", 
			key: i + 1
		});
	}


	$(document).keydown(function(e) {	 
		
		if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='PR_new.php';
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
				printchk('<?php echo $cprno;?>', 'Print');
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

	});

	$(document).ready(function() {

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
		});

		$('#date_needed').datetimepicker({
			format: 'MM/DD/YYYY'
		});

		if(fileslist.length>0){
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: fileslist,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: '<?=$AttachUrlBase?>PReq/<?=$company."_".$cprno?>/{filename}',
				initialPreviewConfig: filesconfigs
			});
		}else{
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("click", function () {
			$(this).select();
		});

		$(".xseluom").on('change', function() {
			var fact = setfactor($(this).val(), itmcode);									
			$('#hdnfactor'+lastRow).val(fact.trim());										
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
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.id+": "+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
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
							myFunctionadd();
							ComputeGross();							
						}
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
		
						//closing for success: function(value){
					}
				}); 		
						
			}//if ebter is clicked
			
		});

		disabled();

	});

	function addItemName(){
		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

			myFunctionadd();		
					
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			
		}

	}

	function myFunctionadd(){

		var itmcode = document.getElementById("txtprodid").value;
		var itmdesc = document.getElementById("txtprodnme").value;
		var itmunit = document.getElementById("hdnunit").value;

		var uomoptions = "";

		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
									
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
			
			uomoptions = "<select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select>";

			var xz = $("#costcenters").val();
			taxoptions = "";
			$.each(jQuery.parseJSON(xz), function() { 
				taxoptions = taxoptions + "<option value='"+this['nid']+"' data-cdesc='"+this['cdesc']+"'>"+this['cdesc']+"</option>";
			});

			var costcntr = "<select class='form-control input-xs' name='txtnSub' id='txtnSub"+lastRow+"'>  <option value='0' data-cdesc=''>NONE</option> " + taxoptions + " </select>";

			$('#MyTable > tbody:last-child').append( 
			"<tr>"
			+"<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"><input type='hidden' value='"+itmdesc+"' name=\"txtcpartdesc\" id=\"txtcpartdesc\">"+itmdesc+"</td>"
			+"<td width='350px'><input type='text' class='form-control input-xs' id='txtcitemdesc' name='txtcitemdesc' placeholder='Enter remarks...' value='"+itmdesc+"' /></td>"
			+"<td width='100px'><input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">&nbsp;&nbsp;"+itmcode+"</td>"
			+"<td style='padding:1px' width='80px'>"+uomoptions+"</td>"
			+"<td style='padding:1px' width='80px'><input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='1' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>"
			+"<td style='padding:1px' width='250px'><input type='text' class='form-control input-xs' id='dremarks"+lastRow+"' name='dremarks' placeholder='Enter remarks...' /></td>"
			+'<td width="150px" style="padding:1px">'+costcntr+'</td>'
			+"<td width='50px' style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /></td>"
		);									

			$("#del"+lastRow).on('click', function() { 
				$(this).closest('tr').remove();
			});

			$("input.numeric").autoNumeric('init',{mDec:2});
			$("input.numeric").on("click", function () {
				$(this).select();
			});
												
			$(".xseluom").on('change', function() {
				var fact = setfactor($(this).val(), itmcode);									
				$('#hdnfactor'+lastRow).val(fact.trim());										
			});

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

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "PR_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){
		$("#frmpos :input").attr("disabled", true);
		
		$("#txtcprno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);

		$("#btnPosting").attr("disabled", false);
		$("#btnCanceling").attr("disabled", false);
		 

		$(".kv-file-zoom").attr("disabled", false);
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
			
				$("#txtcprno").val($("#hdntranno").val());
				$("#txtcprno").attr("readonly", true);
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);	
				
				$("#btnPosting").attr("disabled", true);
				$("#btnCanceling").attr("disabled", true);
		
		}
	}

	function printchk(x,typx){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{

				if(typx=="Print"){

					var url = "PrintPR_PDF.php?hdntransid="+x;
					$("#printid").val(x);

					$("#frmprint").submit();
				}

		}
	}

	function chkform(){
		var ISOK = "YES";
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(lastRow == 0){
				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;NO details found!");
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
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
				
				if(myqty == 0 || myqty == ""){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
				}
				
			});
			
			if(msgz!=""){
				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
				$("#AlertModal").modal('show');

				return false;
				ISOK = "NO";
			}
		}
		
		if(ISOK == "YES"){
			document.getElementById("hdnrowcnt").value = lastRow; 

			//rename input name
			var tx = 0;
			$("#MyTable > tbody > tr").each(function(index) {  
				tx = index + 1;
				$(this).find('input[type=hidden][name="txtcpartdesc"]').attr("name","txtcpartdesc"+tx);
				$(this).find('input[name="txtcitemdesc"]').attr("name","txtcitemdesc"+tx);
				$(this).find('input[type=hidden][name="txtitemcode"]').attr("name","txtitemcode"+tx);
				$(this).find('select[name="seluom"]').attr("name","seluom" + tx);
				$(this).find('input[name="txtnqty"]').attr("name","txtnqty" + tx);
				$(this).find('input[type=hidden][name="hdnmainuom"]').attr("name","hdnmainuom" + tx);
				$(this).find('input[type=hidden][name="hdnfactor"]').attr("name","hdnfactor" + tx);
				$(this).find('input[name="dremarks"]').attr("name","dremarks" + tx);	
				$(this).find('select[name="txtnSub"]').attr("name","txtnSub" + tx);		
			});

			$("#frmpos").submit();

		}

	}

	function trans(x,num){

		$("#typ").val(x);
		$("#modzx").val(num);

		$("#AlertMsg").html("");

		if(x=="CANCEL1"){
			x = "CANCEL";
		}
								
		$("#AlertMsg").html("Are you sure you want to "+x+" PR No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');

	}

	function trans_send(idz){

		var itmstat = "";
		var x = "";
		var num = "";
		var msg = "";

		var x = $("#typ").val();
		var num = $("#modzx").val();

		if(idz=="OK" && (x=="POST" || x=="SEND")){

			$.ajax ({
				url: "PR_Tran.php",
				data: { x: num, typ: x, canmsg: "" },
				dataType: "json",
				beforeSend: function() {
					$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
					$("#alertbtnOK").css("display", "none");
					$("#OK").css("display", "none");
					$("#Cancel").css("display", "none");
				},
				success: function( data ) {
					console.log(data);
					//setmsg(data,num);
					$("#txtctranno").val('<?=$cprno?>');
					$("#frmedit").submit(); 
				}
			});
			

		}else if(idz=="OK" && (x=="CANCEL" || x=="CANCEL1")){
			bootbox.prompt({
				title: 'Enter reason for cancellation.',
				inputType: 'text',
				centerVertical: true,
				callback: function (result) {
					if(result!="" && result!=null){
						$.ajax ({
							url: "PR_Tran.php",
							data: { x: num, typ: x, canmsg: result },
							dataType: "json",
							beforeSend: function() {
								$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
								$("#alertbtnOK").css("display", "none");
								$("#OK").css("display", "none");
								$("#Cancel").css("display", "none");
							},
							success: function( data ) {
								console.log(data);
								setmsg(data,num);
							}
						});
					}else{
						$("#AlertMsg").html("Reason for cancellation is required!");
						$("#alertbtnOK").css("display", "inline");
						$("#OK").css("display", "none");
						$("#Cancel").css("display", "none");
					}						
				}
			});
		}else if(idz=="Cancel"){
			
			$("#AlertMsg").html("");
			$("#AlertModal").modal('hide');
			
		}

	}

</script>
