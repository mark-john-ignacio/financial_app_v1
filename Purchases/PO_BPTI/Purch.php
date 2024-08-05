<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Purch";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	//get users, post cancel and send access
	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Purch_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Purch_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	//UNPOST
	$unpostat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Purch_unpost'");
	if(mysqli_num_rows($sql) == 0){
		$unpostat = "False";
	}

	//"Select * from purchase_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 Group BY cpono HAVING nlevel = MIN(nlevel) Order By cpono, nlevel"
	$chkapprovals = array();
	$sqlappx = mysqli_query($con,"Select A.* FROM purchase_trans_approvals A left join (Select cpono, MIN(nlevel) as nlevel from purchase_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 Group By cpono Order By cpono, nlevel) B on A.cpono=B.cpono where A.compcode='$company' and A.lapproved=0 and A.lreject=0 and A.nlevel=B.nlevel");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			@$chkapprovals[] = $rows; 
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
	<META NAME="robots" CONTENT="noindex,nofollow">

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">

	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-daterangepicker/daterangepicker.css?x=<?=time()?>"/>

	<link href="../../global/css/components.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>

</head>

<body style="padding:5px">
	<div>

		<div class="row">
			<div class="col-xs-12">
				<font size="+2"><u>Purchase Order List</u></font>	
          	</div>
        </div>

		<div class="row">
			<div class="col-xs-12">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='Purch_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

					<?php
						if($unpostat=="True"){
					?>
						<button type="button" class="btn btn-danger btn-sm" onClick="location.href='Purch_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
					<?php
						}
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12" style="padding-top: 5px !important">
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control" placeholder="Search Supplier, PO No, Reference...">
				</div>
				<div class="col-xs-2 text-right nopadwleft">
					<select  class="form-control" name="selstats" id="selstats">
						<option value=""> All Status</option>
						<option value="post" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="post") ? "selected" : "" ) : "";?>> Posted </option>
						<option value="cancel" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="cancel") ? "selected" : "" ) : "";?>> Cancelled </option>
						<option value="void" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="void") ? "selected" : "" ) : "";?>> Voided </option>
						<option value="pending" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="pending") ? "selected" : "" ) : "";?>> Pending </option>
						<option value="approve" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="approve") ? "selected" : "" ) : "";?>> For Approval </option>
					</select>
				</div>
				<div class="col-xs-1 text-right nopadwleft">
					<select  class="form-control" name="selstypes" id="selstypes">
						<option value=""> All Types</option>
						<option value="0" <?=(isset($_REQUEST['stype'])) ? (($_REQUEST['stype']=="0") ? "selected" : "" ) : "";?>> Credit </option>
						<option value="1" <?=(isset($_REQUEST['stype'])) ? (($_REQUEST['stype']=="1") ? "selected" : "" ) : "";?>> Advance </option>
					</select>
				</div>
				<div class="col-xs-2 text-right nopadwleft">
					<select  class="form-control" name="seldtfl" id="seldtfl">
						<option value="A.ddate" <?=(isset($_REQUEST['sdtf'])) ? (($_REQUEST['sdtf']=="A.ddate") ? "selected" : "" ) : "";?>>Encoding Date </option>
						<option value="A.dcutdate" <?=(isset($_REQUEST['sdtf'])) ? (($_REQUEST['sdtf']=="A.dcutdate") ? "selected" : "" ) : "";?>>PO Date </option>
						<option value="A.dneeded" <?=(isset($_REQUEST['sdtf'])) ? (($_REQUEST['sdtf']=="A.dneeded") ? "selected" : "" ) : "";?>>Date Needed </option>
					</select>
				</div>
				<div class="col-xs-3 nopadwleft">
					<div class="input-group input-large">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>										
						</span>
						<input type="text" class="form-control" id="dtefilter" name="dtefilter" placeholder="Date Range..." readonly style="cursor: pointer">
						<span class="input-group-addon" style="cursor: pointer" id="cleardate">	 
							<i class="fa fa-times"></i>								
						</span>
						<input type="hidden" id="dtefilterfrom" value="<?=(isset($_REQUEST['dtfr'])) ? $_REQUEST['dtfr'] : date('Y-m-d', strtotime('-7 days')) ;?>">
						<input type="hidden" id="dtefilterto" value="<?=(isset($_REQUEST['dtto'])) ? $_REQUEST['dtto'] : date("Y-m-d");?>">
					</div>

				</div>
			</div>
		</div>

		<hr>
		<table id="example" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th class="text-center">PO No</th>
					<th class="text-center">Reference</th>
					<th class="text-center" style="min-width: 400px">Supplier</th>
					<th class="text-center">Gross</th>
					<th class="text-center">PO Date</th>
					<th class="text-center">Status</th>
					<th class="text-center">Actions</th>
				</tr>
			</thead>
		</table>

	</div>		
    
	<form name="frmedit" id="frmedit" method="post" action="Purch_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" />
		<input type="hidden" name="hdnsrchval" id="hdnsrchval" />
		<input type="hidden" name="hdnsrchsta" id="hdnsrchsta" />
		<input type="hidden" name="hdnsrchtyp" id="hdnsrchtyp" />
		<input type="hidden" name="hdnsrchdte" id="hdnsrchdte" />
		<input type="hidden" name="hdnsrchdtef" id="hdnsrchdtef" />
		<input type="hidden" name="hdnsrchdtet" id="hdnsrchdtet" />
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

	<!-- 1) Alert Modal For Filter -->
	<div class="modal fade" id="AlertFilterMod" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-top">
				<div class="modal-content">
				<div class="alert-modal-danger">
					<p id="AlertMsgFil"></p>
					<p>
						<center>
							<button type="button" class="btn btn-primary btn-sm" id="OKFil" onclick="trans_filtr('OK')">Ok</button>
							<button type="button" class="btn btn-danger btn-sm" id="CancelFil" onclick="trans_filtr('Cancel')">Cancel</button>
							
							
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnFilOK">Ok</button>
							
							<input type="hidden" id="dtrfromx" value = ""> 
							<input type="hidden" id="dtrtox" value = ""> 

						</center>
					</p>
				</div> 
				</div>
			</div>
		</div>
	</div>

	<!-- 1) TRACKER Modal -->
	<div class="modal fade" id="TrackMod" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">PO Approval Status</h3>
				</div>
						
				<div class="modal-body pre-scrollable" id="divtracker" style="height: 45vh">
					
				</div>

			</div>
		</div>
	</div>

	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>	

	<script type="text/javascript" src="../../global/plugins/bootstrap-daterangepicker/moment.min.js?x=<?=time()?>"></script>
	<script type="text/javascript" src="../../global/plugins/bootstrap-daterangepicker/daterangepicker.js?x=<?=time()?>"></script>

	<script src="../../global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
		
		$(document).on("keyup",function(e){ 
			if(e.keyCode == 112) { //F2
				window.location = "Purch_new.php";
			}
		});

		$(document).ready(function() {
			
			fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>", $('#selstats').val(), $('#selstypes').val(), $('#seldtfl').val(), $('#dtefilterfrom').val(), $('#dtefilterto').val());	

			<?php
				if(isset($_REQUEST['dtfr']) && isset($_REQUEST['dtto'])){
					if($_REQUEST['dtfr'] !="" && $_REQUEST['dtto'] !=""){
			?>
				$('#dtefilter').daterangepicker({
					"autoApply": true,
					"opens": 'left',
					"format": 'MM/DD/YYYY',
					"startDate": moment($('#dtefilterfrom').val()).format('MM/DD/YYYY'),
					"endDate": moment($('#dtefilterto').val()).format('MM/DD/YYYY')
				});  
			<?php
					}else{
						?>
						$('#dtefilter').daterangepicker({
							"autoApply": true,
							"opens": 'left',
							"format": 'MM/DD/YYYY'
						});  

						$('#dtefilter').val('');
						<?php
					}
				}else{
			?>
				$('#dtefilter').daterangepicker({
					"autoApply": true,
					"opens": 'left',
					"format": 'MM/DD/YYYY'
				});  

				$('#dtefilter').val('');
				$('#dtefilterfrom').val('');
				$('#dtefilterto').val('');
			<?php
				}
			?>

			$('#dtefilter').on('apply.daterangepicker', function(ev, picker) {

				$('#dtefilterfrom').val(picker.startDate.format('YYYY-MM-DD'));
				$('#dtefilterto').val(picker.endDate.format('YYYY-MM-DD'));

				filter_check();

			});

			$("#cleardate").on("click", function(){

				$('#dtrfromx').val($('#dtefilterfrom').val());
				$('#dtrtox').val($('#dtefilterto').val());

				$('#dtefilter').val('');
				$('#dtefilterfrom').val('');
				$('#dtefilterto').val('');

				filter_check();
				
			});

			$("#searchByName").keyup(function(){
				filter_check();
			});

			$("#selstats, #selstypes, #seldtfl").change(function(){
				filter_check();
			});
				
			$('body').tooltip({
				selector: '.canceltool',
				title: fetchData,
				html: true,
				placement: 'top'
			});

			function fetchData()
			{
				var fetch_data = '';
				var element = $(this);
				var id = element.attr("data-id");
				var stat = element.attr("data-stat");
				$.ajax({
					url:"../../include/fetchcancel.php",
					method:"POST",
					async: false,
					data:{id:id, stat:stat},
					success:function(data)
					{
						fetch_data = data;
					}
				});   
				return fetch_data;
			}

		});

		function filter_check(){
			var searchByName = $('#searchByName').val();
			var searchBystat = $('#selstats').val();
			var searchByselstypes = $('#selstypes').val();
			var searchBydtfil = $('#seldtfl').val();
			var searchBydtfr = $('#dtefilterfrom').val();
			var searchBydtto = $('#dtefilterto').val();

			if(searchByName=="" && searchBystat=="" && searchByselstypes=="" && searchBydtfr=="" && searchBydtto==""){
				$("#AlertMsgFil").html("&nbsp;&nbsp;<b>Warning!: </b> Loading data without any filters may cause slowdown.<br> Do you want to continue?");
				$("#alertbtnFilOK").css("display", "none");
				$("#OKFil").css("display", "inline");
				$("#CancelFil").css("display", "inline");

				$("#AlertFilterMod").modal("show");
			}else{

				$('#example').DataTable().destroy();
				fill_datatable(searchByName,searchBystat,searchByselstypes,searchBydtfil,searchBydtfr,searchBydtto);
			}
		}

		function trans_filtr($btnclick){
			if($btnclick=="OK"){
				var searchByName = $('#searchByName').val();
				var searchBystat = $('#selstats').val();
				var searchByselstypes = $('#selstypes').val();
				var searchBydtfil = $('#seldtfl').val();
				var searchBydtfr = $('#dtefilterfrom').val();
				var searchBydtto = $('#dtefilterto').val();

				$('#example').DataTable().destroy();
				fill_datatable(searchByName,searchBystat,searchByselstypes,searchBydtfil,searchBydtfr,searchBydtto);
			}else{
				$('#dtefilterfrom').val($('#dtrfromx').val());
				$('#dtefilterto').val($('#dtrtox').val());

				var start_date = moment($('#dtefilterfrom').val()).format('MM/DD/YYYY');
				var end_date = moment($('#dtefilterto').val()).format('MM/DD/YYYY');

				$('#dtefilter').data('daterangepicker').setStartDate(start_date);
				$('#dtefilter').data('daterangepicker').setEndDate(end_date);

			}
			$("#AlertFilterMod").modal("hide");
		}

		function editfrm(x){
			$('#txtctranno').val(x);
			$('#hdnsrchval').val($('#searchByName').val()); 
			$('#hdnsrchsta').val($('#selstats').val());
			$('#hdnsrchtyp').val($('#selstypes').val());
			$('#hdnsrchdte').val($('#seldtfl').val());
			$('#hdnsrchdtef').val($('#dtefilterfrom').val());
			$('#hdnsrchdtet').val($('#dtefilterto').val());
			document.getElementById("frmedit").submit();
		}

		function trans(x,num){

			$("#typ").val(x);
			$("#modzx").val(num);

			$("#AlertMsg").html("");

			if(x=="CANCEL1"){
				x = "CANCEL";
			}
								
			$("#AlertMsg").html("Are you sure you want to "+x+" PO No.: "+num);
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');

		}

		function track(xno){

			$.ajax({
       			type: "POST",
        		url: "th_getapprovers.php",
				data: 'x='+xno,
				//contentType: "application/json; charset=utf-8",
				success: function(result) {
					$("#divtracker").html(result);
				}
      		});


			$("#TrackMod").modal("show");
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
					url: "Purch_Tran.php",
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
						setmsg(data,num);
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
								url: "Purch_Tran.php",
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

		function setmsg(data,num){
			$.each(data,function(key,value){				
				if(value.stat!="False"){
					$("#msg"+num).html(value.stat);
										
					$("#AlertMsg").html("");
						
						$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+value.stat+"...");
						$("#alertbtnOK").show();
						$("#OK").hide();
						$("#Cancel").hide();
						$("#AlertModal").modal('show');

					

				}
				else{
				//	alert(item.ms);

					
					$("#AlertMsg").html("");
					
					$("#AlertMsg").html(value.ms);
					$("#alertbtnOK").show();
					$("#OK").hide();
					$("#Cancel").hide();
					$("#AlertModal").modal('show');
					

				}
			});
		}

		function fill_datatable(searchByName = '', searchBystat = '',searchByselstypes = '',searchBydtfil = '',searchBydtfr = '', searchBydtto = '')
		{
			var dataTable = $('#example').DataTable({
				stateSave: true,
				"processing" : true,
				"serverSide" : true,
				"lengthChange": true,
				"order" : [],
				"searching" : false,
				"ajax" : {
					url:"th_datatable.php",
					type:"POST",
					data:{
						searchByName:searchByName, searchBystat:searchBystat, searchByselstypes:searchByselstypes, searchBydtfil:searchBydtfil ,searchBydtfr:searchBydtfr, searchBydtto:searchBydtto
					}
				},
				"columns": [
					{ "data": null,
						"render": function (data, type, full, row) {
							var sts = "";
							if (full[5] == 1 || full[8] == 1) {
								sts="class='text-danger'";
							}

							return "<a "+sts+" href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";
								
						}
							
					},
					{ "data": 6 },
					{ "data": null,
						"render": function (data, type, full, row) {

							return full[1]+" - "+full[2];
								
						}
							
					},
					{ "data": 9 },
					{ "data": 3 },
					{ "data": null,
							"render": function (data, type, full, row) {
	
								if(full[7] == 0 && full[5]==0){
									return "For Sending";
								}else{
									if (full[4] == 0 && (full[5] == 0)) {
										var chkrejstat = "Pending";
										var xcz = '<?=json_encode(@$chkapprovals)?>';
										if(xcz!=""){
											$.each( JSON.parse(xcz), function( key, val ) {
												if(val.cpono==full[0] && val.userid=='<?=$employeeid?>'){
													chkrejstat = "For Approval";
												}
												
											});
										}
										return chkrejstat;
									}else{
										if (full[4] == 1) {		
											if(full[8] == 1){
												return '<a href="#" class="canceltool" data-id="'+full[0]+'" data-stat="VOID" style="color: red !important"><b>Voided</b></a>';
											}else{
												return 'Posted';
											}			
																					
										}else if (full[5] == 1) { //12 sent 13 void 4 apprve 5 cancel
											return '<a href="#" class="canceltool" data-id="'+full[0]+'" data-stat="CANCELLED" style="color: red !important"><b>Cancelled</b></a>';
										}else{
											var chkrejstat = "Pending";
											var xcz = '<?=json_encode(@$chkapprovals)?>';
											if(xcz!=""){
												$.each( JSON.parse(xcz), function( key, val ) {
													if(val.cpono==full[0] && val.userid=='<?=$employeeid?>'){
														chkrejstat = "For Approval";
													}
													
												});
											}


											return chkrejstat;
										}
									}
								}
								
							}
						},
						{ "data": null,		
								"render": function (data, type, full, row) {

									$msgx = "";
									if(full[7] == 0 && full[5]==0){

										$msgx = "<a href=\"javascript:;\" onClick=\"trans('SEND','"+full[0]+"')\" class=\"btn btn-icon-only white\"> <i class=\"fa fa-share\" style=\"font-size:20px;color: #ffb533;\" title=\"Send transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL1','"+full[0]+"')\" class=\"btn btn-icon-only white<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a>";

									}else{

										if(full[4] == 0 && full[5]==0){

											var chkrejstat1 = "disabled";
											var chkrejstat2 = "disabled";
											var xcz = '<?=json_encode(@$chkapprovals)?>';
											if(xcz!=""){
												$.each( JSON.parse(xcz), function( key, val ) {
													if(val.cpono==full[0] && val.userid=='<?=$employeeid?>'){
														chkrejstat1 = "";
														chkrejstat2 = "";
													}
													//console.log(key,val);
												});
											}

											if(chkrejstat1==""){
												chkrejstat1 = "<?=($poststat!="True") ? " disabled" : ""?>";
											}

											if(chkrejstat2==""){
												chkrejstat2 = "<?=($cancstat!="True") ? " disabled" : ""?>";
											}

											$msgx =	"<button type=\"button\"  onClick=\"trans('POST','"+full[0]+"')\" class=\"btn btn-icon-only white\" "+chkrejstat1+"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></button> <button type=\"button\"  onClick=\"trans('CANCEL','"+full[0]+"')\" class=\"btn btn-icon-only white\" "+chkrejstat2+"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></button>";
										}

									}

									if(full[7] == 1) {
										return "<div id=\"msg"+full[0]+"\"> "+ $msgx +" <button type=\"button\" onClick=\"track('"+full[0]+"')\" class=\"btn btn-icon-only white\"> <i class=\"fa fa-file-text-o\" style=\"font-size:20px;color: #3374ff;\" title=\"Track transaction\"></i></button> </div>"
									}else{
										if($msgx==""){
											$msgx = "-";
										}
										return "<div id=\"msg"+full[0]+"\"> "+ $msgx +" </div>";
									}

								}
						},
		
				],
				"columnDefs": [
					{
						"targets": [3,4],
						"className": "text-center"
					},
					{
						"targets": 1,
						"className": "dt-body-nowrap"
					},
					{
						"targets": [5],
						"className": "text-center dt-body-nowrap"
					},
					{
						"targets": [6],
						"className": "text-center dt-body-nowrap",
						"orderable": false
					}
				],
				"createdRow": function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data[5]==1 || data[8] == 1){
							$(row).addClass('text-danger');
						}
						
				}
			});
		}
	</script>

</body>
</html>