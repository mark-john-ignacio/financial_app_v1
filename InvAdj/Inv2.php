<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvAdj.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">

  <script src="../js/bootstrap.min.js"></script>
  
   <script type="text/javascript" src="../js/jquery.js"></script>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F1
		window.open = "InvAdj.php";
	  }
	});


function editfrm(x){
	//alert(x);
	
	document.getElementById("txtctranno").value = x;
    document.getElementById("frmedit").submit();
}

function trans(x,num){
	//var r = confirm("Are you sure you want to "+x+" TranCode.: "+num);
	//if(r==true){
	//var page = 'InvAdj_Tran.php?x='+num+'&typ='+x;
	//var name = 'popwin';
	//var w = 100;
	//var h = 100;
	//var myleft = (screen.width)?(screen.width-w)/2:100;
	//var mytop = (screen.height)?(screen.height-h)/2:100;
	//var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
	//myPopup = window.open(page, name, setting);
	//}
	window.open('Inv_Tran.php?x='+num+'&typ='+x);
}
</script>

<style>
.right{ text-align: right;
}

.left{
  text-align: left;
}

.center{
  text-align: center;
}

.dataTables_filter input {width:250px}
</style>
</head>

<body style="padding:5px; height:600px" onLoad="document.getElementById('txtcsalesno').focus();">
	<div>
		<section>
			<font size="+2"><u>Inventory Adjustments</u></font>
			<br><br>
			<button type="button" class="btn btn-primary btn-md" onClick="location.href='InvAdj.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New(F1)</button>
            
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction Code</th>
						<th>Month</th>
						<th>Year</th>
                        <th>Prepared By</th>
                        <th>Date Prepared</th>
                        <th>Status</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="InvAdj_rpt.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

	<script type="text/javascript" language="javascript" src="lib/js/jquery.min.js"></script>
	<script type="text/javascript" language="javascript" src="lib/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="dist/bootstrapPager.min.js"></script>
	
	<script>
			$(document).ready(function() {
				var dataTable = $('#example').DataTable( {
					"processing": true,
					"serverSide": true,
					"ajax":{
						url :"Inv-grid.php", // json datasource
						type: "post",  // method  , by default get
						error: function(){  // error handling
							$(".employee-grid-error").html("");
							$("#example").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
							$("#employee-grid_processing").css("display","none");
							
						}
					},
					language: {
						searchPlaceholder: "Search Transaction Code..."
					},
					"bLengthChange": false,
					"bAutoWidth": false,
					"aoColumns": [
                        {"sClass": "left"},
                        {"sClass": "left"},
                        {"sClass": "right"},
                        {"sClass": "left"},
						{"sClass": "right"},
						{"sClass": "center"},

            		]
				} );
				
			} );
	</script>

</body>
</html>