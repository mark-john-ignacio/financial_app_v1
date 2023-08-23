<?php

	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PR.php";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

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

	//UNPOST
	$unpostat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'PR_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$unpostat = "False";
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

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
	<META NAME="robots" CONTENT="noindex,nofollow">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>">

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Purchase Request List</u></font>	
          </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm" onClick="location.href='PR_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
					<?php
						if($unpostat=="True"){
					?>
						<button type="button" class="btn btn-warning btn-sm" onClick="location.href='PR_unpost.php'"><span class="fa fa-refresh"></span>&nbsp;Un-Post Transaction</button>
					<?php
						}
					?>
				</div>
        <div class="col-xs-2 nopadding">
					<div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
				</div>
        <div class="col-xs-1 nopadwtop" style="height:30px !important;">
          <b> Searc PR No: </b>
        </div>
				<div class="col-xs-2 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>" class="form-control input-sm" placeholder="Enter PR No...">
				</div>
				<div class="col-xs-3 text-right nopadwleft">
					<select class="form-control input-sm" name="selwhfrom" id="selwhfrom"> 
						<option value="">ALL</option>	
						<?php
								foreach($rowdetloc as $localocs){									
						?>
									<option value="<?php echo $localocs['nid'];?>"><?php echo $localocs['cdesc'];?></option>										
						<?php	
								}						
						?>
					</select>
				</div>
			</div>

      <br><br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>PR No</th>
						<th>Requested By</th>
						<th>Section</th>
						<th>Date Needed</th>
						<th>Created Date</th>
            <th class="text-center">Status</th>
						<th class="text-center">Actions</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
    
	<form name="frmedit" id="frmedit" method="post" action="PR_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" />
		<input type="hidden" name="hdnsrchval" id="hdnsrchval" /> 
		<input type="hidden" name="hdnsrchsec" id="hdnsrchsec" />
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

	<!-- 1) TRACKER Modal -->
	<div class="modal fade" id="TrackMod" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">PR Approval Status</h3>
				</div>
							
				<div class="modal-body pre-scrollable" id="divtracker" style="height: 60vh">
					
				</div>

			</div>
		</div>
	</div>

</body>
</html>

  <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
		$(document).ready(function() {

			fill_datatable("","");	

			$("#searchByName").keyup(function(){
					var searchByName = $('#searchByName').val();
					var searchBySec = $('#selwhfrom').val();

					$('#example').DataTable().destroy();
					fill_datatable(searchByName, searchBySec);

			});

			$("#selwhfrom").on("change", function() {
				var searchByName = $('#searchByName').val();
				var searchBySec = $('#selwhfrom').val();

				$('#example').DataTable().destroy();
				fill_datatable(searchByName, searchBySec);
			});

		});

		
		$(document).keydown(function(e) {		 
				if(e.keyCode == 112) { //F2
					e.preventDefault();
					window.location = "PR_new.php";
				}
		});


		function editfrm(x){
			$('#txtctranno').val(x); 
			$('#hdnsrchval').val($('#searchByName').val()); 
			$('#hdnsrchsec').val($('#selwhfrom').val()); 
			document.getElementById("frmedit").submit();
		}
		
		function fill_datatable(searchByName,searchBySec){
			var dataTable = $('#example').DataTable( {
				stateSave: true,
		    "processing" : true,
		    "serverSide" : true,
		    "lengthChange": true,
		    "order" : [],
		    "searching" : false,
		    "ajax" : {
					url:"th_datatable.php",
					type:"POST",
					data:{ searchByName: searchByName, searchBySec: searchBySec }
		    },
					"columns": [
						{ "data": null,
								"render": function (data, type, full, row) {
										var sts = "";
										if (full[6] == 1) {
											sts="class='text-danger'";
										}
										return "<a "+sts+" href=\"javascript:;\" onclick=\"editfrm('"+full[0]+"')\">"+full[0]+"</a>";
								}								
						},
						{ "data": 1 },
						{ "data": 2 },
						{ "data": 3 },
						{ "data": 4 },	
						{ "data": null,
							"render": function(data, type, full, row) {

								if(full[7]==0 && full[6]==0){
									return "For Sending";
								}else{
									if(full[5]==0 && full[6]==0){
										return "For Approval";
									}else{
										if(full[5]==1){
											return "Posted";
										}else if(full[6]==1){
											return '<b>Cancelled</b>';
										}else{
											return "Pending";
										}
									}

								}
							}
						},
						{ "data": null,

							"render": function(data, type, full, row) {

								var mgsx = "";

								if(full[6] == 1){
									mgsx = mgsx + "-";
								}else{
									if(full[7]==0){

										mgsx = mgsx + "<a href=\"javascript:;\" onClick=\"trans('SEND','"+full[0]+"','"+full[10]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-share\" style=\"font-size:20px;color: #ffb533;\" title=\"Send transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL1','"+full[0]+"','"+full[10]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color: Red;\" title=\"Cancel transaction\"></i></a>";
										
									}else{

										if(full[5]==0 && full[6] == 0 && full[7] == 1){
											mgsx = mgsx + "<a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','"+full[10]+"')\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('REJECT','"+full[0]+"','"+full[10]+"')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a>";
										}
									}
								}

								if(full[7] == 1){
									if(mgsx=="-"){
										mgsx = "";
									}
									mgsx = mgsx + " <a href=\"javascript:;\" onClick=\"track('"+full[0]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-text-o\" style=\"font-size:20px;color: #3374ff;\" title=\"Track transaction\"></i></a>";
								}

								return mgsx;

							}

						}	
					],
					"columnDefs": [ 
						{
							"targets": [3,4],
							"className": "text-right"
						},
						{
							"targets": [5,6],
							"className": "text-center",
							orderable: false
						}
					],
					"createdRow": function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data[6]==1){
							$(row).addClass('text-danger');
						}
						
					}
				});
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

			if(idz=="OK"){
				var x = $("#typ").val();
				var num = $("#modzx").val();
				
				if(x=="POST"){
					var msg = "POSTED";
				}
				else if(x=="CANCEL" || x=="CANCEL1"){
					var msg = "CANCELLED";
				}
				else if(x=="SEND"){
					var msg = "SENT";
				}

					$.ajax ({
						url: "PR_Tran.php",
						data: { x: num, typ: x },
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
				

			}
			else if(idz=="Cancel"){
				
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
	</script>
