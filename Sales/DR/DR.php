<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "DR.php";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DR_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
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
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='DR_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

					<?php
						if($poststat=="True"){
					?>
					<button type="button" class="btn btn-warning btn-sm" onClick="location.href='DR_unpost.php'"><span class="fa fa-refresh"></span>&nbsp;Un-Post Transaction</button>
					<?php
						}
					?>
				</div>
        <div class="col-xs-3 nopadding">
					<div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
				</div>
        <div class="col-xs-2 nopadwtop" style="height:30px !important;">
          <b> Search Customer/DR No: </b>
        </div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Trans No or Customer...">
				</div>

			</div>

            <br><br><br>
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>DR No</th>
						<th>DR Series No</th>
						<th>Customer</th>
						<th>Delivery Date</th>
            <th>Status</th>
					</tr>
				</thead>

				
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="DR_edit.php">
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
	
	<script>
		var xChkLimitWarn = "";
		var balance = "";

		$(document).ready(function(e) {

			//$('#example').DataTable();
			fill_datatable();	
			$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();
			//	if(searchByName != '')
			//	{
					$('#MyTable').DataTable().destroy();
					fill_datatable(searchByName);
			//	}
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

			
							if(x=='POST'){
								//alert( $("#modzid").val() );
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
							}

							//---------------------------------------------
							
							$.ajax ({
								url: "DR_Tran.php",
								data: { x: num, typ: x, warn: xChkLimitWarn, bal: xBalance },
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
											if(data.trim()!="False"){
												
												$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully Posted...");
												$("#alertbtnOK").show();
												$("#AlertModal").modal('show');
						
											}
											else{
												$("#AlertMsg").htm("");
												
												$("#AlertMsg").html("<b>ERROR: </b>There's a problem generating your account entry!");
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
												$("#AlertModal").modal('show');
							
											}
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
				e.preventDefault();
				window.location = "DR_new.php";
			}
		});


		function fill_datatable(searchByName = '')
		{
		  var dataTable = $('#MyTable').DataTable({
		    "processing" : true,
		    "serverSide" : true,
		    "lengthChange": true,
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
							var sts = "";
							if (full[5] == 1) {
								sts="class='text-danger'";
							}

									return "<a "+sts+" href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";
								
						}
							
					},
					{ "data": 1 },
					{ "data": 2 },
					{ "data": 3 },
					{ "data": null,
							"render": function (data, type, full, row) {
	
								if (full[4] == 1) {
									
									return 'Posted';
								
								}
								
								else if (full[5] == 1) {
								
									return '<b>Cancelled</b>';
								
								}
								
								else{
									return " <div id=\"msg"+full[0]+"\"><a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','Posted','"+full[6]+"',"+full[7]+")\">POST</a> | <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"','Cancelled')\">CANCEL</a></div>";
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
							"targets": 4,
							"className": "text-center dt-body-nowrap"
						}
					],
					"createdRow": function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data[5]==1){
							$(row).addClass('text-danger');
						}
						
					}
		  });
		}


		function editfrm(x){
			document.getElementById("txtctranno").value = x;
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