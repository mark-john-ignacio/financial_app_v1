<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "Quote.php";
include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//get users, post cancel and send access
	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Quote_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Quote_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$unpoststat = "True";	
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Quote_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$unpoststat = "False";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">   
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">
</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Quotation List</u></font>	
            </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-md" onClick="location.href='Quote_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

					<?php
						if($unpoststat=="True"){
					?>
						<button type="button" class="btn btn-warning btn-md" onClick="location.href='Quote_unpost.php'"><span class="fa fa-refresh"></span>&nbsp;Un-Post Transaction</button>
					<?php
						}
					?>

				</div>
				<div class="col-xs-5 nopadding">
				</div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
				</div>
			</div>


            <br><br>
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Quote No</th>
						<th>Type</th>
						<th>Customer</th>
						<th>Date</th>
            <th class="text-center">Status</th>
						<th class="text-center">Actions</th>
					</tr>
				</thead>

				
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="Quote_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
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
                        <button type="button" class="btnmodz btn btn-primary btn-sm" id="OK" onclick="trans_send('OK')">Ok</button>
                        <button type="button" class="btnmodz btn btn-danger btn-sm" id="Cancel" onclick="trans_send('Cancel')">Cancel</button>
                        
                        
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
        <h3 class="modal-title" id="InvListHdr">PO Approval Status</h3>
      </div>
            
      <div class="modal-body pre-scrollable" id="divtracker" style="height: 45vh">
				
			</div>

		</div>
	</div>
</div>

</body>
</html>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {

	fill_datatable();	
	$("#searchByName").keyup(function(){
		var searchByName = $('#searchByName').val();
			//	if(searchByName != '')
			//	{
			$('#MyTable').DataTable().destroy();
			fill_datatable(searchByName);
			//	}
	});
		
});
	
$(document).keydown(function(e) {	
	
	 
	  if(e.keyCode == 112) { //F2
		e.preventDefault();
		window.location = "Quote_new.php";
		
	  }
	});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){

	$("#typ").val(x);
	$("#modzx").val(num);

		$("#AlertMsg").html("");
							
		$("#AlertMsg").html("Are you sure you want to "+x+" Quote No.: "+num);
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

		if(idz=="OK"){
			var x = $("#typ").val();
			var num = $("#modzx").val();
			
			if(x=="POST"){
				var msg = "POSTED";
			}
			else if(x=="CANCEL"){
				var msg = "CANCELLED";
			}
			else if(x=="SEND"){
				var msg = "SENT";
			}

				$.ajax ({
					url: "Quote_Tran.php",
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
						
		}else{
										
			$("#AlertMsg").html("");
											
			$("#AlertMsg").html(value.ms);
			$("#alertbtnOK").show();
			$("#OK").hide();
			$("#Cancel").hide();
			$("#AlertModal").modal('show');
																
		}
	});
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

function fill_datatable(searchByName = ''){
	var dataTable = $('#MyTable').DataTable({
		    "processing" : true,
		    "serverSide" : true,
		    "lengthChange": false,
		    "order" : [],
		    "searching" : false,
		    "ajax" : {
		     url:"th_datatable.php",
		     type:"POST",
		     data:{
		      searchByName:searchByName
		     }
		    },
		    "columns": [
				{ "data": null,

					"render": function (data, type, full, row) {
 							
							return "<a href=\"javascript:;\" onclick=\"editfrm('"+full[0]+"')\">"+full[0]+"</a>";
					}
				},
				{ "data": 8 },
				{ "data": null,

					"render": function (data, type, full, row) {
 							
							return full[1]+" - "+full[2];
					}

				},
				{ "data": 3 },
				{ "data": null,
					"render": function(data, type, full, row) {
						if(full[9]==0){
							return "For Sending";
						}else{
							if(full[5]==0 && full[6]==0){
								return "For Approval";
							}else{
								if(full[5]==1){
									return "Posted";
								}else if(full[6]==1){
									return "Cancelled";
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
						if(full[9]==0){
							mgsx = mgsx + "<a href=\"javascript:;\" onClick=\"trans('SEND','"+full[0]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-share\" style=\"font-size:20px;color: #ffb533;\" title=\"Send transaction\"></i></a>";
						}else{

							if(full[5]==0 && full[6] == 0 && full[9] == 1){
								mgsx = mgsx + "<a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a>";
							}
						}

						mgsx = mgsx + "<a href=\"javascript:;\" onClick=\"track('"+full[0]+"')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-text-o\" style=\"font-size:20px;color: #3374ff;\" title=\"Track transaction\"></i></a>";

						return mgsx;

					}

				}				
        	],
        	"columnDefs": [
					{
						targets: [4,5],
						className: 'text-center', 					
						orderable: false
					}
			  ],
	});
}
</script>