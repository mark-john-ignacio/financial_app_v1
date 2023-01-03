<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">   
<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap.js"></script>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">


</head>

<body style="padding:5px">

<?php
$varitm = $_REQUEST['itm'];
$company = $_SESSION['companyid'];

 if($_REQUEST['id']=="Purch"){
	 $varDet = "Supplier Name";
	 
	 $sql = "select A.ctranno, B.ccode, C.cname, B.dreceived as ddate, A.cunit, A.nprice from receive_t A left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno left join suppliers C on B.compcode=C.compcode and B.ccode=C.ccode where A.compcode='$company' and A.citemno='$varitm' order by B.dreceived DESC LIMIT 10";
?>

  <table id="example" class="table table-striped" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th width="150">Transaction No.</th>
          <th><?php echo $varDet;?></th>
          <th width="100">Date</th>
          <th width="70">UOM</th>
          <th width="100">Price</th>
        </tr>
      </thead>
      <tbody>
        <?php
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
        <tr>
          <td><?php echo $row['ctranno'];?></td>
          <td><?php echo $row['ccode'] ." - ". $row['cname'];?></td>
          <td><?php echo $row['ddate'];?></td>
          <td><?php echo $row['cunit'];?></td>
          <td align="right"><?php echo $row['nprice'];?></td>
        </tr>
        <?php 
				}
				
				mysqli_close($con);
				
				?>
      </tbody>
  </table>

<?php	 
 }elseif($_REQUEST['id']=="Sales"){
	 $varDet = "Customer Name";
	 $varPType = $_REQUEST['ptyp'];
	
	if($varPType=="MU"){ 
	 $sql = "select distinct B.dcutdate as ddate, A.cunit, A.nprice from sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join customers C on B.compcode=C.compcode and B.ccode=C.cempid where A.compcode='$company' and A.citemno='$varitm' and B.lcancelled=0 order by B.dcutdate DESC LIMIT 10";


?>

 <div class="col-xs-5 nopadding">
  <table id="example" class="table table-striped" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th width="100">Date</th>
          <th width="70">UOM</th>
          <th width="100">Price</th>
        </tr>
      </thead>
      <tbody>
        <?php
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
        <tr>
          <td><?php echo $row['ddate'];?></td>
          <td><?php echo $row['cunit'];?></td>
          <td align="right"><?php echo $row['nprice'];?></td>
        </tr>
        <?php 
				}
				
				mysqli_close($con);
				
				?>
      </tbody>
  </table>
</div>

<?php
	}else{

	 $sql = "select A.ctranno, B.cremarks, B.cversion, B.deffectdate as ddate, A.cunit, A.nprice, B.lapproved from items_pm_t A left join items_pm B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.citemno='$varitm' and B.lcancelled=0 order by B.deffectdate DESC LIMIT 10";

?>
  <table id="example" class="table table-striped" cellspacing="0" style="width:50vw">
      <thead>
        <tr>
          <th width="100">Transaction No.</th>
          <th>Remarks</th>
          <th width="100">Effect Date</th>
          <th width="80">Unit</th>
          <th width="100">Price</th>
          <th width="100">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
        <tr>
          <td><?php echo $row['ctranno'];?></td>
          <td><?php echo $row['cremarks'];?></td>
          <td><?php echo $row['ddate'];?></td>
          <td><?php echo $row['cunit'];?></td>
          <td align="right"><?php echo $row['nprice'];?></td>
          <td align="center">
          	<?php 
				if(intval($row['lapproved'])==intval(1)){
					echo "POSTED";
				}
				else{
					"PENDING";
				}
			?>
          </td>
        </tr>
        <?php 
				}
				
				mysqli_close($con);
				
				?>
      </tbody>
  </table>

<?php

	}
 }
?>
		
</body>
</html>