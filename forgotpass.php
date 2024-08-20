
<?php
    if (!isset($_SESSION)) {
        session_start();
    }

	include('vendor/autoload.php');

	require("vendor/phpmailer/phpmailer/src/PHPMailer.php");
	require("vendor/phpmailer/phpmailer/src/SMTP.php");

    include('Connection/connection_string.php');
    include('Model/helper.php');


	if(isset($_POST['email'])){
		$email = $_POST['email'];

		$expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
		$expDate = date("Y-m-d H:i:s",$expFormat);
		$key = md5((2418*2).$email);
		$addKey = substr(md5(uniqid(rand(),1)),3,10);
		$key = $key . $addKey;
			// Insert Temp Table
		mysqli_query($con,"INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`) VALUES ('".$email."', '".$key."', '".$expDate."');");
	 
		$output='<p>Dear user,</p>';
		$output.='<p>Please click on the following link to reset your password.</p>';
		$output.='<p>-------------------------------------------------------------</p>';
		$output.='<p><a href="https://'.$_SERVER['HTTP_HOST'].'/include/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">Reset Password</a></p>'; 
		$output.='<p>-------------------------------------------------------------</p>';
		$output.='<p>The link will expire after 1 day for security reason.</p>';
		$output.='<p>If you did not request this forgotten password email, no action 
		is needed, your password will not be reset. However, you may want to log into 
		your account and change your security password as someone is trying to hack your account.</p>';   
		$output.='<p>Thanks,</p>';
		$output.='<p>Myx Financials,</p>';
		$body = $output; 
		$subject = "Myx Financials - Password Recovery";
 
		$email_to = $email;
		$fromserver = "myxfin@serttech.com"; 

		//use PHPMailer\PHPMailer\PHPMailer; 	
		//use PHPMailer\PHPMailer\Exception;

		$getcred = getEmailCred();

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $getcred['csmtp']; // Enter your host here
		$mail->SMTPAuth = true;
		$mail->Username = $getcred['cusnme']; // Enter your email here
		$mail->Password = $getcred['cuspass']; //Enter your password here
		$mail->SMTPSecure = $getcred['csecure'];
		$mail->Port = $getcred['cport'];
		$mail->IsHTML(true);
		$mail->From = $getcred['cusnme'];
		$mail->FromName = "MYX Financials";
		$mail->Sender = $getcred['cusnme']; // indicates ReturnPath header
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($email_to);
		if(!$mail->Send()){
			$error = 'Mail error: '.$mail->ErrorInfo;

			return false;
		}else{

			$_SESSION['xmessage'] = "An email has been sent to you with instructions on how to reset your password.";
			header("Location: index.php");

 		}

	}else{
		header("Location: index.php");
	}
?>
