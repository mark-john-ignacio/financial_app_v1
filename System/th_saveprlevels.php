<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$isokall = "True";
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";

	$company = $_SESSION['companyid'];

	mysqli_query($con,"UPDATE purchrequest_approvals_id set compcode='".date('m/d/Y_H:i:s')."' where compcode='$company'");


	$result = mysqli_query ($con, "Select * From purchrequest_approvals where compcode='$company'"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$lvlnum = $row['nlevel'];

		//echo $lvlnum."<br>";

		$x1 = intval($_POST['tblPRLVL'.$lvlnum.'count']);

		$amtmin = $_POST['lvlamt'.$lvlnum];

		$con->query("Update purchrequest_approvals set namount = '".$amtmin."' where compcode='$company' and nlevel='".$lvlnum."'");

		if($x1>0){
			for ($x = 1; $x <= $x1; $x++) {
				$userdid1 = $_POST['selprsuser'.$lvlnum.$x];

				if(isset($_POST['selprsecs'.$lvlnum.$x])){
					$itmtypd1 =  ($_POST['selprsecs'.$lvlnum.$x]!=="") ? implode(",",$_POST['selprsecs'.$lvlnum.$x]) : "";
				}else{
					$itmtypd1 = "";
				}

				$sql = "INSERT INTO purchrequest_approvals_id (`compcode`,`pr_approval_id`,`userid`,`locations_id`) values ('$company','$lvlnum','$userdid1','$itmtypd1')";

				if ($con->query($sql) === TRUE) {
					$last_id = $con->insert_id;

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$userdid1','$preparedby',NOW(),'UPDATED','PR APPROVALS $lvlnum','$compname','Update Record')");

				} 
				else{

					printf("Errormessage: %s\n", mysqli_error($con));

					$isokall = "False";
											
				}

			}
		}

	}

if($isokall=="True"){
	?>
	<script>
		alert("PR Approvals Successfully Saved!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}else{
	?>
	<script>
		alert("PR Approvals has error saving!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}

?>
