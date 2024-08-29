<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "MaterialBOM";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid']; 

	$lallowMRP = 0;
	$result=mysqli_query($con,"select * From company");								
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row['compcode'] == $company){
				$lallowMRP =  $row['lmrpmodules'];
			}
		}  

?>
<!DOCTYPE html>
<html>
<head>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">

	<link href="../../global/css/components.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<div class="row">
			<div class="col-xs-12">
				<font size="+2"><u>Material BOM</u></font>	
          	</div>
        </div>

		<div class="row">
			<div class="col-xs-12">
				<div class="col-xs-4 nopadding">
					<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='Items_new.php'" id="btnNew" name="btnNew"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Create New (F1)</button>

					<a class="btn btn-sm btn-warning" name="btndltemplate" id="btndltemplate" href="../bom_template.xlsx"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;DL Template</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12" style="padding-top: 5px !important">
				<div class="col-xs-2 nopadwright">
					<select  class="form-control" name="selstypes" id="selstypes">
						<option value="0" <?=(isset($_REQUEST['stype'])) ? (($_REQUEST['stype']=="0") ? "selected" : "" ) : "";?>> Find Item </option>
						<option value="1" <?=(isset($_REQUEST['stype'])) ? (($_REQUEST['stype']=="1") ? "selected" : "" ) : "";?>> Find Item in BOM </option>
					</select>
				</div>
				<div class="col-xs-2 text-right nopadwright">
					<input type="text" class="form-control"  name="searchByCode" id="searchByCode" value="" placeholder="Item Code..." readonly>
				</div>	
				<div class="col-xs-4 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control" placeholder="Enter Code or Desc...">
				</div>	
			</div>
		</div>

        <hr>

		<table id="MyTable" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="100">Item Code</th>
					<th>Description</th>
					<th width="70">Main UOM</th>						
				</tr>
			</thead>

		</table>

	</div>		


	<form name="frmedit" id="frmedit" method="post" action="items.php">
		<input type="hidden" name="itm" id="itm" />
	</form>		

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../js/bootstrap3-typeahead.min.js"></script>

	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(document).ready(function() {
		
		fill_datatable();
		$('#searchByName').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: $("#searchByName").val() },
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 500px"><span >'+item.id+': '+item.desc+'</span</div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#searchByName').val(item.desc).change(); 				
				$("#searchByCode").val(item.id); 
				
				var searchByName = $('#searchByCode').val();
				var searchByType = $('#selstypes').val();
				
				$('#MyTable').DataTable().destroy();
				fill_datatable(searchByName,searchByType);
			}
		
		});

		$("#selstypes").on("change", function(){
			if($('#searchByName').val()!="" && $('#searchByCode').val()!=""){
				var searchByName = $('#searchByCode').val();
				var searchByType = $('#selstypes').val();
				
				$('#MyTable').DataTable().destroy();
				fill_datatable(searchByName,searchByType);
			}
		});
	});

	$(document).keydown(function(e) {
		if(e.keyCode == 112){//F1
			if(document.getElementById("btnNew").className=="btn btn-primary btn-md"){
				e.preventDefault();
				window.location.href='Items_new.php';
			}
		}
	});
	
 
	function fill_datatable(searchByName = '', searchByType = '')
	{
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
			searchByName:searchByName, searchByType:searchByType
			}
			},
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
							
						return "<a href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"','items.php');\">"+full[0]+"</a>";
							
					}
						
				},
				{ "data": 1 },
				{ "data": 2 },
			],
		});
	}
		  
	function editfrm(x,y){
		document.getElementById("itm").value = x;
		document.getElementById("frmedit").action = y;
		document.getElementById("frmedit").submit();
	}
	</script>


</body>
</html>