<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];

	$cMainItemNo = $_REQUEST['xcitm'];

	$qryver = "";
	if($_REQUEST['xcvers']!="0"){
		$qryver = " and nversion = ".$_REQUEST['xcvers'];
	}

	if (!mysqli_query($con, "UPDATE `mrp_bom` set `compcode` = 'xxx' Where `compcode` = '$company' and `cmainitemno` = '$cMainItemNo'".$qryver)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$reader->setReadDataOnly(true);

	$spreadsheet = $reader->load($_REQUEST['id']);
	$sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
	$data = $sheet->toArray();

	$xmsg = "True";

	foreach($data as $data){
		if($data[0]!="sortnum"){

			$ifGo = "Yes";
			if($_REQUEST['xcvers']!="0"){
				if($data[5]!=$_REQUEST['xcvers']){
					$ifGo = "No";
				}
			}

			if($ifGo == "Yes"){
				$sql = "INSERT INTO `mrp_bom`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nlevel`, `nitemsort`, `nversion`, `ctype`) VALUES ('$company','$cMainItemNo','".$data[1]."','".$data[2]."','".$data[3]."','2','".$data[0]."','".$data[5]."','".$data[4]."')";

				//echo $sql."<br><br>";
				if(!mysqli_query($con,$sql)){
					$xmsg = "False";
				}
			}
		}
	}


	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cMainItemNo','$preparedby',NOW(),'UPLOADED','ITEM BOM','$compname','Uploaded Record')");

	if($xmsg == "True"){
		$concat = $company."_".date("mdYHis");
		mysqli_query($con, "INSERT INTO `mrp_bom_bckup`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nlevel`, `nitemsort`, `nversion`, `ctype`) Select '".$concat."', `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nlevel`, `nitemsort`, `nversion`, `ctype` From mrp_bom Where `compcode` = 'xxx' and `cmainitemno` = '$cMainItemNo'".$qryver);

		mysqli_query($con, "DELETE FROM `mrp_bom` Where `compcode` = 'xxx' and `cmainitemno` = '$cMainItemNo'".$qryver);
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