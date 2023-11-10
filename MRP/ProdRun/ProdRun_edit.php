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

	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);				
	}

	$arrmrpjo_pt = array();
	$sql = "select * from mrp_jo_process_t X where X.compcode='$company' and X.ctranno in (Select ctranno from mrp_jo_process where compcode='$company' and mrp_jo_ctranno  = '$tranno')";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_pt[] = $row2;				
	}

	@$arrname = array();
	$directory = "../../Components/assets/JOR/{$company}_{$tranno}/";
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
								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$tranno;?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
								</button>	
								
								<?php
									if($editstat=="True"){
								?>									
								<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
									Undo Edit<br>(CTRL+Z)
								</button>				
								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$tranno?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
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

		disabled();

	});

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

				var tdmachine = "<td>&nbsp;</td>";
				var tdprocess = "<td>"+value.mrp_process_desc+"</td>";
				var tddatest = "<td>&nbsp;</td>";
				var tddateen = "<td>&nbsp;</td>";
				var tdactual = "<td>&nbsp;</td>";
				var tdoperator = "<td>&nbsp;</td>";
				var tdreject = "<td>&nbsp;</td>";
				var tdscrap = "<td>&nbsp;</td>";
				var tdqc = "<td>&nbsp;</td>";
				var tdrems = "<td>&nbsp;</td>";

				//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
				
				$('#MyJOSubs > tbody:last-child').append('<tr>'+tdmachine + tdprocess + tddatest + tddateen + tdactual + tdoperator + tdreject + tdscrap + tdqc + tdrems + '</tr>');

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

</script>
