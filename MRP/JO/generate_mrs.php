<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];

	$dmonth = date("m");
	$dyear = date("y");

	$mggx = "";

	$def_From = "";
	$def_To = "";

		//check muna if existing na ung MRS for JO
		$sqlempsec = mysqli_query($con,"Select * from invtransfer Where compcode='$company' and mrp_jo_ctranno='".$_POST['id']."' and lcancelled1=0");
		if(mysqli_num_rows($sqlempsec) == 0){
			

		$sqlempsec = mysqli_query($con,"select A.ccode, A.cvalue From parameters A Where A.compcode='$company' and A.cstatus='ACTIVE' and A.ccode in ('JO_MRS_FROM','JO_MRS_TO') Order By A.cdesc");
		$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
		foreach($rowdetloc as $row0){
			if($row0['ccode']=="JO_MRS_FROM"){
				$def_From = $row0['cvalue'];
			}else if($row0['ccode']=="JO_MRS_TO"){
				$def_To = $row0['cvalue'];
			}				
		}

		$chkSales = mysqli_query($con,"select * from invtransfer where compcode='$company' and YEAR(ddatetime) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
		if (mysqli_num_rows($chkSales)==0) {
			$cTranNo = "IT".$dmonth.$dyear."00000";
		}
		else {
			while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
				$lastSI = $row['ctranno'];
			}
			
			
			if(substr($lastSI,2,2) <> $dmonth){
				$cTranNo = "IT".$dmonth.$dyear."00000";
			}
			else{
				$baseno = intval(substr($lastSI,6,5)) + 1;
				$zeros = 5 - strlen($baseno);
				$zeroadd = "";
				
				for($x = 1; $x <= $zeros; $x++){
					$zeroadd = $zeroadd."0";
				}
				
				$baseno = $zeroadd.$baseno;
				$cTranNo = "IT".$dmonth.$dyear.$baseno;
			}
		}

		if (!mysqli_query($con,"INSERT INTO invtransfer(`compcode`, `ctranno`, `cremarks`, `dcutdate`, `ctrantype`, `csection1`, `cpreparedby`, `csection2`,`mrp_jo_ctranno`) values('$company', '$cTranNo', '".$_POST['itm']."\n".$_POST['id']."', curdate(), 'request', '$def_From', '$preparedby', '$def_To', '".$_POST['id']."')")){

			$mggx = "False";

		}else{

			$sql = "select X.* from mrp_jo_process_m X where X.compcode='$company' and X.mrp_jo_ctranno = '".$_POST['id']."' Order By X.nid";
			$resultmain = mysqli_query ($con, $sql); 
			$cntr = 0;
			while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
				$cntr++;
				$cident = $cTranNo."P".$cntr;

				if (!mysqli_query($con,"INSERT INTO invtransfer_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty1`, `nqty2`) VALUES('$company', '$cTranNo','$cident', '$cntr', '".$row2['citemno']."', '".$row2['cunit']."', '".$row2['nqty']."', '".$row2['nqty']."')")){
					$mggx = "False";
				}
			}

			if($mggx == ""){
				$mggx = $cTranNo;
			}

		}
	}else{
		$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
		$mggx = $rowdetloc[0]['ctranno'];

		
	}

	echo $mggx;
	
?>
