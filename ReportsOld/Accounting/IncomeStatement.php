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
<h2>Profit &amp; Loss Statement</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<?php

$sqlgenerals = "select * From accounts where compcode='$company' and cFinGroup='Income Statement' order By cacctno";
$result=mysqli_query($con,$sqlgenerals);

?>
<table cellpadding="3px" width="50%" align="center" border="0">
	
    <?php
    	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
					$nlvl = intval($row['nlevel']);
					
					$indnt = 0;
					if($nlvl>1){
						$indnt = (5 * $nlvl) + ($nlvl * 2);
					}


		if($row['ctype']=="General"){
			

	?>
    <tr>
    	<td colspan="2" style="text-indent:<?php echo $indnt; ?>px"><b><?php echo $row['cacctdesc'];?></b></td>
    </tr>
    
    <?php
		}
		else{
	?>
     <tr>
    	<td style="text-indent:<?php echo $indnt; ?>px"><?php echo $row['cacctdesc'];?></td>
        <td style="text-indent:<?php echo $indnt; ?>px" width="200" align="right">&nbsp;</td>
    </tr>
   
    <?php
		}
		}
	?>
</table>
<hr>

</body>
</html>