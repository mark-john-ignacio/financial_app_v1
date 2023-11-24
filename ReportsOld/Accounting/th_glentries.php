<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesReg.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];				
$date1 = $_REQUEST["date1"];
$date2 = $_REQUEST["date2"];

				$sql = "select * From company where compcode='$company'";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$compname =  $row['compname'];
				}

?>

<html>
<head>

<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Income Statement</title>
</head>

<body style="padding:10px">

<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Income Statement</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>


<?php



$sqlqry = "select * From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctno where compcode='$company' and B.cFinGroup='Income Statement' and ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') order By A.acctno, A.dDate";
$result=mysqli_query($con,$sql);

?>

</body>
</html>