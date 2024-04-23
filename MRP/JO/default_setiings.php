<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$mggx = "";

	if (!mysqli_query($con, "UPDATE parameters set `cvalue` = '".$_POST['selwhfrom']."' where `compcode` = '$company' and `ccode` = 'JO_MRS_FROM'")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{
		$mggx = "True";
	}

	if (!mysqli_query($con, "UPDATE parameters set `cvalue` = '".$_POST['selwhto']."' where `compcode` = '$company' and `ccode` = 'JO_MRS_TO'")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{
		$mggx = "True";
	}

	echo $mggx;

	if($mggx=="True"){
?>
		<script>
			alert("Settings Successfully Saved!");
			window.location.href = "JO.php";
		</script>
<?php
	}else{
?>
		<script>
			alert("<?=$mggx?>");
			window.location.href = "JO.php";
		</script>
<?php
	}
?>
