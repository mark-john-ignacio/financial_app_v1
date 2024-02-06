<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	//echo "<pre>";
	//print_r($_REQUEST);
	//echo "</pre>";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['txtid'];
	$cdesc = $_REQUEST['txtcdesc'];
	$acctdr = $_REQUEST['txtdracctid']; 
	$acctcr = $_REQUEST['txtcracctid'];  

	$taxvats = "";
	$taxvatsrate = "";
	if(isset($_REQUEST['txtvatcode'])){
		$taxvats = $_REQUEST['txtvatcode'];
		$taxvatsrate = $_REQUEST['txtvatcoderate'];
	}

	$taxewts = "";
	$taxewtsrate = "";
	if(isset($_REQUEST['txtewtcode'])){
		$taxewts = implode(',', $_REQUEST['txtewtcode']);
		$taxewtsrate = $_REQUEST['txtewtcoderate'];
	}

	$freqs = $_REQUEST['selfreq']; 

	$nxgross = str_replace(',', '', $_REQUEST['txtngross']);
	$nxnet = str_replace(',', '', $_REQUEST['txtnnet']);
	$nxvat = str_replace(',', '', $_REQUEST['txtnvat']);
	$nxewt = str_replace(',', '', $_REQUEST['txtnewt']);	
	 
	if($code=="new"){
		
		if (!mysqli_query($con,"INSERT INTO proforma_ap (`compcode`,`cdescription`,`cacctcodecr`,`cacctcodedr`,`cvatcode`,`cewtcode`,`cvatrate`,`cewtrate`,`ngross`,`nnetgross`,`nvatamt`,`newtamt`,`cfrequency`,`ddatemodified`) values ('$company','$cdesc','$acctcr','$acctdr','$taxvats','$taxewts','$taxvatsrate','$taxewtsrate','$nxgross','$nxnet','$nxvat','$nxewt','$freqs',NOW())")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
			$last_row = mysqli_insert_id($con);

			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$last_row','$preparedby',NOW(),'INSERTED','A/P PROFORMA','$compname','Inserted New Record')");

			if($_REQUEST['selfreq']=="Monthly"){
				mysqli_query($con,"INSERT INTO proforma_ap_freq(`compcode`, `proforma_ap_id`, `nmonthly`) values('$company','$last_row','".$_REQUEST['selmont']."')");
			}else if($_REQUEST['selfreq']=="Quarterly"){

				$ddd1 = $_REQUEST['selfreqq1']."/".$_REQUEST['selfreqq1d'];
				$ddd2 = $_REQUEST['selfreqq2']."/".$_REQUEST['selfreqq2d'];
				$ddd3 = $_REQUEST['selfreqq3']."/".$_REQUEST['selfreqq3d'];
				$ddd4 = $_REQUEST['selfreqq1']."/".$_REQUEST['selfreqq4d'];

				mysqli_query($con,"INSERT INTO proforma_ap_freq(`compcode`, `proforma_ap_id`, `cq1`, `cq2`, `cq3`, `cq4`) values('$company','$last_row','".$ddd1."','".$ddd2."','".$ddd3."','".$ddd4."')");
			}else if($_REQUEST['selfreq']=="Semi"){

				$sss1 = $_REQUEST['selfreqs1']."/".$_REQUEST['selfreqs1d'];
				$sss2 = $_REQUEST['selfreqs2']."/".$_REQUEST['selfreqs2d'];

				mysqli_query($con,"INSERT INTO proforma_ap_freq(`compcode`, `proforma_ap_id`, `cs1`, `cs2`) values('$company','$last_row','".$sss1."','".$sss2."')");
			}else if($_REQUEST['selfreq']=="Annual"){

				$sabn1 = $_REQUEST['selfreqannm']."/".$_REQUEST['selfreqannmd'];

				mysqli_query($con,"INSERT INTO proforma_ap_freq(`compcode`, `proforma_ap_id`, `dannual`) values('$company','$last_row','".$sabn1."')");
			}


			echo "True";
		}
	}
	else{
	
		if (!mysqli_query($con,"UPDATE proforma_ap set `cdescription` = '$cdesc',`cacctcodecr` = '$acctcr',`cacctcodedr` = '$acctdr',`cvatcode` = '$taxvats',`cewtcode` = '$taxewts',`cvatrate` ='$taxvatsrate', `cewtrate` = '$taxewtsrate',`ngross`= '$nxgross',`nnetgross` = '$nxnet',`nvatamt` = '$nxvat',`newtamt` = '$nxewt', `cfrequency` = '$freqs', `ddatemodified` = NOW() where `compcode` = '$company' and `nidentity` = '$code'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{

			echo "UPDATE proforma_ap set `cdescription` = '$cdesc',`cacctcodecr` = '$acctcr',`cacctcodedr` = '$acctdr',`cvatcode` = '$taxvats',`cewtcode` = '$taxewts',`cvatrate` ='$taxvatsrate', `cewtrate` = '$taxewtsrate',`ngross`= '$nxgross',`nnetgross` = '$nxnet',`nvatamt` = '$nxvat',`newtamt` = '$nxewt', `cfrequency` = '$freqs', `ddatemodified` = NOW() where `compcode` = '$company' and `nidentity` = '$code'";
									
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$code','$preparedby',NOW(),'UPDATED','A/P PROFORMA','$compname','Update Record')");
			
			if($_REQUEST['selfreq']=="Monthly"){
				mysqli_query($con,"UPDATE proforma_ap_freq set `nmonthly` = '".$_REQUEST['selmont']."' WHERE `compcode` = '$company' and  `proforma_ap_id` = '$code'");
			}else if($_REQUEST['selfreq']=="Quarterly"){

				$ddd1 = $_REQUEST['selfreqq1']."/".$_REQUEST['selfreqq1d'];
				$ddd2 = $_REQUEST['selfreqq2']."/".$_REQUEST['selfreqq2d'];
				$ddd3 = $_REQUEST['selfreqq3']."/".$_REQUEST['selfreqq3d'];
				$ddd4 = $_REQUEST['selfreqq4']."/".$_REQUEST['selfreqq4d'];

				mysqli_query($con,"UPDATE proforma_ap_freq set `cq1` = '".$ddd1."', `cq2` = '".$ddd2."', `cq3` = '".$ddd3."', `cq4` = '".$ddd4."' where `compcode`  = '$company' and `proforma_ap_id` = '$code'");

			}else if($_REQUEST['selfreq']=="Semi"){

				$sss1 = $_REQUEST['selfreqs1']."/".$_REQUEST['selfreqs1d'];
				$sss2 = $_REQUEST['selfreqs2']."/".$_REQUEST['selfreqs2d'];

				mysqli_query($con,"UPDATE proforma_ap_freq set `cs1` = '".$sss1."', `cs2` = '".$sss2."' WHERE `compcode` = '$company' and `proforma_ap_id` = '$code'");

			}else if($_REQUEST['selfreq']=="Annual"){

				$sabn1 = $_REQUEST['selfreqannm']."/".$_REQUEST['selfreqannmd'];

				mysqli_query($con,"UPDATE proforma_ap_freq set `dannual` = '".$sabn1."'  WHERE `compcode` = '$company' and `proforma_ap_id` = '$code'");
			}
			
			echo "True";
		}
		
	}

?>

<script>
	alert('Record Succesfully Saved');
	window.location.replace("Proforma.php");
</script>
