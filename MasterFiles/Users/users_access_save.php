<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	$userid = $_REQUEST['userid'];

	$name = $_POST['chkAcc'];
	$ctr = 0;
	$itms = "";

	mysqli_query($con,"delete from users_access where userid='$userid'");

	foreach ($name as $id){
		
		//echo $id;
		$ctr = $ctr + 1;
		$xid = explode("|",$id);

		if($xid[0]=="dashboard" || $xid[0]=="audittrail"){

			$sql = "insert into users_access(pageid,menu_id,userid) 
			values('$xid[0]','$xid[1]','$userid')";

		}else{

			$sql = "insert into users_access(pageid,menu_id,main_id,userid) 
			values('$xid[0]','$xid[1]','$xid[2]','$userid')";

		}

		if (!mysqli_query($con, $sql)) {
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	}


if(isset($_POST['chkSections'])){
	$secz = $_POST['chkSections'];
	$ctr = 0;
	mysqli_query($con,"delete from users_sections where userid='$userid'");
	foreach ($secz as $id){
		$ctr = $ctr + 1;

		$sql = "insert into users_sections(section_nid,userid) 
		values('$id','$userid')";
		
		if (!mysqli_query($con, $sql)) {
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	}
}
if(isset($_POST['radioCheck'])){
	$radio = $_POST["radioCheck"];
	mysqli_query($con, "insert into users_access(pageid,userid) 
	values('$radio','$userid')");

}


 
echo '<script language="javascript">
alert("User\'s Access Successfully Updated")
location.href="users.php?f="
</script>';
?>
