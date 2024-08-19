<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "DR";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];


	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DR_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DR_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}


	$unpoststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DR_unpost'");
	if(mysqli_num_rows($sql) == 0){
		$unpoststat = "False";
	}

	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='INVSYSTEM' and compcode='$company'"); 
									
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$ninvvalue = $all_course_data['cvalue']; 							
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
					<font size="+2"><u>Delivery Receipt List</u></font>	
				</div>
			</div>

			<div class="col-xs-12 nopadwdown">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='DR_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

					<?php
						if($unpoststat=="True"){
					?>
					<button type="button" class="btn btn-danger btn-sm" onClick="location.href='DR_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
					<?php
						}
					?>
				</div>
				<!--<div class="col-xs-2 nopadding">
					<div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
				</div>-->
				<div class="col-xs-3 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
					<b> Search Customer / DR No / Reference: </b>
				</div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control input-sm" placeholder="Search Customer, DR No, Reference...">
				</div>
				<div class="col-xs-2 text-right nopadwleft">
					<select  class="form-control input-sm" name="selstats" id="selstats">
						<option value=""> All Transactions</option>
						<option value="post" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="post") ? "selected" : "" ) : "";?>> Posted </option>
						<option value="cancel" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="cancel") ? "selected" : "" ) : "";?>> Cancelled </option>
						<option value="void" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="void") ? "selected" : "" ) : "";?>> Voided </option>
						<option value="pending" <?=(isset($_REQUEST['st'])) ? (($_REQUEST['st']=="pending") ? "selected" : "" ) : "";?>> Pending </option>
					</select>
				</div>
			</div>

			<br><br>
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>DR No</th>
						<th>DR Series No</th>
						<th>DR Reference</th>
						<th>Reference</th>
						<th>Delivered To</th>
						<th>Delivery Date</th>
						<th>Status</th>
					</tr>
				</thead>		
			</table>
		</section>
	</div>		
    
	<form name="frmedit" id="frmedit" method="post" action="DR_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" />
		<input type="hidden" name="hdnsrchval" id="hdnsrchval" />
		<input type="hidden" name="hdnsrchsta" id="hdnsrchsta" />
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
							<input type="hidden" id="modzid" name="modzid" value = "">
							<input type="hidden" id="modzxcred" name="modzxcred" value = "">
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
<script type="text/javascript" src="../../global/plugins/bootbox/bootbox.min.js"></script>

<script>
	
	var xChkLimitWarn = "";
	var balance = "";

	$(document).keydown(function(e) {		
		if(e.keyCode == 112) { //F2
			e.preventDefault();
			window.location = "DR_new.php";
		}
	});

	$(document).ready(function(e) {

		fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>",$('#selstats').val());	

		$("#searchByName").keyup(function(){
			var searchByName = $('#searchByName').val();
			var searchBystat = $('#selstats').val();

			$('#MyTable').DataTable().destroy();
			fill_datatable(searchByName,searchBystat);

		});

		$("#selstats").change(function(){
			var searchByName = $('#searchByName').val(); 
			var searchBystat = $('#selstats').val(); 

			$('#MyTable').DataTable().destroy();
			fill_datatable(searchByName,searchBystat);

		});

		
		$.ajax({
			url : "../../include/th_xtrasessions.php",
			type: "Post",
			async:false,
			dataType: "json",
			success: function(data)
			{	
				console.log(data);
				$.each(data,function(index,item){
					if(item.chkcustlmt==1){
					//xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
						xChkLimitWarn = 0;
					}
				});
			}
		});

		var xBalance = 0;
		var itmstat = "";
		var x = "";
		var num = "";
		var id = "";
		var xcred = ""; 

		$(".btnmodz").on("click", function (){

			if($('#AlertModal').hasClass('in')==true){
				var idz = $(this).attr('id');
				var x = $("#typ").val();
				var num = $("#modzx").val();

				if(idz=="OK"){
					var id = $("#modzid").val();
					var xcred = $("#modzxcred").val(); 
					
					if(x=="POST"){
						var msg = "POSTED";

						if(xChkLimitWarn==1){
							var xinvs = 0;
							var xors = 0;
							
							$.ajax ({
								url: "../th_creditlimit.php",
								data: { id: $("#modzid").val() },
								async: false,
								dataType: "json",
								success: function( data ) {
																
									console.log(data);
									$.each(data,function(index,item){
										if(item.invs!=null){
											xinvs = item.invs;
										}
										
										if(item.ors!=null){
											xors = item.ors;
										}
										
									});
								}
							});
							
							//alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
									
							xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);						
						}

						gotrans(num, x, msg, "", xBalance);
					}
					else if(x=="CANCEL"){
						var msg = "CANCELLED";
						bootbox.prompt({
							title: 'Enter reason for cancellation.',
							inputType: 'text',
							centerVertical: true,
							callback: function (result) {
								if(result!="" && result!=null){
									gotrans(num,x, msg, result, 0);
								}else{
									$("#AlertMsg").html("Reason for cancellation is required!");
									$("#alertbtnOK").css("display", "inline");
									$("#OK").css("display", "none");
									$("#Cancel").css("display", "none");
								}						
							}
						});

					}

				}else if(idz=="Cancel"){ //if(idz=="OK"){
						
					$("#AlertMsg").html("");
					$("#AlertModal").modal('hide');
						
				}
			
			}
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

	function gotrans(num,x, msg, canmsg, xBalance){
		$.ajax ({
			url: "DR_Tran.php",
			data: { x: num, typ: x, warn: xChkLimitWarn, bal: xBalance, canmsg: canmsg },
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

						//insert sa Inventory
						if(x=="POST"){
							$.ajax ({
								url: "../../include/th_toInv.php",
								data: { tran: num, type: "DR" },
								async: false,
								success: function( data ) {
									itmstat = data.trim();
								}
							});
					
							if(itmstat!="False"){ //Proceed sa insert Account Entry
								<?php
									if($ninvvalue=="perpetual")	{
								?>

								$.ajax ({
									url: "../../include/th_toAcc.php",
									data: { tran: num, type: "DR" },
									async: false,
									success: function( data ) {
										
									}
								});

								<?php
									}
								?>

							}
							else{
								$("#AlertMsg").htm("");
											
								$("#AlertMsg").html("<b>ERROR: </b>There's a problem generating your inventory!");
								$("#alertbtnOK").show();
								$("#OK").hide();
								$("#Cancel").hide();
								$("#AlertModal").modal('show');

							}
						}
						
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

	function fill_datatable(searchByName = '', searchBystat = '')
	{
		var dataTable = $('#MyTable').DataTable({
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
					searchByName:searchByName, searchBystat:searchBystat
				}
			},
			"columns": [
					{ "data": null,
						"render": function (data, type, full, row) {
							var sts = "";
							if (full[5] == 1 || full[9] == 1) {
								sts="class='text-danger'";
							}

									return "<a "+sts+" href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";
								
						}
							
					},
					{ "data": 1 },
					{ "data": 8 },
					{ "data": 10 },
					{ "data": null,
							"render": function (data, type, full, row) {

								return full[6]+" - "+full[2];
									
							}
								
						},
						{ "data": 3 },
						{ "data": null,
							"render": function (data, type, full, row) {

								if (full[4] == 1) {
									
									if(full[9] == 1){
										return '<a href="#" class="canceltool" data-id="'+full[0]+'" data-stat="VOID" style="color: red !important"><b>Voided</b></a>';
									}else{										
										return 'Posted';
									}
								
								}
								
								else if (full[5] == 1) {
								
									return '<a href="#" class="canceltool" data-id="'+full[0]+'" data-stat="CANCELLED" style="color: red !important"><b>Cancelled</b></a>';
								
								}
								
								else{

									return 	"<div id=\"msg"+full[0]+"\"> <a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','Posted','"+full[6]+"',"+full[7]+")\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"','Cancelled')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a> </div>";

								}
							}
						}				
			],
			"columnDefs": [
				{
					"targets": [4,5],
					"className": "text-center dt-body-nowrap"
				}
			],
			"createdRow": function( row, data, dataIndex ) {
				// Set the data-status attribute, and add a class
				if(data[5]==1 || data[9] == 1){
					$(row).addClass('text-danger');
				}
				
			}
		});
	}


	function editfrm(x){
		$('#txtctranno').val(x);
		$('#hdnsrchval').val($('#searchByName').val()); 
		$('#hdnsrchsta').val($('#selstats').val());
		document.getElementById("frmedit").submit();
	}

	function trans(x,num,stat,id,xcred){
		
		$("#typ").val(x);
		$("#modzx").val(num);
		$("#modzid").val(id);
		$("#modzxcred").val(xcred); 

			$("#AlertMsg").html("");
								
			$("#AlertMsg").html("Are you sure you want to "+x+" DR No.: "+num);
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');

	}


</script>