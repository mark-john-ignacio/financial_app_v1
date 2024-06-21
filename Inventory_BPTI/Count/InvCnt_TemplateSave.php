<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$rwcnt = $_REQUEST['rowcnt']; 
	$selwh = $_REQUEST['selwhfrom'];
	$seltmltnme = $_REQUEST['seltempname'];

	$witherrr = 0;

	if($seltmltnme==""){

		$seltmltnmetext = $_REQUEST['newtemptxt'];

		if (!mysqli_query($con,"INSERT INTO invcount_template_names(`compcode`, `section_nid`, `tempname`) values('$company', '$selwh','$seltmltnmetext')")){
			$witherrr++;
		}

		$last_id = mysqli_insert_id($con);

	}else{
		$last_id = $seltmltnme;

		mysqli_query($con,"delete from invcount_template where compcode = '$company' and section_nid='$selwh' and template_id=".$last_id);
	}

	

	for($i = 1 ; $i<=$rwcnt ; $i++ ){
		$thecid = $selwh."_".$_REQUEST['txtsortnum'.$i]."_".$last_id;
		$csortnum = $_REQUEST['txtsortnum'.$i];
		$citemno = $_REQUEST['txtitmcode'.$i];
		$citemdesc = $_REQUEST['txtitmdesc'.$i];
		$citemunit = $_REQUEST['txtcunit'.$i];

		if (!mysqli_query($con,"INSERT INTO invcount_template(`compcode`, `cid`, `section_nid`, `template_id`, `sortnum`, `citemno`, `citemdesc`, `cunit`) values('$company', '$thecid', '$selwh', $last_id,'$csortnum', '$citemno', '$citemdesc', '$citemunit')")){
			//echo "Errormessage: %s\n", mysqli_error($con);
			$witherrr++;
		}
	}

	if($witherrr==0){
?>

	<script>
		alert('Record Succesfully Saved');
		window.location="InvCnt_Template.php";
	</script>

<?php
	}else{
?>

	<script>
		alert('There are errors saving your data!');
		window.location="InvCnt_Template.php";
	</script>

<?php
	}
	

?>
