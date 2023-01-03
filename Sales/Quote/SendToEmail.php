<?php

	require("vendor/phpmailer/phpmailer/src/PHPMailer.php");
    require("vendor/phpmailer/phpmailer/src/SMTP.php");


	if(!isset($_SESSION)){
	session_start();
	}
	require_once "Connection/connection_string.php";

	$csalesno = $_REQUEST['id'];
	$sqlhead = mysqli_query($con,"select * from quote where compcode='$company' and ctranno = '$csalesno'");
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$email = $row['ccontemai'];
		}
	}


	$output='<p>Dear user,</p>';
	$output.='<p>Please click on the following link to reset your password.</p>';
	$output.='<p>-------------------------------------------------------------</p>';
	$output.='<p><a href="http://localhost/MyxFin/include/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">Reset Password</a></p>'; 
	$output.='<p>-------------------------------------------------------------</p>';
	$output.='<p>The link will expire after 1 day for security reason.</p>';
	$output.='<p>If you did not request this forgotten password email, no action 
	is needed, your password will not be reset. However, you may want to log into 
	your account and change your security password as someone may have guessed it.</p>';   
	$output.='<p>Thanks,</p>';
	$output.='<p>Myx Financials,</p>';
	$body = $output; 
	$subject = "Myx Financials - Password Recovery";
 
	$email_to = 'mhaitz.endriga@gmail.com';
	$fromserver = "noreply@serttech.com"; 
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP();
	$mail->Host = "smtp.googlemail.com"; // Enter your host here
	$mail->SMTPAuth = true;
	$mail->Username = "myxwebportal@gmail.com"; // Enter your email here
	$mail->Password = "?May052486..."; //Enter your password here
	$mail->Port = 25;
	$mail->IsHTML(true);
	$mail->From = "noreply@yourwebsite.com";
	$mail->FromName = "Myx Financials";
	$mail->Sender = "myxwebportal@gmail.com"; // indicates ReturnPath header
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($email_to);
	$mail->AddCC('maita.galang@gmail.com','Sert Guro');
	if(!$mail->Send()){
		echo "Mailer Error: " . $mail->ErrorInfo;
	}else{
		echo "Email Successfully Sent";
	}

?>