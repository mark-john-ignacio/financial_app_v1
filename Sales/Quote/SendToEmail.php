<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$tranno = $_POST['cemailtranno'];
	$preparedby = $_SESSION['employeeid'];

	$emailto = $_POST['cemailto'];
	$emailcc = $_POST['cemailcc'];
	$emailbcc = $_POST['cemailbcc'];
	$emailsbj = mysqli_real_escape_string($con, $_POST['cemailsubject']);
	$emailbod = mysqli_real_escape_string($con, $_POST['txtemailremarks']);


	if (!mysqli_query($con, "UPDATE quote set cemailto='$emailto', cemailcc='$emailcc', cemailbcc='$emailbcc', cemailsubject='$emailsbj', cemailbody='$emailbod', cemailsentby='$preparedby', demailsent=NOW() Where compcode='$company' and ctranno='$tranno'")){

		echo "<center><h3>ERROR<br>There is a problem saving your email details!<h3></center>";

	}else{

		$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

        if(mysqli_num_rows($sqlcomp) != 0){
    
            while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
            {
                $key = $rowcomp['code'];
            }
    
        }

		echo "<center><h3>EMAIL SENDING<br>Please Wait!<h3><img src='../../images/emailsend.gif' width='200px'></center>";
		//$xtranno = MyEnc($tranno,$key);

		//echo $xtranno ;

		if($_POST['cemailtrantyp']=="billing"){
			header("refresh:5;url=PrintBilling_Email.php?id=".$tranno);
		}else{
			header("refresh:5;url=PrintQuote_Email.php?id=".$tranno);
		}
		
	}

	


?>