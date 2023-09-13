<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$dmonth = date("m");
	$dyear = date("y");
	$company = $_SESSION['companyid'];

	$chkSales = mysqli_query($con,"select * from mrp_jo where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");

	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "JOR-".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}

		if(substr($lastSI,4,2) <> $dmonth){
			$cSINo = "JOR-".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,8,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
					
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
					
			$baseno = $zeroadd.$baseno;
			$cSINo = "JOR-".$dmonth.$dyear.$baseno;
		}
	}

	
	$cCustID = mysqli_real_escape_string($con, $_POST['txtcustid']);
	$dTargetDate = mysqli_real_escape_string($con, $_POST['txtTargetDate']);
	$cRefSO = mysqli_real_escape_string($con, $_POST['crefSO']); 
	$cPriority = mysqli_real_escape_string($con, $_POST['selpriority']);
	$cDept = mysqli_real_escape_string($con, $_POST['seldept']);
	$cRemarks = mysqli_real_escape_string($con, $_POST['txtcremarks']);

	$cItemNo = mysqli_real_escape_string($con, $_POST['citemno']); 
	$cItemIdent = mysqli_real_escape_string($con, $_POST['nrefident']); 
	$cItemUnit = mysqli_real_escape_string($con, $_POST['txtcunit']);

	$njoqty = mysqli_real_escape_string($con, $_POST['txtjoqty']);	
	$nworkhrs = mysqli_real_escape_string($con, $_POST['txtworkinghrs']);	
	$nsetup = mysqli_real_escape_string($con, $_POST['txtsetuptime']);	
	$ncycle = mysqli_real_escape_string($con, $_POST['txtcycletime']);	
	$ntottime = mysqli_real_escape_string($con, $_POST['txtntotal']);	

	$njoqty = str_replace( ',', '', $njoqty);
	$nworkhrs = str_replace( ',', '', $nworkhrs);
	$nsetup = str_replace( ',', '', $nsetup);
	$ncycle = str_replace( ',', '', $ncycle);
	$ntottime = str_replace( ',', '', $ntottime);

	$dret = 0;
	if(isset($_REQUEST['isWRef'])){
		$dret = 1;
	}

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);


	if (!mysqli_query($con, "INSERT INTO mrp_jo(`compcode`, `ctranno`, `ccode`, `crefSO`, `nrefident`, `citemno`, `cunit`, `nqty`, `dtargetdate`, `cpriority`, `nworkhrs`, `nsetuptime`, `ncycletime`, `ntottime`, `location_id`, `lnoref`, `cremarks`) values('$company', '$cSINo', '$cCustID', '$cRefSO', '$cItemIdent', '$cItemNo', '$cItemUnit', '$njoqty', STR_TO_DATE('$dTargetDate', '%m/%d/%Y'), '$cPriority', '$nworkhrs', '$nsetup', '$ncycle', '$ntottime', '$cDept', $dret,'$cRemarks')")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{

		$mggx = "Record Succesfully Saved\nProceed to process and material generation!";

		//insert attachment

			$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
			// Count the number of uploaded files in array
			$total_count = count($_FILES['upload']['name']);


			if($total_count>=1 && $_FILES['upload']['name'][0] !=""){
				mkdir('../../Components/assets/JOR/'.$company.'_'.$cSINo.'/',0777);
			}

			// Loop through every file
			for( $i=0 ; $i < $total_count ; $i++ ) {
				//The temp file path is obtained
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
				//A file path needs to be present
				if ($tmpFilePath != ""){
						//Setup our new file path
						$newFilePath = "../../Components/assets/JOR/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
						//File is uploaded to temp dir
						move_uploaded_file($tmpFilePath, $newFilePath);
						
				}
			}

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','JOR','$compname','Inserted New Record')");

	}
	

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
</head>

<body style="padding-top:20px">

<h2><center>SAVING JOB ORDER...</center><h2>
<h1><center><span id="counter"> 0 </span></center><h1>

<form action="JO_GenJO.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>

</body>
</html>

<script type="text/javascript">

	var count = 5;

	$(document).ready(function() {
		counter();
	});

	function counter()
  {
		if ( count > 0 )
    {
			count--;
      document.querySelector("#counter").innerHTML = count;
			var c = setTimeout( counter, 500 );
		}else
    {
			document.forms['frmpos'].submit();
		}
	}


 
</script>