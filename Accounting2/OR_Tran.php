<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "OR_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "OR_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
mysqli_query($con,"Update receipt set lapproved=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','RECEIVE MONEY','$compname','Post Record')");

$status = "Posted";

}

if($_REQUEST['typ']=="CANCEL"){
	
mysqli_query($con,"Update receipt set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','RECEIVE MONEY','$compname','Cancel Record')");

$status = "Cancelled";
}

?>
<script type="text/javascript">
	window.opener.document.getElementById("msg<?php echo $tranno;?>").innerHTML = "<?php echo $status; ?>";
	window.close();
</script>

