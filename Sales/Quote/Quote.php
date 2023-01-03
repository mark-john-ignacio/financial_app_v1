<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Quote.php";
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

	<title>Coop Financials</title>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">   
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 
</head>

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus();">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Quotation List</u></font>	
            </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-2 nopadding">
					<button type="button" class="btn btn-primary btn-md" onClick="location.href='Quote_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

				</div>
				<div class="col-xs-7 nopadding">
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
						<th>Customer</th>
						<th>Date</th>
						<th>Gross</th>
                        <th>Status</th>
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

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$('#example').DataTable({bSort:false});
	</script>

</body>
</html>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
		
	fill_datatable();	


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
						url: "Quote_Tran.php",
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

			$('#MyTable').DataTable().destroy();		
			fill_datatable();
				
			}
			else if(idz=="Cancel"){
				
				$("#AlertMsg").html("");
				$("#AlertModal").modal('hide');
				
			}
			
		}
	});

		
});
	
	$(document).keydown(function(e) {	
	e.preventDefault();
	 
	  if(e.keyCode == 112) { //F2
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
				{ "data": null,

					"render": function (data, type, full, row) {
 							
							return full[1]+" - "+full[2];
					}

				},
				{ "data": 3 },
				{ "data": 4 },
				{ "data": null,

					"render": function(data, type, full, row) {

						if(full[5]==0 && full[6] == 0){
							return "<a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"')\">POST</a> | <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"')\">CANCEL</a>";
						}else{

							if(full[7]>0){

								if(full[5]==1){
									return "Posted";
								}
								else if(full[6] == 1){
									return "Cancelled";
								}

							}else{

								if(full[5]==1){
									return "<a href=\"javascript:;\" onClick=\"trans('OPEN','"+full[0]+"')\" style=\"color: red !important\"> Posted </a>";
								}
								else if(full[6] == 1){
									return "<a href=\"javascript:;\" onClick=\"trans('OPEN','"+full[0]+"')\" style=\"color: red !important\"> Cancelled </a>";
								}

							}

						}
					}

				}				
        	],
        	"columnDefs": [
			    {
			        targets: 3,
			        className: 'text-right'
			    },
				{
			        targets: 4,
			        className: 'text-center', 					
			        orderable: false
				}
			  ],
	});
}
</script>