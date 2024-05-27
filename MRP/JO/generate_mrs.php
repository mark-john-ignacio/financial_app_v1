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

	$arraylistjo = array();
	
	$arraylistjo[] = $_POST['id'];

	//check if may materials na nagenerate
	$abcgeenearte = array();
	$zbcvcnt = 0;
	$sql = "select X.*, Y.citemdesc as cRefItmDesc from mrp_jo_process_mrs X Left Join items Y on X.compcode=Y.compcode and X.crefitem=Y.cpartno where X.compcode='$company' and X.mrp_jo_ctranno = '".$_POST['id']."' Order By X.nid";
	$resultmain = mysqli_query ($con, $sql);
	if (mysqli_num_rows($resultmain)>0) {
		$zbcvcnt = 0;
		while($row = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
			$abcgeenearte[] = $row;
		}

		$sqlxc = "select X.* from mrp_jo_process X where X.compcode='$company' and X.mrp_jo_ctranno = '".$_POST['id']."' Order By X.nid";
		$resubjosx = mysqli_query ($con, $sqlxc);
		if (mysqli_num_rows($resubjosx)>0) {
			while($rowzxc = mysqli_fetch_array($resubjosx, MYSQLI_ASSOC)){
				$arraylistjo[] = $rowzxc['ctranno'];
			}
		}

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


			foreach($arraylistjo as $lmp){

				$conti = "FALSE";
				foreach($abcgeenearte as $row2){
					if($row2['mrp_jo_sub']==$lmp){
						$conti = "TRUE";
					}
				}

				if($conti=="TRUE"){
					
					$chkSales = mysqli_query($con,"select * from invtransfer where compcode='$company' and YEAR(ddatetime) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
					if (mysqli_num_rows($chkSales)==0) {
						$cTranNo = "IT".$dyear."000000001";
					}
					else {
						while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
							$lastSI = $row['ctranno'];
						}
						
						
						if(substr($lastSI,2,2) <> $dyear){
							$cTranNo = "IT".$dyear."000000001";
						}
						else{
							$baseno = intval(substr($lastSI,4,9)) + 1;
							$zeros = 9 - strlen($baseno);
							$zeroadd = "";
							
							for($x = 1; $x <= $zeros; $x++){
								$zeroadd = $zeroadd."0";
							}
							
							$baseno = $zeroadd.$baseno;
							$cTranNo = "IT".$dyear.$baseno;
						}
					}

					$itemrefvbn = "";
					foreach($abcgeenearte as $row2){
						if($row2['mrp_jo_sub']==$lmp){
							$itemrefvbn = $row2['cRefItmDesc'];
						}
					}


					if (!mysqli_query($con,"INSERT INTO invtransfer(`compcode`, `ctranno`, `cremarks`, `dcutdate`, `ctrantype`, `csection1`, `cpreparedby`, `csection2`,`mrp_jo_ctranno`) values('$company', '$cTranNo', '".$itemrefvbn."\n".$lmp."', curdate(), 'request', '$def_From', '$preparedby', '$def_To', '".$_POST['id']."')")){

						$mggx = "False";

					}else{
						
						$cntr = 0;
						//while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
						foreach($abcgeenearte as $row2){
							if($row2['mrp_jo_sub']==$lmp){
								$cntr++;
								$cident = $cTranNo."P".$cntr;

								if (!mysqli_query($con,"INSERT INTO invtransfer_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty1`, `nqty2`, `cremarks`) VALUES('$company', '$cTranNo','$cident', '$cntr', '".$row2['citemno']."', '".$row2['cunit']."', '".$row2['nqty']."', '".$row2['nqty']."', '".$row2['cremarks']."')")){
									$mggx = "False";
								}
							}
						}

						mysqli_query($con,"UPDATE mrp_jo_process_mrs set invtransfer_ctranno='".$cTranNo."' Where compcode='$company' and mrp_jo_sub='".$lmp."'");

						if($mggx == ""){
							$mggx = $cTranNo;
						}

					}

				}

			}
		}else{
			$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
			$mggx = $rowdetloc[0]['ctranno'];

			
		}

		echo $mggx;
	}else{
		echo "0";
	}
	
?>
