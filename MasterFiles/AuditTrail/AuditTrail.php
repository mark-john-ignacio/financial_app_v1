<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "AuditTrail.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
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
				<font size="+2"><u>Audit Trail</u></font>	
            </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
        <div class="col-xs-1 nopadwtop" style="height:30px !important;">
          <b> Search Item: </b>
        </div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
				</div>

				<div class="col-xs-3 text-right nopadwleft">
					<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
							<option value="">ALL MODULES</option>

                    <?php
                        $sql = "select DISTINCT module from logfile where compcode='$company' order by module";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                    ?>   
                        <option value="<?php echo $row['module'];?>"><?php echo $row['module']?></option>
                    <?php
                        }                        
                    ?>     
          </select>
				</div>
			</div>
												<br><br>
			<table class="table table-hover" role="grid" id="MyTable">
				<thead>
					<tr>
						<th>Transaction No.</th>
						<th>Module</th>
						<th>Event</th>
						<th>User</th>
            <th>PC Address</th>
            <th>Date</th>
					</tr>
				</thead>

			</table>

		</section>
	</div>		

</html>

  <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	<script>
		$(document).ready(function() {				
			filltable('xxx');

			$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();

				$('#MyTable').DataTable().destroy();
				filltable(searchByName);

			});
		});

		function filltable(searchByName){
			$('#MyTable').DataTable( {
				lengthMenu: [ [50, 75, 100, 150, -1], [50, 75, 100, 150, "All"] ],
				"searching": false,
				"paging": true,
				"serverSide": true,
				"ajax": {
					url: "th_datatable.php",
					type: "POST",
					data:{
						searchByName: searchByName
					}
				},
				"columns": [
					{ "data": 0 },
					{ "data": 1 },
					{ "data": 2 },
					{ "data": 5 },
					{ "data": 3 },
					{ "data": 4 }				
				],
				"columnDefs": [
					{ "targets": 5, "className": "text-right" } 
				],
			});
		}
	</script>
	