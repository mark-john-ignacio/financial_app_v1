<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require '../../vendor/autoload.php';

	//require("../vendor/phpmailer/phpmailer/src/Exception.php");
	//require("../vendor/phpmailer/phpmailer/src/PHPMailer.php");
	//require("../vendor/phpmailer/phpmailer/src/SMTP.php");

	function sendEmail($email_to,$body,$subject,$companyname){

		$fromserver = "myxfin@serttech.com"; 
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = "smtp.gmail.com"; // Enter your host here
		$mail->SMTPAuth = true;
		$mail->Username = "maita.galang@gmail.com"; // Enter your email here
		$mail->Password = "odxipppwdmpgechm"; //Enter your password here
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->IsHTML(true);
		$mail->From = "noreply@serttech.com";
		$mail->FromName = $companyname;
		$mail->Sender = "myxfin@serttech.com"; // indicates ReturnPath header
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($email_to);

		if(!$mail->Send()){
			//echo "Mailer Error: " . $mail->ErrorInfo;
		}else{
			//echo "Email Successfully Sent";
		}
	}


?>
