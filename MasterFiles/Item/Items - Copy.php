<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">    
<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css"/> 

<script type="text/javascript">
function editfrm(x,y){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").action = y;
	document.getElementById("frmedit").submit();
}
</script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
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
            <button type="button" class="btn btn-primary btn-md"  onClick="location.href='Items_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
			
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Part No</th>
						<th>Description</th>
                        <th width="70">Main UOM</th>
						<th width="120" class="text-center">Price History</th>
						<th width="70">Status</th>
						<!--<th width="40">Delete</th>-->
					</tr>
				</thead>

			</table>

		</section>
	</div>		


<!-- PURCH COST VIEW MODAL-->

<div class="modal fade" id="myPurchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Purchase Cost History</b></h5>        
      </div>

	  <div class="modal-body" style="height: 40vh" id="modBody">
      	
	  </div>
      
      <div class="modal-footer">
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Close</button>
	  </div>

    </div>
  </div>
</div>

<form name="frmedit" id="frmedit" method="post" action="Items_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>		

	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
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
				{ "data": 1 },
				{ "data": 2 },
				{ "data": null,
					"render": function (data, type, full, row) {
							
								return "<div class=\"col-sm-12 nopadding\"><div class=\"col-sm-6 nopadding\"><a href=\"javascript:;\" data-toggle=\"modal\" data-target=\"#myPurchModal\" data-id=\""+full[0]+"\" data-label=\"Purchase Cost\" data-val=\"Purch\" class=\"viewCost\"><span class='label label-primary'>Purchase</span></a></div><div class=\"col-sm-6 nopadwleft\"><a href=\"javascript:;\" data-toggle=\"modal\" data-target=\"#myPurchModal\" data-id=\""+full[0]+"\" data-label=\"Sales Price\" data-val=\"Sales\" class=\"viewCost\"><span class='label label-info'>&nbsp;&nbsp;&nbsp;Sales&nbsp;&nbsp;&nbsp;</span></a>";
							
					}
				},
				{ "data": null,
					"render": function (data, type, full, row){

						
						if(full[3]=="ACTIVE"){
						 	return "<div id=\"itmstat"+full[0]+"\"><span class='label label-success'>&nbsp;Active&nbsp;</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a></div>";
						}
						else{
							return "<div id=\"itmstat"+full[0]+"\"><span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a></div>";
						}

					}
				}
				//,
				//{ "data": null,
				// 	"render": function(data, type, full, row){
						
				//		return "<input class='btn btn-danger btn-xs' type='button' id='row_"+full[0]+"_delete' value='delete' onClick=\"deleteRow('"+full[0]+"');\"/>";
				//	}
					
				//}
				
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
					window.location.href='Items_new.php';
				}
		}
	});
	
	$(document).on("click", ".viewCost", function() {
		var myId = $(this).data('id');
		var myLabel = $(this).data('label');
		var myVal = $(this).data('val');
		var myPTyp = $(this).data('ptyp');
		
		$("#myModalLabel").html("<b>Top 10 (Latest) "+myLabel+" History ("+myId+")</b>");
		
			$.ajax({
				type: "GET", 
				url: "Items_getHistory.php",
				data: "id="+myVal+"&itm="+myId+"&ptyp="+myPTyp,
				success: function(html) {
					$("#modBody").html(html);
				}
			});

	});
	
	function setStat(code, stat){
			$.ajax ({
				url: "th_itmsetstat.php",
				data: { code: code,  stat: stat },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data == "True"){

					  if(stat=="ACTIVE"){
						 $("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
					  }else{
						 $("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
					  }
						
						$("#itmerr").html("<b>SUCCESS: </b> "+code+" Status changed to <b><u>"+stat+"</u></b>");
						$("#itmerr").attr("class", "itmalert alert alert-success");
						$("#itmerr").css({'display':'inline', 'padding':'8px'});

					}
					else{

						$("#itmerr").html("<b>Error: </b>"+ data);
						$("#itmerr").attr("class", "itmalert alert alert-danger")
						$("#itmerr").css({'display':'inline', 'padding':'8px'});

					}
				}
			
			});

	}
		
		function deleteRow(xid){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "item" },
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


</body>
</html>