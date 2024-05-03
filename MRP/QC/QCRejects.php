<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "QCRejects";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
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
				<font size="+2"><u>QC Rejects (JO List)</u></font>	
            </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<!--<button type="button" class="btn btn-primary btn-sm" onClick="location.href='JO_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>-->

					
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
    
	<form name="frmedit" id="frmedit" method="post" action="QCRejects_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" />
		<input type="hidden" name="hdnsrchval" id="hdnsrchval" />
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

						if (full[5] >= 1) {
							if(full[6] == 0){
								return "<span class='label label-success' style='padding: 5px !important; font-size: 10px;'>Completed</span>"; 
							}else{
							
								return "<span class='label label-warning' style='padding: 5px !important; font-size: 10px;'>In Progress</span>"; 
							}
						
						}else{

							return "<span class='label label-danger' style='padding: 5px !important; font-size: 10px;'>Pending</span>";

						}
						
						
					}
				}
			],
			"columnDefs": [
				{
					"targets": [3,4,5],
					"className": "text-center dt-body-nowrap"
				}
			]
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