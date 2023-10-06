<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	$tranno = $_REQUEST['txtctranno'];
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$dmonth = date("m");
	$dyear = date("y");


	$msgz = "";
	$status = "True";

	$JOQty = 0;
	$JOItem = "";
	//get mainwty
	$sql = "select * from mrp_jo X where X.compcode='$company' and X.ctranno = '$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$JOItem = $row2['citemno'];
		$JOQty = $row2['nqty'];
	}

	$sql = "select X.ctranno, X.nrefident, X.citemno, A.citemdesc, X.cunit, X.nqty as totqty
	from mrp_jo_process X
	left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
	where X.compcode='$company' and X.mrp_jo_ctranno = '$tranno' order by X.ctranno";

	$dmainaryy = array();
	$xtranlit = array();
	$xitmslit = array();
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$dmainaryy[] = $row2;
		$xtranlit[] = $row2['ctranno'];
		$xitmslit[] = $row2['nrefident'];
	}

	mysqli_query($con,"DELETE FROM mrp_jo_process_t where compcode='$company' and ctranno in ('".implode("','",$xtranlit)."')");

	//allprocesslist
	$arrprolist  = array();
	$chkSales = mysqli_query($con,"select A.citemno, A.items_process_id, B.cdesc from mrp_process_t A left join mrp_process B on A.compcode=B.compcode and A.items_process_id=B.nid where A.compcode='$company' Order By A.citemno, A.nid");
	while($rs3 = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$arrprolist[] = $rs3;
	}
	
		foreach($dmainaryy as $row2){

			
			foreach($arrprolist as $rs4){
				if($rs4['citemno']==$row2['citemno']){

					if (!mysqli_query($con, "INSERT INTO mrp_jo_process_t(`compcode`, `ctranno`, `mrp_process_id`, `mrp_process_desc`) values('$company', '".$row2['ctranno']."', '".$rs4['items_process_id']."', '".$rs4['cdesc']."')")) {
					
						$status = "False";
						$msgz = $msgz . "<b>ERROR ON ".$row2['ctranno'].": </b>There's a problem generating your process!";

					}

				}
			}

		}


	//mysqli_query($con,"Update so set lsent=1 where compcode='$company' and ctranno='$tranno'");

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	//echo json_encode($json2);


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
</head>

<body style="padding-top:20px">

<h3><center>GENERATING PROCESS LIST...</center><h3>
<h1><center><span id="counter"> 0 </span></center><h1>

<form action="JO_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$tranno;?>" />
</form>

</body>
</html>

<script type="text/javascript">

	var count = 5;

	$(document).ready(function() {
		counter();
	});

	function counter()
  {
		if ( count > 0 )
    {
			count--;
      document.querySelector("#counter").innerHTML = count;
			var c = setTimeout( counter, 500 );
		}else
    {
			document.forms['frmpos'].submit();
		}
	}


 
</script>