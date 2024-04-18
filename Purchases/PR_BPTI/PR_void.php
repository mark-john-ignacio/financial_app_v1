<?php

	if(!isset($_SESSION)){
		session_start();
	}
	
	$_SESSION['pageid'] = "PR_unpost.php";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

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

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>

<body style="padding:5px">
	<form action="PR_void_tran.php" name="frmunpost" id="frmunpost" method="POST">
	
		<div>
			<section>
					<div>
						<div style="float:left; width:50%">
							<font size="+2"><u>Purchase Request List</u></font>	
						</div>
					</div>
				<br><br>

				<div class="col-xs-12 nopadwdown">
					<div class="col-xs-6 nopadding">
						<button type="button" class="btn btn-danger btn-sm" id="btnsubmit" name="btnsubmit"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
					</div>
					<div class="col-xs-1 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
						<b> Search PR No: </b>
					</div>
					<div class="col-xs-2 text-right nopadding">
						<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>" class="form-control input-sm" placeholder="Search PR No...">
					</div>
					<div class="col-xs-3 text-right nopadwleft">
						<select class="form-control input-sm" name="selwhfrom" id="selwhfrom"> 
							<?php
								foreach($rowdetloc as $localocs){		
									$slctd = "";
									if(isset($_REQUEST['loc'])){
										if($_REQUEST['loc']==$localocs['nid']){
											$slctd = "selected";
										}
									}	
							?>
									<option value="<?php echo $localocs['nid'];?>" <?=$slctd?>><?php echo $localocs['cdesc'];?></option>										
							<?php	
									}						
							?>
						</select>
					</div>
				</div>


				<br><br>

				<table id="example" class="table table-hover " cellspacing="1" width="100%">
					<thead>
						<tr>
							<td align="center"> <input id="allbox" type="checkbox" value="Check All" /></td>
							<th>PR No</th>
							<th>Requested By</th>
							<th>Section</th>
							<th class="text-center">Trans Date</th>
							<th class="text-center">Date Needed</th>
						</tr>
					</thead>
				</table>

			</section>
		</div>		
		<input type="hidden" name="hdnreason" id="hdnreason" value="">
	</form>  

	<!-- PRINT OUT MODAL-->
	<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-contnorad">   
			<div class="modal-bodylong">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
			
				<iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
					
			</div>
		</div>
	</div>
	</div>
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

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../../global/plugins/bootbox/bootbox.min.js"></script>

<script type="text/javascript">

	$(document).ready(function () {
		fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>",$('#selwhfrom').val());

		$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();
				var searchBySec = $('#selwhfrom').val();
				var searchBystat = $('#selstats').val();

				$('#example').DataTable().state.clear();
				$('#example').DataTable().destroy();
				fill_datatable(searchByName, searchBySec);

		});

		$("#selwhfrom").on("change", function() {
			var searchByName = $('#searchByName').val();
			var searchBySec = $('#selwhfrom').val();
			var searchBystat = $('#selstats').val();

			$('#example').DataTable().state.clear();
			$('#example').DataTable().destroy();
			fill_datatable(searchByName, searchBySec);
		});
	
		$('#btnsubmit').click(function() {
			checked = $("input[type=checkbox]:checked").length;

			if(!checked) {
				$("#AlertMsg").html("You must check at least one checkbox.");
				$("#AlertModal").modal('show');

				return false;
			}else{

				bootbox.prompt({
					title: 'Enter reason for void.',
					inputType: 'text',
					centerVertical: true,
					callback: function (result) {
						if(result!="" && result!=null){
							$("#hdnreason").val(result);
							$("#frmunpost").submit();
						}else{
							$("#AlertMsg").html("Reason for void is required!");
							$("#AlertModal").modal('show');
						}						
					}
				});

			}

		});

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
	});

	function printchk(x){
		$("#myprintframe").attr("src","PrintPR.php?hdntransid="+x);
		$("#PrintModal").modal('show');
	}

	function fill_datatable(searchByName = '', searchBySec = ''){
		var dataTable = $('#example').DataTable( {
			stateSave: true,
			"processing" : true,
			"serverSide" : true,
			"lengthChange": true,
			"order" : [],
			"searching" : false,
			"ajax" : {
				url:"th_dtvoid.php",
				type:"POST",
				data:{ searchByName: searchByName, searchBySec: searchBySec }
			},
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
						return "<input name=\"allbox[]\" id=\"chk"+full[0]+"\" type=\"checkbox\" value=\""+full[0]+"\" />";
					}								
				},
				{ "data": null,
					"render": function (data, type, full, row) {
						return "<a href=\"javascript:;\" onClick=\"printchk('"+full[0]+"');\">"+full[0]+"</a>";
					}
				},
				{ "data": 1 },
				{ "data": 2 },
				{ "data": 3 },
				{ "data": 4 }		
			],
			"columnDefs": [ 
				{
					"targets": 0,
					"className": "text-center",
					orderable: false
				},
				{
					"targets": [5,4],
					"className": "text-center"
				}
			],
			"createdRow": function( row, data, dataIndex ) {
				// Set the data-status attribute, and add a class
				if(data[6]==1 || data[8] == 1){
					$(row).addClass('text-danger');
				}
				
			}
		});
	}

</script>