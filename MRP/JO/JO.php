<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "JobOrders";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];


	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'JobOrders_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'JobOrders_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	/*
	$unpoststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SO_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$unpoststat = "False";
	}
	*/

	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
				
	}

	$def_From = "";
	$def_To = "";

	$sqlempsec = mysqli_query($con,"select A.ccode, A.cvalue From parameters A Where A.compcode='$company' and A.cstatus='ACTIVE' and A.ccode in ('JO_MRS_FROM','JO_MRS_TO') Order By A.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		if($row0['ccode']=="JO_MRS_FROM"){
			$def_From = $row0['cvalue'];
		}else if($row0['ccode']=="JO_MRS_TO"){
			$def_To = $row0['cvalue'];
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
					<font size="+2"><u>Job Order List</u></font>	
				</div>
			</div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm" onClick="location.href='JO_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>	
					
					<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#GenMrsMod"><i class="fa fa-gear"></i></button>

				</div>
				<div class="col-xs-3 nopadding">
					<div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
				</div>
				<div class="col-xs-2 nopadwtop" style="height:30px !important;">
				<b> Search Customer/SO No/JO No: </b>
				</div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>" class="form-control input-sm" placeholder="Enter Trans No or Customer or SO No....">
				</div>
			</div>

			<br><br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>JO No</th>
						<th>SO No</th>
						<th>Customer</th>
						<th>Target Date</th>
						<th>Priority</th>
						<th>Status</th>
					</tr>
				</thead>

				
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="JO_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
	<input type="hidden" name="hdnsrchval" id="hdnsrchval" />
</form>		


	<!-- Settings Gen MRS -->
	<div class="modal fade" id="GenMrsMod" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">

			<form id="frmgenset" action="default_setiings.php" method="POST">
				<div class="modal-header">
					<h3 class="modal-title" id="InvListHdr">MRS Default Settings</h3>
				</div>
							
				<div class="modal-body" style="height: 20vh">

					<div class="col-xs-12 nopadding">
						<div class="col-xs-3 nopadwtop" id="secfrom">
							<b>Requesting Section: </b>
						</div>
						<div class="col-xs-6 nopadding">
							<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
							<?php
								$issel = 0;
									foreach($arrallsec as $localocs){
										if($def_From!=""){
											if($localocs['nid']==$def_From){
												$issel++;
											}else{
												$issel = 0;
											}
										}else{
											$issel++;
										}
										
								?>
									<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
								<?php	
									}						
								?>
							</select>
						</div>
					</div>	
					
					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-3 nopadwtop" id="secto">
							<b>Issuing Section: </b>
						</div>
						<div class="col-xs-6 nopadding">
							<select class="form-control input-sm" name="selwhto" id="selwhto">
							<?php
								$issel = 0;
									foreach($arrallsec as $localocs){
										if($def_To!=""){
											if($localocs['nid']==$def_To){
												$issel++;
											}else{
												$issel = 0;
											}
										}else{
											$issel++;
										}
								?>
									<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
								<?php	
									}						
								?>
							</select>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>				
			</form>

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
                <p>
                    <center>
                        <button type="button" class="btnmodz btn btn-primary btn-sm" id="OK">Ok</button>
                        <button type="button" class="btnmodz btn btn-danger btn-sm" id="Cancel">Cancel</button>
                        
                        
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

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">
	$(document).keydown(function(e) {		 
	  if(e.keyCode == 112) { //F2
			e.preventDefault();
			window.location = "JO_new.php";
	  }
	});

	$(document).ready(function(e) {

		fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>");	

		$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();

				$('#example').DataTable().destroy();
				fill_datatable(searchByName);

		});

		var itmstat = "";
		var x = "";
		var num = "";
		var msg = "";


		$(".btnmodz").on("click", function (){

			if($('#AlertModal').hasClass('in')==true){
				var idz = $(this).attr('id');

				if(idz=="OK"){
					var x = $("#typ").val();
					var num = $("#modzx").val();
					
					if(x=="POST"){
						var msg = "POSTED";
					}
					else if(x=="CANCEL"){
						var msg = "CANCELLED";
					}
					
						$.ajax ({
							url: "JO_Tran.php",
							data: { x: num, typ: x },
							async: false,
							dataType: "json",
							beforeSend: function(){
								$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
								$("#alertbtnOK").hide();
								$("#OK").hide();
								$("#Cancel").hide();
								$("#AlertModal").modal('show');
							},
							success: function( data ) {
								console.log(data);
								$.each(data,function(index,item){
									
									itmstat = item.stat;
									
									if(itmstat!="False"){
										$("#msg"+num).html(item.stat);
										
											$("#AlertMsg").html("");
											
											$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
											$("#alertbtnOK").show();
											$("#OK").hide();
											$("#Cancel").hide();
											$("#AlertModal").modal('show');
					
									}
									else{
										$("#AlertMsg").html("");
										
										$("#AlertMsg").html(item.ms);
										$("#alertbtnOK").show();
										$("#OK").hide();
										$("#Cancel").hide();
										$("#AlertModal").modal('show');
					
									}
								});
							}
						});
					
				}
				else if(idz=="Cancel"){
					
					$("#AlertMsg").html("");
					$("#AlertModal").modal('hide');
					
				}
				
			}
		});

	});

	function fill_datatable(searchByName){

		var table = $('#example').DataTable({
			stateSave: true,
			"searching": false,
			"paging": true,
			"serverSide": true,
			"ajax": {
				url: "th_datatable.php",
				type: "POST",
				data:{
					searchByName: searchByName
				}
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
					"render": function (data, type, full, row) {

						if (full[5] == 1) {
							
							return 'Posted';
						
						}
						
						else if (full[6] == 1) {
						
							return '<b>Cancelled</b>';
						
						}
						
						else{

							return 	"<div id=\"msg"+full[0]+"\"> <a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"')\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a> </div>";

						}
					}
				}
			],
			"columnDefs": [
				{
					"targets": [3,5],
					"className": "text-center dt-body-nowrap"
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

	function editfrm(x){
		$('#txtctranno').val(x); 
		$('#hdnsrchval').val($('#searchByName').val()); 
		document.getElementById("frmedit").submit();
	}

	function trans(x,num){

		$("#typ").val(x);
		$("#modzx").val(num);

		$("#AlertMsg").html("");
								
		$("#AlertMsg").html("Are you sure you want to "+x+" Job Order No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');

	}

</script>