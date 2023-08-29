<?php

	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	//echo "<pre>";
	//print_r($_REQUEST);
	//echo "</pre>";

	$company = $_SESSION['companyid'];
	$cMainItemNo = $_REQUEST['cmainitemno'];

	if (!mysqli_query($con, "UPDATE `mrp_bom` set `compcode` = 'xxx' Where `compcode` = '$company' and `cmainitemno` = '$cMainItemNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$rowcnt = $_REQUEST['rowcnt'];
	$xmsg = "True";
	for($z=1; $z<=$rowcnt; $z++){
		$csort = mysqli_real_escape_string($con,$_REQUEST['txtsortnum'.$z]);
		$citemno = mysqli_real_escape_string($con,$_REQUEST['txtitmcode'.$z]);
		$cunitz = mysqli_real_escape_string($con,$_REQUEST['txtcunit'.$z]);
		$clevel = mysqli_real_escape_string($con,$_REQUEST['txtlvl'.$z]);

				$qty1 = 0;
				$qty2 = 0;
				$qty3 = 0;
				$qty4 = 0;
				$qty5 = 0;

				$getcnt = intval($_REQUEST['hdncount']);
				for ($i = 1; $i <= $getcnt; $i++) {

					if($i==1){
						$qty1 = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$i.$z]));
					}

					if($i==2){
						$qty2 = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$i.$z]));
					}

					if($i==3){
						$qty3 = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$i.$z]));
					}

					if($i==4){
						$qty4 = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$i.$z]));
					}

					if($i==5){
						$qty5 = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$i.$z]));
					}
				}

				if(!mysqli_query($con,"INSERT INTO `mrp_bom`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nqty2`, `nqty3`, `nqty4`, `nqty5`, `nlevel`, `nitemsort`) values('$company', '$cMainItemNo', '$citemno', '$cunitz', '$qty1', '$qty2', '$qty3', '$qty4', '$qty5', '$clevel', '$csort')")){
			
					printf("Errormessage: %s\n", mysqli_error($con));
					$xmsg = "False";
				}
	}


	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cMainItemNo','$preparedby',NOW(),'INSERTED','ITEM BOM','$compname','Update Record')");

	if($xmsg == "True"){
		mysqli_query($con, "DELETE FROM `mrp_bom` Where `compcode` = 'xxx' and `cmainitemno` = '$cMainItemNo'");
?>

	<script>
		alert('Record Succesfully Saved');
		window.location = "items.php?itm=<?=$cMainItemNo?>";
	</script>

	<?php
	}else{
?>
	<script>
		alert('Theres a problem saving your file!');
		window.location = "items.php?itm=<?=$cMainItemNo?>";
	</script>
<?php
	}
	?>
