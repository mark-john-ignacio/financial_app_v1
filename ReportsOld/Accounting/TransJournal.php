<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesReg.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];				
$fxmod = str_replace(",","','",$_REQUEST['typs']);
$date1 = $_REQUEST["dtefr"];
$date2 = $_REQUEST["dteto"];

$qry = "";
if($fxmod!=""){
	$qry = " and a.cmodule in ('".$fxmod."')";
}
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" href="../../Bootstrap/css/bootstrap-select.css?t=<?php echo time();?>">
    
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script src="../../Bootstrap/js/seljs/bootstrap-select.js?t=<?php echo time();?>" defer></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transaction Journal</title>
</head>

<body style="padding:10px">
<table width="100%" class="table table-condensed">

  <tr>
    <th class="text-center">Date</th>
    <th class="text-center">ID</th>
    <th colspan="2" class="text-center">Account</th>
    <th class="text-center">Debit</th>
    <th class="text-center">Credit</th>
  </tr>
  
<?php
$sql = "
select a.ddate, a.ctranno, a.cmodule, a.acctno, a.ctitle, a.ndebit, a.ncredit
From glactivity a 
where a.compcode='$company' and a.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qry."
order by A.ctranno, A.ddate, A.dpostdate, A.nidentity";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
	?>
  <tr>
    <td><?php echo date_format(date_create($row['ddate']), "m/d/Y");?></td>
    <td><?php echo $row['ctranno'];?></td>
    <td><?php echo $row['acctno'];?></td>
    <td><?php echo $row['ctitle'];?></td>
    <td align="right"><?php echo (($row['ndebit'] > 0) ? $row['ndebit'] : '');?></td>
    <td align="right"><?php echo (($row['ncredit'] > 0) ? $row['ncredit'] : '');?></td>
  </tr>
	<?php 
        }
    ?>
</table>
</body>
</html>