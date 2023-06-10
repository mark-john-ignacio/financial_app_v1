<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "bir2307.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	//PAYOR INFO
	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
		$comptin =  str_replace("-",".",$row['comptin']);
		$compadd =  $row['compadd']; 
		$compzip =  $row['compzip'];
		//$comptin = $row['comptin'];
	}

	//PAYEE INFO
	$sql = "select * From suppliers where compcode='$company' and ccode='".$_POST["txtCustID"]."'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$payeename =  $row['cname'];
		$payeetin =  str_replace("-",".",$row['ctin']);
		$payeeadd =  $row['chouseno']; 
		if($row['ccity']!=""){
			$payeeadd = $payeeadd . ", ".$row['ccity'];
		}
		if($row['cstate']!=""){
			$payeeadd = $payeeadd . ", ".$row['cstate'];
		}
		if($row['ccountry']!=""){
			$payeeadd = $payeeadd . ", ".$row['ccountry'];
		}
		$payeezip =  $row['czip'];
	}


	$date1 = date("mdY", strtotime($_POST["date1"]));
	$date2 = date("mdY", strtotime($_POST["date2"]));
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BIR 2307</title>
	<style type="text/css">
		.form-container{
				position: relative;
				text-align: center;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 8.5in;
				height: 13in;
		}

		.datefrom{
			position: absolute;
			top: 132px;
			left: 195px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.dateto{
			position: absolute;
			top: 132px;
			left: 543px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
		}

		.payeetin{
			position: absolute;
			top: 175px;
			left: 271px;
			width: 278px;
			height:  25px;    
			letter-spacing: 12px;
			
		}

		.payeename{
			position: absolute;
			top: 213px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payeeadd{
			position: absolute;
			top: 253px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payeezip{
			position: absolute;
			top: 253px;
			left: 743px;
			width: 0.70in;
			height:  20px;    
			text-align: left;
			letter-spacing: 10px;
		}

		.payortin{
			position: absolute;
			top: 337px;
			left: 271px;
			width: 278px;
			height:  25px;    
			letter-spacing: 12px;
			
		}

		.payorname{
			position: absolute;
			top: 375px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payoradd{
			position: absolute;
			top: 415px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payorzip{
			position: absolute;
			top: 415px;
			left: 743px;
			width: 0.70in;
			height:  20px;    
			text-align: left;
			letter-spacing: 10px;
		}
		 
	</style>
</head>

<body>
	<div class="form-container" style="font-size: 0.9em;font-weight: bold;" >
		<img src="../../bir_forms/bir2307_page1.jpg" width="100%">
		<div class="datefrom"><?=$date1 ?></div>
		<div class="dateto"><?=$date2 ?></div> 

		<div class="payeetin"><?=$payeetin?></div> 
		<div class="payeename"><?=$payeename?></div> 
		<div class="payeeadd"><?=$payeeadd?></div> 
		<div class="payeezip"><?=$payeezip?></div>
		
		<div class="payortin"><?=$comptin?></div>
		<div class="payorname"><?=$compname?></div>
		<div class="payoradd"><?=$compadd?></div>
		<div class="payorzip"><?=$compzip?></div>
	</div>
</body>
</html>