<?php
//ob_start();
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../Accounting/InsertToGL.php');
include('../Accounting/InsertToInv.php');

$company = $_SESSION['companyid'];
$sino = $_GET['x'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

//get SI cutdate

		$dcutdate = mysqli_query($con,"Select * from sales Where ctranno='$sino'");
		
		while($dcutdatez = mysqli_fetch_array($dcutdate, MYSQLI_ASSOC)){
			
			$dates = $dcutdatez['dcutdate'];
					
		}


if($_GET['t']=="post"){
	
mysqli_query($con,"UPDATE sales set lapproved=1 where compcode='$company' and ctranno='$sino'");
mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$sino','$preparedby',NOW(),'POSTED','POS RETAIL','$compname','Post Record')");

SIEntry($sino);
ToInv($sino,"POS","OUT",$dates);


//Update items table stockonhands
		$UpdateItem = mysqli_query($con,"Select A.citemno, B.citemdesc, B.nqty, A.nqty as nqtyin, A.nfactor as nfactorin From sales_t A left join items B on A.citemno=B.cpartno Where A.ctranno='$sino'");
		
		while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			
			$itmpartno = $itmupdate['citemno'];
			$nstock = $itmupdate['nqty'] - ($itmupdate['nqtyin'] * $itmupdate['nfactorin']);
			
			mysqli_query($con,"Update items set nqty=$nstock where cpartno='$itmpartno'");
					
		}
}
elseif($_GET['t']=="can" or $_GET['t']=="can2"){
	mysqli_query($con,"UPDATE sales set lcancelled=1, lapproved=0 where compcode='$company' and ctranno='$sino'");
mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$sino','$preparedby',NOW(),'CANCEL','POS RETAIL','$compname','Cancelled Record')");
	
//delete ulit ung entry sa ledger
mysqli_query($con,"Delete From glactivity where compcode='$company' and ctranno='$sino'");

//dahil auto post need ireverse entry inventory	
$csiX = "CAN-".$sino;
			mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$csiX', NOW(),'$dates','POS', A.citemno, A.cunit, A.nqty, A.cunit,1, A.nqty, A.ncost, A.nprice, 0, 0, 0 From sales_t A where A.csalesno='$sino'");

//Update items table stockonhands .. balik sa inventory
		//$UpdateItem = mysqli_query($con,"Select A.citemno, B.citemdesc, B.nqty, A.nqty as nqtyin, A.nfactor as nfactorin From sales_t A left join items B on A.citemno=B.cpartno Where A.ctranno='$sino'");
		
		//while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			
		//	$itmpartno = $itmupdate['citemno'];
		//	$nstock = $itmupdate['nqty'] + ($itmupdate['nqtyin'] * $itmupdate['nfactorin']);
			
		//	mysqli_query($con,"Update items set nqty=$nstock where cpartno='$itmpartno'");
					
		//}


}
//header( 'Location: ../Sys/' ) ;

if($_GET['t']=="can2"){
	echo "<script>window.location.href='list.php'</script>";
}
else{
	echo "<script>window.location.href='../Sys/'</script>";
}



?>
