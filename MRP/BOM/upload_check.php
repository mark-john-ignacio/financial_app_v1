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

	if (!mysqli_query($con, "UPDATE `mrp_bom` set `compcode` = 'xxx' Where `compcode` = '$company' and `cmainitemno` = '$cMainItemNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$reader->setReadDataOnly(true);

	$spreadsheet = $reader->load($_REQUEST['id']);
	$sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
	$data = $sheet->toArray();

	$xmsg = "True";

	foreach($data as $data){
		if($data[0]!="sortnum"){
			$sql = "INSERT INTO `mrp_bom`(`compcode`, `cmainitemno`, `citemno`, `cunit`, `nqty1`, `nqty2`, `nqty3`, `nqty4`, `nqty5`, `nlevel`, `nitemsort`) VALUES ('$company','$cMainItemNo','".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."','".$data[5]."','".$data[6]."','".$data[7]."','".$data[8]."','".$data[0]."')";

			//echo $sql."<br><br>";
			if(!mysqli_query($con,$sql)){
				$xmsg = "False";
			}
		}
	}


	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cMainItemNo','$preparedby',NOW(),'UPLOADED','ITEM BOM','$compname','Uploaded Record')");

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