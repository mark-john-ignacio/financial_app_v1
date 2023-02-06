<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS.php";
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

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>


</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>SI Non-Trade List</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary btn-md" onClick="location.href='SI_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Invoice No</th>
						<th>Customer</th>
                        <th>Order Date</th>
						<th>Delivery Date</th>
						<th>Gross</th>
                        <th width="100">Status</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="SI_edit.php">
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
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
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
	$(document).ready(function() {
		var table = $('#example').DataTable( {
			"searching": true,
        	"paging": true,
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
 							
							return "<a href=\"javascript:;\" onclick=\"editfrm('"+full[0]+"')\">"+full[0]+"</a>";
					}
						
				},
				{ "data": 1 },
				{ "data": 2 },
				{ "data": 3 },
				{ "data": 4 },	
				{ "data": null,
					"render": function (data, type, full, row) {
 
						if (full[5] == 1) {
							
							return 'POSTED';
						
						}
						 
						else if (full[6] == 1) {
						 
							return 'CANCELLED';
						 
						}
						
						else{
							return " <div id=\"msg"+full[0]+"\"><a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','Posted','"+full[7]+"',"+full[8]+")\">POST</a> | <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"','Cancelled')\">CANCEL</a></div>";
						}
					}
				}
        	],
			"serverSide": true,
			"ajax": {
				url: "SI_serverside.php",
				type: "POST",
			},
			"order": [[ 2, "desc" ]],
			"columnDefs": [ {
			  "targets": 4,
			  "className": "text-right"
			} ],
		} );
			
	
	} );

		
$(document).keydown(function(e) {	
	
	 
	  if(e.keyCode == 112) { //F2
		  e.preventDefault();
		  window.location = "SI_new.php";
	  }
});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num,msg,id,xcred){
var itmstat = "";

if(x=="POST"){
			//generate GL ENtry muna
			$.ajax ({
				dataType: "text",
				url: "../../include/th_toAcc.php",
				data: { tran: num, type: "IN" },
				async: false,
				success: function( data ) {
					//alert(data.trim());
					if(data.trim()=="True"){
						itmstat = "OK";
					}
					else{
						itmstat = data.trim();	
					}
				}
			});
			//alert(itmstat);
			
			//Send SMS lng
			
			//$.ajax ({
			//	dataType: "text",
			//	url: "SI_SMS.php",
			//	data: { x: num },
			//	async: false,
			//	success: function( data ) {
					//WALA GAGAWIN
			//	}
			//});


	}
else{
	var itmstat = "OK";	
}

if(itmstat=="OK"){

	alert("SI_Tran.php?x="+num+"&typ="+x);
	
	$.ajax ({
		url: "SI_Tran.php",
		data: { x: num, typ: x },
		async: false,
		dataType: "json",
		beforeSend: function(){
			$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
			$("#alertbtnOK").hide();
			$("#AlertModal").modal('show');
		},
		success: function( data ) {
			
			console.log(data);
			$.each(data,function(index,item){
				
				itmstat = item.stat;
				
				if(itmstat!="False"){
					varx0 = item.stat;
					$("#msg"+num).html(varx0.toUpperCase());
					
						$("#AlertMsg").html("");
						
						$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

				}
				else{
					$("#AlertMsg").html("");
					
					$("#AlertMsg").html(item.ms);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');

				}
			});
		}
	});
}else{				
					$("#AlertMsg").html("");

					$("#AlertMsg").html("<b>ERROR: </b>There's a problem with your transaction!<br>"+itmstat);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');

}
}
</script>

</body>
</html>