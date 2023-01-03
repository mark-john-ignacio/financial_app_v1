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
function editfrm(x,y){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").action = y;
	document.getElementById("frmedit").submit();
}
</script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">


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
            
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<!--<th>Item Code</th>-->
						<th width="100">Part No</th>
						<th>Description</th>
                        <th width="70">Main UOM</th>
						<th width="100" class="text-center">Price List</th>
                        <!--<th>Cost</th>
                        <th>Retail</th>
						<th>Qty  in Stock</th>-->
						<th width="70">Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
				$sql = "select * from Items where compcode='$company' order by citemdesc";
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>

						<td>
                        <a href="javascript:;" onClick="editfrm('<?php echo $row['cpartno'];?>','Items_edit.php');"><?php echo $row['cpartno'];?>
                        </a>
                        </td>
						<td><?php echo $row['citemdesc'];?>
                        	<div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['cpartno'];?>" style="display: inline"></div>
                        </td>
                        <td><?php echo $row['cunit'];?></td>
						<td align="right">
                        <div class="col-sm-12 nopadding">
                         <div class="col-sm-6 nopadding">
                        <a href="javascript:;" data-toggle="modal" data-target="#myPurchModal" data-id="<?php echo $row['cpartno'];?>" data-label="Purchase Cost" data-val="Purch" data-ptyp="<?php echo $row['cpricetype'];?>" class="viewCost"><span class='label label-primary'>Purchase</span></a>
                          </div>
                          <div class="col-sm-6 nopadwleft">
                        <a href="javascript:;" data-toggle="modal" data-target="#myPurchModal" data-id="<?php echo $row['cpartno'];?>" data-label="Sales Price" data-val="Sales" data-ptyp="<?php echo $row['cpricetype'];?>" class="viewCost"><span class='label label-info'>&nbsp;&nbsp;&nbsp;Sales&nbsp;&nbsp;&nbsp;</span></a>
                          </div>
                        </div>
                        </td>
                        <!--<td align="right"><?php// echo $row['nretailcost'];?></td>
						<td align="right"><?php// echo $row['nqty'];?></td>-->
						<td>
                        <div id="itmstat<?php echo $row['cpartno'];?>">
						<?php 
						if($row['cstatus']=="ACTIVE"){
						 	echo "<span class='label label-success'>&nbsp;Active&nbsp;</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['cpartno'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['cpartno'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}
						?>
                        </div>
                        </td>
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>
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
		
    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(function() { 
		$(".itmalert").hide();
		$('#example').DataTable();
	});

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