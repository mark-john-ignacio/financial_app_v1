<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "SalesRet.php";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];


	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SalesRet_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SalesRet_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$unpoststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SalesRet_unpost.php'");
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
				<font size="+2"><u>Sales Return List</u></font>	
            </div>
        </div>

			<div class="col-xs-12 nopadwdown">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm" onClick="location.href='SR_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

					<?php
						if($unpoststat=="True"){
					?>
					<button type="button" class="btn btn-danger btn-sm" onClick="location.href='SR_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
					<?php
						}
					?>
				</div>
        <div class="col-xs-3 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
          <b> Search Customer / SR No / Reference: </b>
        </div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control input-sm" placeholder="Search Customer, SR No, Reference...">
				</div>
				<div class="col-xs-2 text-right nopadwleft">
					<select  class="form-control input-sm" name="selstats" id="selstats">
						<option value=""> All Transactions</option>
						<option value="post"> Posted </option>
						<option value="cancel"> Cancelled </option>
						<option value="void"> Voided </option>
						<option value="pending"> Pending </option>
					</select>
				</div>
			</div>

      <br><br>
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Return No</th>
						<th>Reference</th>
						<th>Customer</th>
            <th>Transaction Date</th>
						<th>Return Date</th>
            <th>Status</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="SR_edit.php">
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

	<script type="text/javascript">
		$(document).ready(function(e) {

			fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>");	

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


				var xBalance = 0;
				var itmstat = "";
				var x = "";
				var num = "";
				var id = "";
				var xcred = ""; 

				$(".btnmodz").on("click", function (){

					if($('#AlertModal').hasClass('in')==true){
						var idz = $(this).attr('id');
						
						if(idz=="OK"){
							var x = $("#typ").val();
							var num = $("#modzx").val();
							var id = $("#modzid").val();
							var xcred = $("#modzxcred").val(); 
							
							if(x=="POST"){
								var msg = "POSTED";
							}
							else if(x=="CANCEL"){
								var msg = "CANCELLED";
							}
							
							$.ajax ({
								url: "SR_Tran.php",
								data: { x: num, typ: x},
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
							
							//----------------------------

							if(itmstat=="Posted"){
								//Pag Posted .. Insert to inventory table -MINUS
						
								$.ajax ({
									url: "../../include/th_toInv.php",
									data: { tran: num, type: "SRet" },
									async: false,
									success: function( data ) {
											itmstat = data.trim();
									}
								});
						
								if(itmstat=="False"){ //Proceed sa insert Account Entry
									
									$("#AlertMsg").htm("");
												
									$("#AlertMsg").html("<b>ERROR: </b>There's a problem generating your inventory!");
									$("#alertbtnOK").show();
									$("#OK").hide();
									$("#Cancel").hide();
									$("#AlertModal").modal('show');

								}
						
							}
							
							//------------------------------------
	
	
						}else if(idz=="Cancel"){ //if(idz=="OK"){
								
							$("#AlertMsg").html("");
							$("#AlertModal").modal('hide');
								
						}
					
					}
				});

		});

		$(document).keydown(function(e) {	
			
				if(e.keyCode == 112) { //F2
				window.location = "SR_new.php";
				}
		});

		function editfrm(x){
			$('#txtctranno').val(x); 
			$('#hdnsrchval').val($('#searchByName').val()); 
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
						searchByName: searchByName, searchBystat: searchBystat
					}
		    },
		    "columns": [
					{ "data": null,
						"render": function (data, type, full, row) {
							var sts = "";
							if (full[5] == 1 || full[9]==1) {
								sts="class='text-danger'";
							}

									return "<a "+sts+" href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";
								
						}
							
					},
					{ "data": 8 },
					{ "data": 1 },
					{ "data": 2 },
					{ "data": 3 },
					{ "data": null,
							"render": function (data, type, full, row) {
	
								if (full[4] == 1) {
									
									if(full[9] == 1){
										return '<b>Voided</b>';
									}else{										
										return 'Posted';
									}
								
								}
								
								else if (full[5] == 1) {
								
									return '<b>Cancelled</b>';
								
								}
								
								else{

									return 	"<div id=\"msg"+full[0]+"\"> <a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','Posted','"+full[6]+"',"+full[7]+")\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"','Cancelled')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a> </div>";
									
								}
							}
						}				
        	],
					"columnDefs": [
						{
							"targets": 3,
							"className": "text-right"
						},
						{
							"targets": [4,5],
							"className": "text-center dt-body-nowrap"
						}
					],
					"createdRow": function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data[5]==1 || data[9]==1){
							$(row).addClass('text-danger');
						}
						
					}
		  });
		}
	</script>