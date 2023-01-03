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
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css">
    
  <script type="text/javascript" src="../js/jquery.js"></script>
  
  <script type="text/javascript" src="lib/js/bootstrap-select.js"></script>
  <script src="../js/bootstrap.min.js"></script>

  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body style="padding:5px">
<?php 

$cust = "";					
$dTDate = "";
$cremarks = "";
$ngross = 0;
$salesno = $_REQUEST['id'];;

$sql = "select a.*,b.cname from receive a left join suppliers b on a.ccode=b.ccode where a.lapproved=1 and a.ctranno='$salesno' order by ctranno";

				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$cust = $row['cname'];
					$dTDate = $row['dreceived'];
					$cremarks = $row['cremarks'];
					$ngross = $row['ngross'];
					
				}
				
				mysqli_close($con);
?>
<table width="100%" cellpadding="2" cellspacing="2">
<tr>
	<td width="23%"><b>Customer: </b></td>
    <td width="77%">&nbsp;&nbsp;<input type="text" name="typ" id="typ" value="<?php echo $cust; ?>" readonly style="border:none; height:30px; width:90%"/></td>
</tr>
<tr>
	<td><b>Receive Date: </b></td>
    <td>&nbsp;&nbsp;<input type="text" name="date" id="date" value="<?php echo $dTDate; ?>" readonly style="border:none; height:30px; width:90%"/></td>
</tr>
<tr>
	<td><b>Remarks: </b></td>
    <td>&nbsp;&nbsp;<input type="text" name="rem" id="rem" value="<?php echo $cremarks; ?>" readonly style="border:none; height:30px; width:90%"/></td>
</tr>
<tr>
  <td><b>Gross: </b></td>
  <td>&nbsp;&nbsp;<input type="text" name="gross" id="gross" value="<?php echo $ngross; ?>" readonly style="border:none; height:30px; width:90%"/></td>
</tr>
</table>
</body>
</html>