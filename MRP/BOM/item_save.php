<?php

	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	//echo "<pre>";
//	print_r($_REQUEST);
	//echo "</pre>";

	$company = $_SESSION['companyid'];
	$cMainItemNo = $_REQUEST['cmainitemno'];

	//Insert Default if not exist
	$getitems = mysqli_query($con,"SELECT * FROM `mrp_bom_label` where compcode='$company' and citemno='$cMainItemNo' and nversion=1"); 

	if (mysqli_num_rows($getitems)==0) {
		mysqli_query($con,"insert into mrp_bom_label(compcode,citemno,nversion,cdesc,ldefault) values('".$_SESSION['companyid']."','$cMainItemNo',1,'Default',1)");
	}

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
		$ctype = mysqli_real_escape_string($con,$_REQUEST['selType'.$z]);

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

				if(!mysqli_query($con,"INSERT INTO `mrp_bom`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nqty2`, `nqty3`, `nqty4`, `nqty5`, `nlevel`, `nitemsort`, `ctype`) values('$company', '$cMainItemNo', '$citemno', '$cunitz', '$qty1', '$qty2', '$qty3', '$qty4', '$qty5', '$clevel', '$csort', '$ctype')")){
			
					printf("Errormessage: %s\n", mysqli_error($con));
					$xmsg = "False";
				}
	}

	if($xmsg=="True"){
		if (!mysqli_query($con, "DELETE FROM `mrp_bom` Where `compcode` = 'xxx' and `cmainitemno` = '$cMainItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
	}


	//update process
	if (!mysqli_query($con, "DELETE from `mrp_process_t` where `compcode` = '$company' and `citemno` = '$cMainItemNo'")) {
		if(mysqli_error($con)!=""){
			$myerror =  "Error PROCESSES DEL: ".mysqli_error($con);
		}
	} 

	$ProcRowCnt = $_REQUEST['hdnprocesslist'];
	if($ProcRowCnt>=1){
		//echo $UnitRowCnt;
		for($z=1; $z<=$ProcRowCnt; $z++){
			$cItemProc = $_REQUEST['selproc'.$z];
			
			//mysqli_query($con,"INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)");
			
			if (!mysqli_query($con, "INSERT INTO `mrp_process_t`(`compcode`, `items_process_id`, `citemno`) VALUES ('$company','$cItemProc','$cMainItemNo')")) {
					if(mysqli_error($con)!=""){
						echo "Error UOM: ".mysqli_error($con);
					}
			} 
			$cItemProc = 0;

		}
	}

	//parameters 
	$getworkhrs = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['nworkinghrs']));
	$getsetup = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['nsetuptime']));
	$getcycle = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['ncycletime']));

	$getitems = mysqli_query($con,"SELECT * FROM `mrp_items_parameters` where compcode='$company' and citemno='$cMainItemNo'"); 
	if (mysqli_num_rows($getitems)!=0) {
		if (!mysqli_query($con, "UPDATE `mrp_items_parameters` set `nworkhrs` = '$getworkhrs', `nsetuptime` = '$getsetup', `ncycletime` = '$getcycle' Where `compcode` = '$company' and `citemno` = '$cMainItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
	}else{
		if (!mysqli_query($con, "INSERT INTO `mrp_items_parameters`(`compcode`, `citemno`, `nworkhrs`, `nsetuptime`, `ncycletime`) VALUES ('$company','$cMainItemNo','$getworkhrs','$getsetup','$getcycle')")) {
			printf("Errormessage: %s\n", mysqli_error($con));
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
