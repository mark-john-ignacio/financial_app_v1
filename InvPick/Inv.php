<?php
if(!isset($_SESSION)){
session_start();
}
//$_SESSION['pageid'] = "InvRec.php";

include('../Connection/connection_string.php');
//include('../include/denied.php');
//include('../include/access.php');

$company = $_SESSION['companyid'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">    
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap.js"></script>
</head>

<body style="padding:5px; height:750px">
	<div>
		<section>
         <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Dispatch/Picking</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="javascript:;" id="btnSet"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
<br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction Code</th>
						<th>Remarks</th>
            <th>Prepared By</th>
            <th>Date Prepared</th>
						<th>Delivery Date</th>
            <th>Status</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
     
<form name="frmedit" id="frmedit" method="post" action="InvPick_Edit.php">
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
            

    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
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
				{ "data": 6 },	
				{ "data": 2 },
				{ "data": 3 },
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
				url: "serverside.php",
				type: "POST",
			},
			"order": [[ 2, "desc" ]],
			"columnDefs": [ {
			  "targets": 4,
			  "className": "text-right"
			} ],
		} );
			
	
	$("#btnSet").on("click", function(){
window.location = "InvPick_new.php";
		
	});	

});

		
$(document).keydown(function(e) {	
	
	 
	  if(e.keyCode == 112) { //F2
		  e.preventDefault();
		  window.location = "InvPick_new.php";
	  }
});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

	</script>

</body>
</html>