<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "RFP_edit.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$ccvno = $_REQUEST['txtctranno'];

	//echo $_SERVER['SERVER_NAME'];

	$sqlchk = mysqli_query($con,"select a.*, b.cname, e.cname as cbankname from rfp a left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno='$ccvno'");

	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../RFP_Files/'.$company.'_'.$ccvno.'/')) {
		$allfiles = scandir('../../RFP_Files/'.$company.'_'.$ccvno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		echo "NO FILES";
	}

	//echo json_encode(@$arrname);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
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

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">

<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

<?php
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

			$cCode = $row['ccode'];
			$cName = $row['cname'];
			
			$cpaymeth = $row['cpaymethod']; 

			$cBank = $row['cbankcode'];
			$cBankName = $row['cbankname'];
			$cRefAPVNo = $row['capvno'];

			$dTransdate = $row['dtransdate'];

			$cnAmount = $row['ngross']; 
			$cnBalamt = $row['nbalance'];

			$cdRemarks = $row['cremarks'];
		
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
		}
?>
	<form action="RFP_editsave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data" onsubmit="return chkform()">
		<fieldset>
				<legend>Request For Payment Details</legend>
				
					<table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
							<tH>Tran No.:</tH>
							<td>
								<div class="col-xs-12"  style="padding-left:2px">
									<div class="col-xs-4 nopadding">
										<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
										</div>
									</div>
								
								<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
								
								<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
								<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
								
							</td>
							<td colspan="2"><div id="statmsgz" style="display:inline"></div></td>
							
						</tr>

						<tr>
							<td><span style="padding:2px"><b>Paid To:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px">
									<div class="col-xs-4 nopadding ">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly value="<?=$cCode?>">
									</div>
									<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4" value="<?=$cName?>">
									</div>
								</div>
							</td>
							<td><span style="padding:2px"><b>APV No.:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class="col-xs-6 nopadding">
										<input type="text" class="form-control input-sm required" id="txtrefapv" name="txtrefapv" width="20px" placeholder="Search APV No..." required autocomplete="off" tabindex="4" readonly value="<?=$cRefAPVNo?>">
									</div>
									<div class="col-xs-2 nopadwleft">
										<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchapv" id="btnsearchapv"><i class="fa fa-search"></i></button>
									</div>

								</div>
							</td>
						</tr>
					
						<tr>
						<td width="150"><span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px" id="paymntdescdet">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtBank" class="form-control input-sm required" name="txtBank" value="<?=$cBank?>" placeholder="Bank Code" readonly required>
									</div>
									<div class="col-xs-1 nopadwleft">
										<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchbank" id="btnsearchbank"><i class="fa fa-search"></i></button>
									</div>
									<div class="col-xs-8 nopadwleft">
										<input type="text" class="form-control input-sm required" id="txtBankName" name="txtBankName" width="20px" tabindex="1" placeholder="Bank Name..." required value="<?=$cBankName?>" autocomplete="off" readonly>
									</div>
									
								</div>

							</td>
							<td width="150"><span style="padding:2px"><b>Payment Method</b></span></td>
							<td>
								<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
									<div class="col-xs-8 nopadding">
										<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
											<option value="cheque" <?=($cpaymeth=="cheque") ? "selected" : ""?>>Cheque</option>
											<option value="cash" <?=($cpaymeth=="cash") ? "selected" : ""?>>Cash</option>
											<option value="bank transfer" <?=($cpaymeth=="bank transfer") ? "selected" : ""?>>Bank Transfer</option>
											<option value="mobile payment" <?=($cpaymeth=="mobile payment") ? "selected" : ""?>>Mobile Payment</option>
											<option value="credit card" <?=($cpaymeth=="credit card") ? "selected" : ""?>>Credit Card</option>
											<option value="debit card" <?=($cpaymeth=="debit card") ? "selected" : ""?>>Debit Card</option>
										</select>
									</div>
							</td>
							
						</tr>

						<tr>
							<td><span style="padding:2px"><b>Remarks</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<input type="text" class="form-control input-sm" id="txtcremarks" name="txtcremarks" tabindex="1" placeholder="Remarks..." value="<?=$cdRemarks?>" autocomplete="off">
								</div>
							</td>
							<td><span style="padding:2px" id="chkdate"><b>Due Date:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class='col-xs-8 nopadding'>
											<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?=date("m/d/Y",strtotime($dTransdate));?>" />
									</div>
								</div>
							</td>							
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><span style="padding:2px" id="chkdate"><b>Amount to Pay:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class='col-xs-8 nopadding'>
											<input type='text' class="form-control input-sm text-right" name="txtnamount" id="txtnamount" value="<?=$cnAmount?>" />
											<input type='hidden' name="txtnamountbal" id="txtnamountbal" value="<?=$cnBalamt?>" />   
									</div>
								</div>
							</td>
						</tr>

					</table>

					<h4>Attachments <small><i>(jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></small></h4> 
					<input id="file-0" name="upload[]" type="file" multiple>

					<br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
													
											<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RFP.php';" id="btnMain" name="btnMain">
												Back to Main<br>(ESC)
											</button>
										
											<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='RFP_new.php';" id="btnNew" name="btnNew">
												New<br>(F1)
											</button>

											<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
												Undo Edit<br>(CTRL+Z)
											</button>
									
											<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$ccvno?>');" id="btnPrint" name="btnPrint">
												Print<br>(F4)
											</button>
								    
											<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
												Edit<br>(CTRL+E)
											</button>
											
											<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
												Save<br>(CTRL+S)
											</button>
											
							</td>
						</tr>
						
					</table>

			</fieldset>

	</form>

<?php
	}else{
?>
	<form action="RFP_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Request For Payment</legend>	
			<table width="100%" border="0">
				<tr>
					<tH width="100">Tran No.:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
					</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Transaction No. DID NOT EXIST!</b></font></tH>
					</tr>
			</table>
		</fieldset>
	</form>
<?php
	}
?>

				<!-- DETAILS ONLY -->
				<div class="modal fade" id="myAPModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">AP List</h3>
							</div>
										
							<div class="modal-body pre-scrollable">
										
								<table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>AP No.</th>
											<th>Date</th>
											<th>Payment For</th>
											<th>Payable Amount</th>
										</tr>
									</thead>
									<tbody>
																			
									</tbody>
								</table>

							</div> 
										
							<div class="modal-footer">
								<button type="button" id="btnSave2" onClick="InsertSI()" class="btn btn-primary">Insert</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->

				<!-- Banks List -->
				<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="BanksListHeader">Bank List</h3>
							</div>
							
							<div class="modal-body pre-scrollable">
							
								<table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>Bank Code</th>
											<th>Bank Name</th>
											<th>Bank Acct No</th>
										</tr>
									</thead>
									<tbody>																
									</tbody>
								</table>
							</div>         	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Banks modal -->

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

				<!-- PRINT OUT MODAL-->
				<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog modal-lg">
								<div class="modal-contnorad">   
										<div class="modal-bodylong">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
								
											<iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
						
											
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
		fileslist.push("https://<?=$_SERVER['HTTP_HOST']?>/RFP_Files/<?=$ccvno?>/" + object.name)

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
			url: "th_filedelete.php?id="+object.name+"&code=<?=$ccvno?>", 
			key: i + 1
		});
	}

	$(document).keydown(function(e) {	 
		
		if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='RFP_new.php';
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
				printchk('<?=$ccvno?>');
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

	$(document).ready(function() {

		$("#txtnamount").autoNumeric('init',{mDec:2,wEmpty:'zero'});
		
		$('#txtChekDate').datetimepicker({
			format: 'MM/DD/YYYY',
		});


		if(fileslist.length>0){
			$("#file-0").fileinput({
				theme: 'fa5',
				uploadUrl: '#',
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:2000,
				maxFileCount: 5,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: fileslist,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/RFP_Files/<?=$company."_".$ccvno?>/{filename}',
				initialPreviewConfig: filesconfigs
			});
		}else{
			$("#file-0").fileinput({
				theme: 'fa5',
				uploadUrl: '#',
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:2000,
				maxFileCount: 5,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}


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
				return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 

				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);
					
			//	showapvmod(item.id);

			}
		});

		$("#btnsearchbank").on("click", function() {

			$('#MyDRDetList tbody').empty();
			
				$.ajax({
					url: 'th_banklist.php',
					dataType: 'json',
					async:false,
					method: 'post',
					success: function (data) {
					// var classRoomsTable = $('#mytable tbody');
						console.log(data);
						$.each(data,function(index,item){

								$("<tr id=\"bank"+index+"\">").append(
									$("<td>").text(item.ccode),
									$("<td>").text(item.cname),
									$("<td>").text(item.cbankacctno)
								).appendTo("#MyDRDetList tbody");
									
							$("#bank"+index).on("click", function() {
								$("#txtBank").val(item.ccode);
								$("#txtBankName").val(item.cname);
								
								$("#myChkModal").modal("hide");
							});

						});

					},
					error: function (req, status, err) {

						$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

						console.log('Something went wrong', status, err);
					}
				});

			
			$("#myChkModal").modal("show");
		});
		
		$("#btnsearchapv").on("click", function() {
			var custid = $("#txtcustid").val();
			showapvmod(custid)
		});

		disabled();

	});
			
	function showapvmod(custid){

		$('#MyAPVList tbody').empty();

		$.ajax({
			url: 'th_APVlist.php',
			data: { code: custid },
			dataType: 'json',
			async:false,
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){
							
					if(item.ctranno=="NO"){
						alert("No Available Reference.");
										
						$('#txtcust').val("").change(); 
						$("#txtcustid").val("");

					}
					else{
				
						$("<tr id=\"APV"+index+"\">").append(
							$("<td>").html("<a href='javascript:;' onclick='InsertSI("+index+")'>"+item.ctranno+"</a> <input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'>"),
							$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
							$("<td>").html(item.cpaymentfor+"<input type='hidden' id='APVPayFor"+index+"' name='APVPayFor"+index+"' value='"+item.cpaymentfor+"'>"),
							$("<td>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'>")
						).appendTo("#MyAPVList tbody");
										
						$("#myAPModal").modal("show");
									
					}

				});

			},
			error: function (req, status, err) {

				$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				console.log('Something went wrong', status, err);
			}
		});

	}

	function InsertSI(xyz){	
	    
		var a = $("#APVtxtno"+xyz).val();

		$("#txtrefapv").val(a);
		$('#myAPModal').modal('hide');
  
	};

	function chkform(){
		
		var emptyFields = $('input.required').filter(function() {
			return $(this).val() === "";
		}).length;

		if (emptyFields === 0) {
			return true;
		} else {
			
			$("#AlertMsg").html("<b>ERROR: </b>Required Fields!<br>Supplier Code/Name, Bank Code/Name, and APV No.");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
		}

	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "RFP_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){

		$("#frmpos :input").attr("disabled", true);
		
		$("#txtctranno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);
		$(".kv-file-zoom").attr("disabled", false);

	}

	function enabled(){
		if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
			if(document.getElementById("hdnposted").value==1){
				var msgsx = "POSTED"
			}
			
			if(document.getElementById("hdncancel").value==1){
				var msgsx = "CANCELLED"
			}
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{

			$("#frmpos :input").attr("disabled", false);
			
				
				$("#txtctranno").attr("readonly", true);
				$("#txtctranno").val($("#hdnorigNo").val());
				
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);		
		}

	}

	function printchk(x){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{

				var url = "RFP_print.php?x="+x;
				
				$("#myprintframe").attr('src',url);


				$("#PrintModal").modal('show');

		}
	}
</script>
