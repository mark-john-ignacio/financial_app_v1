<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Bank.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
function editfrm(x){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").submit();
}

</script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>  


<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>



</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Bank Masterlist</u></font>	
            </div>
        </div>
			<br><br>
           			 <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"  onClick="location.href='Bank_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
			<br><br>			
			<table class="table table-hover" role="grid" id="MyTable">
				<thead>
					<tr>
						<th>Bank Code</th>
						<th>Bank Name</th>
						<th>Next Check</th>
						<th>Bank Acct No</th>
                        <th>Status</th>
                        <th>Delete</th>
					</tr>
				</thead>

			</table>

		</section>
	</div>		


<form name="frmedit" id="frmedit" method="get" action="Bank_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>		

</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	<script>
	$(document).ready(function() {
		var table = $('#MyTable').DataTable( {
			"searching": true,
        	"paging": true,
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
							
								return "<a href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"','Items_edit.php');\">"+full[0]+"</a>";
							
					}
						
				},
				{ "data": null,
					 	"render": function (data, type, full, row) {

									return full[1]+"&nbsp;&nbsp;<div class=\"itmalert alert alert-danger nopadding\" id=\"itm"+full[0]+"\" style=\"display: none\"></div>";

						}
					
				},
				{ "data": 2 },
				{ "data": 3 },
				{ "data": null,
					"render": function (data, type, full, row){

						
						if(full[4]=="ACTIVE"){
						 	return "<div id=\"itmstat"+full[0]+"\"><span class='label label-success'>&nbsp;Active&nbsp;</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a></div>";
						}
						else{
							return "<div id=\"itmstat"+full[0]+"\"><span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a></div>";
						}

					}
				},
				{ "data": null,
				 	"render": function(data, type, full, row){
						
						return "<input class='btn btn-danger btn-xs' type='button' id='row_"+full[0]+"_delete' value='delete' onClick=\"deleteRow('"+full[0]+"');\"/>";
					}
					
				}
				
        	],
			"order": [[ 1, "asc" ]],
			"serverSide": true,
			"ajax": {
				url: "serverside.php",
				type: "POST",
			},
			"columnDefs": [
				{ "targets": 4, "className": "text-center" } 
			],
		} );
			
		
	} );

	$(document).keydown(function(e) {
		if(e.keyCode == 112){//F1
				if(document.getElementById("btnNew").className=="btn btn-primary btn-md"){
					e.preventDefault();
					window.location.href='Bank_new.php';
				}
		}
	});

	function setStat(code, stat){
			$.ajax ({
				url: "th_bansetstat.php",
				data: { code: code,  stat: stat },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data == "True"){
						$("#itm"+code).html("<b>Error: </b>"+ data);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).css('display', 'inline');
					}
					else{
					  if(stat=="ACTIVE"){
						 $("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
					  }else{
						 $("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
					  }
						
						$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).css('display', 'inline');
					}
				}
			
			});

	}
		
		function deleteRow(xid){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "bank" },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data.trim() != "True"){
						$("#itm"+xid).html("<b>Error: </b>"+ data);
						$("#itm"+xid).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+xid).css('display', 'inline');
					}
					else{
					  	location.reload();
					}
				}
			
			});
		}
	</script>
	