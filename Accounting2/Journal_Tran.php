<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Journal_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Journal_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');



if($_REQUEST['typ']=="POST"){
	
mysqli_query($con,"Update journal set lapproved=1, ddateposted = NOW() where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','JOURNAL ENTRY','$compname','Post Record')");

$status = "Posted";

}

if($_REQUEST['typ']=="CANCEL"){
	
	echo $_REQUEST['x'];
	
mysqli_query($con,"Update journal set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','JOURNAL ENTRY','$compname','Cancel Record')");

$status = "Cancelled";
}

?>
<script type="text/javascript">
	window.opener.document.getElementById("msg<?php echo $tranno;?>").innerHTML = "<?php echo $status; ?>";
	window.close();
</script>

