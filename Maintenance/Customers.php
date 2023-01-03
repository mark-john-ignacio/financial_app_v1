<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Customers.php";

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

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<title>Coop Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Customers Master List</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-md" onClick="location.href='Customers_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <button type="button" class="btn btn-warning btn-md" id="btnSet" name="btnSet"><span class="glyphicon glyphicon glyphicon-refresh"></span>&nbsp;Update From HRIS</button>

          <div id="divmsgdel" style="display:inline"></div>  <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Customer Code</th>
						<th>Customer Name</th>
						<th>Tin No.</th>
                        <th>Terms</th>
						<th>Status</th>
                       <!-- <th>Delete</th>-->
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
					$sql = "select * from customers where compcode='$company' order by ccode";
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ccode'];?>');" class=info><?php echo $row['ccode'];?></a></td>
						<td><?php echo utf8_encode($row['cname']);?>
                        <div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['ccode'];?>" style="display: inline"></div>
                        </td>
						<td><?php echo $row['ctin'];?></td>
                        <td><?php echo $row['cterms'];?></td>
						<td>
                        <div id="itmstat<?php echo $row['ccode'];?>">
						<?php 
						if($row['cstatus']=="ACTIVE"){
						 	echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}
						?>
                        </div>
                        </td>
                       <!-- <td align="center"><input class='btn btn-danger btn-xs' type='button' id='row_<?php //echo $row['cempid']; ?>_delete' value='delete' onClick="deleteRow('<?php //echo $row['cempid'];?>');"/></td>-->
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>	
    
<!--SETTINGS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">Employee Master List Update</h3>
            </div>
            <div class="modal-body" style="height: 10vh">
            	
                <div class="col-xs-12">
                	<div class="col-xs-5 nopadding">
                    	<font size="4"><b>Total HRIS Record: </b></font>
                    </div>
                	<div class="col-xs-3 nopadwleft text-right" id="divHrisNum">
                    </div>
                	
                </div>
  				<div class="col-xs-12">&nbsp;</div>
                
                <div class="col-xs-12">
                	<div class="col-xs-5 nopadding">
                    	<font size="4"><b>Total System Record: </b></font>
                    </div>
                	<div class="col-xs-3 nopadwleft text-right" id="divSysNum">
                    </div>
                	
                </div>
          	
            	
            </div>
            
            <div class="modal-footer">
               <button type="button" class="btn btn-info btn-md" name="setStart" id="setStart"><span class="glyphicon glyphicon glyphicon-refresh"></span> Start</button>

            </div>

            
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
    
    
    
    	
<form name="frmedit" id="frmedit" method="post" action="Customers_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>		
		

    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>

	$(function() { 
		$(".itmalert").hide();
		$('#example').DataTable();
		
			$("#btnSet").on('click', function() {
				$('#SetModal').modal('show');
				
				$.ajax ({
					url: "th_getcusttotal.php",
					data: { code: 'hris' },
					async: false,
					dataType: "text",
					success: function( data ) {
						$("#divHrisNum").html("<font size='4'><b>"+data+"</b></font>");
					}
				});

				$.ajax ({
					url: "th_getcusttotal.php",
					data: { code: 'system' },
					async: false,
					dataType: "text",
					success: function( data ) {
						$("#divSysNum").html("<font size='4'><b>"+data+"</b></font>");
					}
				});
				
			});

	});

	$(document).keydown(function(e) {
		if(e.keyCode == 112){//F1
				if(document.getElementById("btnNew").className=="btn btn-primary btn-md"){
					e.preventDefault();
					window.location.href='Customers_new.php';
				}
		}
	});

	function setStat(code, stat){
			$.ajax ({
				url: "th_cussetstat.php",
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
