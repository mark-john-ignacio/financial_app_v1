
<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$tranno = $_REQUEST['cemailtranno'];
	$preparedby = $_SESSION['employeeid'];

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$key = $rowcomp['code'];
		}

	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<title>Myx Financials</title>
</head>

<body style="padding:5px">

	<form action="PrintPO_Email.php" method="post" name="frmQPrint" id="frmQprint">
		<input type="hidden" name="hdntransid" id="hdntransid" value="<?=$tranno; ?>">
	</form>

	<?php

		$emailto = $_REQUEST['cemailto'];
		$emailcc = $_REQUEST['cemailcc'];
		$emailbcc = $_REQUEST['cemailbcc'];
		$emailsbj = mysqli_real_escape_string($con, $_REQUEST['cemailsubject']);
		$emailbod = mysqli_real_escape_string($con, $_REQUEST['txtemailremarks']);


		if (!mysqli_query($con, "UPDATE purchase set cemailto='$emailto', cemailcc='$emailcc', cemailbcc='$emailbcc', cemailsubject='$emailsbj', cemailbody='$emailbod', cemailsentby='$preparedby', demailsent=NOW() Where compcode='$company' and cpono='$tranno'")){

			echo "<center><h3>ERROR<br>There is a problem saving your email details!<h3></center>";

		}else{

			echo "<center><h3>EMAIL SENDING<br>Please Wait!<h3><img src='../../images/emailsend.gif' width='200px'></center>";
		?>			
			<script> 
				setTimeout(function() {
					alert("here");
					$("#frmQprint").submit();		
				}, 3000); 
			</script>
		<?php			
			
		}

		?>

</body>