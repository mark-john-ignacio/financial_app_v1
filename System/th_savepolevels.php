<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$isokall = "True";
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];

	mysqli_query($con,"UPDATE purchase_approvals_id set compcode='".date('m/d/Y_H:i:s')."' where compcode='$company'");


	$result = mysqli_query ($con, "Select * From purchase_approvals where compcode='$company'"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		$lvlnum = $row['nlevel'];

		$x1 = intval($_POST['tbLVL'.$lvlnum.'count']);

		$amtmin = $_POST['lvlamt'.$lvlnum];

		$con->query("Update purchase_approvals set namount = '".$amtmin."' where compcode='$company' and nlevel='".$lvlnum."'");

		if($x1>0){
			for ($x = 1; $x <= $x1; $x++) {
				$userdid1 = $_POST['selposuser'.$lvlnum.$x];

				if(isset($_POST['selpoitmtyp'.$lvlnum.$x])){
					$itmtypd1 =  ($_POST['selpoitmtyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selpoitmtyp'.$lvlnum.$x]) : "";
				}else{
					$itmtypd1 = "";
				}

				if(isset($_POST['selposutyp'.$lvlnum.$x])){
					$supptyp1 =  ($_POST['selposutyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selposutyp'.$lvlnum.$x]) : "";
				}else{
					$supptyp1 = "";
				}

				$sql = "INSERT INTO purchase_approvals_id (`compcode`,`po_approval_id`,`userid`,`items`,`suppliers`) values ('$company','$lvlnum','$userdid1','$itmtypd1','$supptyp1')";

				if ($con->query($sql) === TRUE) {
					$last_id = $con->insert_id;

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$userdid1','$preparedby',NOW(),'UPDATED','PO APPROVALS $lvlnum','$compname','Update Record')");

				} 
				else{

					//printf("Errormessage: %s\n", mysqli_error($con));

					$isokall = "False";
											
				}

			}
		}

	}

if($isokall=="True"){
	?>
	<script>
		alert("PO Approvals Successfully Saved!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}else{
	?>
	<script>
		alert("PO Approvals has error saving!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}

?>
