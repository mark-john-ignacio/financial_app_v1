<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Purch_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Purch_cancel";
}

include('../include/access.php');


//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


if($_REQUEST['typ']=="POST"){
	
mysqli_query($con,"Update purchase set lapproved=1 where compcode='$company' and cpono='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','PURCHASE ORDER','$compname','Post Record')");

$status = "Posted";
}

if($_REQUEST['typ']=="CANCEL"){
	
mysqli_query($con,"Update purchase set lcancelled=1 where compcode='$company' and cpono='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','PURCHASE ORDER','$compname','Cancel Record')");

$status = "Cancelled";
}

?>
<script type="text/javascript">
	window.opener.document.getElementById("msg<?php echo $tranno;?>").innerHTML = "<?php echo $status; ?>";
	window.close();
</script>

