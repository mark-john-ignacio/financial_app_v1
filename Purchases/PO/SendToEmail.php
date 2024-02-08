
<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$tranno = $_REQUEST['cemailtranno'];
	$preparedby = $_SESSION['employeeid'];

	$emailto = $_REQUEST['cemailto'];
	$emailcc = $_REQUEST['cemailcc'];
	$emailbcc = $_REQUEST['cemailbcc'];
	$emailsbj = mysqli_real_escape_string($con, $_REQUEST['cemailsubject']);
	$emailbod = mysqli_real_escape_string($con, $_REQUEST['txtemailremarks']);


	if (!mysqli_query($con, "UPDATE purchase set cemailto='$emailto', cemailcc='$emailcc', cemailbcc='$emailbcc', cemailsubject='$emailsbj', cemailbody='$emailbod', cemailsentby='$preparedby', demailsent=NOW() Where compcode='$company' and cpono='$tranno'")){

		echo "<center><h3>ERROR<br>There is a problem saving your email details!<h3></center>";

	}else{

		$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

        if(mysqli_num_rows($sqlcomp) != 0){
    
            while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
            {
                $key = $rowcomp['code'];
            }
    
        }

		//echo $tranno . " <br> ";

		echo "<center><h3>EMAIL SENDING<br>Please Wait!<h3><img src='../../images/emailsend.gif' width='200px'></center>";
		//$xtranno = MyEnc($tranno,$key);
	?>
		<form action="PrintPO_Email.php" method="post" name="frmQPrint" id="frmQprint" target="_blank">
			<input type="hidden" name="hdntransid" id="hdntransid" value="<?php echo $tranno; ?>">
		</form>

		<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
		<script> 
			$("frmQPrint").submit(); 
		</script>";
	<?php
		//echo $xtranno . " , " . $key ;
		//header("refresh:3;url=PrintPO_Email.php?id=".$xtranno);
		
		
	}

	


?>