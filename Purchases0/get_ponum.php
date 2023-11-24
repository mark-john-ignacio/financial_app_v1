<?php
if(!isset($_SESSION)){
session_start();
}

$company = $_SESSION['companyid'];
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select a.*,b.cname from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono LIKE '%$q%' and a.lcancelled=0 and a.lapproved=1";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$csalesno = $rs['cpono'];
		$ccode = $rs['ccode'];
		$cname = $rs['cname'];
		$ctype = $rs['cpurchasetype'];
		echo "$csalesno|$ccode|$cname|$ctype\n";
	}



//echo $sql;

?>
