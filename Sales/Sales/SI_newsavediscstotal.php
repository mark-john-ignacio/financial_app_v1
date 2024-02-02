<?php
	if(!isset($_SESSION)){
	session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$trancode = $_REQUEST['trancode'];
		
	$ntotgrossdisc = 0;
	$hdr = mysqli_query($con,"Select ngrossdisc from sales where compcode='$company' and ctranno='$trancode'");
	if (mysqli_num_rows($hdr)!=0) {
		$all_course_data = mysqli_fetch_array($hdr, MYSQLI_ASSOC);						 
		$ntotgrossdisc = $all_course_data['ngrossdisc']; 							
	}		

	$dediscs = 0;
	$det = mysqli_query($con,"Select ndiscount from sales_t where compcode='$company' and ctranno='$trancode' and ndiscount > 0");
	if (mysqli_num_rows($det)!=0) {
		while($rows = mysqli_fetch_array($det, MYSQLI_ASSOC)){
			$dediscs = $dediscs + floatval($rows['ndiscount']);
		}
	}

	//update totaldisc sa header
	$xtotal = floatval($ntotgrossdisc) + floatval($dediscs);
	if (!mysqli_query($con,"UPDATE sales set ntotaldiscounts=".$xtotal." where compcode='$company' and ctranno='$trancode'")){
		echo "Errormessage: %s\n", mysqli_error($con);
	}else{
		echo "True";
	}
?>
