<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

$company = $_SESSION['companyid'];

// get last row of received msg in CSV
	$rows = file("../../SMSCaster/RecvSms.csv");
	$last_row = array_pop($rows);
	$data = str_getcsv($last_row); 
	
//pag wla pa sa table insert sa database for log

	// kunin dn ung last ID sa database
$lastSI="0";
$chkSales = mysqli_query($con,"select * from smsrcv where compcode='$company' Order By cmsgid desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
}

if((int)$data[0]!=(int)$lastSI){
	//insert to dbase
		if (!mysqli_query($con, "INSERT INTO smsrcv(`compcode`, `cmsgid`, `cport`, `cfrom`, `cmsg`, `cdate`) values('$company', '".$data[0]."', '".$data[1]."', '".$data[2]."', '".$data[3]."', '".$data[4]."')")) {
		//echo "False";
	} 
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>

<body style="padding:5px">
	<div class="col-sm-12 nopadding">
    	<div class="col-sm-2 nopadding">
        	<b>From: </b>
        </div>
        <div class="col-sm-4 nopadding">
        	<?php echo $data[2]; ?>
        </div>

    	<div class="col-sm-2 nopadding">
        	<b>Date: </b>
        </div>
        <div class="col-sm-3 nopadding">
        	<?php echo $data[4]; ?>
        </div>

    </div>
    
    <div class="col-sm-12 nopadding">
    	<div class="col-sm-2 nopadding">
        	<b>Body: </b>
        </div>
        <div class="col-sm-10 nopadding">
        	<?php echo $data[3]; ?>
        </div>
    </div>
</body>
</html>

