<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function () {

    (function ($) {

        $('#filter').keyup(function () {

            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();

        })

    }(jQuery));

	$("tbody tr").click(function() {
		console.log('clicked');
		$(this).addClass('highlight').siblings().removeClass("highlight");
	});
	
	$('#myTable').find('tr').click( function(){
	  var row = $(this).find('td:first').text();
	  $('#txtprodid').val(row);
	});


});


function Cancel(x){
	location.href = 'confirmcan.php?x='+x;
}
</script>

<style>
.table-striped tbody tr.highlight td { background-color: #039; color:#FFF}
</style>
</head>

<body style="padding:10px">

	<div class="input-group"> 
	  <span class="input-group-addon">Filter</span>
 	   <input id="filter" type="text" class="form-control" placeholder="Type here...">
    </div>
    <br>


			<table cellspacing="0" width="100%" border="0" class="table table-striped" id="myTable">
				<thead>
					<tr class="info">
						<td><b>Sales No</b></td>
						<td><b>Customer</b></td>
						<td align="right"><b>Gross</b></td>
                        <!--<td align="right"><b>Due</b></td>-->
                        <td align="right">&nbsp;</td>
					</tr>
				</thead>

				 <tbody class="searchable">
              	<?php

				$sql = "select a.*,b.cname from sales a left join customers b on a.ccode=b.cempid where DATE(dcutdate) = CURDATE() and lcancelled=0 order by ctranno DESC";
				
				//echo $sql ; 
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td valign="middle"><?php echo $row['ctranno'];?></td>
						<td valign="middle"><?php echo $row['ccode'];?> - <?php echo utf8_encode($row['cname']);?> </td>
                        <td align="right" valign="middle"><?php echo $row['ngross'];?></td>
                       <!-- <td align="right" valign="middle"><?php //echo $row['ndue'];?></td>-->
                        <td align="right" valign="middle"><button type="button" class="btn btn-warning btn-xs" id="btncancel" onClick="Cancel('<?php echo $row['ctranno'];?>')">
    <span class="glyphicon glyphicon-remove"></span></button>
</td>
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

<input type="hidden" id="txtprodid" name="txtprodid">
	
</body>
</html>