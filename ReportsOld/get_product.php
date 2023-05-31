<?php
if(!isset($_SESSION)){
session_start();
}
$company = $_SESSION['companyid'];

require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select * from items where compcode='$company' and citemdesc LIKE '%$q%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$cid = $rs['cpartno'];
		$cname = $rs['citemdesc'];
		$nprice = $rs['nretailcost'];
		$cunit = $rs['cunit'];
		$ndisc = $rs['ndiscount'];
		echo "$cname|$cid|$nprice|$cunit|$ndisc\n";
	}



//echo $sql;

?>
