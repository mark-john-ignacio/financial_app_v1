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

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">    
<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css"/> 

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Production - Items Master List</u></font>	
          </div>
            
          <div style="float:right; width:30%; text-align:right">
            	<!--<font size="+1"><a href="javascript:;" onClick="paramchnge('ITEMTYP')">Type</a> | <a href="javascript:;" onClick="paramchnge('ITEMCLS')">Classification</a> | <a href="javascript:;" onClick="paramchnge('ITMUNIT')">UOM</a></font>	-->

						<div class="itmalert alert alert-danger text-center" style="padding: 2px !important; display: none" id="itmerr" >WRONG ERROR</div>
							
          </div>
          
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-7 nopadding">
						<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='Items_new.php'" id="btnNew" name="btnNew"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Create New (F1)</button>

						<a class="btn btn-sm btn-warning" name="btndltemplate" id="btndltemplate" href="../bom_template.xlsx"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;DL Template</a>

						<!--<a href="Items_xls.php" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> &nbsp; Export To Excel</a>-->
				</div>

        <div class="col-xs-1 nopadwtop" style="height:30px !important;">
          <b> Search Item: </b>
        </div>
				<div class="col-xs-4 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
				</div>

				<!--<div class="col-xs-3 text-right nopadwleft">
					<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
							<option value="">ALL</option>

                    <?php
                        /*
												$sql = "select * from groupings where ctype='ITEMTYP' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
												*/
                    ?>   
                        <option value="<?//php echo $row['ccode'];?>"><?//php echo $row['cdesc']?></option>
                    <?php
                        /*} */                       
                    ?>     
          </select>
				</div>-->

			</div>


            
            
            
			
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Item Code</th>
						<th>Description</th>
            <th width="70">Main UOM</th>						
					</tr>
				</thead>

			</table>

		</section>
	</div>		


	<form name="frmedit" id="frmedit" method="post" action="items.php">
		<input type="hidden" name="itm" id="itm" />
	</form>		

	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(document).ready(function() {
		
		fill_datatable();	
		$("#searchByName").keyup(function(){
		   var searchByName = $('#searchByName').val();
		  // if(searchByName != '')
		  // {
		    $('#MyTable').DataTable().destroy();
		    fill_datatable(searchByName);
		 //  }
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
	
 
		  function fill_datatable(searchByName = '')
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
		      searchByName:searchByName
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