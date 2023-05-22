<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$isokall = "True";
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];

	mysqli_query($con,"UPDATE quote_approvals_id set compcode='".date('m/d/Y_H:i:s')."' where compcode='$company'");


	$result = mysqli_query ($con, "Select * From quote_approvals where compcode='$company'"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		$lvlnum = $row['nlevel'];

		$x1 = intval($_POST['tbLQL'.$lvlnum.'count']);

		if($x1>0){
			for ($x = 1; $x <= $x1; $x++) {
				$userdid1 = $_POST['selqosuser'.$lvlnum.$x];

				if(isset($_POST['selqoitmtyp'.$lvlnum.$x])){
					$itmtypd1 =  ($_POST['selqoitmtyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selqoitmtyp'.$lvlnum.$x]) : "";
				}else{
					$itmtypd1 = "";
				}

				if(isset($_POST['selqosutyp'.$lvlnum.$x])){
					$supptyp1 =  ($_POST['selqosutyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selqosutyp'.$lvlnum.$x]) : "";
				}else{
					$supptyp1 = "";
				}

				if(isset($_POST['selqotrtyp'.$lvlnum.$x])){
					$qortyp1 =  ($_POST['selqotrtyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selqotrtyp'.$lvlnum.$x]) : "";
				}else{
					$qortyp1 = "";
				}

				$sql = "INSERT INTO quote_approvals_id (`compcode`,`qo_approval_id`,`userid`,`items`,`suppliers`,`qotype`) values ('$company','$lvlnum','$userdid1','$itmtypd1','$supptyp1','$qortyp1')";

				if ($con->query($sql) === TRUE) {
					$last_id = $con->insert_id;

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$userdid1','$preparedby',NOW(),'UPDATED','QUOTE APPROVALS $lvlnum','$compname','Update Record')");

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
		alert("Quote Approvals Successfully Saved!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}else{
	?>
	<script>
		alert("Quote Approvals has error saving!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}

?>
