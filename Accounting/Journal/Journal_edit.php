<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Journal";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access.php');

$company = $_SESSION['companyid'];

$poststat = "True";
$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Journal_edit'");
if(mysqli_num_rows($sql) == 0){
	$poststat = "False";
}

$printstat = "True"; 
$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Journal_print'");
if(mysqli_num_rows($sql) == 0){
	$printstat = "False";
}

$cjeno = $_REQUEST['txtctranno'];
$sqlhead = mysqli_query($con,"select a.* from journal a where a.compcode='$company' and a.ctranno = '$cjeno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$ddate = $row['djdate'];
		$cmemo = $row['cmemo'];
		$totdebit = $row['ntotdebit'];
		$totcredit = $row['ntotcredit'];
		$tottax = $row['ntottax'];
		$ltaxinc = $row['ltaxinc'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];
	}
}

@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/Journal/'.$company.'_'.$cjeno.'/')) {
		$allfiles = scandir('../../Components/assets/Journal/'.$company.'_'.$cjeno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		//echo "NO FILES";
	}

	//get locations of cost center
	@$clocs = array();
	$gettaxcd = mysqli_query($con,"SELECT nid, cdesc FROM `locations` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$clocs[] = $row; 
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
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

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

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus(); disabled();">
<input type="hidden" id="costcenters" value='<?=json_encode($clocs)?>'>
<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

<?php
if (mysqli_num_rows($sqlhead)!=0) {
?>

<form action="Journal_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" onSubmit="return chkform();" enctype="multipart/form-data">
	<fieldset>
    	<legend>
				<div class="col-xs-6 nopadding"> Journal Entry Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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

			</legend>	

				<ul class="nav nav-tabs">
					<li class="active"><a href="#jed">Journal Details</a></li>
					<li><a href="#attc">Attachments</a></li>
				</ul>

					<div class="tab-content">  

						<div id="jed" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px;">
         
							<table width="100%" border="0">
								<tr>
									<tH>JOURNAL No.:</tH>
									<td  style="padding:2px;">
											
											<div class="col-xs-3 nopadding">
												<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter Journal No..." required autocomplete="off" value="<?php echo $cjeno;?>"  onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
												</div>
											
										<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cjeno;?>">
										
										<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
										<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
										<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?=$lVoid;?>">
									
									
									
									</td>    
									<td style="padding:2px;" colspan="2"  align="righ">
										
										<div id="statmsgz" class="small" style="color:#F00"></div> 
										<!--<input type="checkbox" name="lTaxInc" id="lTaxInc" value="YES" <?php //if ($ltaxinc==1) { echo "checked"; }?> >-->
									</td>
								</tr>
								<tr>
									<tH><span style="padding:2px">DATE:</span></tH>
									<td style="padding:2px;">
									<div class="col-xs-5 nopadding">
										<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($ddate),'m/d/Y'); ?>" />
									</div>
									<tH><span style="padding:2px">Total Debit:</span></tH>
									<td style="padding:2px;">
									<div class="col-xs-5 nopadding">
									<input type='text' class='form-control input-sm' name='txtnDebit' id='txtnDebit' value="<?php echo $totdebit;?>" style="text-align:right" readonly>
									</div>
									</td>
								</tr>
								<tr>
									<tH width="100" rowspan="3" valign="top">MEMO:</tH>
									<td rowspan="3" style="padding:2px;" valign="top"><div class="col-xs-10 nopadding">
										<textarea class="form-control" rows="3" id="txtremarks" name="txtremarks"><?php echo $cmemo;?></textarea>
									</div>
									<tH><span style="padding:2px">Total Credit:</span></tH>
									<td style="padding:2px;">
									<div class="col-xs-5 nopadding">
									<input type='text' class='form-control input-sm' name='txtnCredit' id='txtnCredit' value="<?php echo $totcredit;?>" style="text-align:right" readonly>
									</div>
									</td>
								</tr>
								<tr>
									<tH width="150" style="padding:2px">&nbsp;<!--Tax:--></tH>
									<td style="padding:2px">&nbsp;
									<!--
									<div class="col-xs-5 nopadding">
										<input type='text' class='form-control input-sm' name='txtnTax' id='txtnTax' value="<?php// echo $tottax;?>" style="text-align:right" readonly>
									</div>
									-->
									</td>
								</tr>
								<tr>
									<tH style="padding:2px">Out of Balance:</tH>
									<td style="padding:2px"><div class="col-xs-5 nopadding">
										<input type='text' class='form-control input-sm' name='txtnOutBal' id='txtnOutBal' value="0.00" style="text-align:right" readonly>
									</div></td>
								</tr>
							</table>

						</div>
						<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />

							</div>
					</div>

				<hr>
				<div class="col-xs-12 nopadwdown"><b>Details</b></div>
    
				<small><i>*Press tab after remarks field (last row) to add new line..</i></small>

				<div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
			
					<table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">
		
						<tr>
							<th style="border-bottom:1px solid #999">Acct#</th>
							<th style="border-bottom:1px solid #999">Account Title</th>
							<th style="border-bottom:1px solid #999">Debit</th>
							<th style="border-bottom:1px solid #999">Credit</th>
							<th style="border-bottom:1px solid #999">Cost Center</th>
							<th style="border-bottom:1px solid #999">Remarks</th>
							<th style="border-bottom:1px solid #999">&nbsp;</th>
						</tr>
						<tbody class="tbody">
				
							<?php 
								$sqlbody = mysqli_query($con,"select a.* from journal_t a where a.compcode='$company' and a.ctranno = '$cjeno' order by a.nident");

								if (mysqli_num_rows($sqlbody)!=0) {
									$cntr = 0;
									while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
										$cntr = $cntr + 1;
								
							?>

									<tr>
										<td width="100px" style="padding:1px"><input type="text" class="typeahead1 form-control input-xs" name="txtcAcctNo<?php echo $cntr; ?>" id="txtcAcctNo<?php echo $cntr; ?>"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();" value="<?php echo $rowbody['cacctno'];?>"></td>
										<td><input type="text" class="form-control input-xs" name="txtcAcctDesc<?php echo $cntr; ?>" id="txtcAcctDesc<?php echo $cntr; ?>"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();" value="<?php echo $rowbody['ctitle'];?>"></td>
										<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit<?php echo $cntr; ?>" id="txtnDebit<?php echo $cntr; ?>" value="<?php echo $rowbody['ndebit'];?>" autocomplete="off"></td>
										<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit<?php echo $cntr; ?>" id="txtnCredit<?php echo $cntr; ?>" value="<?php echo $rowbody['ncredit'];?>" autocomplete="off"></td>
										<td width="100px" style="padding:1px">
											<?php
												$costoption = "";
												foreach(@$clocs as $xr){
													if($rowbody['csub']==$xr['nid']){
														$isselected = "selected";
													}else{
														$isselected = "";
													}
													$costoption = $costoption."<option value='".$xr['nid']."' data-cdesc='".$xr['cdesc']."' ".$isselected.">".$xr['cdesc']."</option>";
												}
											?>
											<select class='form-control input-xs' name="txtnSub<?php echo $cntr; ?>" id="txtnSub<?php echo $cntr; ?>">  
												<option value='' data-cdesc=''>NONE</option>
												<?=$costoption?>
											</select>
									
										</td>
										<td width="200px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcRem<?php echo $cntr; ?>" id="txtcRem<?php echo $cntr; ?>" placeholder="Remarks..." autocomplete="off" onFocus="this.select();" value="<?php echo $rowbody['cremarks'];?>"></td>
										<td width="40px" align="right">
										<?php
											if ($cntr > 1){
											?>
											<input class="btn btn-danger btn-xs" type="button" id="row_<?php echo $cntr; ?>_delete" value="delete" onClick="deleteRow(this);"/>
											<?php
											}
										?>
										</td>
									</tr>
									
							<?php 
										}
									}
							?>
					
							<script>
								$(function(){

									$("#txtcAcctNo1").typeahead({
										autoSelect: true,
										source: function(request, response) {
											$.ajax({
												url: "th_accounts.php",
												dataType: "json",
												data: {
													query: $("#txtcAcctNo1").val()
												},
												success: function (data) {
													response(data);
												}
											});
										},
										displayText: function (item) {
											return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
										},
										highlighter: Object,
										afterSelect: function(item) { 					
														
											$('#txtcAcctNo1').val(item.id).change(); 
											$('#txtcAcctDesc1').val(item.name); 
											$('#txtnDebit1').focus();
											
										}
									});


									$("#txtcAcctDesc1").typeahead({
										autoSelect: true,
										source: function(request, response) {
											$.ajax({
												url: "th_accounts.php",
												dataType: "json",
												data: {
													query: $("#txtcAcctDesc1").val()
												},
												success: function (data) {
													response(data);
												}
											});
										},
										displayText: function (item) {
											return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
										},
										highlighter: Object,
										afterSelect: function(item) { 					
														
											$('#txtcAcctDesc1').val(item.name).change(); 
											$('#txtcAcctNo1').val(item.id); 
											$('#txtnDebit1').focus();
											
										}
									});

								});
							</script>
						</tbody>
							
					</table>
					<input type="hidden" name="hdnACCCnt" id="hdnACCCnt">
				</div>

				<?php
					if($poststat=="True" || $printstat=="True"){
				?>
				<br>
				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td width="50%">
							<?php
								if($poststat=="True"){
							?>
							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Journal.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>
					
							<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Journal_new.php';" id="btnNew" name="btnNew">
								New<br>(F1)
							</button>

							<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
								Undo Edit<br>(CTRL+Z)
							</button>

							<?php
								}
								if($printstat=="True"){
							?>

							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cjeno;?>');" id="btnPrint" name="btnPrint">
								Print<br>(F4)
							</button>
							<?php
								}
								if($poststat=="True"){
							?>
							<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
								Edit<br>(CTRL+E)    
							</button>
						
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
								Save<br>(CTRL+S)    
							</button>
							<?php
								}
							?>
						</td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
				<?php
					}
			?>
    </fieldset>
</form>

<?php
}
else{
?>
	<form action="Journal_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Record Journal Entry </legend>	
			<table width="100%" border="0">
				<tr>
					<tH width="100">JOURNAL No.:</tH>
					<td style="padding:2px;">
					<div class="col-xs-2 nopadding">
						<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter Journal No..." required autocomplete="off" value="<?php echo $cjeno;?>"  onKeyUp="chkSIEnter(event.keyCode,'frmpos2');">
					</div>
					</td>
					</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Journal No. DID NOT EXIST!</b></font></tH>
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

	<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-contnorad">   
				<div class="modal-bodylong">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
			
				<iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:11in; display:block; margin:0px; padding:0px; border:0px"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

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

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var xtc = "";
	for (var i = 0; i < xzconfig.length; i++) {
    var object = xzconfig[i];
		//alert(object.ext + " : " + object.name);
		$vrx = encodeURIComponent(object.name);
		fileslist.push("<?=$AttachUrlBase?>Journal/<?=$company."_".$cjeno?>/" + $vrx)

		if(jQuery.inArray(object.ext, arroffice) !== -1){
			xtc = "office";
		}else if(jQuery.inArray(object.ext, arrimg) !== -1){
			xtc = "image";
		}else if(object.ext=="txt"){
			xtc = "text";
		}else{
			xtc = object.ext;
		}

		filesconfigs.push({
			type : xtc, 
			caption : object.name,
			width : "120px",
			url: "th_filedelete.php?id="+object.name+"&code=<?=$cjeno?>"+"&typ=Journal", 
			key: i + 1
		});
	}

	<?php
		if($poststat=="True"){
	?>
	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='Jorunal_new.php';
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
	<?php
		}
	?>

	$(function(){

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

	    $('#date_delivery').datetimepicker({
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
					initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/Journal/<?=$company."_".$cjeno?>/{filename}',
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
				})
			}
	
			$("input.numeric").autoNumeric('init',{mDec:2,wEmpty: 'zero'});
			//$("input.numeric").numeric();
			$("input.numeric").on("focus", function () {
				$(this).select();
			});
									
			$("input.numeric").on("keyup", function () {
				GoToComp($(this).attr('name'));
			});

			$("#txtnDebit").autoNumeric('init',{mDec:2,wEmpty:'zero'});
			$("#txtnCredit").autoNumeric('init',{mDec:2,wEmpty:'zero'});

			$('#MyTable :input').keydown(function(e) {

						
				var cnt = $('#MyTable tr').length;
				var inFocus = $(this).attr('id');
				var thisName = inFocus.replace(/\d+/g, '');
				var thisindex = inFocus.replace(/\D/g,'');
				
				var lstrow = parseInt(cnt)-1;
				
				if(thisName=="txtcRem"){
					if(e.keyCode==9){
						e.preventDefault();
					}
					if(parseInt(thisindex)==lstrow){
						InsertRows(e.keyCode,thisName,cnt);
					}
				}
				
				
				
				//TABLE NAVIGATION
				tblnavigate(e.keyCode,inFocus);	   
				
			});

	});

	function tblnavigate(x,txtinput){
		
					var inputCNT = txtinput.replace(/\D/g,'');
					var inputNME = txtinput.replace(/\d+/g, '');
					
					switch(x){
						case 39: // <Left>
							if(inputNME=="txtcAcctNo"){
								$("#txtcAcctDesc"+inputCNT).focus();
							}
							else if(inputNME=="txtcAcctDesc"){
								$("#txtnDebit"+inputCNT).focus();
							}
							else if(inputNME=="txtnDebit"){
								$("#txtnCredit"+inputCNT).focus();
							}
							else if(inputNME=="txtnCredit"){
								$("#txtnSub"+inputCNT).focus();
							}
							else if(inputNME=="txtnSub"){
								$("#txtcRem"+inputCNT).focus();
							}
							else if(inputNME=="txtcRem"){
								var idx =  parseInt(inputCNT) + 1;
								$("#txtcAcctNo"+idx).focus();
							}
							
							break;
						case 38: // <Up>  
							var idx =  parseInt(inputCNT) - 1;
											$("#"+inputNME+idx).focus();
							break;
						case 37: // <Right>
							if(inputNME=="txtcAcctNo"){
								var idx =  parseInt(inputCNT) - 1;
								$("#txtcRem"+idx).focus();
							}
							else if(inputNME=="txtcAcctDesc"){
								$("#txtcAcctNo"+inputCNT).focus();
							}
							else if(inputNME=="txtnDebit"){
								$("#txtcAcctDesc"+inputCNT).focus();
							}
							else if(inputNME=="txtnCredit"){
								$("#txtnDebit"+inputCNT).focus();
							}
							else if(inputNME=="txtnSub"){
								$("#txtnCredit"+inputCNT).focus();
							}
							else if(inputNME=="txtcRem"){
								$("#txtnSub"+inputCNT).focus();
							}

							break;
						case 40: // <Down>
							var idx =  parseInt(inputCNT) + 1;
											$("#"+inputNME+idx).focus();
							break;
					}       


	}


	function InsertRows(thisKey,thisNme,rowCount){

		var xz = $("#costcenters").val();
		taxoptions = "";
		$.each(jQuery.parseJSON(xz), function() { 
			taxoptions = taxoptions + "<option value='"+this['nid']+"' data-cdesc='"+this['cdesc']+"'>"+this['cdesc']+"</option>";
		});

		var costcntr = "<select class='form-control input-xs' name='txtnSub"+rowCount+"' id='txtnSub"+rowCount+"'>  <option value='' data-cdesc=''>NONE</option> " + taxoptions + " </select>"; 

		//alert(thisKey +" and "+ thisNme);
		if(thisKey==9){
			$('#MyTable > tbody:last-child').append(
				'<tr>'// need to change closing tag to an opening `<tr>` tag.
				+'<td width="100px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcAcctNo'+rowCount+'" id="txtcAcctNo'+rowCount+'"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();"></td>'
				+'<td><input type="text" class="form-control input-xs" name="txtcAcctDesc'+rowCount+'" id="txtcAcctDesc'+rowCount+'"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();"></td>'
				+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit'+rowCount+'" id="txtnDebit'+rowCount+'" value="0.00" autocomplete="off"></td>'
				+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit'+rowCount+'" id="txtnCredit'+rowCount+'" value="0.00" autocomplete="off"></td>'
				+'<td width="100px" style="padding:1px">'+costcntr+'</td>'
				+'<td width="200px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcRem'+rowCount+'" id="txtcRem'+rowCount+'" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"></td>'
				+'<td width="40px" align="right"><input class="btn btn-danger btn-xs" type="button" id="row_'+rowCount+'_delete" value="delete" onClick="deleteRow(this);"/></td>'
				+'</tr>');
							
			$("#txtcAcctNo"+rowCount).typeahead({
				autoSelect: true,
				source: function(request, response) {
					$.ajax({
						url: "th_accounts.php",
						dataType: "json",
						data: {
							query: $("#txtcAcctNo"+rowCount).val()
						},
						success: function (data) {
							response(data);
						}
					});
				},
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
				},
				highlighter: Object,
				afterSelect: function(item) { 					
								
					$('#txtcAcctNo'+rowCount).val(item.id).change(); 
					$('#txtcAcctDesc'+rowCount).val(item.name); 
					$('#txtnDebit'+rowCount).focus();
					
				}
			});

			$("#txtcAcctDesc"+rowCount).typeahead({
				autoSelect: true,
				source: function(request, response) {
					$.ajax({
						url: "th_accounts.php",
						dataType: "json",
						data: {
							query: $("#txtcAcctDesc"+rowCount).val()
						},
						success: function (data) {
							response(data);
						}
					});
				},
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
				},
				highlighter: Object,
				afterSelect: function(item) { 					
								
					$('#txtcAcctDesc'+rowCount).val(item.name).change(); 
					$('#txtcAcctNo'+rowCount).val(item.id); 
					$('#txtnDebit'+rowCount).focus();
					
				}
			});

			$('#MyTable :input').keydown(function(e) {
				var cnt = $('#MyTable tr').length;
				var inFocus = $(this).attr('id');
				var thisName = inFocus.replace(/\d+/g, '')
				var thisindex = inFocus.replace(/\D/g,'');
				
				var lstrow = parseInt(cnt)-1;
				
				if(thisName=="txtcRem"){
					if(e.keyCode==9){
					e.preventDefault();
					}
					if(parseInt(thisindex)==lstrow){
					InsertRows(e.keyCode,thisName,cnt);
					}
				}
		
				tblnavigate(e.keyCode,inFocus);
				
			});
			
			$("input.numeric").autoNumeric('init',{mDec:2,wEmpty: 'zero'});
	
			//$("input.numeric").numeric();
			$("input.numeric").on("focus", function () {
				$(this).select();
			});
			
			$("input.numeric").on("keyup", function () {
				GoToComp($(this).attr('name'));
			});
		
			$("#txtcAcctNo"+rowCount).focus();
			///	$("#txtcAcctNo"+rowCount).focus();

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
				var tempcAcctNo = document.getElementById('txtcAcctNo' + z);
				var tempcAcctDesc = document.getElementById('txtcAcctDesc' + z);
				var tempnDebit = document.getElementById('txtnDebit' + z);
				var tempnCredit= document.getElementById('txtnCredit' + z);
				var tempnSub= document.getElementById('txtnSub' + z);
				var tempcRem= document.getElementById('txtcRem' + z);
				var tempdel = document.getElementById('row_'+z+'_delete');
				
				var x = z-1;
				tempcAcctNo.id = "txtcAcctNo" + x;
				tempcAcctNo.name = "txtcAcctNo" + x;
				tempcAcctDesc.id = "txtcAcctDesc" + x;
				tempcAcctDesc.name = "txtcAcctDesc" + x;
				tempnDebit.id = "txtnDebit" + x;
				tempnDebit.name = "txtnDebit" + x;
				tempnCredit.id = "txtnCredit" + x;
				tempnCredit.name = "txtnCredit" + x;
				tempnSub.id = "txtnSub" + x;
				tempnSub.name = "txtnSub" + x;
				tempcRem.id = "txtcRem" + x;
				tempcRem.name = "txtcRem" + x;
				tempdel.id = "row_"+x+"_delete";
				tempdel .name = "row_"+x+"_delete"; 
				//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

			}
			GoToComp("txtnDebit" + x);
			
			GoToComp("txtnCredit" + x);
	}


		function GoToComp(Nme){
			var thisname = Nme.replace(/\d+/g, '')
			var cnt = $('#MyTable tr').length;
			
			cnt = cnt - 1;

				var x = 0;
				
				for (i = 1; i <= cnt; i++) {
					x = x + parseFloat($("#"+thisname+i).val().replace(/,/g,''));
				}

			
			if(thisname=="txtnDebit"){
								
				$("#txtnDebit").val(x);
				$("#txtnDebit").autoNumeric('destroy');
				$("#txtnDebit").autoNumeric('init',{mDec:2,wEmpty:'zero'});
				
			}
			else if(thisname=="txtnCredit"){
				
				$("#txtnCredit").val(x);
				$("#txtnCredit").autoNumeric('destroy');
				$("#txtnCredit").autoNumeric('init',{mDec:2,wEmpty:'zero'});
				
			}
			
			//Compute out of balance
				if ($("#txtnDebit").val().replace(/,/g,'') >= $("#txtnCredit").val().replace(/,/g,'')){
				var xcrd = $("#txtnDebit").val().replace(/,/g,'');
				var xdeb = $("#txtnCredit").val().replace(/,/g,'');
				}
				else if($("#txtnCredit").val().replace(/,/g,'') >= $("#txtnDebit").val().replace(/,/g,'')){
				var xdeb = $("#txtnDebit").val().replace(/,/g,'');
				var xcrd = $("#txtnCredit").val().replace(/,/g,'');
				}
				else if((parseFloat($("#txtnCredit").val().replace(/,/g,'')) == 0 && parseFloat($("#txtnDebit").val().replace(/,/g,'')) == 0)){
					var xdeb = 0;
				var xcrd = 0;
				}
				
				
				txtnOutBal = Math.abs(xdeb - xcrd); 
				
				$("#txtnOutBal").val(txtnOutBal);
				$("#txtnOutBal").autoNumeric('destroy');
				$("#txtnOutBal").autoNumeric('init',{mDec:2,wEmpty:'zero'});
				

		}

	function chkform(){
		//Double Chk Journal Number
			var ISOK = "YES";
		
		if ($("#txtctranno").val()==""){

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Journal No. Required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			$("#txtctranno").focus();
			return false;
			
			ISOK = "NO";
		}
		
		//Details Checking
		var cnt = $('#MyTable tr').length;
		cnt  = parseInt(cnt)-1;
		
		for (i = 1; i <= cnt; i++) {
			if($("#txtcAcctNo"+i).val() == "" || $("#txtcAcctDesc"+i).val() == ""){
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Valid Account ID and Description is required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				$("#txtcAcctNo"+i).focus();
				
				return false;
				
				
				
				ISOK = "NO";
			}
			
			if($("#txtnCredit"+i).val().replace(/,/g,'')==0 && $("#txtnDebit"+i).val().replace(/,/g,'')==0){
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Input Debit or Credit amount for this row!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
				$("#txtnDebit"+i).focus();
				
				return false;
				
				
				
				ISOK = "NO";
			}
			
		}
		
		if(parseFloat($("#txtnDebit").val().replace(/,/g,'')) != parseFloat($("#txtnCredit").val().replace(/,/g,''))){ 
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Unbalanced details!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			return false;
			
			ISOK = "NO";
		}
		
		if(ISOK=="YES"){
			$("#hdnACCCnt").val(cnt);
			document.getElementById("frmpos").submit();
		}
		else{
			return false;
		}
	}


	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "Journal_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function printchk(x){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{
			var url = "Journal_print.php?x="+x;
			
			$("#myprintframe").attr('src',url);


			$("#PrintModal").modal('show');
			

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
				if(document.getElementById("hdnvoid").value==1){
					var msgsx = "VOIDED";
				}else{
					var msgsx = "POSTED";
				}
			}
			
			if(document.getElementById("hdncancel").value==1){
				var msgsx = "CANCELLED"
			}
			
			$("#statmsgz").html("&nbsp;&nbsp;TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!");
			//$("#statmsgz").show();
			
		}
		else{

			$("#frmpos :input").attr("disabled", false);
			
				$("#txtctranno").val($("#hdntranno").val());
				$("#txtctranno").attr("readonly", true);
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);
					
		}

	}

</script>
