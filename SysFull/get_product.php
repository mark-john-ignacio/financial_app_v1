<?php
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select * from items where citemdesc LIKE '%$q%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$cid = $rs['cpartno'];
		$cname = $rs['citemdesc'];
		$nprice = $rs['nretailcost'];
		$cunit = $rs['cunit'];
		echo "$cname|$cid|$nprice|$cunit\n";
	}



//echo $sql;

?>
