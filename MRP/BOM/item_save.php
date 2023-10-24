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

	//Insert Default if not exist
	$getitems = mysqli_query($con,"SELECT * FROM `mrp_bom_label` where compcode='$company' and citemno='$cMainItemNo' and nversion=1"); 

	if (mysqli_num_rows($getitems)==0) {
		mysqli_query($con,"insert into mrp_bom_label(compcode,citemno,nversion,cdesc,ldefault) values('".$_SESSION['companyid']."','$cMainItemNo',1,'Default',1)");
	}

	if (!mysqli_query($con, "UPDATE `mrp_bom` set `compcode` = 'xxx' Where `compcode` = '$company' and `cmainitemno` = '$cMainItemNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno='".$cMainItemNo ."'");

	$rowcount=mysqli_num_rows($sqllabelnme);
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);

	$totdcount = 1;
	if($rowcount>1){
		$totdcount = $rowcount;
	}

	for ($xz = 1; $xz <= $totdcount; $xz++) {

		$rowcnt = $_REQUEST['rowcnt'.$xz];
		$xmsg = "True";
		for($z=1; $z<=$rowcnt; $z++){
			$csort = mysqli_real_escape_string($con,$_REQUEST['txtsortnum'.$xz.$z]);
			$citemno = mysqli_real_escape_string($con,$_REQUEST['txtitmcode'.$xz.$z]);
			$cunitz = mysqli_real_escape_string($con,$_REQUEST['txtcunit'.$xz.$z]);
			$clevel = mysqli_real_escape_string($con,$_REQUEST['txtlvl'.$xz.$z]);
			$ctype = mysqli_real_escape_string($con,$_REQUEST['selType'.$xz.$z]);
			$qty = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtnqty'.$xz.$z]));
					

			if(!mysqli_query($con,"INSERT INTO `mrp_bom`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nlevel`, `nitemsort`, `ctype`, `nversion`) values('$company', '$cMainItemNo', '$citemno', '$cunitz', '$qty', '$clevel', '$csort', '$ctype', '$xz')")){
				
				printf("Errormessage: %s\n", mysqli_error($con));
				$xmsg = "False";
			}
		}

		//eco saving
		$cecosn = mysqli_real_escape_string($con,$_REQUEST['bomecosn'.$xz]);
		$cecorev = mysqli_real_escape_string($con,$_REQUEST['bomecorev'.$xz]);
		$cecoprep = mysqli_real_escape_string($con,$_REQUEST['bomecoprep'.$xz]);
		$cecodte = mysqli_real_escape_string($con,$_REQUEST['bomecodate'.$xz]);
		$cecodesc = mysqli_real_escape_string($con,$_REQUEST['bomecodesc'.$xz]);

		if(!mysqli_query($con,"UPDATE `mrp_bom_label` set `ecoSN` = '$cecosn', `ecoRev` = '$cecorev', `ecoPrepared` = '$cecoprep', `ecoDate` = '$cecodte', `ecoDesc` = '$cecodesc' where `compcode` = '$company' and `citemno` = '".$cMainItemNo ."' and nversion=".$xz)){				
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

	$getcustomer = mysqli_real_escape_string($con, $_REQUEST['citemcustomer']);
	$getproject = mysqli_real_escape_string($con, $_REQUEST['citemproj']);
	$gettitle = mysqli_real_escape_string($con, $_REQUEST['citemtitl']);

	$getitems = mysqli_query($con,"SELECT * FROM `mrp_items_parameters` where compcode='$company' and citemno='$cMainItemNo'"); 
	if (mysqli_num_rows($getitems)!=0) {
		if (!mysqli_query($con, "UPDATE `mrp_items_parameters` set `nworkhrs` = '$getworkhrs', `nsetuptime` = '$getsetup', `ncycletime` = '$getcycle', `ccustomer` = '$getcustomer', `cproject` = '$getproject', `ctitle` = '$gettitle' Where `compcode` = '$company' and `citemno` = '$cMainItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
	}else{
		if (!mysqli_query($con, "INSERT INTO `mrp_items_parameters`(`compcode`, `citemno`, `nworkhrs`, `nsetuptime`, `ncycletime`, `ccustomer`, `cproject`, `ctitle`) VALUES ('$company','$cMainItemNo','$getworkhrs','$getsetup','$getcycle', '$getcustomer', '$getproject', '$gettitle')")) {
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
