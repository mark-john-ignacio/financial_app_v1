<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

function numberTowords($num)
{
	$ones = array(
		0 =>"ZERO",
		1 => "ONE",
		2 => "TWO",
		3 => "THREE",
		4 => "FOUR",
		5 => "FIVE",
		6 => "SIX",
		7 => "SEVEN",
		8 => "EIGHT",
		9 => "NINE",
		10 => "TEN",
		11 => "ELEVEN",
		12 => "TWELVE",
		13 => "THIRTEEN",
		14 => "FOURTEEN",
		15 => "FIFTEEN",
		16 => "SIXTEEN",
		17 => "SEVENTEEN",
		18 => "EIGHTEEN",
		19 => "NINETEEN",
		"014" => "FOURTEEN"
	);
	$tens = array( 
		0 => "ZERO",
		1 => "TEN",
		2 => "TWENTY",
		3 => "THIRTY", 
		4 => "FORTY", 
		5 => "FIFTY", 
		6 => "SIXTY", 
		7 => "SEVENTY", 
		8 => "EIGHTY", 
		9 => "NINETY" 
	); 
	$hundreds = array( 
		"HUNDRED", 
		"THOUSAND", 
		"MILLION", 
		"BILLION", 
		"TRILLION", 
		"QUARDRILLION"
	); /*limit t quadrillion */
	$num = number_format($num,2,".",","); 
	$num_arr = explode(".",$num); 
	$wholenum = $num_arr[0]; 
	$decnum = $num_arr[1]; 
	$whole_arr = array_reverse(explode(",",$wholenum)); 
	krsort($whole_arr,1); 
	$rettxt = ""; 

	foreach($whole_arr as $key => $i){
	
		while(substr($i,0,1)=="0")
			$i=substr($i,1,5);
			if($i!=="") {

				if($i < 20){ 
					/* echo "getting:".$i; */
					$rettxt .= $ones[$i]; 
				}elseif($i < 100){ 
					if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)] . "-"; 
					if(substr($i,1,1)!="0") $rettxt .= "".$ones[substr($i,1,1)]; 
				}else{ 
					if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 

					if(substr($i,1,1)==1){
						if(substr($i,2,1)==0){
							$rettxt .= " ".$tens[substr($i,1,1)];
						}else{
							$rettxt .= " ".$ones[substr($i,1,2)];
						}
					}else{
						if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
						if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
					}

				} 

			}
			
			if($key > 0){ 
				$rettxt .= " ".$hundreds[$key]." "; 
			}
		} 

		if($decnum > 0){
			$rettxt .= " PESOS AND ";
		
			if($decnum < 20){
				//$rettxt .= $ones[$decnum];
			}elseif($decnum < 100){
			//	$rettxt .= $tens[substr($decnum,0,1)];
			//	$rettxt .= " ".$ones[substr($decnum,1,1)];
			}

			$rettxt .= $decnum ."/100";

		}else{
			$rettxt .= " PESOS ONLY";
		}
	return $rettxt;
}

	$company = $_SESSION['companyid'];

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}
	
	$csalesno = $_REQUEST['id'];
	$sqlhead = mysqli_query($con,"select A.*, B.cname, B.cdoctype from paybill A left join bank B on A.compcode=B.compcode and A.cbankcode=B.ccode where A.compcode='$company' and A.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$Payee = $row['cpayee'];
			$Date = $row['dcheckdate'];

			$Particulars = $row['cparticulars'];
			$Amount = $row['npaid']; 

			$Bankname = $row['cname']; 

			$Paymeth = $row['cpaymethod'];
			$Refno = ($row['cpaymethod']=="cheque") ? $row['ccheckno'] : $row['cpayrefno']; 

			$cdoctype = $row['cdoctype'];
			//1. BDO/LANDBANK CHECK FORMAT
			//2. METROBANK CHECK FORMAT
			//3. EASTWEST CHECK FORMAT
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Check</title>
	
</head>
<body>

<?php

	if($cdoctype==1){
		$dmo = date_format(date_create($Date),"m");
		$ddt = date_format(date_create($Date),"d");
		$dyr = date_format(date_create($Date),"Y");
?>
	<style>
		body{
			font-family: 'Courier New', monospace !important;
			font-weight: 900 !important;
		}
		table {
			border-collapse: collapse;
		}
		.ewdatemo{
			position: absolute;
			float: right;
			top: 45px;
			right: 215px;
			width: 30px;
			height:  25px;    
			letter-spacing:5px;
			font-size: 8pt;			
			/*border: 1px solid #000;*/				
		}
		.ewdatedy{
			position: absolute;
			float: right;
			top: 45px;
			right: 179px;
			width: 30px;
			height:  25px;    
			letter-spacing:5px;
			font-size: 8pt;
		}
		.ewdateyr{
			position: absolute;
			float: right;
			top: 45px;
			right: 139px;
			width: 30px;
			height:  25px;    
			letter-spacing:5px;
			font-size: 8pt;
		}
		.ewdamt{
			position: absolute;
			float: right;
			top: 80px;
			right:70px;
			width: 150px;
			height:  18px;   
			/*border: 1px solid #000;*/
		}
		.ewdpay{
			position: absolute;
			top: 81px;
			left: 165px;
			width: 338px;
			height:  18px;   
			/*border: 1px solid #000;*/
		}
		.ewdamtwords{
			position: absolute;
			top: 110px;
			left: 132px;
			width: 600px;
			height:  18px;    
			/*border: 1px solid #000;*/
			
		}
		
	</style>
		
	<div class="ewdatemo"><?=$dmo?></div>
	<div class="ewdatedy"><?=$ddt?></div>
	<div class="ewdateyr"><?=$dyr?></div>

	<div class="ewdamt"><?=number_format($Amount,2)?></div>
	<div class="ewdpay"><?=$Payee?></div>
	<div class="ewdamtwords"><?=numberTowords($Amount)?></div>
<?php
	}elseif($cdoctype==2){
?>

		<style>
			body{
				font-family: 'Courier New', monospace !important;
				font-weight: 900 !important;
			}
			table {
				border-collapse: collapse;
			}
			.ewdatemo{
				position: absolute;
				float: right;
				top: 45px;
				right: 215px;
				width: 30px;
				height:  25px;    
				letter-spacing:5px;
				font-size: 8pt;			
				/*border: 1px solid #000;*/				
			}
			.ewdatedy{
				position: absolute;
				float: right;
				top: 45px;
				right: 179px;
				width: 30px;
				height:  25px;    
				letter-spacing:5px;
				font-size: 8pt;
			}
			.ewdateyr{
				position: absolute;
				float: right;
				top: 45px;
				right: 139px;
				width: 30px;
				height:  25px;    
				letter-spacing:5px;
				font-size: 8pt;
			}
			.ewdamt{
				position: absolute;
				float: right;
				top: 75px;
				right:90px;
				width: 150px;
				height:  18px;    
				/*border: 1px solid #000;*/
			}
			.ewdpay{
				position: absolute;
				top: 80px;
				left: 130px;
				width: 338px;
				height:  18px;   

				/*border: 1px solid #000;*/
			}
			.ewdamtwords{
				position: absolute;
				top: 108px;
				left: 108px;
				width: 600px;
				height:  18px;    

				/*border: 1px solid #000;*/
				
			}
			
		</style>
<?php
		$dmo = date_format(date_create($Date),"m");
		$ddt = date_format(date_create($Date),"d");
		$dyr = date_format(date_create($Date),"Y");
?>

	<div class="ewdatemo"><?=$dmo?></div>
	<div class="ewdatedy"><?=$ddt?></div>
	<div class="ewdateyr"><?=$dyr?></div>

	<div class="ewdamt"><?=number_format($Amount,2)?></div>
	<div class="ewdpay"><?=$Payee?></div>
	<div class="ewdamtwords"><?=numberTowords($Amount)?></div>
<?php
	}elseif($cdoctype==3){
?>

	<style>
		body{
			font-family: 'Courier New', monospace !important;
			font-weight: 900 !important;
		}
		table {
			border-collapse: collapse;
		}
		.ewdatemo{
			position: absolute;
			float: right;
			top: 45px;
			right: 225px;
			width: 30px;
			height:  25px;    
			letter-spacing:8px;
			font-size: 8pt;
			/*border: 1px solid #000;*/
			
		}
		.ewdatedy{
			position: absolute;
			float: right;
			top: 45px;
			right: 179px;
			width: 30px;
			height:  25px;    
			letter-spacing:8px;
			font-size: 8pt;
		}
		.ewdateyr{
			position: absolute;
			float: right;
			top: 45px;
			right: 129px;
			width: 30px;
			height:  25px;    
			letter-spacing:8px;
			font-size: 8pt;
		}
		.ewdamt{
			position: absolute;
			float: right;
			top: 75px;
			right:90px;
			width: 150px;
			height:  18px;    
			/*border: 1px solid #000;*/
		}
		.ewdpay{
			position: absolute;
			top: 80px;
			left: 150px;
			width: 338px;
			height:  18px;    
			line-height: 15px;
			/*border: 1px solid #000;*/
		}
		.ewdamtwords{
			position: absolute;
			top: 108px;
			left: 108px;
			width: 600px;
			height:  18px;    
			/*border: 1px solid #000;*/
			
		}
		
	</style>
<?php
		$dmo = date_format(date_create($Date),"m");
		$ddt = date_format(date_create($Date),"d");
		$dyr = date_format(date_create($Date),"Y");

?> 
	<div class="ewdatemo"><?=$dmo?></div>
	<div class="ewdatedy"><?=$ddt?></div>
	<div class="ewdateyr"><?=$dyr?></div>

	<div class="ewdamt"><?=number_format($Amount,2)?></div>
	<div class="ewdpay"><?=$Payee?></div>
	<div class="ewdamtwords"><?=numberTowords($Amount)?></div>

<?php
	}
?>

</body>
</html>
