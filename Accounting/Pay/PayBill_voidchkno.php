<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	$chkno = $_REQUEST['chkno'];
	$rem = $_REQUEST['rem'];
	$ctyp = $_REQUEST['xtyp']; 
	$authcode = $_REQUEST['authcode']; 
	$chkno = $_REQUEST['chkno'];

	$ccurchk = "";
	$ccurchklast = "";
	$ccurchkbk = "";

	//getlatest
	$sql = mysqli_query($con,"Select * from bank_check where compcode='$company' and ccode='$y' and ccheckto <> ccurrentcheck"); 
	while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
	{
		$ccurchk = $row['ccurrentcheck'];
		$ccurchklast = $row['ccheckto'];
		$ccurchkbk = $row['ccheckno'];
	}

	$ifisyes = "True";
	$ifisyesmsg = "";

	if((float)$ccurchk==(float)$chkno){

			$cnewchk = (float)$chkno + 1;

			$sql = "Update bank_check set ccurrentcheck='$cnewchk' where compcode='$company' and ccode='$y' and ccheckno ='".$_REQUEST['chkbkno']."'"; 
			if (!mysqli_query($con, $sql)) {
				if(mysqli_error($con)!=""){
					$ifisyes = "False";
					$ifisyesmsg = "Error: ".mysqli_error($con);
				}
			}

			$cnewchk = $cnewchk.":".$ccurchkbk;
		
	}else{

		$cnewchk = $ccurchk.":".$ccurchkbk;

	}

		echo $cnewchk;

		if($ctyp=="void"){

			$sql2 = "INSERT INTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`ccheckbook`,`cremarks`,`ddate`,`cauthcode`) VALUES('$company','$y','$chkno','".$_REQUEST['chkbkno']."','$rem',NOW(),'$authcode')"; 

		}elseif($ctyp=="reserve"){

			$sql2 = "INSERT INTO bank_reserves(`compcode`,`cbankcode`,`ccheckno`,`ccheckbook`,`cremarks`,`ddate`,`cauthcode`) VALUES('$company','$y','$chkno','".$_REQUEST['chkbkno']."','$rem',NOW(),'$authcode')";

		}
		
		//insert in void table
		
		if (!mysqli_query($con, $sql2)) {
			if(mysqli_error($con)!=""){
				echo "Error: ".mysqli_error($con);

				//."<br>"."INSERT INTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`) VALUES('$company','$y','$chkno','$rem',NOW())"
			}
		}
			
	
?>
