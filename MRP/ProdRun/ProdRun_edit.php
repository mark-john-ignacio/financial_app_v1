<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "ProdRun";

	include('../../Connection/connection_string.php');
	//include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$tranno = $_REQUEST['txtctranno'];

	$_SESSION['myxtoken'] = gen_token();

	//EDITING
	$postedt = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'ProdRun_edit'");
	if(mysqli_num_rows($sql) == 0){
		$postedt = "False";
	}

	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);				
	}

	$arrmrpjo_pt = array();
	$sql = "select nid,ctranno,mrp_process_id,mrp_process_desc,nmachineid,DATE_FORMAT(ddatestart, \"%m/%d/%Y %h:%i:%s %p\") as ddatestart,DATE_FORMAT(ddateend, \"%m/%d/%Y %h:%i:%s %p\") as ddateend,nactualoutput,operator_id,nrejectqty,nscrapqty,cqcpostedby,cremarks,X.lpause from mrp_jo_process_t X where X.compcode='$company' and (X.ctranno in (Select ctranno from mrp_jo_process where compcode='$company' and mrp_jo_ctranno  = '$tranno') or X.ctranno='$tranno')";

	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_pt[] = $row2;				
	}
	

	$arrmachines = array();
	$sqlmrpmach = mysqli_query($con,"select A.nid, A.cdesc From mrp_machines A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetmach = $sqlmrpmach->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetmach as $row0){
		$arrmachines[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);				
	}

	$arroperators = array();
	$sqlmrpoprts = mysqli_query($con,"select A.nid, A.cdesc From mrp_operators A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetoprt = $sqlmrpoprts->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetoprt as $row0){
		$arroperators[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);		 
	}

	@$arrname = array();
	$directory = "../../Components/assets/ProdRun/{$company}_{$tranno}/";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
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

<body style="padding:5px">

	<form action="ProdRun_updatesave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">
		<input type="hidden" name="ctranno" id="ctranno" value="<?=$tranno?>" />
		<fieldset>
				<legend>			
					<div class="col-xs-6 nopadding"> Job Order Details </div>  
				</legend>

					<ul class="nav nav-tabs">
						<li class="active"><a href="#subjo">Sub-Job Details</a></li>
						<li><a href="#attc">Attachments</a></li>
					</ul>

					<div class="tab-content" style="overflow: inherit !important">  

						<div id="subjo" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px;">
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
										$arrreslx = array();
										$sql = "select X.*, A.citemdesc from mrp_jo_process X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' and X.mrp_jo_ctranno = '$tranno' Order By X.nid";
										$resultmain = mysqli_query ($con, $sql); 
										while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
											$arrreslx[] = $row2;
										}

										foreach($arrreslx as $row2){
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
									<!--MAIN -->
									<?php
										$sql = "select X.*, A.citemdesc from mrp_jo X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' and X.ctranno = '$tranno' Order By X.nid";
										$resultmain = mysqli_query ($con, $sql); 
										while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
									?>
										<tr id="tr0">
											<td><a href="javascript:;" onclick="getprocess('<?=$row2['ctranno']?>','<?=$row2['citemdesc']?>','tr0')"><?=$row2['ctranno']?></a></td>
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

							<div style="border: 1px solid #919b9c; height: 40vh; overflow: auto">
								<div id="tableContainer" class="alt2" dir="ltr" style="
									margin: 0px;
									padding: 3px;
									width: 1570px;
									height: 300px;
									text-align: left;">

										<table id="MyJOSubs" class="MyTable table-sm table-bordered" border="1">
											<thead>
												<tr>										
													<th width='200px' style="text-align: center; padding:1px">Process</th>
													<th width='200px' style="background-color: #c5def7; text-align: center; padding:1px"> Machine</th>
													<th width='158px' style="background-color: #dbfdb2; text-align: center; padding:1px"> Date Started</th>
													<th width='158px' style="background-color: #fdb2b2; text-align: center; padding:1px"> Date Ended</th>
													<th width='100px' style="background-color: #e5b2fd; text-align: center; padding:1px"> Actual Output</th>
													<th width='200px' style="background-color: #e5b2fd; text-align: center; padding:1px">Operator</th>
													<th width='80px' style="background-color: #e5b2fd; text-align: center; padding:1px">Update</th>
												</tr>
											</thead>
											<tbody class="tbody">

											</tbody>
										</table>
								</div>
							</div>

						</div>	

						<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />

						</div>

						

					</div>
						
						
					<br><br><br><br><br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">																
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='JO.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>		
								<!--<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?//=$tranno;?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
								</button>	-->
								
								<?php
									//if($editstat=="True"){
								?><!--							
								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>				
								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?//=$tranno?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
								</button>
								<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
									Edit<br>(CTRL+E)    
								</button>																										
								<button type="submit" class="btn btn-success btn-sm" tabindex="6">
									Update JO<br> (CTRL+S)
								</button>	-->	
								<?php
									//}
								?>												
							</td>
						</tr>									
					</table>


			</fieldset>

	</form>

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

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../../global/plugins/bootbox/bootbox.min.js"></script>

<script type="text/javascript">

	var list_file = [];
	var file_config = [];
	var extender;
	var file_name = <?= json_encode(@$arrname) ?>;	

	if(file_name.length != 0){
		var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
		var arrimg = new Array("jpg","png","gif","jpeg");

		/**
		 * setting up an list of file and config of a file
		 */

		file_name.map(({name, ext}, i) => {
			list_file.push("<?=$AttachUrlBase?>ProdRun/<?=$company."_".$tranno?>/" + name)
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
	}

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
					showUpload: true,
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
					initialPreviewDownloadUrl: '<?=$AttachUrlBase?>ProdRun/<?=$company."_".$tranno?>/{filename}',
					initialPreviewConfig: file_config
				});
			} else {
				$("#file-0").fileinput({
					showUpload: true,
					showClose: false,
					allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
					overwriteInitial: false,
					maxFileSize:100000,
					maxFileCount: 5,
					browseOnZoneClick: true,
					fileActionSettings: { showUpload: false, showDrag: false, }
				});
			}

	});

	$(document).on('change', 'select.ncmachine', function(e) {
		let yx = $(this).data("val");

		if($(this).val()!=""){
			$("#btnStart"+yx).removeAttr("disabled"); 
			$("#btnStart"+yx).removeClass("disabled");

			setVals(yx,"nmachineid",$(this).val());
		}
	});

	$(document).on('click', 'button.nbtnstart', function(e) {

		let yx = $(this).data("val");

		if($("#selmachine"+yx).val()==""){

			$("#AlertMsg").html("Select Machine!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}else{
	
			let yxval = moment().format('YYYY-MM-DD HH:mm:ss');

			setVals(yx,"ddatestart",yxval);

			yxval = moment().format('MM/DD/YYYY hh:mm:ss A');
			$("#tdS"+yx).html("<input type=\"text\" class=\"txtdatestart form-control input-sm text-center\" value=\""+yxval+"\" readonly style=\"cursor: pointer\" data-val=\""+yx+"\">");
			
			$("#btnEnd"+yx).removeAttr("disabled"); 
			$("#btnEnd"+yx).removeClass("disabled");

			$("#selmachine"+yx).prop('disabled', true);
			$("#txtnactual"+yx).removeAttr("disabled");
			$("#seloperator"+yx).removeAttr("disabled"); 

			$("#btnUpActual"+yx).removeClass("disabled");
			$("#btnUpActual"+yx).removeAttr("disabled");
		}

	});

	$(document).on('click', 'button.nbtnend', function(e) {

		let yx = $(this).data("val");
		let yxval = moment().format('YYYY-MM-DD HH:mm:ss');

		if($("#txtnactual"+yx).val()==0 || $("#txtnactual"+yx)=="" || $("#seloperator"+yx).val() == ""){
			$("#AlertMsg").html("Please enter your actual output and operator!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}else{
			setVals(yx,"ddateend",yxval);
			setVals(yx,"nactualoutput",$("#txtnactual"+yx).val());
			setVals(yx,"operator_id",$("#seloperator"+yx).val());

			updateLog(yx, $("#txtnactual"+yx).val(), $("#seloperator"+yx).val());

			yxval = moment().format('MM/DD/YYYY hh:mm:ss A');
			$("#tdE"+yx).html("<input type=\"text\" class=\"txtdateend form-control input-sm text-center\" value=\""+yxval+"\" readonly style=\"cursor: pointer\" data-val=\""+yx+"\" style=\"cursor: pointer\">");

			$("#txtnactual"+yx).attr("disabled", true);
			$("#seloperator"+yx).attr("disabled", true); 

			$("#btnUpActual"+yx).addClass("disabled");
			$("#btnUpActual"+yx).attr("disabled", true);
		}

	}); 

	$(document).on('click', 'button.nbtnupactual', function(e) {
		let yx = $(this).data("val");

		setVals(yx,"nactualoutput",$("#txtnactual"+yx).val());
		setVals(yx,"operator_id",$("#seloperator"+yx).val());
		updateLog(yx, $("#txtnactual"+yx).val(), $("#seloperator"+yx).val());

		$("#AlertMsg").html("Actual output and operator updated!");
		$("#alertbtnOK").show();
		$("#AlertModal").modal('show');
	});

	$(document).on('click', 'input.txtdateend', function(e) {
		let yx = $(this).data("val");

		bootbox.prompt({
			title: 'Enter reason for resetting Date Ended.',
			inputType: 'text',
			centerVertical: true,
			callback: function (result) {
				if(result==""){					
					bootbox.alert({
						message: "Reason for resetting Date Ended is required!",
						size: "small",
						className: "bootalert"
					});
				}else if(result!=""){
					setVals(yx,"ddateend","", result);

					$("#tdE"+yx).html("<button type=\"button\" class=\"nbtnend btn btn-danger btn-sm btn-block\" id=\"btnEnd"+yx+"\" data-val=\""+yx+"\">End</button>");

					$("#txtnactual"+yx).attr("disabled", false);
					$("#seloperator"+yx).attr("disabled", false); 

					$("#btnUpActual"+yx).removeClass("disabled");
					$("#btnUpActual"+yx).attr("disabled", false);
				}				
			}
		});
	});

	$(document).on('click', 'input.txtdatestart', function(e) {
		let yx = $(this).data("val");

		$vsar = getlogs(yx);

		if(parseInt($vsar)==0){
			bootbox.prompt({
				title: 'Enter reason for resetting Date Start.',
				inputType: 'text',
				centerVertical: true,
				callback: function (result) {
					if(result==""){					
						bootbox.alert({
							message: "Reason for resetting Date Start is required!",
							size: "small",
							className: "bootalert"
						});
					}else if(result!=""){
						setVals(yx,"ddatestart","", result);

						$("#tdS"+yx).html("<button type=\"button\" class=\"nbtnstart btn btn-success btn-sm btn-block\" id=\"btnStart"+yx+"\" data-val=\""+yx+"\" >Start</button>");
						
						$("#txtnactual"+yx).attr("disabled", true);
						$("#seloperator"+yx).attr("disabled", true); 

						$("#btnUpActual"+yx).addClass("disabled");
						$("#btnUpActual"+yx).attr("disabled", true);
					}				
				}
			});
		}else{
			bootbox.alert({
				message: "You cannot reset Date Start because of Actual Output logs!",
				size: "small",
				className: "bootalert"
			});
		}
	});

	function getprocess($xtran,$xitms,$trid){
		var file_name = '<?= json_encode($arrmrpjo_pt) ?>';
		var file_machines = '<?= json_encode($arrmachines) ?>';
		var file_operators = '<?= json_encode($arroperators) ?>';		   

		$('tr').removeClass("selectedJO");

		$("#"+$trid).addClass("selectedJO");

		$("#subjodets").html("<h5>"+$xtran+": "+$xitms+"<h5>");
		$("#MyJOSubs tbody").empty(); 

		var lqcnext = 1;
		var obj = jQuery.parseJSON(file_name);
		$.each(obj, function(key,value) {
			if(value.ctranno == $xtran){

				lastRow = value.nid;

				var dreject ="";
				var dscrap = "";
				var dactual = "";
				var tdmach = "";
				var tdoper = "";
				var dstart = "";
				var deend = "";

				//if(lqcnext==1){

					//machine Select
					machoptions = "";
					var xmachine = value.nmachineid;
					$.each(jQuery.parseJSON(file_machines), function() { 
						let xstat = "";
						if(xmachine==this['nid']){
							xstat = "selected";
						}
						machoptions = machoptions + "<option value='"+this['nid']+"' "+xstat+">"+this['cdesc']+"</option>";
					});

					var mach_stat = "";
					if ((value.ddatestart!="null" && value.ddatestart!=null && value.ddatestart!="") || value.lpause==1) {
						mach_stat = "disabled";
					}
					tdmach = "<select class='ncmachine form-control input-xs "+mach_stat+"' name=\"selmachine\" id=\"selmachine"+lastRow+"\" data-val=\""+lastRow+"\" "+mach_stat+"><option></option>" + machoptions + "</select>";
					
					var x = value.ddatestart;
					if (x!="null" && x!=null && x!="") {
						dstart = "<input type=\"text\" class=\"txtdatestart form-control input-sm text-center\" value=\""+value.ddatestart+"\" readonly style=\"cursor: pointer\" data-val=\""+lastRow+"\">";
					}else{
						let stat = "disabled";
						if(value.nmachineid !=0){
							stat = "";
						}
						dstart = "<button type=\"button\" class=\"nbtnstart btn btn-success btn-sm btn-block "+stat+"\" id=\"btnStart"+lastRow+"\" data-val=\""+lastRow+"\" "+stat+">Start</button>";
					}
					
					var x = value.ddateend;
					if (x!="null" && x!=null && x!="") {
						deend = "<input type=\"text\" class=\"txtdateend form-control input-sm text-center\" value=\""+value.ddateend+"\" readonly style=\"cursor: pointer\" data-val=\""+lastRow+"\">";
					}else{
						let stat = "disabled";
						let y = value.ddatestart;
						if((y!="null" && y!=null && y!="") && value.lpause==0){
							stat = "";
						}
						deend = "<button type=\"button\" class=\"nbtnend btn btn-danger btn-sm btn-block "+stat+"\" id=\"btnEnd"+lastRow+"\" data-val=\""+lastRow+"\" "+stat+">End</button>";
					}
					
					var entr_stat = "disabled";
					if ((value.ddatestart!="null" && value.ddatestart!=null && value.ddatestart!="" && (value.ddateend=="" || value.ddateend==null || value.ddateend=="null")) && value.lpause==0) {
						entr_stat = "";
					}
					//operators Select
					opeoptions = "";
					var xoperator = value.operator_id;
					$.each(jQuery.parseJSON(file_operators), function() { 
						let xstat = "";
						if(xoperator==this['nid']){
							xstat = "selected";
						}
						opeoptions = opeoptions + "<option value='"+this['nid']+"' "+xstat+">"+this['cdesc']+"</option>";
					});

					tdoper = "<select class='form-control input-xs "+entr_stat+"' id=\"seloperator"+lastRow+"\" data-val=\""+lastRow+"\" "+entr_stat+"><option></option>" + opeoptions + "</select>";  

					dactual = "<input type=\"text\" class=\"form-control input-sm text-right\" id=\"txtnactual"+lastRow+"\" data-val=\""+lastRow+"\" value=\""+value.nactualoutput+"\" "+entr_stat+">";
					
					dupdact = "<button type=\"button\" class=\"nbtnupactual btn btn-warning btn-sm btn-block "+entr_stat+" \" id=\"btnUpActual"+lastRow+"\" data-val=\""+lastRow+"\" "+entr_stat+">Update Output</button>";

				//}

				if(value.lpause==1){
					$addmsg = " <span class=\"text-danger\"> (ON PAUSE)</span>";
				}else{
					$addmsg = "";
				}

				var tdprocess = "<td style='padding:1px'>"+value.mrp_process_desc+$addmsg+"</td>";
				var tdmachine = "<td style='padding:1px'>"+tdmach+"</td>";
				var tddatest = "<td style='padding:1px' id=\"tdS"+lastRow+"\">"+dstart+"</td>";
				var tddateen = "<td style='padding:1px' id=\"tdE"+lastRow+"\">"+deend+"</td>";
				var tdactual = "<td style='padding:1px'>"+dactual+"</td>";
				var tdoperator = "<td style='padding:1px'>"+tdoper+"</td>";
				var tdupdtebtn = "<td style='padding:1px'>"+dupdact+"</td>";
				
				$('#MyJOSubs > tbody:last-child').append('<tr>'+tdprocess + tdmachine + tddatest + tddateen + tdactual + tdoperator + tdupdtebtn + '</tr>');


				$("#selmachine"+lastRow).select2({
					placeholder: "Please select the machine"
				});

				$("#seloperator"+lastRow).select2({
					placeholder: "Please select the operator"
				});

				machoptions = "";
				lqcnext = value.lqcposted;
			}

		}); 
	}

	function setVals(processid, colnme, colval, resetmsg = ""){
		var $issset = "False";
		$.ajax ({
			url: "th_setstat.php",
			data: { processid: processid,  colnme: colnme, colval: colval, resetmsg: resetmsg },
			async: false,
			dataType: "text",
			success: function( data ) {
				$issset = data;
			}			
		});
	}

	function updateLog(processid, actualoutput, actualoperator){
		var $issset = "False";
		$.ajax ({
			url: "th_updatelog.php",
			data: { processid: processid,  actualoutput: actualoutput, actualoperator: actualoperator },
			async: false,
			dataType: "text",
			success: function( data ) {
				$issset = data;
			}			
		});
	}

	function getlogs(processid){
		var $issset = "False";
		$.ajax ({
			url: "th_getlog.php",
			data: { processid: processid },
			async: false,
			dataType: "text",
			success: function( data ) {
				$issset = data;
			}			
		});

		return $issset;
	}

</script>
