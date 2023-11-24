<?php
if(!isset($_SESSION)){
session_start();
}

$company = $_SESSION['companyid'];
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select a.*,b.cname from receive a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno LIKE '%$q%' and a.lcancelled=0 and a.lapproved=1";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$crrno = $rs['ctranno'];
		$ccode = $rs['ccode'];
		$cname = $rs['cname'];
		$ctype = $rs['creceivetype'];
		echo "$crrno|$ccode|$cname|$ctype\n";
	}



//echo $sql;

?>
