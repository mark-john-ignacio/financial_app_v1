<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "users_access.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cooperative System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="lib/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="../js/jquery.js"></script>
  <script src="lib/js/jquery.min.js"></script>
  <script src="lib/js/bootstrap.min.js"></script>
  
</head>
<body style="padding:5px">
<div>

		<section>
			<font size="+2"><u>Shares &amp; Savings</u></font>
			<br><br>

  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">Transactions</a></li>
    <li><a href="#menu1">Salary Deductions</a></li>
  </ul>

  <div class="tab-content">
   
   
    <div id="home" class="tab-pane fade in active">
		<br>
			<button type="button" class="btn btn-primary" onClick="location.href='Shares_new.php'">Create New Transaction</button>
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Trans No</th>
                        <th>Type</th>
						<th>Cutoff Date</th>
						<th>Trans Date</th>
						<th>Status</th>
					</tr>
				</thead>
                
				<tbody>
              	<?php
				$sql = "select a.*,b.dfrom, b.dto from savingshares a left join cutcodes b on a.cutcode=b.num order by a.cutcode, a.ctranno";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>','Shares_edit.php');"><?php echo $row['ctranno'];?></a></td>
 						<td><?php echo $row['ctype'];?></td>
                       <td><?php echo $row['dfrom'];?> To <?php echo $row['dto'];?></td>
                        <td><?php echo $row['ddate'];?></td>
						<td align="center">
                        <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')">CANCEL</a>
							<?php
                            }
							else{
								if(intval($row['lcancelled'])==intval(1)){
									echo "Cancelled";
								}
								if(intval($row['lapproved'])==intval(1)){
									echo "Posted";
								}
							}
							
							?>
                            </div>
                        </td>
					</tr>
                <?php 
				}
				
				//mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

    </div>
    <div id="menu1" class="tab-pane fade">
		<br>
			<button type="button" class="btn btn-primary" onClick="location.href='SharesDed_new.php'">Create New Transaction</button>
            <br><br>
			<table id="example2" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Trans No</th>
                        <th>Name</th>
                        <th>Type</th>
						<th>Cutoff Date</th>
						<th>Trans Date</th>
						<th>Status</th>
					</tr>
				</thead>
                
				<tbody>
              	<?php
				$sql2 = "select a.*,b.dfrom, b.dto, c.cname from salarydeduct a left join cutcodes b on a.dcutcode=b.num left join customers c on a.ccode=c.cempid order by a.dcutcode, a.ctranno";
				
				$result2=mysqli_query($con,$sql2);
				
					if (!mysqli_query($con, $sql2)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row2['ctranno'];?>','SharesDed_edit.php');"><?php echo $row2['ctranno'];?></a></td>
                        <td nowrap><?php echo $row2['ccode']." - ".$row2['cname'];?></td>
 						<td><?php echo $row2['ctype'];?></td>
                       <td><?php echo $row2['dfrom'];?> To <?php echo $row2['dto'];?></td>
                        <td><?php echo $row2['ddate'];?></td>
						<td align="center">
                        <div id="msg<?php echo $row2['ctranno'];?>">
                        	<?php 
							if(intval($row2['lcancelled1'])==intval(0) && intval($row2['lposted1'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row2['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row2['ctranno'];?>')">CANCEL</a>
							<?php
                            }
							else{
								if(intval($row2['lcancelled1'])==intval(1)){
									echo "Cancelled";
								}
								if(intval($row2['lposted1'])==intval(1)){
									echo "Posted";
								}
							}
							
							?>
                            </div>
                        </td>
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
            </table>

    </div>
</div>

</section>
</div>

</form>

<form name="frmedit" id="frmedit" method="post" action="Shares_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>	
	

	<script type="text/javascript" language="javascript" src="lib/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="dist/bootstrapPager.min.js"></script>
	
	<script>
	$('#example').DataTable({
	   pagingType: "bootstrapPager",
	   pagerSettings: {
			searchOnEnter: true,
			language:  "Page ~ of ~ pages"
	   }
	});

	$('#example2').DataTable({
	   pagingType: "bootstrapPager",
	   pagerSettings: {
			searchOnEnter: true,
			language:  "Page ~ of ~ pages"
	   }
	});


$(document).ready(function(){
    $(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
    $('.nav-tabs a').on('shown.bs.tab', function(event){
        var x = $(event.target).text();         // active tab
        var y = $(event.relatedTarget).text();  // previous tab
       // $(".act span").text(x);
        //$(".prev span").text(y);
    });
});
	
	
	function editfrm(x,y){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").action = y;
	document.getElementById("frmedit").submit();
}


	</script>
</body>
</html>
