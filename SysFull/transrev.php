<?php
//ob_start();
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../Accounting/InsertToGL.php');
include('../Inventory/InsertToInv.php');

$company = $_SESSION['companyid'];
$sino = $_GET['x'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


}
elseif($_GET['t']=="can" or $_GET['t']=="can2"){
	mysqli_query($con,"UPDATE sales set lcancelled=1, lapproved=0 where compcode='$company' and csalesno='$sino'");
mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$sino','$preparedby',NOW(),'CANCEL','POS RETAIL','$compname','Cancelled Record')");

}
//header( 'Location: ../Sys/' ) ;

if($_GET['t']=="can2"){
	echo "<script>window.location.href='list.php'</script>";
}
else{
	echo "<script>window.location.href='../Sys/'</script>";
}



?>
