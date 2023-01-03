<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

$ctranno = $_REQUEST['citemno'];
$nqty = $_REQUEST['qty'];

$nqtyin = $nqty;

$company = $_SESSION['companyid'];


//$sqlhead = mysqli_query($con2,"select A.ctranno, A.citemno, A.cunit, A.nqty, A.nprice, B.dreceived, B.ddate from receive_t A left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode = '$company' and A.citemno='$ctranno' and YEAR(B.dreceived) < '2018' Order by B.dreceived desc, B.ddate desc ");

$sqlhead = mysqli_query($con2,"select A.ctranno, A.citemno, A.cunit, A.nqty, A.ncostin as nprice, A.dcutdate as dreceived, A.ddatetime as ddate from tblinventory A where A.compcode = '$company' and A.citemno='$ctranno' and YEAR(A.dcutdate) < '2018' and A.ctranno='BEG' <> 0 Order by A.dcutdate desc, A.ddatetime desc");

echo mysqli_num_rows($sqlhead)."<br>";

if (mysqli_num_rows($sqlhead)!=0) {
	
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
	echo $nqtyin."<br>";	
	 if($nqtyin > 0 ){
		 
		$nqtyb4 = $nqtyin;	
		$nqtyin = floatval($nqtyin) - floatval($row['nqty']);
		
		if($nqtyin >= 1 ){
			
			$qtyinsert = $row['nqty'];
			
		}else{
			
			$qtyinsert = $nqtyb4;
		}
		
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dexpired`) values('$company','".$row['ctranno']."','$ctranno','".$row['cunit']."','".$qtyinsert."','".$row['cunit']."','1','".$qtyinsert."','".$row['nprice']."','".$row['dreceived']."','".$row['dreceived']."')");
			
			echo "OK"."<br>";
			
	 }
		
	}
	
}


mysqli_query($con2,"Update itemcost set crem = 'Y' where compcode='$company' and citemno='$ctranno'");

?>
 <script>
 	top.window.location="uploadtran_Del.php";
 </script>
