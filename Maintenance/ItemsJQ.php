<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">   
<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
function editfrm(x){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").submit();
}
</script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

</head>

<body style="padding:5px">
	<div>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Items Master List</u></font>	
            </div>
            
          <!--
            <div style="float:right; width:30%; text-align:right">
            	<font size="+1"><a href="javascript:;" onClick="paramchnge('ITEMTYP')">Type</a> | <a href="javascript:;" onClick="paramchnge('ITEMCLS')">Classification</a> | <a href="javascript:;" onClick="paramchnge('ITMUNIT')">UOM</a></font>	
            </div>
          -->
          
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-md"  onClick="location.href='Items_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <br><br>
			
			<table id="example" cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
				<thead>
					<tr>
						<th width="100">Part No</th>
						<th>Description</th>
                        <th width="70">Main UOM</th>
						<th width="70">Status</th>
					</tr>
				</thead>

			</table>

	</div>		


<form name="frmedit" id="frmedit" method="post" action="Items_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>		
		
    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
			$(document).ready(function() {
				var dataTable = $('#example').DataTable( {
					"processing": true,
					"serverSide": true,
					"ajax":{
						url :"items-grid.php", // json datasource
						type: "post",  // method  , by default get
						error: function (req, status, err) {  // error 
							$(".items-grid-error").html("");
							$("#example").append('<tbody class="items-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
							$("#example_processing").css("display","none");
							
						}
					}
				} );
				
				$(".itmalert").hide();
				
			} );


	function setStat(code, stat){
			$.ajax ({
				url: "th_itmsetstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data!="True"){
						$("#itm"+code).html("<b>Error: </b>"+ data);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).show();
					}
					else{
					  if(stat=="ACTIVE"){
						$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
					  }else{
						 $("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
					  }
						
						$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();

					}
				}
			
			});

	}
	</script>


</body>
</html>