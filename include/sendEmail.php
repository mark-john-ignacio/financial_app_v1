<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require '../../vendor/autoload.php';

	//require("../vendor/phpmailer/phpmailer/src/Exception.php");
	//require("../vendor/phpmailer/phpmailer/src/PHPMailer.php");
	//require("../vendor/phpmailer/phpmailer/src/SMTP.php");

	function sendEmail($email_to,$body,$subject,$companyname,$getcred){

		$fromserver = $getcred['cusnme']; 
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $getcred['csmtp']; // Enter your host here
		$mail->SMTPAuth = true;
		$mail->Username = $getcred['cusnme']; // Enter your email here
		$mail->Password = $getcred['cuspass']; //Enter your password here
		$mail->SMTPSecure = $getcred['csecure'];
		$mail->Port = $getcred['cport'];;
		$mail->IsHTML(true);
		$mail->From = $getcred['cusnme'];
		$mail->FromName = $companyname;
		$mail->Sender = $getcred['cusnme']; // indicates ReturnPath header
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
