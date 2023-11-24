<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Deposit_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Deposit_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
mysqli_query($con,"Update deposit set lapproved=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','BANK DEPOSIT','$compname','Post Record')");

$status = "Posted";


				$sqlbody = mysqli_query($con,"select * from deposit_t where compcode='$company' and  ctranno = '$tranno' order by nidentity");

						if (mysqli_num_rows($sqlbody)!=0) {
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								
								$cornum = $rowbody['corno'];
								mysqli_query($con,"Update receipt set ldeposited=1 where compcode='$company' and ctranno='$cornum'");
								
							}
						}

}

if($_REQUEST['typ']=="CANCEL"){
	
mysqli_query($con,"Update deposit set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','BANK DEPOSIT','$compname','Cancel Record')");

$status = "Cancelled";
}

?>
<script type="text/javascript">
	window.opener.document.getElementById("msg<?php echo $tranno;?>").innerHTML = "<?php echo $status; ?>";
	window.close();
</script>

